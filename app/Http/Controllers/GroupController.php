<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Group;
use App\Models\Mentee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    public function groups(){
        $group = DB::table('groups')
                    ->join('programs', 'groups.program_id', '=', 'programs.id')
                    ->join('mentor', 'groups.mentor_id', '=', 'mentor.id')
                    ->join('batch', 'programs.batch_id', '=', 'batch.id')
                    ->select(['groups.*', 'programs.program_name', 'batch.batch_name', 'mentor.fullname'])->get();
        
        for ($i=0; $i < count($group); $i++) { 
            $group[$i]->numberOfMentee = Mentee::where('group_id', $group[$i]->id)->count();
            $group[$i]->program_name = "[" . $group[$i]->batch_name . "] " . $group[$i]->program_name;
        }

        return response()->json(['data' => $group]);
    }

    public function detail($group_id){
        $group = DB::table('groups')
                    ->join('mentor', 'groups.mentor_id', '=', 'mentor.id')
                    ->join('programs', 'groups.program_id', '=', 'programs.id')
                    ->join('batch', 'programs.batch_id', '=', 'batch.id')
                    ->where('groups.id', $group_id)
                    ->select([
                        'groups.*', 
                        'mentor.id as mentor_id', 'mentor.fullname as mentor_name', 'mentor.skill as mentor_skill', 'mentor.image as mentor_image',
                        'mentor.email as mentor_email', 'mentor.phone as mentor_phone',
                        'programs.program_name', 
                        'batch.batch_name'
                    ])->first();

        $mentees = Mentee::where('group_id', $group_id)->get();
        $group->mentees = $mentees;
        $group->assigned_mentor = $group->mentor_id;

        return response()->json(['data' => $group]);
    }

    public function add(Request $request){
        $program_id = $request->input('program_id');
        $mentor_id = $request->input('mentor_id');
        $name = $request->input('name');
        $status = $request->input('status');
        $mentees = $request->input('mentees');

        Group::insert([
            'program_id' => $program_id,
            'mentor_id' => $mentor_id,
            'name' => $name,
            'status' => $status,
            'created_at' => date_create()
        ]);

        $group_id = Group::where('program_id', $program_id)
                            ->where('mentor_id', $mentor_id)->get('id');

        for ($i=0; $i < count($mentees); $i++) { 
            Mentee::where('id', $mentees[$i])->update([
                'group_id' => $group_id[0]->id,
                'updated_at' => date_create()
            ]);
        }

        return response()->json(['msg' => 'success']);
    }

    public function delete($group_id){
        Group::where('id', $group_id)->delete();

        return response()->json(['msg' => 'success']);
    }

    public function update(Request $request){
        Group::where('id', $request->input('id'))
            ->update([
                'name' => $request->input('name'),
                'updated_at' => date_create()
            ]);

        return response()->json(['msg' => 'success']);
    }
}
