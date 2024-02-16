<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Imports\MenteeImport;
use App\Models\Group;
use App\Models\Mentee;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;

class MenteeController extends Controller
{
    public function mentees(){
        $mentees = DB::table('mentee')
                        ->leftJoin('groups', 'mentee.group_id', '=', 'groups.id')
                        ->get(['mentee.*', 'groups.name as group_name']);
        $mentees_total = $mentees->count();
        $active_mentees = $mentees->where('status', 'Active')->count();

        return response()->json(['data' => [
            'mentees' => $mentees,
            'mentees_total' => $mentees_total,
            'active_mentees' => $active_mentees
        ]]);
    }

    public function addFromExcell(Request $request){
        Excel::import(new MenteeImport, $request->file('excel_file'));
        return response()->json(['msg' => 'success']);
    }

    public function add(Request $request){
        $mentees = json_decode($request['datas'], true);

        for ($i=0; $i < count($mentees); $i++) {
            if($request->hasFile('image-'.$i+1)){
                $image = $request->file('image-'.$i+1);
                $filename = time() . preg_replace('/\s+/', '-', $mentees[$i]['name']) . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/mentees/profile_picture/'), $filename);
                $mentees[$i]['image'] = 'uploads/mentees/profile_picture/' . $filename;
            }else{
                $mentees[$i]['image'] = 'uploads/mentees/profile_picture/default-profile.jpg';
            }

            $mentees[$i]['created_at'] = date_create();
        }

        $insert = Mentee::insert($mentees);

        if($insert){
            return response()->json(['msg' => 'success']);
        }

        return response()->json(['msg' => 'fail']);
    }

    public function detail(Request $request){
        $mentee_id = $request->input('mentee_id');
        $mentee = Mentee::where('id', $mentee_id)->first();
        $program_assigned = 'Not Assign Yet!';
        $group_assigned = 'Not Assign Yet!';
        $mentor_assigned = 'Not Assign Yet!';
        $scoring_data = [];
        $absence_data = [];

        if($mentee->group_id != null){
            $group_assigned = DB::table('groups AS g')
                ->join('mentor AS m', 'g.mentor_id', '=', 'm.id')
                ->where('g.id', $mentee->group_id)
                ->first(['g.name', 'm.*']);
            $mentor_assigned = $group_assigned->id;

            $program_assigned = DB::table('groups AS g')
                ->join('programs AS p', 'g.program_id', '=', 'p.id')
                ->where('g.id', $mentee->group_id)
                ->first();

            $scoring_data = DB::table('assignment AS a')
                ->leftJoin('scoring AS s', function($join) use ($mentee_id){
                    $join->on('a.id', '=', 's.assignment_id')
                        ->where('s.mentee_id', $mentee_id);
                })->where('a.program_id', $program_assigned->id)
                ->get(['a.id AS assignment_id', 'a.name', 'a.deadline', 's.id AS scoring_id', 's.score']);

            $absence_data = DB::table('activity AS a')
                ->leftJoin('absence AS ab', function($join) use ($mentee_id){
                    $join->on('a.id', '=', 'ab.activity_id')
                        ->where('ab.mentee_id', $mentee_id);
                })->where('a.program_id', $program_assigned->id)
                ->get(['a.id AS activity_id', 'a.name', 'a.date', 'ab.id AS absence_id', 'ab.present', 'ab.information']);

            for($i = 0; $i < count($absence_data); $i++){
                if($absence_data[$i]->present == 1){
                    $absence_data[$i]->present = true;
                }else{
                    $absence_data[$i]->present = false;
                }
            }

            $program_assigned = $program_assigned->program_name;
        }

        $mentee->mentor_assigned = $mentor_assigned;
        $mentee->program_assigned = $program_assigned;
        $mentee->group_assigned = $group_assigned;
        $mentee->scoring_data = $scoring_data;
        $mentee->absence_data = $absence_data;

        return response()->json(['data' => $mentee]);
    }

    public function resetProfilePicture($mentee_id){
        $mentee = Mentee::findOrFail($mentee_id);

        File::delete(public_path($mentee['image']));

        Mentee::where('id', $mentee_id)->update([
            'image' => 'uploads/mentees/profile_picture/default-profile.jpg',
            'updated_at' => date_create()
        ]);

        $mentee = Mentee::findOrFail($mentee_id);

        return response()->json(['data' => $mentee]);
    }

    public function setProfilePicture($mentee_id, Request $request){
        $mentee = Mentee::findOrFail($mentee_id);
        $image = $request->file('image');
        $filename = time() . preg_replace('/\s+/', '-', $mentee['name']) . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('uploads/mentees/profile_picture/'), $filename);

        Mentee::where('id', $mentee_id)->update([
            'image' => 'uploads/mentees/profile_picture/' . $filename,
            'updated_at' => date_create()
        ]);

        $mentee = Mentee::findOrFail($mentee_id);

        return response()->json(['data' => $mentee]);
    }

    public function edit(Request $request){
        $mentee = $request->all();
        Mentee::where('id', $mentee['mentee_id'])->update([
            'id' => $mentee['mentee_id'],
            'name' => $mentee['name'],
            'gender' => $mentee['gender'],
            'university' => $mentee['university'],
            'major' => $mentee['major'],
            'semester' => $mentee['semester'],
            'email' => $mentee['email'],
            'phone' => $mentee['phone'],
            'status' => $mentee['status'],
            'updated_at' => date_create()
        ]);

        return response()->json(['msg' => 'success']);
    }

    public function getMenteeNotInGroup(){
        $mentees = DB::select('
            SELECT * FROM mentee where group_id is null
        ');

        return response()->json(['data' => $mentees]);
    }

    public function delete(Request $request){
        $mentee_id = $request->input('mentee_id');

        Mentee::where('id', $mentee_id)->delete();
        return response()->json(['msg' => 'success']);
    }

    public function assignMentee(Request $request){
        $group_id = $request->input('group_id');
        $mentees = $request->input('mentees');

        for ($i=0; $i < count($mentees); $i++) {
            Mentee::where('id', $mentees[$i])
                ->update(['group_id' => $group_id]);
        }

        return response()->json(['msg' => 'success']);
    }

    public function kickMentee(Request $request){
        Mentee::where('id', $request->input('mentee_id'))
                ->update(['group_id' => null]);

        return response()->json(['msg' => 'success']);
    }
}
