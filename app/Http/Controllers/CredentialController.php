<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\Purpose;
use App\Models\Credential;
use App\Models\StudentLink;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CredentialController extends Controller
{
    public function getCredential()
    {
        $credentials = Credential::get();

        return response()->json($credentials);
    }

    public function createCredential(Request $request)
    {
        $request->validate([
            'credential_name' => ['required'],
            'amount' => ['required'],
            'count_day' => ['required'],
        ]);

        $existingCredential = Credential::where('credential_name', $request->credential_name)
            ->exists();

        if ($existingCredential) {
            throw ValidationException::withMessages([
                'message' => 'This credential name is already exists.'
            ]);
        } else {
            $credential = Credential::create([
                'credential_name' => $request->credential_name,
                'amount' => $request->amount,
                'on_page' => $request->on_page,
                'count_day' => $request->count_day
            ]);
            if ($request->has('purpose_id')) {
                Link::create([
                    'purpose_id' => $request->purpose_id,
                    'credential_id' => $credential->id
                ]);
            }
        }
    }

    public function editCredential(Request $request)
    {
        $request->validate([
            'credential_name' => ['required'],
            'amount' => ['required'],
            'count_day' => ['required'],
        ]);

        $existingCredential = Credential::where('credential_name', $request->credential_name)
            ->where('id', '!=', $request->id)
            ->exists();

        if ($existingCredential) {
            throw ValidationException::withMessages([
                'message' => 'This credential name already exists.'
            ]);
        } else {
            $credential = Credential::where('id', $request->id)
                ->first();
            $credential->update([
                'credential_name' => $request->credential_name,
                'amount' => $request->amount,
                'on_page' => $request->on_page,
                'count_day' => $request->count_day
            ]);

            $link = Link::where('credential_id', $credential->id)
                ->first();

            if ($request->has('purpose_id')) {
                if (!$link) {
                    Link::create([
                        'purpose_id' => $request->purpose_id,
                        'credential_id' => $credential->id,
                    ]);
                } else {
                    $link->update([
                        'purpose_id' => $request->purpose_id,
                    ]);
                }
            } elseif ($link) {
                $link->delete();
            }
        }
    }

    public function getPurpose()
    {
        $purposes = Purpose::get();

        return response()->json($purposes);
    }

    public function createPurpose(Request $request)
    {
        $request->validate([
            'purpose_name' => ['required'],
        ]);

        $existingPurpose = Purpose::where('purpose_name', $request->purpose_name)
            ->exists();

        if ($existingPurpose) {
            throw ValidationException::withMessages([
                'message' => 'This purpose name is already exists.'
            ]);
        } else {
            Purpose::create([
                'purpose_name' => $request->purpose_name,
            ]);
        }
    }

    public function editPurpose(Request $request)
    {
        $request->validate([
            'purpose_name' => ['required'],
        ]);

        $existingPurpose = Purpose::where('purpose_name', $request->purpose_name)
            ->where('id', '!=', $request->id)
            ->exists();

        if ($existingPurpose) {
            throw ValidationException::withMessages([
                'message' => 'This purpose name is already exists.'
            ]);
        } else {
            Purpose::where('id', $request->id)
                ->update([
                    'purpose_name' => $request->purpose_name,
                ]);
        }
    }

    public function getLink()
    {
        $link = Link::get();

        return response()->json($link);
    }

    public function getStudentLink()
    {
        $studentLink = StudentLink::get();

        return response()->json($studentLink);
    }
}
