<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProgramController extends Controller
{
    public function add(Request $request){
        $program = $request->all();
        $program['program_status'] = 'Upcoming';
        $program['created_at'] = date_create();

        Program::create($program);
        return response()->json(['msg' => 'success']);
    }
    
    public function programs(){
        $program_total = Program::count();
        $ongoing_program_total = Program::where('program_status', 'Ongoing')->count();
        $categories = Program::distinct('program_categorie')->get('program_categorie');
        $status = Program::distinct('program_status')->get('program_status');
        $programs = Program::join('batch', 'programs.batch_id', '=', 'batch.id')
            ->select('programs.*', 'batch.batch_name', 'batch.batch_status')->get();
        $batch = Batch::all(['batch_name', 'batch_status']);

        $collected_categories = [];
        $collected_status = [];

        for($i=0; $i<count($categories); $i++){
            array_push($collected_categories, $categories[$i]['program_categorie']);
        }

        for($i=0; $i<count($status); $i++){
            array_push($collected_status, $status[$i]['program_status']);
        }
        
        return response()->json(['data' => [
            'program_total' => $program_total,
            'ongoing_program' => $ongoing_program_total,
            'categories' => $collected_categories,
            'status' => $collected_status,
            'programs' => $programs,
            'batches' => $batch
        ]]);
    }

    public function detail($program_id){
        $program = Program::findOrFail($program_id);
        return response()->json(['data' => $program]);
    }

    public function edit(Request $request){
        $program = $request->all();
        Program::where('id', $program['program_id'])->update([
            'program_name' => $program['program_name'],
            'program_desc' => $program['program_desc'],
            'program_categorie' => $program['program_categorie'],
            'program_status' => $program['program_status'],
            'batch_id' => $program['batch_id'],
            'updated_at' => date_create()
        ]);

        return response()->json(['msg' => 'success']);
    }

    public function delete($program_id){
        Program::where('id', $program_id)->delete();

        return response()->json(['msg' => 'success']);
    }

    public function programOnBatch($batch_id){
        $programs = Program::join('batch', 'programs.batch_id', '=', 'batch.id')
                        ->where('programs.batch_id', $batch_id)
                        ->select('programs.*', 'batch.batch_name')
                        ->get();
        return response()->json(['data' => $programs]);
    }
}
