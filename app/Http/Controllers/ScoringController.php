<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Group;
use App\Models\Program;
use App\Models\Scoring;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use setasign\Fpdi\Fpdi;

class ScoringController extends Controller
{
    public function getScoringByAssignment($assignment_id){
        $assignment = Assignment::join('assignment_type AS t', 'assignment.type_id', '=', 't.id')
            ->where('assignment.id', $assignment_id)->get(['assignment.*', 't.type'])->first();

        $program = Program::join('batch AS b', 'programs.batch_id', '=', 'b.id')
            ->where('programs.id', $assignment->program_id)
            ->select(['programs.*', 'b.batch_name'])
            ->first();
        
        $mentees = DB::table('mentee AS m')
            ->join('groups AS g', 'm.group_id', '=', 'g.id')
            ->where('g.program_id', $assignment->program_id)
            ->join('mentor AS me', 'g.mentor_id', '=', 'me.id')
            ->leftJoin('scoring AS s', function($join) use ($assignment_id) {
                $join->on('m.id', '=', 's.mentee_id')
                    ->where('s.assignment_id', $assignment_id);
                })
            ->get(['m.id', 'm.name', 'm.major', 'm.status', 'g.name AS group_name', 'me.fullname', 's.score', 's.id AS scoring_id']);

        return response()->json(['data' => [
            'assignment' => $assignment,
            'program' => $program,
            'mentees' => $mentees
        ]]);
    }

    public function getScoringByAssignmentMentor($assignment_id, Request $request){
        $assignment = Assignment::join('assignment_type AS t', 'assignment.type_id', '=', 't.id')
            ->where('assignment.id', $assignment_id)->get(['assignment.*', 't.type'])->first();

        $program = Program::join('batch AS b', 'programs.batch_id', '=', 'b.id')
            ->where('programs.id', $assignment->program_id)
            ->select(['programs.*', 'b.batch_name'])
            ->first();
        
        $mentees = DB::table('mentee AS m')
            ->join('groups AS g', 'm.group_id', '=', 'g.id')
            ->join('mentor AS me', 'g.mentor_id', '=', 'me.id')
            ->leftJoin('scoring AS s', function($join) use ($assignment_id) {
                $join->on('m.id', '=', 's.mentee_id')
                    ->where('s.assignment_id', $assignment_id);
                })
            ->where('g.program_id', $assignment->program_id)
            ->where('g.mentor_id', $request->input('mentor_id'))
            ->get(['m.id', 'm.name', 'm.major', 'm.status', 'g.name AS group_name', 'me.fullname', 's.score', 's.id AS scoring_id']);

        return response()->json(['data' => [
            'assignment' => $assignment,
            'program' => $program,
            'mentees' => $mentees
        ]]);
    }

    public function submitScore(Request $request){
        if($request->input('scoring_id') == null){
            Scoring::insert([
                'assignment_id' => $request->input('assignment_id'),
                'mentee_id' => $request->input('mentee_id'),
                'score' => $request->input('score'),
                'created_at' => date_create()
            ]);
        }else{
            Scoring::where('id', $request->input('scoring_id'))
                ->update([
                    'assignment_id' => $request->input('assignment_id'),
                    'mentee_id' => $request->input('mentee_id'),
                    'score' => $request->input('score'),
                    'updated_at' => date_create()
                ]);
        }

        return response()->json(['msg' => 'success']);
    }

    public function printCertificate(Request $request){
        $mentee_id = $request->input('mentee_id');
        $program_id = $request->input('program_id');

        $end = DB::table('programs AS p')
            ->join('batch AS b', 'p.batch_id', '=', 'b.id')
            ->where('p.id', $request->input('program_id'))
            ->first('b.batch_end')->batch_end;
        $assignment = Assignment::where('program_id', $program_id)->count();
        $datas = DB::table('scoring AS s')
                ->join('mentee AS m', 's.mentee_id', '=', 'm.id')
                ->join('assignment AS a', 's.assignment_id', '=', 'a.id')
                ->join('programs AS p', 'a.program_id', '=', 'p.id')
                ->where('p.id', '=', $program_id)
                ->where('m.id', '=', $mentee_id)
                ->groupBy('s.mentee_id');

        $mentee_name = $datas->first('m.name')->name;
        $score = $datas->sum('s.score');
        $avg_score = ceil($score / $assignment);
        $grade = '';

        if($avg_score > 80){
            $grade = 'A';
        }elseif($avg_score > 75){
            $grade = 'AB';
        }elseif($avg_score > 65){
            $grade = 'B';
        }elseif($avg_score > 60){
            $grade = 'BC';
        }elseif($avg_score > 50){
            $grade = 'C';
        }else{
            $grade = 'F';
        }

        $filename = 'Certificate_Of_Completion_' . $mentee_name . '.pdf' ;
        $master_template = public_path() . '/certification_template/score_master.pdf';

        if(is_file(public_path() . '/certificate/score/' . $filename)){
            File::delete(public_path('certificate/score/' . $filename));
        }
        
        $this->fillPDF($master_template, $filename, $mentee_name, $grade, $score, $end);

        return response()->json(['link' => '/certificate/score/' . $filename]);
    }

    public function fillPDF($master_template, $filename, $mentee_name, $grade, $score, $end){
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

        // grade
        $pdf->SetFont('Montserrat-Light', '', 14);
        $pdf->SetTextColor(24, 28, 39);
        $pdf->SetY(140);
        $pdf->Cell(0, 0, 'by Obtaining Grade: ' . $grade, 0, 1, 'C');

        // accumulated score
        $pdf->SetFont('Montserrat-Light', '', 14);
        $pdf->SetTextColor(24, 28, 39);
        $pdf->SetY(146);
        $pdf->Cell(0, 0, 'and accumulated ' . $score . ' points!', 0, 1, 'C');

        // date
        $pdf->SetFont('Montserrat-SemiBold', '', 14);
        $pdf->SetTextColor(24, 28, 39);
        $pdf->SetY(168);
        $pdf->SetX(81);
        $pdf->Cell(0, 0, $end, 0, 1, 'L');

        $pdf->Output(public_path() . '/certificate/score/' . $filename, 'F');
    }
}
