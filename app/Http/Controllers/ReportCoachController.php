<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityRegistration;
use App\Models\Practice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ReportCoachController extends Controller
{
    public function index()
    {
        return view('coach.reports.index');
    }

    public function generateReport(Request $request)
    {
        $reportType = $request->input('type');
        $status = $request->input('status');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $yearLevel = $request->input('year_level');
        $fileType = $request->input('file_type');

        if ($reportType == 1 || $reportType == 2) {
            $query = ActivityRegistration::query();

            if ($reportType == 1) {
                $query->where('status', 0);
            } elseif ($reportType == 2) {
                $query->where('status', 1);
            }


            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }

            if ($status) {
                $query->where('status', $status);
            }

            if ($yearLevel) {
                // Ensure that $yearLevel is always treated as an array
                if (!is_array($yearLevel)) {
                    $yearLevel = [$yearLevel];
                }

                if (count($yearLevel) > 0) {
                    $query->whereHas('user', function ($q) use ($yearLevel) {
                        $q->whereIn('year_level', $yearLevel);
                    });
                }
            }

            $results = $query->get();
            // $results->load('user', 'sport');
            /* Tryout Player */
            if ($fileType === 'pdf') {
                if ($reportType == 1) {
                    return $this->generateTryoutParticipantPDF($results, $startDate, $endDate);
                }
            }
            if ($fileType === 'docx') {
                if ($reportType == 1) {
                    return $this->generateTryoutParticipantDocx($results, $startDate, $endDate);
                }
            }
            if ($fileType === 'excel') {
                if ($reportType == 1) {
                    return $this->generateTryoutParticipantExcel($results, $startDate, $endDate);
                }
            }
            /* Official Players */

            if ($fileType === 'pdf') {
                if ($reportType == 2) {
                    return $this->generateOfficialParticipantPDF($results, $startDate, $endDate);
                }
            }
            if ($fileType === 'docx') {
                if ($reportType == 2) {
                    return $this->generateOfficialParticipantDocx($results, $startDate, $endDate);
                }
            }
            if ($fileType === 'excel') {
                if ($reportType == 2) {
                    return $this->generateOfficialParticipantExcel($results, $startDate, $endDate);
                }
            }
        } elseif ($reportType == 3) {
            $query = Practice::query();

            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }

            if ($status) {
                $query->where('status', $status);
            }

            if ($yearLevel) {
                if (!is_array($yearLevel)) {
                    $yearLevel = [$yearLevel];
                }

                if (count($yearLevel) > 0) {
                    $query->whereHas('user', function ($q) use ($yearLevel) {
                        $q->whereIn('year_level', $yearLevel);
                    });
                }
            }

            $results = $query->get();
            $results->load('user', 'activity.sport');

            if ($fileType == 'pdf') {
                return $this->generatePracticePDF($results, $startDate, $endDate, $reportType); // Pass reportType to the generatePDF method
            }
            if ($fileType == 'docx') {
                return $this->generatePracticeDOCX($results, $startDate, $endDate, $reportType); // Pass reportType to the generatePDF method
            }
            if ($fileType == 'excel') {
                return $this->generatePracticeEXCEL($results, $startDate, $endDate, $reportType); // Pass reportType to the generatePDF method
            }
        } else {
            $query = Activity::query();
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }

            if ($status) {
                $query->where('status', $status);
            }

            if ($yearLevel) {
                if (!is_array($yearLevel)) {
                    $yearLevel = [$yearLevel];
                }

                if (count($yearLevel) > 0) {
                    $query->whereHas('user', function ($q) use ($yearLevel) {
                        $q->whereIn('year_level', $yearLevel);
                    });
                }
            }

            $results = $query->get();
            $results->load('user');

            if ($fileType == 'pdf') {
                return $this->generateActivityPDF($results, $startDate, $endDate, $reportType); // Pass reportType to the generatePDF method
            }
            if ($fileType == 'docx') {
                return $this->generateActivityDOCX($results, $startDate, $endDate);
            }
            if ($fileType == 'excel') {
                return $this->generateActivityEXCEL($results, $startDate, $endDate, $reportType);
            }
        }
    }

    protected function generateActivityPDF($results, $startDate, $endDate, $reportType)
    {
        switch ($reportType) {
            case 1:
                $view = 'coach.reports.tryout-reports';
                break;
            case 2:
                $view = 'coach.reports.official-player-report';
                break;
            case 3:
                $view = 'coach.reports.practice-report';
                break;
            case 4:
                $view = 'coach.reports.activity-report';
                break;
                // default:
                //     $view = 'coach.reports.default-report'; // Add a default case if necessary
                //     break;
        }
        $query = Activity::query();
        $results = $query->get();
        Log::info('Generating PDF report with view: ' . $results);

        // Load the selected view with the data
        $pdf = Pdf::loadView($view, [
            'registrations' => $results,
            'startDate' => $startDate,
            'endDate' => $endDate
        ])->setPaper('a4', 'landscape');

        // Stream the PDF file with the current date in the filename
        return $pdf->stream('activities_report_' . now()->format('Y_m_d') . '.pdf');
    }

    protected function generateActivityDOCX($results, $startDate, $endDate)
    {
        $query = Activity::query();
        $results = $query->get();


        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        $section->addText("Bataan Peninsula State University", ['bold' => true, 'size' => 16, 'color' => '800000']);
        $section->addText("Bind Together", ['bold' => true, 'size' => 14, 'color' => '800000']);
        $section->addText("Activities Report for $startDate - $endDate", ['bold' => true, 'size' => 12]);
        $section->addTextBreak(1);

        $table = $section->addTable();
        $table->addRow();
        $table->addCell(2000)->addText("Title", ['bold' => true]);
        $table->addCell(2000)->addText("Activity Type", ['bold' => true]);
        $table->addCell(2000)->addText("Target Audience", ['bold' => true]);
        $table->addCell(2000)->addText("Address", ['bold' => true]);
        $table->addCell(2000)->addText("Venue", ['bold' => true]);
        $table->addCell(2000)->addText("Activity Duration", ['bold' => true]);

        // Helper function to get activity type
        function getActivityType($type)
        {
            switch ($type) {
                case \App\Enums\ActivityType::Audition:
                    return 'Audition';
                case \App\Enums\ActivityType::Tryout:
                    return 'Tryout';
                case \App\Enums\ActivityType::Practice:
                    return 'Practice';
                case \App\Enums\ActivityType::Competition:
                    return 'Competition';
                default:
                    return 'Unknown';
            }
        }

        // Helper function to get target player
        function getTargetPlayer($target)
        {
            return $target == 1 ? 'Official Player' : 'All Student';
        }


        foreach ($results as $activity) {
            $table->addRow();
            $table->addCell(2000)->addText($activity->title);
            $table->addCell(2000)->addText(getActivityType($activity->type));
            $table->addCell(2000)->addText(getTargetPlayer($activity->target_player));
            $table->addCell(2000)->addText($activity->address ?? 'N/A');
            $table->addCell(2000)->addText($activity->venue);
            $table->addCell(2000)->addText("{$activity->start_date} to {$activity->end_date}");
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'activity_report_') . '.docx';
        $phpWord->save($tempFile, 'Word2007');

        return response()->download($tempFile)->deleteFileAfterSend(true);
    }

    protected function generateActivityEXCEL($results, $startDate, $endDate)
    {
        $query = Activity::query();

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $results = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();


        $sheet->setTitle('Activities Report');
        $sheet->setCellValue('A1', 'Bataan Peninsula State University')
            ->setCellValue('A2', 'Bind Together')
            ->setCellValue('A3', "Activities Report for $startDate - $endDate");


        $sheet->getStyle('A1:A3')->getFont()->setBold(true)->setSize(14)->getColor()->setARGB('800000');
        $sheet->getStyle('A3')->getFont()->setSize(12);


        $sheet->setCellValue('A5', 'Title')
            ->setCellValue('B5', 'Activity Type')
            ->setCellValue('C5', 'Target Audience')
            ->setCellValue('D5', 'Address')
            ->setCellValue('E5', 'Venue')
            ->setCellValue('F5', 'Activity Duration');


        $sheet->getStyle('A5:E5')->getFont()->setBold(true);
        function getActivityTypeAct($type)
        {
            switch ($type) {
                case \App\Enums\ActivityType::Audition:
                    return 'Audition';
                case \App\Enums\ActivityType::Tryout:
                    return 'Tryout';
                case \App\Enums\ActivityType::Practice:
                    return 'Practice';
                case \App\Enums\ActivityType::Competition:
                    return 'Competition';
                default:
                    return 'Unknown';
            }
        }

        function getTargetPlayerAct($target)
        {
            return $target == 1 ? 'Official Player' : 'All Student';
        }
        $row = 6;
        foreach ($results as $activity) {
            $sheet->setCellValue("A$row", $activity->title);
            $sheet->setCellValue("B$row", getActivityTypeAct($activity->type));
            $sheet->setCellValue("C$row", getTargetPlayerAct($activity->target_player));
            $sheet->setCellValue("D$row", $activity->address);
            $sheet->setCellValue("E$row", $activity->venue);
            $sheet->setCellValue("F$row", "{$activity->start_date} to {$activity->end_date}");
            $row++;
        }

        foreach (range('A', 'E') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }


        $tempFile = tempnam(sys_get_temp_dir(), 'activity_report_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        return response()->download($tempFile)->deleteFileAfterSend(true);
    }


    protected function generatePracticePDF($results, $startDate, $endDate)
    {
        $query = Practice::query();

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $results = $query->get();

        log::info('Results:', $results->toArray());

        $pdf = Pdf::loadView('coach.reports.practice-report', [
            'registrations' => $results,
            'startDate' => $startDate,
            'endDate' => $endDate
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('practices_report_' . now()->format('Y_m_d') . '.pdf');
    }



    /* Practice docs */
    protected function generatePracticeDOCX($results, $startDate, $endDate)
    {
        $query = Practice::query();

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $results = $query->with('user')->get();


        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        $section->addText("Bataan Peninsula State University", ['bold' => true, 'size' => 16, 'color' => '800000']);
        $section->addText("Bind Together", ['bold' => true, 'size' => 14, 'color' => '800000']);
        $section->addText("Activities Report for $startDate - $endDate", ['bold' => true, 'size' => 12]);
        $section->addTextBreak(1);

        $table = $section->addTable();
        $table->addRow();
        $table->addCell(2000)->addText("Name", ['bold' => true]);
        $table->addCell(2000)->addText("Response", ['bold' => true]);
        $table->addCell(2000)->addText("Reason", ['bold' => true]);
        $table->addCell(2000)->addText("Date Registered", ['bold' => true]);


        foreach ($results as $practices) {
            $table->addRow();
            $table->addCell(2000)->addText($practices->user->firstname . ' ' . $practices->user->lastname ?? 'N/A');
            $table->addCell(2000)->addText($practices->activity->reponse ?? 'N/A');
            $table->addCell(2000)->addText($practices->reason ?? 'N/A');
            $table->addCell(2000)->addText($practices->created_at ?? 'N/A');
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'practices_report_') . '.docx';
        $phpWord->save($tempFile, 'Word2007');

        return response()->download($tempFile)->deleteFileAfterSend(true);
    }
    /* Practice Excel */
    protected function generatePracticeEXCEL($results, $startDate, $endDate)
    {
        $query = Practice::query();

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $results = $query->with('user')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();


        $sheet->setTitle('Activities Report');
        $sheet->setCellValue('A1', 'Bataan Peninsula State University')
            ->setCellValue('A2', 'Bind Together')
            ->setCellValue('A3', "Activities Report for $startDate - $endDate");


        $sheet->getStyle('A1:A3')->getFont()->setBold(true)->setSize(14)->getColor()->setARGB('800000');
        $sheet->getStyle('A3')->getFont()->setSize(12);


        $sheet->setCellValue('A5', 'Name')
            ->setCellValue('B5', 'Response')
            ->setCellValue('C5', 'Reason')
            ->setCellValue('D5', 'Date Registered');



        $sheet->getStyle('A5:E5')->getFont()->setBold(true);


        $row = 6;
        foreach ($results as $practices) {
            $sheet->setCellValue("A$row", $practices->user->firstname . ' ' . $practices->user->lastname ?? 'N/A');
            $sheet->setCellValue("B$row", $practices->activity->response ?? 'N/A');
            $sheet->setCellValue("C$row", $practices->reason ?? 'N/A');
            $sheet->setCellValue("D$row", "{$practices->created_at}" ?? 'N/A');
            $row++;
        }

        foreach (range('A', 'D') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }


        $tempFile = tempnam(sys_get_temp_dir(), 'activity_report_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        return response()->download($tempFile)->deleteFileAfterSend(true);
    }

    protected function generateTryoutParticipantPDF($results, $startDate, $endDate)
    {
        $query = ActivityRegistration::query();

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $results = $query->with('user')->get();

        $pdf = Pdf::loadView('coach.reports.tryout-player-report', [
            'tryout_players' => $results,
            'startDate' => $startDate,
            'endDate' => $endDate
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('tryout-player-report' . now()->format('Y_m_d') . '.pdf');
    }

    protected function generateTryoutParticipantDocx($results, $startDate, $endDate)
    {
        $query = ActivityRegistration::query();

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $results = $query->with(['user.sport', 'user.campus'])->get();

        $phpWord = new PhpWord();
        $section = $phpWord->addSection([
            'orientation' => 'landscape'
        ]);

        Log::info($results);

        $section->addText("Bataan Peninsula State University", ['bold' => true, 'size' => 16, 'color' => '800000']);
        $section->addText("Bind Together", ['bold' => true, 'size' => 14, 'color' => '800000']);
        $section->addText("Tryout Players Report for $startDate - $endDate", ['bold' => true, 'size' => 12]);
        $section->addTextBreak(1);

        $table = $section->addTable();
        $table->addRow();
        $table->addCell(2000)->addText("", ['bold' => true]);
        $table->addCell(2000)->addText("Name", ['bold' => true]);
        $table->addCell(2000)->addText("Year Level", ['bold' => true]);
        $table->addCell(2000)->addText("Email", ['bold' => true]);
        $table->addCell(2000)->addText("Height", ['bold' => true]);
        $table->addCell(2000)->addText("Weight", ['bold' => true]);
        $table->addCell(2000)->addText("Person to Contact", ['bold' => true]);
        $table->addCell(2000)->addText("Emergency Contact Number", ['bold' => true]);
        $table->addCell(2000)->addText("Date Registered", ['bold' => true]);

        foreach ($results as $official_player) {
            $table->addRow();
            $table->addCell(2000)->addText(($official_player->user->firstname ?? '') . ' ' . ($official_player->user->lastname ?? ''));
            $table->addCell(2000)->addText($official_player->user->year_level ?? 'N/A');
            $table->addCell(2000)->addText($official_player->user->email ?? 'N/A');
            $table->addCell(2000)->addText($official_player->height ?? 'N/A');
            $table->addCell(2000)->addText($official_player->weight ?? 'N/A');
            $table->addCell(2000)->addText($official_player->relationship ?? 'N/A');
            $table->addCell(2000)->addText($official_player->emergency_contact ?? 'N/A');
            $table->addCell(2000)->addText($official_player->created_at ? $official_player->created_at->format('Y-m-d') : 'N/A');
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'tryout_players_report_') . '.docx';
        $phpWord->save($tempFile, 'Word2007');

        return response()->download($tempFile)->deleteFileAfterSend(true);
    }


    protected function generateTryoutParticipantExcel($results, $startDate, $endDate)
    {
        $query = ActivityRegistration::query();

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $results = $query->with(['user.sport', 'user.campus'])->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();


        $sheet->setTitle('Tryout Players Report');
        $sheet->setCellValue('A1', 'Bataan Peninsula State University')
            ->setCellValue('A2', 'Bind Together')
            ->setCellValue('A3', "Tryout Players Report for $startDate - $endDate");


        $sheet->getStyle('A1:A3')->getFont()->setBold(true)->setSize(14)->getColor()->setARGB('800000');
        $sheet->getStyle('A3')->getFont()->setSize(12);


        $sheet->setCellValue('A5', 'Name')
            ->setCellValue('B5', 'Year Level')
            ->setCellValue('C5', 'Email')
            ->setCellValue('D5', 'Height')
            ->setCellValue('E5', 'Weight')
            ->setCellValue('F5', 'Person to Contact')
            ->setCellValue('G5', 'Emergency Contact Number')
            ->setCellValue('H5', 'Date Registered');


        $sheet->getStyle('A5:H5')->getFont()->setBold(true);


        $row = 6;
        foreach ($results as $official_player) {
            $sheet->setCellValue("A$row", $official_player->user->firstname . ' ' . $official_player->user->lastname ?? 'N/A');
            $sheet->setCellValue("B$row", $official_player->user->year_level ?? 'N/A');
            $sheet->setCellValue("C$row", $official_player->user->email ?? 'N/A');
            $sheet->setCellValue("D$row", $official_player->height ?? 'N/A');
            $sheet->setCellValue("E$row", $official_player->weight ?? 'N/A');
            $sheet->setCellValue("F$row", $official_player->relationship ?? 'N/A');
            $sheet->setCellValue("G$row", $official_player->emergency_contact ?? 'N/A');
            $sheet->setCellValue("H$row", $official_player->created_at);

            $row++;
        }

        foreach (range('A', 'E') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }


        $tempFile = tempnam(sys_get_temp_dir(), 'tryout_players_report_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        return response()->download($tempFile)->deleteFileAfterSend(true);
    }

    protected function generateOfficialParticipantPDF($results, $startDate, $endDate)
    {
        $query = ActivityRegistration::query();

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $results = $query->with('user')->get();

        $pdf = Pdf::loadView('coach.reports.official-player-report', [
            'official_players' => $results,
            'startDate' => $startDate,
            'endDate' => $endDate
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('official-player-report' . now()->format('Y_m_d') . '.pdf');
    }

    protected function generateOfficialParticipantDocx($results, $startDate, $endDate)
    {
        $query = ActivityRegistration::query();

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $results = $query->with(['user.sport', 'user.campus'])->get();

        $phpWord = new PhpWord();
        $section = $phpWord->addSection([
            'orientation' => 'landscape'
        ]);

        Log::info($results);

        $section->addText("Bataan Peninsula State University", ['bold' => true, 'size' => 16, 'color' => '800000']);
        $section->addText("Bind Together", ['bold' => true, 'size' => 14, 'color' => '800000']);
        $section->addText("Official Players Report for $startDate - $endDate", ['bold' => true, 'size' => 12]);
        $section->addTextBreak(1);

        $table = $section->addTable();
        $table->addRow();
        $table->addCell(2000)->addText("", ['bold' => true]);
        $table->addCell(2000)->addText("Name", ['bold' => true]);
        $table->addCell(2000)->addText("Year Level", ['bold' => true]);
        $table->addCell(2000)->addText("Email", ['bold' => true]);
        $table->addCell(2000)->addText("Height", ['bold' => true]);
        $table->addCell(2000)->addText("Weight", ['bold' => true]);
        $table->addCell(2000)->addText("Person to Contact", ['bold' => true]);
        $table->addCell(2000)->addText("Emergency Contact Number", ['bold' => true]);
        $table->addCell(2000)->addText("Date Registered", ['bold' => true]);

        foreach ($results as $official_player) {
            $table->addRow();
            $table->addCell(2000)->addText(($official_player->user->firstname ?? '') . ' ' . ($official_player->user->lastname ?? ''));
            $table->addCell(2000)->addText($official_player->user->year_level ?? 'N/A');
            $table->addCell(2000)->addText($official_player->user->email ?? 'N/A');
            $table->addCell(2000)->addText($official_player->height ?? 'N/A');
            $table->addCell(2000)->addText($official_player->weight ?? 'N/A');
            $table->addCell(2000)->addText($official_player->relationship ?? 'N/A');
            $table->addCell(2000)->addText($official_player->emergency_contact ?? 'N/A');
            $table->addCell(2000)->addText($official_player->created_at ? $official_player->created_at->format('Y-m-d') : 'N/A');
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'official_players_report_') . '.docx';
        $phpWord->save($tempFile, 'Word2007');

        return response()->download($tempFile)->deleteFileAfterSend(true);
    }


    protected function generateOfficialParticipantExcel($results, $startDate, $endDate)
    {
        $query = ActivityRegistration::query();

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $results = $query->with(['user.sport', 'user.campus'])->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();


        $sheet->setTitle('Official Players Report');
        $sheet->setCellValue('A1', 'Bataan Peninsula State University')
            ->setCellValue('A2', 'Bind Together')
            ->setCellValue('A3', "Official Players Report for $startDate - $endDate");


        $sheet->getStyle('A1:A3')->getFont()->setBold(true)->setSize(14)->getColor()->setARGB('800000');
        $sheet->getStyle('A3')->getFont()->setSize(12);


        $sheet->setCellValue('A5', 'Name')
            ->setCellValue('B5', 'Year Level')
            ->setCellValue('C5', 'Email')
            ->setCellValue('D5', 'Height')
            ->setCellValue('E5', 'Weight')
            ->setCellValue('F5', 'Person to Contact')
            ->setCellValue('G5', 'Emergency Contact Number')
            ->setCellValue('H5', 'Date Registered');


        $sheet->getStyle('A5:H5')->getFont()->setBold(true);


        $row = 6;
        foreach ($results as $official_player) {
            $sheet->setCellValue("A$row", $official_player->user->firstname . ' ' . $official_player->user->lastname ?? 'N/A');
            $sheet->setCellValue("B$row", $official_player->user->year_level ?? 'N/A');
            $sheet->setCellValue("C$row", $official_player->user->email ?? 'N/A');
            $sheet->setCellValue("D$row", $official_player->height ?? 'N/A');
            $sheet->setCellValue("E$row", $official_player->weight ?? 'N/A');
            $sheet->setCellValue("F$row", $official_player->relationship ?? 'N/A');
            $sheet->setCellValue("G$row", $official_player->emergency_contact ?? 'N/A');
            $sheet->setCellValue("H$row", $official_player->created_at);

            $row++;
        }

        foreach (range('A', 'E') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }


        $tempFile = tempnam(sys_get_temp_dir(), 'official_players_report_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        return response()->download($tempFile)->deleteFileAfterSend(true);
    }
}
