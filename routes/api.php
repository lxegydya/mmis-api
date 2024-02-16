<?php

use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ActivityTypeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AssignmentTypeController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\MenteeController;
use App\Http\Controllers\MentorController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ScoringController;
use App\Models\Activity;
use App\Models\ActivityType;
use App\Models\Assignment;
use App\Models\Batch;
use App\Models\Mentee;
use App\Models\Mentor;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Cookie;

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
Route::get('admin/dashboard', function (Request $request) {
    $total_batch = Batch::count();
    $ongoing_batch = Batch::where('batch_status', 'Ongoing')->first();
    $countdown_ongoing_batch = $ongoing_batch == null ? '0' : ceil((strtotime($ongoing_batch['batch_end']) - time()) / 86400);
    $ongoing_program = Program::where('program_status', 'Ongoing')->count();
    $active_mentee = Mentee::where('status', 'Active')->count();
    $mentor = Mentor::count('fullname');

    return response()->json([
        'data' => [
            'total_batch' => $total_batch,
            'ongoing_batch' => $ongoing_batch == null ? '' : $ongoing_batch['batch_name'],
            'countdown_ongoing_batch' => $countdown_ongoing_batch,
            'ongoing_program' => $ongoing_program,
            'active_mentee' => $active_mentee,
            'mentors' => $mentor
        ]
    ]);
});

// Mentor - Dashboard
Route::post('mentor/dashboard', function(Request $request){
    $ongoing_batch = Batch::where('batch_status', 'Ongoing')->first();
    $ongoing_program = Program::where('program_status', 'Ongoing')->count();
    $mentor = Mentor::count('fullname');

    $active_mentee = DB::table('mentee AS m')
        ->join('groups AS g', function($join) use ($request){
            $join->on('g.id', '=', 'm.group_id')
                ->where('g.mentor_id', $request->input('mentor_id'));
        })
        ->where('m.status', 'Active')
        ->count('m.id');

    return response()->json(['data' => [
        'ongoing_batch' => $ongoing_batch == null ? '' : $ongoing_batch['batch_name'],
        'ongoing_program' => $ongoing_program,
        'active_mentee' => $active_mentee,
        'mentors' => $mentor
    ]]);
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
Route::post('/mentor/{mentor_id}/password/reset', [MentorController::class, 'resetPassword']);


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
Route::post('/group/edit/name', [GroupController::class, 'update']);
Route::post('/group/delete/{group_id}', [GroupController::class, 'delete']);


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
Route::post('/absence/input', [AbsenceController::class, 'inputAbsence']);

// Absence => Super Admin
Route::get('/absence/list', [AbsenceController::class, 'superadminAbsence']);
Route::post('/super-admin/absence/activities', [AbsenceController::class, 'superadminAbsenceActivities']);
Route::post('/super-admin/absence/{activity_id}', [AbsenceController::class, 'getAbsenceList']);
Route::get('/absence/{program_id}/export', [AbsenceController::class, 'export']);

// Absence => Mentor
Route::post('/absence/mentor/list', [AbsenceController::class, 'mentorAbsence']);
Route::post('/absence/mentor/activities', [AbsenceController::class, 'mentorAbsenceActivities']);
Route::post('/absence/mentor/activities/{activity_id}', [AbsenceController::class, 'absenceActivityDetail']);

// Assignment
Route::get('/assignments', [AssignmentController::class, 'getAssignments']);
Route::get('/assignment/program', [AssignmentController::class, 'getPrograms']);
Route::get('/assignments/{program_id}', [AssignmentController::class, 'assignmentByProgram']);
Route::get('/assignment/get/{assignment_id}', [AssignmentController::class, 'detail']);
Route::get('/assignment/get-preparation-data', [AssignmentController::class, 'getPreparationData']);
Route::post('/assignments/add', [AssignmentController::class, 'add']);
Route::post('/assignments/update', [AssignmentController::class, 'update']);
Route::post('/assignment/delete', [AssignmentController::class, 'delete']);

// Assignment => Mentor
Route::post('/assignment/mentor/program', [AssignmentController::class, 'getProgramsByMentor']);
Route::post('/assignments/mentor/program/{program_id}', [AssignmentController::class, 'assignmentByProgramMentor']);

// Assignment Type
Route::get('/assignment/type/get', [AssignmentTypeController::class, 'getAllType']);
Route::get('/assignment/type/get/{type_id}', [AssignmentTypeController::class, 'getTypeById']);
Route::post('/assignment/type/create', [AssignmentTypeController::class, 'add']);
Route::post('/assignment/type/update', [AssignmentTypeController::class, 'update']);
Route::post('/assignment/type/delete', [AssignmentTypeController::class, 'delete']);


// Scoring
Route::get('/scoring/get/{assignment_id}', [ScoringController::class, 'getScoringByAssignment']);
Route::post('/scoring/appraise', [ScoringController::class, 'submitScore']);
Route::get('/scoring/{program_id}/export', [ScoringController::class, 'export']);

// Scoring => Mentor
Route::post('/scoring/mentor/get/{assignment_id}', [ScoringController::class, 'getScoringByAssignmentMentor']);

// Recap
Route::get('/recap/get', function(){
    $datas = DB::table('mentee AS m')
        ->join('groups AS g', 'm.group_id', '=', 'g.id')
        ->join('programs AS p', 'g.program_id', '=', 'p.id')
        ->join('batch AS b', 'p.batch_id', '=', 'b.id')
        ->join('mentor AS men', 'g.mentor_id', '=', 'men.id')
        ->select([
            'm.*', 'b.batch_name', 'p.id AS program_id', 'p.program_name',
            'g.name AS group_name', 'men.fullname AS mentor_name'
        ])->get();

    for($i=0; $i<count($datas); $i++){
        $activity_count = Activity::where('program_id', $datas[$i]->program_id)->count();
        $present_count = DB::table('absence AS a')
            ->join('mentee AS m', 'a.mentee_id', '=', 'm.id')
            ->join('activity AS ac', 'ac.id', '=', 'a.activity_id')
            ->join('programs AS p', 'ac.program_id', '=', 'p.id')
            ->where('p.id', '=', $datas[$i]->program_id)
            ->where('m.id', '=', $datas[$i]->id)
            ->where('a.present', '=', 1)
            ->groupBy('a.mentee_id')
            ->count(['a.mentee_id']);
        $datas[$i]->activity_count = $activity_count;
        $datas[$i]->present_count = $present_count;

        $assignment_count = Assignment::where('program_id', $datas[$i]->program_id)->count();
        $joined_score_data = DB::table('scoring AS s')
            ->join('mentee AS m', 's.mentee_id', '=', 'm.id')
            ->join('assignment AS a', 's.assignment_id', '=', 'a.id')
            ->join('programs AS p', 'a.program_id', '=', 'p.id')
            ->where('p.id', '=', $datas[$i]->program_id)
            ->where('m.id', '=', $datas[$i]->id)
            ->groupBy('s.mentee_id');
        $grade_count = $joined_score_data->count(['s.mentee_id']);
        $mentee_score = $joined_score_data->sum('s.score');
        $datas[$i]->scoring = [
            'sum_score' => $mentee_score,
            'count_graded' => $grade_count,
            'count_assignment' => $assignment_count
        ];
    }

    return response()->json(['data' => $datas]);
});


// Certificate
Route::post('/certificate/score', [ScoringController::class, 'printCertificate']);
Route::post('/certificate/absence', [AbsenceController::class, 'printCertificate']);
