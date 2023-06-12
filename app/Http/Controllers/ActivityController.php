<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityType;
use App\Models\Batch;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityController extends Controller
{
    public function activities(){
        $activities = DB::table('activity AS a')
                        ->join('programs AS p', 'a.program_id', '=', 'p.id')
                        ->join('batch AS b', 'p.batch_id' , '=', 'b.id')
                        ->join('activity_type AS t', 'a.type_id', '=', 't.id')
                        ->select(['a.*', 'b.batch_name', 'p.program_name', 't.type'])
                        ->get();
        $type = ActivityType::all(['id', 'type']);
        $batch = Batch::all(['id', 'batch_name']);
        $program = Program::all(['id', 'program_name']);

        return response()->json(['data' => [
            'activities' => $activities,
            'types' => $type,
            'batches' => $batch,
            'programs' => $program
        ]]);
    }

    public function add(Request $request){
        Activity::insert([
            'name' => $request->input('name'),
            'program_id' => $request->input('program_id'),
            'type_id' => $request->input('type_id'),
            'date' => $request->input('date'),
            'created_at' => date_create()
        ]);

        return response()->json(['msg' => 'success']);
    }

    public function dropdownItem(){
        $programs = DB::table('programs AS p')
                    ->join('batch AS b', 'p.batch_id', '=', 'b.id')
                    ->select(['p.*', 'b.batch_name', 'b.id AS batch_id'])->get();
        $types = ActivityType::all(['id', 'type']);

        return response()->json(['data' => [
            'programs' => $programs,
            'types' => $types
        ]]);
    }

    public function delete(Request $request){
        Activity::where('id', $request->input('id'))->delete();

        return response()->json(['msg' => 'success']);
    }

    public function detail(Request $request){
        $activity = Activity::where('id', $request->input('id'))->first();

        return response()->json(['data' => $activity]);
    }

    public function update(Request $request){
        Activity::where('id', $request->input('id'))->update([
            'name' => $request->input('name'),
            'date' => $request->input('date'),
            'program_id' => $request->input('program_id'),
            'type_id' => $request->input('type_id')
        ]);

        return response()->json(['msg' => 'success']);
    }
}
