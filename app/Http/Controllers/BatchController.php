<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\BatchResource;
use App\Models\Batch;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    public function index(){
        $batchs = Batch::all();
        return BatchResource::collection($batchs);
    }

    public function batches(){
        $batch_total = Batch::count();
        $ongoing_batch = Batch::where('batch_status', 'Ongoing')->first();
        $batches = Batch::all(['id', 'batch_name', 'batch_start', 'batch_end', 'batch_status']);
        return response()->json([
            'data' => [
                'batch_total' => $batch_total,
                'ongoing_batch' => $ongoing_batch == null ? '-' : $ongoing_batch->batch_name,
                'batches' => $batches
            ]
        ]);
    }

    public function detail($batch_id){
        $batch = Batch::findOrFail($batch_id);
        return new BatchResource($batch);
    }

    public function add(Request $request){
        $batch = $request->all();
        $batch['batch_status'] = 'Upcoming';
        $batch['created_at'] = date_create();

        Batch::create($batch);
        return response()->json(['msg' => 'success']);
    }

    public function edit(Request $request){
        $batch = $request->all();
        Batch::where('id', $batch['batch_id'])->update([
            'batch_name' => $batch['batch_name'],
            'batch_start' => $batch['batch_start'],
            'batch_end' => $batch['batch_end'],
            'batch_status' => $batch['batch_status'],
            'updated_at' => date_create()
        ]);

        return response()->json(['msg' => 'success']);
    }

    public function delete($batch_id){
        Batch::where('id', $batch_id)->delete();

        return response()->json(['msg' => 'success']);
    }

    public function batchesNotFinished(){
        $batch = Batch::where('batch_status' , '!=', 'Finished')->get();

        return response()->json(['data' => $batch]);
    }
}
