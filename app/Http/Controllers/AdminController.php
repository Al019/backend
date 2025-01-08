<?php

namespace App\Http\Controllers;

use App\Mail\PasswordMail;
use App\Models\RequestCredential;
use App\Models\StudentLink;
use App\Models\User;
use App\Models\Staff;
use App\Models\Submit;
use App\Models\Student;
use App\Models\Document;
use App\Models\Information;
use Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Exports\StudentExport;
use App\Imports\StudentImport;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    public function importStudent(Request $request)
    {
        $request->validate([
            'import_file' => ['required', 'file', 'mimes:xlsx,xls'],
        ]);

        Excel::import(new StudentImport, $request->file('import_file'));
    }

    public function exportStudent()
    {
        return Excel::download(new StudentExport, 'students.xlsx');
    }

    public function getStudent()
    {
        $students = Student::with('information')
            ->get();

        return response()->json($students);
    }

    public function addStaff(Request $request)
    {
        $request->validate([
            'last_name' => ['required'],
            'first_name' => ['required'],
            'gender' => ['required'],
            'email_address' => ['required', 'email', 'unique:informations'],
            'contact_number' => ['required'],
            'role' => ['required'],
        ]);

        $password = Str::random(8);

        Mail::to($request->email_address)->send(new PasswordMail($password));

        $user = User::create([
            'password' => bcrypt($password),
            'role' => $request->role,
        ]);

        $info = Information::create([
            'last_name' => $request->last_name,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'gender' => $request->gender,
            'email_address' => $request->email_address,
            'contact_number' => $request->contact_number
        ]);

        Staff::create([
            'user_id' => $user->id,
            'info_id' => $info->id,
            'is_active' => $request->is_active
        ]);
    }

    public function getStaff()
    {
        $staffs = User::with('staff.information')
            ->whereIn('role', ['staff', 'cashier'])
            ->get();

        return response()->json($staffs);
    }

    public function getRecord(Request $request)
    {
        $students = Student::with('information')->get();

        $complete = [];
        $incomplete = [];

        foreach ($students as $student) {
            $requiredDocuments = Document::where('document_type', $student->student_type)
                ->pluck('id')
                ->toArray();

            $submittedDocuments = Submit::where('student_id', $student->id)
                ->where('submit_status', 'confirm')
                ->with('record')
                ->get()
                ->flatMap(function ($submit) {
                    return $submit->record->pluck('document_id');
                })
                ->toArray();

            if (empty(array_diff($requiredDocuments, $submittedDocuments))) {
                $complete[] = $student;
            } else {
                $incomplete[] = $student;
            }
        }

        return response()->json($request->status === 'complete' ? $complete : $incomplete);
    }

    public function getRequirement(Request $request)
    {
        $student = Student::where('id_number', $request->id_number)->first();

        $status = $request->input('status', 'empty');

        $documents = Document::where('document_type', $student->student_type)->get();

        $submits = Submit::where('student_id', $student->id)
            ->with('record')
            ->get()
            ->flatMap(function ($submit) {
                return $submit->record->map(function ($record) use ($submit) {
                    return [
                        'document_id' => $record->document_id,
                        'submit_status' => $submit->submit_status,
                    ];
                });
            })
            ->keyBy('document_id');

        $statusCounts = [
            'empty' => 0,
            'review' => 0,
            'confirm' => 0,
            'resubmit' => 0,
        ];

        $requirements = $documents->map(function ($document) use ($submits, $status, &$statusCounts) {
            $submit = $submits->get($document->id);

            $documentStatus = $submit['submit_status'] ?? 'pending';

            if ($documentStatus === 'pending') {
                $statusCounts['empty']++;
            } else {
                $statusCounts[$documentStatus]++;
            }

            if ($status === 'empty' && (!$submit || $documentStatus === 'pending')) {
                return [
                    'id' => $document->id,
                    'document_name' => $document->document_name,
                    'submit_status' => $documentStatus,
                ];
            }

            if ($submit && $documentStatus === $status) {
                return [
                    'id' => $document->id,
                    'document_name' => $document->document_name,
                    'submit_status' => $documentStatus,
                ];
            }

            return null;
        })->filter();

        return response()->json([
            'requirements' => $requirements->values(),
            'counts' => $statusCounts,
        ]);
    }

    public function getSoftCopy(Request $request)
    {
        $student = Student::where('id_number', $request->id_number)
            ->first();

        $document = Document::where('id', $request->document_id)
            ->first();

        $softCopy = Submit::where('student_id', $student->id)
            ->whereHas('record', function ($query) use ($request) {
                $query->where('document_id', $request->document_id);
            })
            ->with('record')
            ->first();

        return response()->json([
            'document' => $document,
            'soft_copy' => $softCopy
        ]);
    }

    public function confirmSubmit(Request $request)
    {
        Submit::where('id', $request->submit_id)
            ->update([
                'submit_status' => 'confirm'
            ]);
    }

    public function declineSubmit(Request $request)
    {
        Submit::where('id', $request->submit_id)
            ->update([
                'submit_message' => $request->others === null ? $request->message : $request->others,
                'submit_status' => 'resubmit'
            ]);
    }

    public function getCredentialRequest(Request $request)
    {
        $credentialRequests = \App\Models\Request::where('request_status', $request->status)
            ->with('student.information', 'request_credential.credential', 'payment')
            ->get();

        $statusCounts = [
            'review' => \App\Models\Request::where('request_status', 'review')->count(),
            'pay' => \App\Models\Request::where('request_status', 'pay')->count(),
            'process' => \App\Models\Request::where('request_status', 'process')->count(),
            'receive' => \App\Models\Request::where('request_status', 'receive')->count(),
        ];

        return response()->json([
            'requests' => $credentialRequests,
            'counts' => $statusCounts
        ]);
    }

    public function getRequestDetail(Request $request)
    {
        $reqDetail = \App\Models\Request::where('request_number', $request->request_number)
            ->with('student.information', 'request_credential.credential', 'request_credential.credential_purpose.purpose', 'payment')
            ->first();

        return response()->json($reqDetail);
    }

    public function editPage(Request $request)
    {
        RequestCredential::where('id', $request->id)
            ->update([
                'page' => $request->page
            ]);
    }

    public function requestConfirm(Request $request)
    {
        $reqCred = RequestCredential::where('request_id', $request->id)
            ->whereHas('credential')
            ->first();

        if ($reqCred->credential->on_page === 1 && $reqCred->page === 1) {
            throw ValidationException::withMessages([
                'message' => 'Please edit the pages.',
            ]);
        }

        \App\Models\Request::where('id', $request->id)
            ->update([
                'request_status' => 'pay'
            ]);
    }

    public function requestDecline(Request $request)
    {
        $request->validate([
            'message' => ['required']
        ]);

        \App\Models\Request::where('id', $request->id)
            ->update(attributes: [
                'request_status' => 'cancel',
                'request_message' => $request->others === null ? $request->message : $request->others
            ]);

        if ($request->has('credential_id')) {
            StudentLink::where('student_id', $request->student_id)
                ->where('credential_id', $request->credential_id)
                ->delete();
        }
    }

    public function requestProcess(Request $request)
    {
        \App\Models\Request::where('id', $request->id)
            ->update([
                'request_status' => 'process'
            ]);
    }

    public function requestFinish(Request $request)
    {
        \App\Models\Request::where('id', $request->id)
            ->update([
                'request_status' => 'receive'
            ]);
    }

    public function requestRelease(Request $request)
    {
        \App\Models\Request::where('id', $request->id)
            ->update([
                'request_status' => 'complete'
            ]);

        RequestCredential::where('request_id', $request->id)
            ->update([
                'req_cred_status' => 'release'
            ]);
    }

    public function cancelRequest(Request $request)
    {
        $request->validate([
            'password' => ['required'],
        ]);

        $user_id = $request->user()->id;

        $user = User::find($user_id);

        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'message' => 'The provided password are incorrect.',
            ]);
        }

        \App\Models\Request::where('id', $request->id)
            ->update([
                'request_status' => 'cancel',
                'message' => 'The student requested to the admin to cancel the request.'
            ]);

        RequestCredential::where('request_id', $request->id)
            ->update([
                'page' => '1'
            ]);

        if ($request->has('credential_id')) {
            StudentLink::where('student_id', $request->student_id)
                ->where('credential_id', $request->credential_id)
                ->delete();
        }
    }
}
