<?php

namespace App\Http\Controllers;

use App\Services\PartyImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportController extends Controller
{
    public function __construct(
        protected PartyImportService $importService
    ) {}

    /**
     * Display the Excel/CSV import interface.
     */
    public function index(): View
    {
        return view('import.index');
    }

    /**
     * Handle file upload and process the spreadsheet import.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,csv,txt', 'max:10240'],
        ], [
            'file.required' => 'Please select an Excel (.xlsx) or CSV (.csv) file to upload.',
            'file.mimes' => 'The uploaded file must be an Excel spreadsheet (.xlsx) or CSV file (.csv).',
            'file.max' => 'File size cannot exceed 10MB.',
        ]);

        try {
            $result = $this->importService->import($request->file('file'));

            // Store failed rows in session for downloadable error report
            if (! empty($result['failed_rows'])) {
                session(['import_failed_rows' => $result['failed_rows']]);
            } else {
                session()->forget('import_failed_rows');
            }

            return back()->with('import_result', $result);
        } catch (\Throwable $e) {
            return back()->withErrors([
                'file' => 'Failed to parse file: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * Download sample import template CSV.
     */
    public function template(): Response
    {
        $csvContent = $this->importService->generateCsvTemplate();

        return response($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="party_import_template.csv"',
        ]);
    }

    /**
     * Download failed rows error report as CSV.
     */
    public function downloadErrors(): StreamedResponse|RedirectResponse
    {
        $failedRows = session('import_failed_rows', []);

        if (empty($failedRows)) {
            return back()->with('toast_info', 'No error report available.');
        }

        return response()->streamDownload(function () use ($failedRows) {
            $handle = fopen('php://output', 'w');

            // Collect headers
            $headers = array_keys($failedRows[0]);
            fputcsv($handle, $headers);

            foreach ($failedRows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, 'party_import_errors.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}
