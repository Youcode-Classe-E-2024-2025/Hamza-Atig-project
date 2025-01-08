<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

include BASE_PATH . 'config/database.php';

$query = "
    SELECT 
        p.project_name,
        t.task_title,
        t.task_description,
        t.due_date,
        t.status,
        u1.username AS created_by,
        u2.username AS assigned_to
    FROM 
        projects p
    LEFT JOIN 
        tasks t ON p.project_id = t.project_id
    LEFT JOIN 
        users u1 ON p.created_by = u1.user_id
    LEFT JOIN 
        users u2 ON t.assigned_to = u2.user_id
";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setCellValue('A1', 'Project Name');
    $sheet->setCellValue('B1', 'Task Title');
    $sheet->setCellValue('C1', 'Task Description');
    $sheet->setCellValue('D1', 'Due Date');
    $sheet->setCellValue('E1', 'Status');
    $sheet->setCellValue('F1', 'Created By');
    $sheet->setCellValue('G1', 'Assigned To');

    $row = 2;
    while ($data = $result->fetch_assoc()) {
        $sheet->setCellValue('A' . $row, $data['project_name']);
        $sheet->setCellValue('B' . $row, $data['task_title']);
        $sheet->setCellValue('C' . $row, $data['task_description']);
        $sheet->setCellValue('D' . $row, $data['due_date']);
        $sheet->setCellValue('E' . $row, $data['status']);
        $sheet->setCellValue('F' . $row, $data['created_by']);
        $sheet->setCellValue('G' . $row, $data['assigned_to']);
        $row++;
    }

    $writer = new Xlsx($spreadsheet);
    $filename = 'projects_export_' . date('Y-m-d') . '.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer->save('php://output');
    exit;
} else {
    echo "No data found.";
}

$conn->close();