<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\CredentialController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', [UserController::class, 'user']);

    Route::post('/admin/import-student', [AdminController::class, 'importStudent']);
    Route::get('/admin/export-student', [AdminController::class, 'exportStudent']);
    Route::get('/admin/get-student', [AdminController::class, 'getStudent']);
    Route::post('/admin/add-staff', [AdminController::class, 'addStaff']);
    Route::get('/admin/get-staff', [AdminController::class, 'getStaff']);
    Route::get('/admin/get-record', [AdminController::class, 'getRecord']);
    Route::get('/admin/get-requirement', [AdminController::class, 'getRequirement']);
    Route::get('/admin/get-softcopy', [AdminController::class, 'getSoftCopy']);
    Route::post('/admin/confirm-submit', [AdminController::class, 'confirmSubmit']);
    Route::post('/admin/decline-submit', [AdminController::class, 'declineSubmit']);
    Route::get('/admin/get-credential-request', [AdminController::class, 'getCredentialRequest']);
    Route::get('/admin/get-request-detail', [AdminController::class, 'getRequestDetail']);
    Route::post('/admin/edit-page', [AdminController::class, 'editPage']);
    Route::post('/admin/request-confirm', [AdminController::class, 'requestConfirm']);
    Route::post('/admin/request-decline', [AdminController::class, 'requestDecline']);
    Route::post('/admin/request-process', [AdminController::class, 'requestProcess']);
    Route::post('/admin/request-finish', [AdminController::class, 'requestFinish']);
    Route::post('/admin/request-release', [AdminController::class, 'requestRelease']);
    Route::post('/admin/cancel-request', [AdminController::class, 'cancelRequest']);

    Route::get('/cashier/get-credential-request', [CashierController::class, 'getCredentialRequest']);
    Route::get('/cashier/get-request-detail', [CashierController::class, 'getRequestDetail']);
    Route::post('/cashier/request-confirm', [CashierController::class, 'requestConfirm']);

    Route::get('/document/get-student-type', [DocumentController::class, 'getStudentType']);
    Route::post('/document/create-document', [DocumentController::class, 'createDocument']);
    Route::get('/document/get-document', [DocumentController::class, 'getDocument']);
    Route::post('/document/edit-document', [DocumentController::class, 'editDocument']);

    Route::get('/credential/get-credential', [CredentialController::class, 'getCredential']);
    Route::post('/credential/create-credential', [CredentialController::class, 'createCredential']);
    Route::post('/credential/edit-credential', [CredentialController::class, 'editCredential']);
    Route::get('/credential/get-purpose', [CredentialController::class, 'getPurpose']);
    Route::post('/credential/create-purpose', [CredentialController::class, 'createPurpose']);
    Route::post('/credential/edit-purpose', [CredentialController::class, 'editPurpose']);
    Route::get('/credential/get-link', [CredentialController::class, 'getLink']);
    Route::get('/credential/get-student-link', [CredentialController::class, 'getStudentLink']);

    Route::get('/student/get-requirement', [StudentController::class, 'getRequirement']);
    Route::post('/student/submit-requirement', [StudentController::class, 'submitRequirement']);
    Route::post('/student/resubmit-requirement', [StudentController::class, 'resubmitRequirement']);
    Route::get('/student/get-softcopy', [StudentController::class, 'getSoftCopy']);
    Route::get('/student/get-record-status', [StudentController::class, 'getRecordStatus']);
    Route::get('/student/get-request-count', [StudentController::class, 'getRequestCount']);
    Route::get('/student/get-payment-status', [StudentController::class, 'getPaymentStatus']);
    Route::post('/student/request-credential', [StudentController::class, 'requestCredential']);
    Route::get('/student/get-request-status', [StudentController::class, 'getRequestStatus']);
    Route::get('/student/get-request-detail', [StudentController::class, 'getRequestDetail']);
    Route::post('/student/cancel-request', [StudentController::class, 'cancelRequest']);
    Route::post('/student/request-claim', [StudentController::class, 'requestClaim']);
    Route::post('/student/request-again-credential', [StudentController::class, 'requestAgainCredential']);

    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::get('/logout', [AuthController::class, 'logout']);

});

Route::post('/login', [AuthController::class, 'login']);
