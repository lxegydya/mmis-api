<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Activity;
use App\Models\Group;
use App\Models\Mentee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AbsenceController extends Controller
{
    public function superadminAbsence(){
        $programs = DB::table('programs AS p')
            ->join('batch AS b', 'p.batch_id', '=', 'b.id')
            ->select(['p.program_name', 'p.id AS program_id', 'b.id', 'b.batch_name', 'p.program_status'])->get();

        for($i=0; $i<count($programs); $i++){
            $activities_count = Activity::where('program_id', $programs[$i]->program_id)->count();
            $mentees = DB::table('mentee AS m')
                        ->join('groups AS g', 'm.group_id', '=', 'g.id')
                        ->join('programs AS p', 'g.program_id', '=', 'p.id')
                        ->where('g.program_id', $programs[$i]->program_id)
                        ->count('m.id');

            $mentors = DB::table('mentor AS m')
                        ->join('groups AS g', 'g.mentor_id', '=', 'm.id')
                        ->join('programs AS p', 'g.program_id', '=', 'p.id')
                        ->select(['m.fullname'])->where('g.program_id', $programs[$i]->program_id)->get();
            $programs[$i]->mentors = $mentors;
            $programs[$i]->mentees = $mentees;
            $programs[$i]->activities_count = $activities_count;
        }

        return response()->json(['data' => $programs]);
    }

    public function superadminAbsenceActivities(Request $request){
        $activities = DB::table('activity AS a')
                        ->join('programs AS p', 'a.program_id', '=', 'p.id')
                        ->join('activity_type AS t', 'a.type_id', '=', 't.id')
                        ->where('a.program_id', $request->input('id'))
                        ->select(['a.*', 'p.program_name', 't.type'])
                        ->get();

        for($i=0; $i<count($activities); $i++){
            $mentees_total = DB::table('mentee AS m')
                            ->join('groups AS g', 'm.group_id', '=', 'g.id')
                            ->join('programs AS p', 'g.program_id', 'p.id')
                            ->count('m.id');
            $mentees_count = Absence::where('activity_id', $activities[$i]->id)
                                ->where('present', true)
                                ->count('mentee_id');
            $activities[$i]->mentees_count = $mentees_count;
            $activities[$i]->mentees_total = $mentees_total;
        }

        return response()->json(['data' => $activities]);
    }

    public function getAbsenceList($activity_id){
        $activity = Activity::where('id', $activity_id)->get(['id', 'name', 'date', 'program_id'])->first();
        $groups = Group::where('program_id', $activity->program_id)
            ->join('mentor', 'groups.mentor_id', '=', 'mentor.id')
            ->get(['groups.id', 'groups.mentor_id', 'mentor.fullname', 'groups.name']);
        
        for($i=0; $i<count($groups); $i++){
            $mentees = DB::table('mentee AS m')
                ->leftJoin('absence AS a', function($join) use ($activity_id) {
                    $join->on('m.id', '=', 'a.mentee_id')
                        ->where('a.activity_id', $activity_id);
                })
                ->where('m.group_id', $groups[$i]->id)
                ->get(['m.id', 'm.name', 'm.status', 'a.present', 'a.id AS absence_id']);
            
            for($j=0; $j<count($mentees); $j++){
                if($mentees[$j]->present == 1){
                    $mentees[$j]->present = true;
                }else{
                    $mentees[$j]->present = false;
                }
            }

            $groups[$i]->mentees = $mentees;
        }

        return response()->json(['data' => [
            'activity' => $activity,
            'groups' => $groups
        ]]);
    }

    public function inputAbsence(Request $request){
        $absence_list = $request->input('absence_list');
        for($i=0; $i<count($absence_list); $i++){
            if($absence_list[$i]['absence_id'] == null){
                Absence::insert([
                    'activity_id' => $request->input('activity_id'),
                    'mentee_id' => $absence_list[$i]['id'],
                    'present' => $absence_list[$i]['present'],
                    'created_at' => date_create()
                ]);
            }else{
                Absence::where('id', $absence_list[$i]['absence_id'])
                ->update([
                    'activity_id' => $request->input('activity_id'),
                    'mentee_id' => $absence_list[$i]['id'],
                    'present' => $absence_list[$i]['present'],
                    'updated_at' => date_create()
                ]);
            }
        }

        return response()->json(['msg' => 'success']);
    }
}
