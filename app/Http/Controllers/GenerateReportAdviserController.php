<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityRegistration;
use App\Models\Practice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\PhpWord;
use Illuminate\Support\Facades\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpWord\Style\Section;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Log;

class GenerateReportAdviserController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $reportType = $request->input('type');
        $status = $request->input('status');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $yearLevel = $request->input('year_level');
        $sports = $request->input('sports');
        $fileType = $request->input('file_type');
        $activityType = $request->input('activity_type');

        if ($reportType == 1) {  // Practices
            $query = Practice::query();

            /*      Log::info('Query SQL:', ['sql' => $query->toSql()]);
            Log::info('Query Bindings:', ['bindings' => $query->getBindings()]); */


            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
            $results = $query->with('user')->get();
            /*  if ($yearLevel) {
                if (!is_array($yearLevel)) {
                    $yearLevel = [$yearLevel];
                }

                if (count($yearLevel) > 0) {
                    $query->whereHas('user', function ($q) use ($yearLevel) {
                        $q->whereIn('year_level', $yearLevel);
                    });
                }
            } */


            /*       $results->load('user', 'activity'); */



            if ($fileType == 'pdf') {
                return $this->generatePracticePDF($results, $startDate, $endDate);
            } elseif ($fileType === 'docx') {
                return $this->generatePracticeDocx($results, $startDate, $endDate);
            } elseif ($fileType === 'excel') {
                return $this->generatePracticeExcel($results, $startDate, $endDate);
            }
        } elseif ($reportType == 2) {  // List Auditionee
            $query = ActivityRegistration::query()->where('status', 1);

            // Filter by date range
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }

            // Filter by activity type
            if ($activityType) {
                $query->whereHas('activity', function ($q) use ($activityType) {
                    $q->whereIn('activity_type', $activityType);
                });
            }

            // Filter by sports
            if ($sports && count($sports) > 0) {
                $query->whereHas('activity', function ($q) use ($sports) {
                    $q->whereIn('sport_id', $sports);
                });
            }

            // Filter by status
            /*      if ($status) {
                $query->whereIn('status', $status);
            }

            // Filter by year level
            if ($yearLevel && count($yearLevel) > 0) {
                $query->whereHas('user', function ($q) use ($yearLevel) {
                    $q->whereIn('year_level', $yearLevel);
                });
            } */

            $results = $query->get();

            Log::info('Results:', $results->toArray());

            // Generate the report based on file type
            if ($fileType === 'pdf') {
                return $this->generateAuditioneePDF($results, $startDate, $endDate, $reportType);
            } elseif ($fileType === 'docx') {
                return $this->generateAuditioneeDocx($results, $startDate, $endDate);
            } elseif ($fileType === 'excel') {
                return $this->generateAuditioneeExcel($results, $startDate, $endDate);
            }
        } elseif ($reportType == 3) {  // Activity
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
                return $this->generatePDF($results, $startDate, $endDate, $reportType);
            } elseif ($fileType === 'docx') {
                return $this->generateActivityDocx($results, $startDate, $endDate);
            } elseif ($fileType === 'excel') {
                return $this->generateActivityExcel($results, $startDate, $endDate);
            }
        } elseif ($reportType == 4) {  // Official Performer
            $query = ActivityRegistration::query()->where('status', 1);

            // Filter by date range
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }

            // Filter by activity type
            if ($activityType) {
                $query->whereHas('activity', function ($q) use ($activityType) {
                    $q->whereIn('activity_type', $activityType);
                });
            }

            // Filter by sports
            if ($sports && count($sports) > 0) {
                $query->whereHas('activity', function ($q) use ($sports) {
                    $q->whereIn('sport_id', $sports);
                });
            }

            /*   // Filter by status
            if ($status) {
                $query->whereIn('status', $status);
            }

            // Filter by year level
            if ($yearLevel && count($yearLevel) > 0) {
                $query->whereHas('user', function ($q) use ($yearLevel) {
                    $q->whereIn('year_level', $yearLevel);
                });
            } */

            $results = $query->get();

            // Generate the report based on file type
            if ($fileType === 'pdf') {
                return $this->generatePerformerPDF($results, $startDate, $endDate, $reportType);
            } elseif ($fileType === 'docx') {
                return $this->generatePerformerDocx($results, $startDate, $endDate);
            } elseif ($fileType === 'excel') {
                return $this->generatePerformerExcel($results, $startDate, $endDate);
            }
        }
    }

    /* PDF */
    protected function generatePDF($results, $startDate, $endDate, $reportType)
    {
        switch ($reportType) {
            case 1:
                $view = 'coach.reports.tryout-reports';
                break;
            case 2:
                $view = 'coach.reports.official-player-report';
                break;
            case 3:
                $view = 'coach.reports.activity-report';
                break;
                // default:
                //     $view = 'coach.reports.default-report'; // Add a default case if necessary
                //     break;
        }

        // Load the selected view with the data
        $pdf = Pdf::loadView($view, [
            'registrations' => $results,
            'startDate' => $startDate,
            'endDate' => $endDate
        ])->setPaper('a4', 'landscape');

        // Stream the PDF file with the current date in the filename
        return $pdf->stream('activities_report_' . now()->format('Y_m_d') . '.pdf');
    }
    /* Ewan */
    protected function generateDocx($results, $startDate, $endDate)
    {
        $phpWord = new PhpWord();

        // Set default font and size for the document
        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(12);

        // Add a section with landscape orientation
        $section = $phpWord->addSection([
            'orientation' => 'landscape',
            'marginTop' => 400,
            'marginBottom' => 400,
            'marginLeft' => 600,
            'marginRight' => 600,
        ]);

        // Add the university logo and header information with proper alignment
        $header = $section->addHeader();
        $header->addImage(public_path('images/bindtogether-logo.png'), [
            'width' => 100,
            'height' => 100,
            'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::LEFT,
            'wrappingStyle' => 'inline'
        ]);

        $header->addText('Bataan Peninsula State University', ['bold' => true, 'size' => 16, 'color' => '800000'], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $header->addText('Bind Together', ['italic' => true, 'size' => 12, 'color' => '800000'], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $header->addText('City of Balanga, 2100 Bataan', ['italic' => true, 'size' => 10], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $header->addText('Tel: (047) 237-3309 | www.bpsu.edu.ph | Email: bpsu.bindtogether@gmail.com', ['italic' => true, 'size' => 10], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);

        $section->addTextBreak(1);
        $section->addText("Activities Report for $startDate - $endDate", ['bold' => true, 'size' => 12], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);

        $section->addTextBreak(1);

        // Add table with proper cell formatting and 75% width
        $tableStyle = [
            'borderSize' => 6,
            'borderColor' => '999999',
            'cellMargin' => 80,
            'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER,
            'width' => 100 * 75,  // 75% width
        ];
        $phpWord->addTableStyle('activityTable', $tableStyle);

        $table = $section->addTable('activityTable');
        $table->addRow();
        $table->addCell(2000)->addText('User', ['bold' => true]);
        $table->addCell(2000)->addText('Sport', ['bold' => true]);
        $table->addCell(2000)->addText('Height', ['bold' => true]);
        $table->addCell(2000)->addText('Weight', ['bold' => true]);
        $table->addCell(2000)->addText('Date Registered', ['bold' => true]);

        // Loop through the registrations and add rows to the table
        foreach ($results as $registration) {
            $table->addRow();
            $table->addCell(2000)->addText($registration->user->firstname . ' ' . $registration->user->lastname);
            $table->addCell(2000)->addText($registration->activity->sport->name);
            $table->addCell(2000)->addText($registration->height);
            $table->addCell(2000)->addText($registration->weight);
            $table->addCell(2000)->addText($registration->created_at->format('Y-m-d'));
        }

        // Add footer for signature with right alignment
        $footer = $section->addFooter();
        $footer->addText('Reported by:', ['bold' => true], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::RIGHT]);
        $footer->addText('(Space for signature)', ['italic' => true], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::RIGHT]);
        $footer->addText('(name of the report generator)', ['italic' => true], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::RIGHT]);

        // Generate and return the Word document
        $fileName = 'activities_report_' . now()->format('Y_m_d') . '.docx';
        $tempFile = storage_path($fileName);
        $phpWord->save($tempFile, 'Word2007');

        return response()->download($tempFile)->deleteFileAfterSend(true);
    }


    /* Activity docs */
    protected function generateActivityDocx($results, $startDate, $endDate)
    {
        $query = Activity::query();

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $results = $query->with('user')->get();

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


        $phpWord = new PhpWord();
        $section = $phpWord->addSection([
            'orientation' => 'landscape'
        ]);



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

        foreach ($results as $activity) {
            $table->addRow();
            $table->addCell(2000)->addText($activity->title);
            $table->addCell(2000)->addText(getActivityType($activity->type));
            $table->addCell(2000)->addText(getTargetPlayer($activity->target_player));
            $table->addCell(2000)->addText($activity->address);
            $table->addCell(2000)->addText($activity->venue);
            $table->addCell(2000)->addText("{$activity->start_date} to {$activity->end_date}");
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'activity_report_') . '.docx';
        $phpWord->save($tempFile, 'Word2007');

        return response()->download($tempFile)->deleteFileAfterSend(true);
    }
    /* Activity Excel */
    protected function generateActivityExcel($results, $startDate, $endDate)
    {
        $query = Activity::query();

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

        function getActivityTypeExcel($type)
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
        function getTargetPlayerExcel($target)
        {
            return $target == 1 ? 'Official Player' : 'All Student';
        }



        $sheet->setCellValue('A5', 'Title')
            ->setCellValue('B5', 'Type')
            ->setCellValue('C5', 'Target Audience')
            ->setCellValue('D5', 'Venue')
            ->setCellValue('E5', 'Address')
            ->setCellValue('F5', 'Activity Duration');


        $sheet->getStyle('A5:F5')->getFont()->setBold(true);


        $row = 6;
        foreach ($results as $activity) {
            $sheet->setCellValue("A$row", $activity->title);
            $sheet->setCellValue("B$row", getActivityTypeExcel($activity->type));
            $sheet->setCellValue("C$row", getTargetPlayerExcel($activity->target_player));
            $sheet->setCellValue("D$row", $activity->venue);
            $sheet->setCellValue("E$row", $activity->address);
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


    /* Practice PDF */


    protected function generatePracticePDF($results, $startDate, $endDate)
    {
        /* $query = Practice::query();

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $results = $query->with('user')->get(); */

        log::info('Results:', $results->toArray());

        $pdf = Pdf::loadView('adviser.reports.practice-report', [
            'practices' => $results,
            'startDate' => $startDate,
            'endDate' => $endDate
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('practices_report_' . now()->format('Y_m_d') . '.pdf');
    }



    /* Practice docs */
    protected function generatePracticeDocx($results, $startDate, $endDate)
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
    protected function generatePracticeExcel($results, $startDate, $endDate)
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

    /* Auditionee List PDF */

    protected function generateAuditioneePDF($results, $startDate, $endDate)
    {
        $query = ActivityRegistration::query()->where('status', 0);
        $results = $query->get();

        $pdf = Pdf::loadView('adviser.reports.auditionee-report', [
            'auditionee' => $results,
            'startDate' => $startDate,
            'endDate' => $endDate
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('auditionee_report_' . now()->format('Y_m_d') . '.pdf');
    }



    /* Auditionee List Docx */
    protected function generateAuditioneeDocx($results, $startDate, $endDate)
    {
        $query = ActivityRegistration::query()->where('status', 0);
        $results = $query->get();

        /*   if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } */

        /*      $results = $query->with('user')->get(); */

        $phpWord = new PhpWord();

        // Set the section to landscape orientation
        $sectionStyle = array('orientation' => 'landscape');
        $section = $phpWord->addSection($sectionStyle);

        $section->addText("Bataan Peninsula State University", ['bold' => true, 'size' => 16, 'color' => '800000']);
        $section->addText("Bind Together", ['bold' => true, 'size' => 14, 'color' => '800000']);
        $section->addText("Auditionee Report for $startDate - $endDate", ['bold' => true, 'size' => 12]);
        $section->addTextBreak(1);

        $table = $section->addTable();
        $table->addRow();
        $table->addCell(2000)->addText("Name", ['bold' => true]);
        $table->addCell(2000)->addText("Year Level", ['bold' => true]);
        $table->addCell(2000)->addText("Email", ['bold' => true]);
        $table->addCell(2000)->addText("Height", ['bold' => true]);
        $table->addCell(2000)->addText("Weight", ['bold' => true]);
        $table->addCell(2000)->addText("Person to Contact", ['bold' => true]);
        $table->addCell(2000)->addText("Emergency Contact Number", ['bold' => true]);
        $table->addCell(2000)->addText("Date Registered", ['bold' => true]);

        foreach ($results as $auditionee) {
            $table->addRow();
            $table->addCell(2000)->addText($auditionee->user->firstname . ' ' . $auditionee->user->lastname);
            $table->addCell(2000)->addText($auditionee->user->year_level);
            $table->addCell(2000)->addText($auditionee->user->email);
            $table->addCell(2000)->addText($auditionee->height);
            $table->addCell(2000)->addText($auditionee->weight);
            $table->addCell(2000)->addText($auditionee->relationship);
            $table->addCell(2000)->addText($auditionee->emergency_contact);
            $table->addCell(2000)->addText($auditionee->created_at->format('Y-m-d'));
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'auditionee_report_') . '.docx';
        $phpWord->save($tempFile, 'Word2007');

        return response()->download($tempFile)->deleteFileAfterSend(true);
    }


    /* Auditionee Excel */

    protected function generateAuditioneeExcel($results, $startDate, $endDate)
    {
        $query = ActivityRegistration::query()->where('status', 0);
        $results = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();


        $sheet->setTitle('Auditionee Report');
        $sheet->setCellValue('A1', 'Bataan Peninsula State University')
            ->setCellValue('A2', 'Bind Together')
            ->setCellValue('A3', "Auditionee Report for $startDate - $endDate");


        $sheet->getStyle('A1:A3')->getFont()->setBold(true)->setSize(14)->getColor()->setARGB('800000');
        $sheet->getStyle('A3')->getFont()->setSize(12);


        $sheet->setCellValue('A5', 'Name')
            ->setCellValue('B5', 'Year Level')
            ->setCellValue('C5', 'Email')
            ->setCellValue('D5', 'Height')
            ->setCellValue('E5', 'Weight')
            ->setCellValue('F5', 'Person to contact')
            ->setCellValue('G5', 'Emergency Contact Number')
            ->setCellValue('H5', 'Date Registered');



        $sheet->getStyle('A5:H5')->getFont()->setBold(true);


        $row = 6;
        foreach ($results as $auditionee) {
            $sheet->setCellValue("A$row", $auditionee->user->firstname . ' ' . $auditionee->user->lastname);
            $sheet->setCellValue("B$row", $auditionee->user->year_level);
            $sheet->setCellValue("C$row", $auditionee->user->email);
            $sheet->setCellValue("D$row", $auditionee->height);
            $sheet->setCellValue("E$row", "{$auditionee->weight}");
            $sheet->setCellValue("F$row", $auditionee->relationship);
            $sheet->setCellValue("G$row", $auditionee->emergency_contact);
            $sheet->setCellValue("H$row", "{$auditionee->created_at}");
            $row++;
        }

        foreach (range('A', 'E') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }


        $tempFile = tempnam(sys_get_temp_dir(), 'auditionee_report_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        return response()->download($tempFile)->deleteFileAfterSend(true);
    }


    /* Performer List Pdf */
    protected function generatePerformerPDF($results, $startDate, $endDate)
    {
        $query = ActivityRegistration::query()->where('status', 1);
        $results = $query->get();

        $pdf = Pdf::loadView('adviser.reports.performer-report', [
            'performer' => $results,
            'startDate' => $startDate,
            'endDate' => $endDate
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('performer_report_' . now()->format('Y_m_d') . '.pdf');
    }


    /* Performer List Docx */
    protected function generatePerformerDocx($results, $startDate, $endDate)
    {
        $query = ActivityRegistration::query()->where('status', 1);
        $results = $query->get();

        /*    if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $results = $query->with('user')->get(); */

        $phpWord = new PhpWord();

        // Set the section to landscape orientation
        $sectionStyle = array('orientation' => 'landscape');
        $section = $phpWord->addSection($sectionStyle);

        $section->addText("Bataan Peninsula State University", ['bold' => true, 'size' => 16, 'color' => '800000']);
        $section->addText("Bind Together", ['bold' => true, 'size' => 14, 'color' => '800000']);
        $section->addText("Performer Report for $startDate - $endDate", ['bold' => true, 'size' => 12]);
        $section->addTextBreak(1);

        $table = $section->addTable();
        $table->addRow();
        $table->addCell(2000)->addText("Name", ['bold' => true]);
        $table->addCell(2000)->addText("Year Level", ['bold' => true]);
        $table->addCell(2000)->addText("Email", ['bold' => true]);
        $table->addCell(2000)->addText("Height", ['bold' => true]);
        $table->addCell(2000)->addText("Weight", ['bold' => true]);
        $table->addCell(2000)->addText("Person to Contact", ['bold' => true]);
        $table->addCell(2000)->addText("Emergency Contact Number", ['bold' => true]);
        $table->addCell(2000)->addText("Date Registered", ['bold' => true]);

        foreach ($results as $auditionee) {
            $table->addRow();
            $table->addCell(2000)->addText($auditionee->user->firstname . ' ' . $auditionee->user->lastname);
            $table->addCell(2000)->addText($auditionee->user->year_level);
            $table->addCell(2000)->addText($auditionee->user->email);
            $table->addCell(2000)->addText($auditionee->height);
            $table->addCell(2000)->addText($auditionee->weight);
            $table->addCell(2000)->addText($auditionee->relationship);
            $table->addCell(2000)->addText($auditionee->emergency_contact);
            $table->addCell(2000)->addText($auditionee->created_at->format('Y-m-d'));
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'performer_report_') . '.docx';
        $phpWord->save($tempFile, 'Word2007');

        return response()->download($tempFile)->deleteFileAfterSend(true);
    }


    /* Performer Excel */

    protected function generatePerformerExcel($results, $startDate, $endDate)
    {
        $query = ActivityRegistration::query()->where('status', 1);
        $results = $query->get();

        /*       if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $results = $query->with('user')->get(); */

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();


        $sheet->setTitle('Performer Report');
        $sheet->setCellValue('A1', 'Bataan Peninsula State University')
            ->setCellValue('A2', 'Bind Together')
            ->setCellValue('A3', "Performer Report for $startDate - $endDate");


        $sheet->getStyle('A1:A3')->getFont()->setBold(true)->setSize(14)->getColor()->setARGB('800000');
        $sheet->getStyle('A3')->getFont()->setSize(12);


        $sheet->setCellValue('A5', 'Name')
            ->setCellValue('B5', 'Year Level')
            ->setCellValue('C5', 'Email')
            ->setCellValue('D5', 'Height')
            ->setCellValue('E5', 'Weight')
            ->setCellValue('F5', 'Person to contact')
            ->setCellValue('G5', 'Emergency Contact Number')
            ->setCellValue('H5', 'Date Registered');



        $sheet->getStyle('A5:H5')->getFont()->setBold(true);


        $row = 6;
        foreach ($results as $auditionee) {
            $sheet->setCellValue("A$row", $auditionee->user->firstname . ' ' . $auditionee->user->lastname);
            $sheet->setCellValue("B$row", $auditionee->user->year_level);
            $sheet->setCellValue("C$row", $auditionee->user->email);
            $sheet->setCellValue("D$row", $auditionee->height);
            $sheet->setCellValue("E$row", "{$auditionee->weight}");
            $sheet->setCellValue("F$row", $auditionee->relationship);
            $sheet->setCellValue("G$row", $auditionee->emergency_contact);
            $sheet->setCellValue("H$row", "{$auditionee->created_at}");
            $row++;
        }

        foreach (range('A', 'E') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }


        $tempFile = tempnam(sys_get_temp_dir(), 'performer_report_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        return response()->download($tempFile)->deleteFileAfterSend(true);
    }


    /*    protected function generateCoachesPDF($fileType)
    {
        $coaches = Coach::all(); // Fetch all coaches, or you can apply filters based on request parameters.

        // Load the selected view with the data
        $pdf = Pdf::loadView('coach.reports.list-report', [
            'coaches' => $coaches,
        ])->setPaper('a4', 'landscape');

        // Stream the PDF file with the current date in the filename
        return $pdf->stream('coaches_report_' . now()->format('Y_m_d') . '.pdf');
    } */
}
