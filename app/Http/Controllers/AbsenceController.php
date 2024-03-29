<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AbsenceExport;
use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Activity;
use App\Models\Batch;
use App\Models\Group;
use App\Models\Mentee;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use setasign\Fpdi\Fpdi;

class AbsenceController extends Controller
{
    public function superadminAbsence(){
        $programs = DB::table('programs AS p')
            ->join('batch AS b', 'p.batch_id', '=', 'b.id')
            ->select(['p.program_name', 'p.id AS program_id', 'b.id', 'b.batch_name', 'p.program_status'])->get();

        $batch = Batch::all();

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

        return response()->json(['data' => [
            'programs' => $programs,
            'batches' => $batch
        ]]);
    }

    public function mentorAbsence(Request $request){
        $mentor_id = $request->input('mentor_id');
        $batch = Batch::all();
        $programs = DB::table('programs AS p')
            ->join('batch AS b', 'p.batch_id', '=', 'b.id')
            ->join('groups AS g', function($join) use ($mentor_id){
                $join->on('p.id', '=', 'g.program_id')
                    ->where('g.mentor_id', '=', $mentor_id);
            })->select(['p.program_name', 'p.id AS program_id', 'b.id', 'b.batch_name', 'p.program_status'])->get();

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

        return response()->json(['data' => [
            'programs' => $programs,
            'batches' => $batch
        ]]);
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
                            ->where('p.id', $request->input('id'))
                            ->count('m.id');

            $mentees_count = DB::table('absence AS a')
                            ->where('a.activity_id', $activities[$i]->id)
                            ->where('a.present', true)
                            ->whereIn('a.mentee_id', DB::table('mentee AS m')
                                ->join('groups AS g', 'm.group_id', '=', 'g.id')
                                ->where('g.program_id', $request->input('id'))
                                ->select(['m.id'])
                            )->distinct()->count('a.mentee_id');

            $activities[$i]->mentees_count = $mentees_count;
            $activities[$i]->mentees_total = $mentees_total;
        }

        $program = Program::where('programs.id', $request->input('id'))
            ->join('batch AS b', 'programs.batch_id', '=', 'b.id')->first();

        return response()->json(['data' => $activities, 'program' => $program]);
    }

    public function mentorAbsenceActivities(Request $request){
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
                            ->where('g.mentor_id', $request->input('mentor_id'))
                            ->where('p.id', $request->input('id'))
                            ->count('m.id');

            $mentees_count = DB::table('absence AS a')
                                ->where('a.activity_id', $activities[$i]->id)
                                ->where('a.present', true)
                                ->whereIn('a.mentee_id', DB::table('mentee AS m')
                                    ->join('groups AS g', 'm.group_id', '=', 'g.id')
                                    ->where('g.mentor_id', $request->input('mentor_id'))
                                    ->where('g.program_id', $request->input('id'))
                                    ->select(['m.id'])
                                )->count('a.mentee_id');

            $activities[$i]->mentees_count = $mentees_count;
            $activities[$i]->mentees_total = $mentees_total;
        }

        $program = Program::where('programs.id', $request->input('id'))
            ->join('batch AS b', 'programs.batch_id', '=', 'b.id')->first();

        return response()->json(['data' => $activities, 'program' => $program]);
    }

    public function getAbsenceList($activity_id){
        $activity = Activity::findOrFail($activity_id);

        $groups = Group::where('program_id', $activity->program_id)
            ->join('mentor', 'groups.mentor_id', '=', 'mentor.id')
            ->get(['groups.id', 'groups.mentor_id', 'mentor.fullname', 'groups.name']);

        $program = Program::where('programs.id', $activity->program_id)
            ->join('batch AS b', 'programs.batch_id', '=', 'b.id')->first();

        for($i=0; $i<count($groups); $i++){
            $mentees = DB::table('mentee AS m')
            ->leftJoin(DB::raw('(SELECT DISTINCT mentee_id, present, information FROM absence WHERE activity_id = ' . $activity_id . ') AS a'), function($join) {
                $join->on('m.id', '=', 'a.mentee_id');
            })
            ->where('m.group_id', $groups[$i]->id)
            ->select('m.id', 'm.name', 'm.status', 'a.present', 'a.information')
            ->get();

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
        ], 'program' => $program]);
    }

    public function absenceActivityDetail($activity_id, Request $request){
        $activity = Activity::where('id', $activity_id)->get(['id', 'name', 'date', 'program_id'])->first();
        $groups = Group::where('program_id', $activity->program_id)
            ->where('groups.mentor_id', $request->input('mentor_id'))
            ->join('mentor', 'groups.mentor_id', '=', 'mentor.id')
            ->get(['groups.id', 'groups.mentor_id', 'mentor.fullname', 'groups.name']);

        $program = Program::where('programs.id', $activity->program_id)
            ->join('batch AS b', 'programs.batch_id', '=', 'b.id')->first();

        for($i=0; $i<count($groups); $i++){
            $mentees = DB::table('mentee AS m')
                ->leftJoin('absence AS a', function($join) use ($activity_id) {
                    $join->on('m.id', '=', 'a.mentee_id')
                        ->where('a.activity_id', $activity_id);
                })
                ->where('m.group_id', $groups[$i]->id)
                ->get(['m.id', 'm.name', 'm.status', 'a.present', 'a.id AS absence_id', 'a.information']);

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
            'groups' => $groups,
        ], 'program' => $program]);
    }

    public function inputAbsence(Request $request){
        $absence_list = $request->input('absence_list');

        foreach ($absence_list as $absence_item) {
            $existingAbsence = Absence::where('activity_id', $request->input('activity_id'))
                ->where('mentee_id', $absence_item['id'])
                ->first();

            if ($existingAbsence) {
                $existingAbsence->update([
                    'present' => $absence_item['present'],
                    'information' => $absence_item['information'],
                    'updated_at' => now()
                ]);
            } else {
                Absence::insert([
                    'activity_id' => $request->input('activity_id'),
                    'mentee_id' => $absence_item['id'],
                    'present' => $absence_item['present'],
                    'information' => $absence_item['information'],
                    'created_at' => now()
                ]);
            }
        }

        return response()->json(['msg' => 'success']);
    }

    public function printCertificate(Request $request){
        $mentee_name = Mentee::where('id', $request->input('mentee_id'))->first()['name'];
        $activity_count = Activity::where('program_id', $request->input('program_id'))->count();
        $present_count = Absence::where('mentee_id', $request->input('mentee_id'))->count();

        $end = DB::table('programs AS p')
            ->join('batch AS b', 'p.batch_id', '=', 'b.id')
            ->where('p.id', $request->input('program_id'))
            ->first('b.batch_end')->batch_end;

        $filename = 'Certificate_Of_Attendance_' . $mentee_name . '.pdf' ;
        $master_template = public_path() . '/certification_template/absence_master.pdf';

        if(!is_file(public_path() . '/certificate/absence/' . $filename)){
            $this->fillPDF($master_template, $filename, $mentee_name, $activity_count, $present_count, $end);
        }

        return response()->json(['link' => '/certificate/absence/' . $filename]);
    }

    public function fillPDF($master_template, $filename, $mentee_name, $activity_count, $present_count, $end){
        $pdf = new Fpdi();
        $pdf->setSourceFile($master_template);
        $pdf->AddFont('Montserrat-Bold','','Montserrat-Bold.php');
        $pdf->AddFont('Montserrat-Light','','Montserrat-Light.php');
        $pdf->AddFont('Montserrat-SemiBold','','Montserrat-SemiBold.php');

        $template = $pdf->importPage(1);

        $pdf->AddPage('L', 'A4');
        $pdf->useTemplate($template);

        // name
        $pdf->SetFont('Montserrat-Bold', '', 56);
        $pdf->SetTextColor(190, 30, 45);
        $pdf->SetY(107);
        $pdf->Cell(0, 0, $mentee_name, 0, 1, 'C');

        //attendance
        $pdf->SetFont('Montserrat-Light', '', 14);
        $pdf->SetTextColor(24, 28, 39);
        $pdf->SetY(140);
        $pdf->Cell(0, 0, 'with the Percentage of Attendance is ' . round($present_count / $activity_count * 100) . '%', 0, 1, 'C');

        // date
        $pdf->SetFont('Montserrat-SemiBold', '', 14);
        $pdf->SetTextColor(24, 28, 39);
        $pdf->SetY(168);
        $pdf->SetX(81);
        $pdf->Cell(0, 0, $end, 0, 1, 'L');

        $pdf->Output(public_path() . '/certificate/absence/' . $filename, 'F');
    }

    public function export(Request $request, $program_id)
    {
        $program = Program::select('programs.program_name AS name', 'batch.batch_name AS batch')
            ->join('batch', 'programs.batch_id', '=', 'batch.id')
            ->where('programs.id', '=', $program_id)
            ->first();

        if($request->input('mentor_id') == null){
            $mentees = Mentee::select('mentee.id', 'mentee.name')
                ->whereIn('group_id', Group::select('id')->where('program_id', $program_id))
                ->groupBy('mentee.id')
                ->orderBy('mentee.name')
                ->get();
        }else{
            $mentees = Mentee::select('mentee.id', 'mentee.name')
                ->whereIn('group_id', Group::select('id')->where('program_id', $program_id))
                ->whereIn('group_id', Group::select('id')->where('mentor_id', $request->input('mentor_id')))
                ->groupBy('mentee.id')
                ->orderBy('mentee.name')
                ->get();
        }

        foreach ($mentees as $index => $data) {
            $menteeId = $data['id'];

            $activities = DB::table('activity')
                ->where('program_id', $program_id)
                ->select('id', 'name')
                ->orderBy('id')
                ->get();

            $absences = DB::table('absence')
                ->select('activity_id', 'present')
                ->where('mentee_id', '=', $menteeId)
                ->distinct()
                ->get()
                ->keyBy('activity_id');

            $absenceList = [];
            foreach ($activities as $activity) {
                $present = isset($absences[$activity->id]) ? $absences[$activity->id]->present : 0;
                $absenceList[] = [
                    'name' => $activity->name,
                    'present' => $present
                ];
            }

            $mentees[$index]['absence_list'] = $absenceList;
        }

        $export = new AbsenceExport($mentees);
        $excelFile = 'exports/[Absence] ' . preg_replace('/[\/:*?"<>|]/', '', $program->name) . ' - ' . $program->batch . '.xlsx';
        Excel::store($export, $excelFile);
        $storagePath = Storage::path($excelFile);

        return response()->download($storagePath, '[Absence] ' . preg_replace('/[\/:*?"<>|]/', '', $program->name) . ' - ' . $program->batch . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename=[Absence] ' . preg_replace('/[\/:*?"<>|]/', '', $program->name) . ' - ' . $program->batch . '.xlsx',
        ])->deleteFileAfterSend(true);
    }
}
