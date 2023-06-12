<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentType;
use App\Models\Batch;
use App\Models\Program;
use App\Models\Scoring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssignmentController extends Controller
{
    public function getAssignments(){
        $batches = Batch::all(['id', 'batch_name']);
        $programs = Program::all(['id', 'program_name']);
        $types = AssignmentType::all('id', 'type');
        $assignments = DB::table('assignment AS a')
            ->join('programs AS p', 'a.program_id', '=', 'p.id')
            ->join('assignment_type AS t', 'a.type_id', '=', 't.id')
            ->join('batch AS b', 'p.batch_id', '=', 'b.id')
            ->select(['a.*', 'p.program_name', 'b.batch_name', 't.type'])
            ->get();

        return response()->json(['data' => [
            'batches' => $batches,
            'programs' => $programs,
            'types' => $types,
            'assignments' => $assignments
        ]]);
    }

    public function getPrograms(){
        $batches = Batch::all(['id', 'batch_name']);
        $programs = DB::table('programs AS p')
            ->join('batch AS b', 'p.batch_id', '=', 'b.id')
            ->select(['p.program_name', 'p.id AS program_id', 'b.id', 'b.batch_name', 'p.program_status'])->get();

        for($i=0; $i<count($programs); $i++){
            $assignment_count = Assignment::where('program_id', $programs[$i]->program_id)->count();
            $programs[$i]->assignment_count = $assignment_count;
        }

        return response()->json(['data' => [
            'programs' => $programs,
            'batches' => $batches
        ]]);
    }

    public function assignmentByProgram($program_id){
        $assignments = DB::table('assignment AS a')
                        ->join('programs AS p', 'a.program_id', '=', 'p.id')
                        ->join('assignment_type AS t', 'a.type_id', '=', 't.id')
                        ->where('a.program_id', $program_id)
                        ->select(['a.*', 'p.program_name', 't.type'])
                        ->get();

        $types = AssignmentType::all(['id', 'type']);
        $program = Program::join('batch AS b', 'programs.batch_id', '=', 'b.id')
            ->where('programs.id', $program_id)->select(['programs.*', 'b.batch_name'])->first();

        for($i=0; $i<count($assignments); $i++){
            $mentees_total = DB::table('mentee AS m')
                            ->join('groups AS g', 'm.group_id', '=', 'g.id')
                            ->join('programs AS p', 'g.program_id', 'p.id')
                            ->count('m.id');
            $mentees_count = Scoring::where('assignment_id', $assignments[$i]->id)
                                ->where('status', '!=', 'Not-Submitted')
                                ->count('mentee_id');
            $assignments[$i]->mentees_count = $mentees_count;
            $assignments[$i]->mentees_total = $mentees_total;
        }

        return response()->json(['data' => [
            'assignments' => $assignments,
            'program' => $program,
            'types' => $types
        ]]);
    }

    public function getPreparationData(){
        $programs = Program::join('batch AS b', 'programs.batch_id', '=', 'b.id')
            ->select(['programs.id', 'programs.program_name', 'b.batch_name'])->get();
        $types = AssignmentType::all(['id', 'type']);

        return response()->json(['data' => [
            'programs' => $programs,
            'types' => $types
        ]]);
    }

    public function add(Request $request){
        Assignment::insert([
            "program_id" => $request->input('program'),
            "type_id" => $request->input('type'),
            "name" => $request->input('name'),
            "description" => $request->input('description'),
            "deadline" => $request->input('deadline'),
            "created_at" => date_create()
        ]);

        return response()->json(['msg' => 'success']);
    }

    public function detail($assignment_id){
        $assignment = Assignment::where('id', $assignment_id)->first();

        return response()->json(['data' => $assignment]);
    }

    public function update(Request $request){
        Assignment::where('id', $request->input('id'))->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'deadline' => $request->input('deadline'),
            'type_id' => $request->input('type'),
            'program_id' => $request->input('program'),
            'updated_at' => date_create()
        ]);

        return response()->json(['msg' => 'success']);
    }

    public function delete(Request $request){
        Assignment::where('id', $request->input('id'))->delete();

        return response()->json(['msg' => 'success']);
    }
}
