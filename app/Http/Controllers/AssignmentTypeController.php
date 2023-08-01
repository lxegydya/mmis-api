<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AssignmentType;
use Illuminate\Http\Request;

class AssignmentTypeController extends Controller
{
    public function getAllType(){
        $types = AssignmentType::all();

        return response()->json(['data' => $types]);
    }

    public function getTypeById($type_id){
        $type = AssignmentType::where('id', $type_id)->first();

        return response()->json(['data' => $type]);
    }

    public function add(Request $request){
        AssignmentType::insert([
            'type' => $request->input('type'),
            'created_at' => date_create()
        ]);

        return response()->json(['msg' => 'success']);
    }

    public function update(Request $request){
        AssignmentType::where('id', $request->input('id'))
            ->update([
                "type" => $request->input('type'),
                "updated_at" => date_create()
            ]);
        
        return response()->json(['msg' => 'success']);
    }

    public function delete(Request $request){
        AssignmentType::where('id', $request->input('id'))->delete();

        return response()->json(['msg' => 'success']);
    }
}
