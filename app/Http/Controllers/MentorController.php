<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Mentor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class MentorController extends Controller
{
    public function mentors(){
        $mentors = Mentor::all();

        for($i=0; $i<count($mentors); $i++){
            $mentors[$i]['skill'] = array_map('trim', explode(',', $mentors[$i]['skill']));
            $group_count = DB::table('mentor AS m')
                ->leftJoin('groups AS g', 'm.id', '=', 'g.mentor_id')
                ->where('g.mentor_id', $mentors[$i]->id)
                ->count('g.id');
            $mentors[$i]['group_count'] = $group_count;
        }

        return response()->json(['data' => $mentors]);
    }

    public function add(Request $request){
        $mentor = $request->all(['fullname', 'email', 'phone', 'password', 'skill']);
        
        $mentor['created_at'] = date_create();
        $mentor['password'] = Crypt::encryptString($mentor['password']);
        $mentor['status'] = 'Active';

        if($request->hasFile('image')){
            $image = $request->file('image');
            $filename = time() . $image->getClientOriginalName();
            $image->move(public_path('uploads/mentors/profile_picture/'), $filename);
            $mentor['image'] = 'uploads/mentors/profile_picture/' . $filename;
        }else{
            $mentor['image'] = 'uploads/mentors/profile_picture/default-profile.jpg';
        }

        Mentor::create($mentor);
        return response()->json(['msg' => 'success']);
    }

    public function detail($mentor_id){
        $mentor = Mentor::findOrFail($mentor_id);

        return response()->json(['data' => $mentor]);
    }

    public function resetProfilePicture($mentor_id){
        $mentor = Mentor::findOrFail($mentor_id);

        File::delete(public_path($mentor['image']));

        Mentor::where('id', $mentor_id)->update([
            'image' => 'uploads/mentors/profile_picture/default-profile.jpg',
            'updated_at' => date_create()
        ]);

        $mentor = Mentor::findOrFail($mentor_id);

        return response()->json(['data' => $mentor]);
    }

    public function setProfilePicture($mentor_id, Request $request){
        $image = $request->file('image');
        $filename = time() . $image->getClientOriginalName();
        $image->move(public_path('uploads/mentors/profile_picture/'), $filename);

        Mentor::where('id', $mentor_id)->update([
            'image' => 'uploads/mentors/profile_picture/' . $filename,
            'updated_at' => date_create()
        ]);

        $mentor = Mentor::findOrFail($mentor_id);

        return response()->json(['data' => $mentor]);
    }

    public function edit(Request $request){
        $mentor = $request->all();
        Mentor::where('id', $mentor['mentor_id'])->update([
            'fullname' => $mentor['fullname'],
            'email' => $mentor['email'],
            'phone' => $mentor['phone'],
            'skill' => $mentor['skill'],
            'status' => $mentor['status'],
            'updated_at' => date_create()
        ]);

        $mentor_data = Mentor::where('id', $mentor['mentor_id'])->first();

        return response()->json(['msg' => 'success', 'data' => $mentor_data]);
    }

    public function delete($mentor_id){
        $mentor = Mentor::where('id', $mentor_id)->first();
        File::delete(public_path($mentor['image']));
        $mentor->delete();

        return response()->json(['msg' => 'success']);
    }

    public function getMentorNotInGroup($program_id){
        $mentees = DB::select('
            SELECT * FROM mentor WHERE id NOT IN (SELECT mentor_id FROM `groups` WHERE program_id = ' . $program_id . ')'
        );

        return response()->json(['data' => $mentees]);
    }

    public function resetPassword($mentor_id, Request $request){
        $role = $request->input('role');
        $mentor = Mentor::where('id', $mentor_id)->first();

        if($role == 'mentor'){
            if($request->input('password') == Crypt::decryptString($mentor->password)){
                Mentor::where('id', $mentor_id)->update([
                    'password' => Crypt::encryptString($request->input('new_password')),
                    'updated_at' => date_create()
                ]);

                return response()->json(['result' => 'success', 'msg' => 'Password Changed Successfully!']);
            }else{
                return response()->json(['result' => 'fail', 'msg' => 'Old Password is not match!']);
            }
        }else{
            Mentor::where('id', $mentor_id)->update([
                'password' => Crypt::encryptString($request->input('new_password')),
                'updated_at' => date_create()
            ]);

            return response()->json(['result' => 'success', 'msg' => 'Password Changed Successfully!']);
        }
    }
}
