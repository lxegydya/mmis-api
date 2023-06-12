<?php

use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ActivityTypeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\MenteeController;
use App\Http\Controllers\MentorController;
use App\Http\Controllers\ProgramController;
use App\Models\ActivityType;
use App\Models\Batch;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Auth
Route::post('/login', [AdminController::class, 'login']);


// Admin - Dashboard
Route::get('admin/dashboard', function () {
    $total_batch = Batch::count();
    $ongoing_batch = Batch::where('batch_status', 'Ongoing')->first();
    $countdown_ongoing_batch = floor((strtotime($ongoing_batch['batch_end']) - time()) / 86400);
    $ongoing_program = Program::where('program_status', 'Ongoing')->count();

    return response()->json([
        'data' => [
            'total_batch' => $total_batch,
            'ongoing_batch' => $ongoing_batch['batch_name'],
            'countdown_ongoing_batch' => $countdown_ongoing_batch,
            'ongoing_program' => $ongoing_program
        ]
    ]);
});


// Batch
Route::get('/batchs', [BatchController::class, 'index']);
Route::get('/batches', [BatchController::class, 'batches']);
Route::get('/batches/not-finished', [BatchController::class, 'batchesNotFinished']);
Route::get('/batch/{batch_id}', [BatchController::class, 'detail']);
Route::post('/batch/create', [BatchController::class, 'add']);
Route::post('/batch/edit', [BatchController::class, 'edit']);
Route::post('/batch/delete/{batch_id}', [BatchController::class, 'delete']);


// Program
Route::get('/programs', [ProgramController::class, 'programs']);
Route::get('/program/{program_id}', [ProgramController::class, 'detail']);
Route::post('/program/create', [ProgramController::class, 'add']);
Route::post('/program/edit', [ProgramController::class, 'edit']);
Route::post('/program/delete/{program_id}', [ProgramController::class, 'delete']);
Route::get('/program/batch/{batch_id}', [ProgramController::class, 'programOnBatch']);


// Mentor
Route::get('/mentors', [MentorController::class, 'mentors']);
Route::get('/mentor/{mentor_id}', [MentorController::class, 'detail']);
Route::post('/mentor/create', [MentorController::class, 'add']);
Route::post('/mentor/{mentor_id}/profile/reset-profile-picture', [MentorController::class, 'resetProfilePicture']);
Route::post('/mentor/{mentor_id}/profile/set-profile-picture', [MentorController::class, 'setProfilePicture']);
Route::post('/mentor/edit', [MentorController::class, 'edit']);
Route::post('/mentor/delete/{mentor_id}', [MentorController::class, 'delete']);
Route::post('/mentor/not-in-group/{program_id}', [MentorController::class, 'getMentorNotInGroup']);


// Mentee
Route::get('/mentees', [MenteeController::class, 'mentees']);
Route::post('/mentee/create', [MenteeController::class, 'add']);
Route::post('/mentee/detail', [MenteeController::class, 'detail']);
Route::post('/mentee/{mentee_id}/profile/reset-profile-picture', [MenteeController::class, 'resetProfilePicture']);
Route::post('/mentee/{mentee_id}/profile/set-profile-picture', [MenteeController::class, 'setProfilePicture']);
Route::post('/mentee/edit', [MenteeController::class, 'edit']);
Route::get('/mentee/not-in-group', [MenteeController::class, 'getMenteeNotInGroup']);
Route::post('/mentee/delete', [MenteeController::class, 'delete']);
Route::post('/mentee/create/from-excel', [MenteeController::class, 'addFromExcell']);
Route::post('/mentee/assign', [MenteeController::class, 'assignMentee']);
Route::post('/mentee/kick', [MenteeController::class, 'kickMentee']);


// Group
Route::get('/groups', [GroupController::class, 'groups']);
Route::post('/group/create', [GroupController::class, 'add']);
Route::post('/group/{group_id}', [GroupController::class, 'detail']);


// Activity
Route::get('/activity', [ActivityController::class, 'activities']);
Route::post('/activity/detail', [ActivityController::class, 'detail']);
Route::get('/activity/dropdown-item', [ActivityController::class, 'dropdownItem']);
Route::post('/activity/create', [ActivityController::class, 'add']);
Route::post('/activity/update', [ActivityController::class, 'update']);
Route::post('/activity/delete', [ActivityController::class, 'delete']);


// Type
Route::get('/type', [ActivityTypeController::class, 'types']);
Route::post('/type/create', [ActivityTypeController::class, 'add']);
Route::post('/type/detail', [ActivityTypeController::class, 'detail']);
Route::post('/type/update', [ActivityTypeController::class, 'update']);
Route::post('/type/delete', [ActivityTypeController::class, 'delete']);


// Absence
Route::get('/super-admin/absence', [AbsenceController::class, 'superadminAbsence']);
Route::post('/super-admin/absence/activities', [AbsenceController::class, 'superadminAbsenceActivities']);
Route::post('/super-admin/absence/{activity_id}', [AbsenceController::class, 'getAbsenceList']);
Route::post('/absence/input', [AbsenceController::class, 'inputAbsence']);


// Assignment
Route::get('/assignments', [AssignmentController::class, 'getAssignments']);
Route::get('/assignment/program', [AssignmentController::class, 'getPrograms']);
Route::get('/assignments/{program_id}', [AssignmentController::class, 'assignmentByProgram']);
Route::get('/assignment/get/{assignment_id}', [AssignmentController::class, 'detail']);
Route::get('/assignment/get-preparation-data', [AssignmentController::class, 'getPreparationData']);
Route::post('/assignments/add', [AssignmentController::class, 'add']);
Route::post('/assignments/update', [AssignmentController::class, 'update']);
Route::post('/assignment/delete', [AssignmentController::class, 'delete']);