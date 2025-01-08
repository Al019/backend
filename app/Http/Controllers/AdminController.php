<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Staff;
use App\Models\Submit;
use App\Models\Student;
use App\Models\Document;
use App\Models\Information;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Exports\StudentExport;
use App\Imports\StudentImport;
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
}
