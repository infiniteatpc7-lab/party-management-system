<?php

namespace App\Services;

use App\Models\Party;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PartyImportService
{
    /**
     * Process and import parties from an uploaded Excel or CSV file.
     *
     * @return array{
     *     total: int,
     *     imported: int,
     *     failed: int,
     *     duplicates: int,
     *     errors: array<int, array{row: int, name: string, error: string}>,
     *     failed_rows: array<int, array>
     * }
     */
    public function import(UploadedFile $file): array
    {
        $filePath = $file->getRealPath();
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray(null, true, true, true);

        if (empty($rows)) {
            return [
                'total' => 0,
                'imported' => 0,
                'failed' => 0,
                'duplicates' => 0,
                'errors' => [['row' => 1, 'name' => 'N/A', 'error' => 'File is empty']],
                'failed_rows' => [],
            ];
        }

        // Header mapping
        $headerRow = array_shift($rows);
        $headerMap = [];
        foreach ($headerRow as $colKey => $colName) {
            if ($colName !== null) {
                $normalized = strtolower(trim(str_replace([' ', '-'], '_', (string) $colName)));
                $headerMap[$normalized] = $colKey;
            }
        }

        // Validate required header
        if (! isset($headerMap['party_name'])) {
            return [
                'total' => 0,
                'imported' => 0,
                'failed' => 0,
                'duplicates' => 0,
                'errors' => [['row' => 1, 'name' => 'Header', 'error' => 'Missing required column "party_name"']],
                'failed_rows' => [],
            ];
        }

        $total = 0;
        $imported = 0;
        $failed = 0;
        $duplicates = 0;
        $errors = [];
        $failedRows = [];
        $seenInFile = [];

        foreach ($rows as $rowIndex => $row) {
            // Check if row is completely empty
            $rowValues = array_filter($row, fn ($v) => $v !== null && trim((string) $v) !== '');
            if (empty($rowValues)) {
                continue;
            }

            $total++;

            $partyName = isset($headerMap['party_name']) && isset($row[$headerMap['party_name']])
                ? trim((string) $row[$headerMap['party_name']])
                : '';
            $mobileNumber = isset($headerMap['mobile_number']) && isset($row[$headerMap['mobile_number']])
                ? trim((string) $row[$headerMap['mobile_number']])
                : null;
            $mapLink = isset($headerMap['map_link']) && isset($row[$headerMap['map_link']])
                ? trim((string) $row[$headerMap['map_link']])
                : null;
            $latRaw = isset($headerMap['latitude']) && isset($row[$headerMap['latitude']])
                ? trim((string) $row[$headerMap['latitude']])
                : null;
            $lngRaw = isset($headerMap['longitude']) && isset($row[$headerMap['longitude']])
                ? trim((string) $row[$headerMap['longitude']])
                : null;
            $partyTypeRaw = isset($headerMap['party_type']) && isset($row[$headerMap['party_type']])
                ? strtolower(trim((string) $row[$headerMap['party_type']]))
                : 'new';
            $priorityStatusRaw = isset($headerMap['priority_status']) && isset($row[$headerMap['priority_status']])
                ? strtolower(trim((string) $row[$headerMap['priority_status']]))
                : 'non_priority';
            $isLeadRaw = isset($headerMap['is_lead']) && isset($row[$headerMap['is_lead']])
                ? strtolower(trim((string) $row[$headerMap['is_lead']]))
                : (isset($headerMap['lead']) && isset($row[$headerMap['lead']])
                    ? strtolower(trim((string) $row[$headerMap['lead']]))
                    : '0');

            // Validate party name
            if ($partyName === '') {
                $failed++;
                $errors[] = ['row' => $rowIndex, 'name' => 'Unnamed', 'error' => 'Party Name is required'];
                $failedRows[] = array_merge($row, ['_error' => 'Party Name is required']);

                continue;
            }

            // Normalize party type
            $partyType = in_array($partyTypeRaw, ['old'], true) ? 'old' : 'new';

            // Normalize priority status
            $priorityStatus = in_array($priorityStatusRaw, ['priority', 'high', 'yes', '1'], true)
                ? 'priority'
                : 'non_priority';

            // Normalize lead status
            $isLead = in_array($isLeadRaw, ['1', 'true', 'yes', 'y', 'lead'], true);

            // Handle coordinates: direct or extract from map link
            $lat = is_numeric($latRaw) ? (float) $latRaw : null;
            $lng = is_numeric($lngRaw) ? (float) $lngRaw : null;

            if (($lat === null || $lng === null) && ! empty($mapLink)) {
                $extracted = CoordinateExtractor::extract($mapLink);
                if ($extracted) {
                    $lat = $extracted['latitude'];
                    $lng = $extracted['longitude'];
                }
            }

            // If coordinates provided, validate boundaries
            if ($lat !== null || $lng !== null) {
                if ($lat === null || $lng === null || ! CoordinateExtractor::isValid($lat, $lng)) {
                    $failed++;
                    $errors[] = ['row' => $rowIndex, 'name' => $partyName, 'error' => 'Invalid latitude or longitude coordinates'];
                    $failedRows[] = array_merge($row, ['_error' => 'Invalid coordinates']);

                    continue;
                }
            }

            // Duplicate detection: check within current file
            $dedupKey = strtolower($partyName).'|'.($mobileNumber ?: 'nomobile');
            if (isset($seenInFile[$dedupKey])) {
                $duplicates++;
                $errors[] = ['row' => $rowIndex, 'name' => $partyName, 'error' => 'Duplicate record detected within uploaded file'];
                $failedRows[] = array_merge($row, ['_error' => 'Duplicate in file']);

                continue;
            }
            $seenInFile[$dedupKey] = true;

            // Duplicate detection: check against database
            $existsInDb = Party::where('party_name', $partyName)
                ->when($mobileNumber, fn ($q) => $q->where('mobile_number', $mobileNumber))
                ->exists();

            if ($existsInDb) {
                $duplicates++;
                $errors[] = ['row' => $rowIndex, 'name' => $partyName, 'error' => 'Duplicate record exists in database'];
                $failedRows[] = array_merge($row, ['_error' => 'Duplicate in database']);

                continue;
            }

            // Insert valid party
            try {
                Party::create([
                    'party_name' => $partyName,
                    'mobile_number' => $mobileNumber ?: null,
                    'map_link' => $mapLink ?: null,
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'party_type' => $partyType,
                    'priority_status' => $priorityStatus,
                    'is_lead' => $isLead,
                ]);
                $imported++;
            } catch (\Throwable $e) {
                $failed++;
                $errors[] = ['row' => $rowIndex, 'name' => $partyName, 'error' => 'Database error: '.$e->getMessage()];
                $failedRows[] = array_merge($row, ['_error' => $e->getMessage()]);
            }
        }

        return [
            'total' => $total,
            'imported' => $imported,
            'failed' => $failed,
            'duplicates' => $duplicates,
            'errors' => $errors,
            'failed_rows' => $failedRows,
        ];
    }

    /**
     * Generate sample CSV content for download.
     */
    public function generateCsvTemplate(): string
    {
        $headers = ['party_name', 'mobile_number', 'map_link', 'latitude', 'longitude', 'party_type', 'priority_status', 'is_lead'];
        $sampleRows = [
            ['Grand Hotel Ballroom', '+91 9876543210', 'https://maps.google.com/?q=12.9715987,77.5945627', '12.9715987', '77.5945627', 'new', 'priority', '1'],
            ['Metro Convention Center', '+91 9123456780', 'https://maps.google.com/?q=12.9760000,77.6000000', '12.9760000', '77.6000000', 'new', 'non_priority', '0'],
            ['Greenwood Banquet Hall', '+91 9888877777', 'https://maps.google.com/?q=12.9650000,77.5850000', '12.9650000', '77.5850000', 'old', 'priority', '1'],
            ['City Palace Lawn', '+91 9999900000', '', '12.9800000', '77.6100000', 'old', 'non_priority', '0'],
        ];

        $output = fopen('php://temp', 'r+');
        fputcsv($output, $headers);
        foreach ($sampleRows as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $content = stream_get_contents($output);
        fclose($output);

        return $content;
    }
}
