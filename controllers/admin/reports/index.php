<?php
// controllers/admin/reports/index.php

adminAuth();

use Models\Report;

require_once base_path('models/Report.php');

try {
    $reportModel = new Report();

    // Get all reports
    $recent_reports = $reportModel->getAllReports();

    // Get statistics
    $stats = $reportModel->getReportsStats();
    $operationStats = $reportModel->getOperationTypeStats();

    // Prepare stats array for the view
    $statsArray = [
        'operations' => [
            'all' => $stats['total_reports'] ?? 0,
            'collection' => 0,
            'sweeping' => 0,
            'flushing' => 0,
            'deClogging' => 0,
            'cleanup' => 0
        ]
    ];

    // Map operation types to stats
    foreach ($operationStats as $opStat) {
        $typeName = strtolower(str_replace(' ', '', $opStat['type_name']));
        switch ($typeName) {
            case 'garbagecollection':
            case 'garbagecollecion': // Handle typo in SQL
                $statsArray['operations']['collection'] = $opStat['report_count'];
                break;
            case 'streetsweeping':
                $statsArray['operations']['sweeping'] = $opStat['report_count'];
                break;
            case 'flushing':
                $statsArray['operations']['flushing'] = $opStat['report_count'];
                break;
            case 'de-clogging':
                $statsArray['operations']['deClogging'] = $opStat['report_count'];
                break;
            case 'cleanupdrives':
                $statsArray['operations']['cleanup'] = $opStat['report_count'];
                break;
        }
    }

    view('admin/reports/reports.index.view.php', [
        'recent_reports' => $recent_reports,
        'stats' => $statsArray
    ]);

} catch (\Exception $e) {
    // Handle errors gracefully
    view('admin/reports/reports.index.view.php', [
        'recent_reports' => [],
        'stats' => [
            'operations' => [
                'all' => 0,
                'collection' => 0,
                'sweeping' => 0,
                'flushing' => 0,
                'deClogging' => 0,
                'cleanup' => 0
            ]
        ]
    ]);
}