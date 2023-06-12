<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ActivityType;
use Illuminate\Http\Request;

class ActivityTypeController extends Controller
{
    public function types(){
        $types = ActivityType::all();

        return response()->json(['data' => $types]);
    }

    public function detail(Request $request){
        $type = ActivityType::findOrFail($request->input('id'));

        return response()->json(['data' => $type]);
    }

    public function update(Request $request){
        ActivityType::where('id', $request->input('id'))->update([
            'type' => $request->input('type'),
            'updated_at' => date_create()
        ]);

        return response()->json(['msg' => 'success']);
    }

    public function add(Request $request){
        ActivityType::insert([
            'type' => $request->input('type')
        ]);

        return response()->json(['msg' => 'success']);
    }

    public function delete(Request $request){
        ActivityType::where('id', $request->input('id'))->delete();

        return response()->json(['msg' => 'success']);
    }
}
