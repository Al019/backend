<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class CashierController extends Controller
{
    public function getCredentialRequest()
    {
        $credentialRequests = \App\Models\Request::whereDoesntHave('payment')
            ->where('request_status', 'pay')
            ->with('student.information', 'request_credential.credential', 'payment')
            ->get();

        return response()->json($credentialRequests);
    }

    public function getRequestDetail(Request $request)
    {
        $reqDetail = \App\Models\Request::where('request_number', $request->request_number)
            ->with('student.information', 'request_credential.credential', 'request_credential.credential_purpose.purpose', 'payment')
            ->first();

        return response()->json($reqDetail);
    }

    public function requestConfirm(Request $request)
    {
        $request->validate([
            'or_number' => ['required', 'digits:7']
        ]);

        Payment::create([
            'request_id' => $request->id,
            'or_number' => $request->or_number
        ]);
    }
}
