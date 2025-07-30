<?php
// upload.php — Report upload, QR generation, PDF update

// Dependencies: composer require endroid/qr-code, setasign/fpdf, setasign/fpdi
require 'vendor/autoload.php';
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use setasign\Fpdi\Fpdi;

// Folder to store uploaded reports
$uploadDir = __DIR__ . '/reports/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Get form data
$name = $_POST['patient_name'] ?? '';
$mobile = $_POST['mobile'] ?? '';
$type = $_POST['report_type'] ?? '';

// File upload
$file = $_FILES['report_file'];
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$uniqueName = strtolower(preg_replace('/\s+/', '_', $name)) . '_' . time() . '.' . $ext;
$savePath = $uploadDir . $uniqueName;

if (move_uploaded_file($file['tmp_name'], $savePath)) {
    // Create QR code with download link
    $downloadLink = 'https://yourdomain.com/reports/' . $uniqueName;
    $qr = QrCode::create($downloadLink)->setSize(150);
    $writer = new PngWriter();
    $qrResult = $writer->write($qr);
    $qrPath = $uploadDir . 'qr_' . time() . '.png';
    $qrResult->saveToFile($qrPath);

    // Add QR code to PDF
    $pdf = new Fpdi();
    $pdf->AddPage();
    $pageCount = $pdf->setSourceFile($savePath);
    $tpl = $pdf->importPage(1);
    $pdf->useTemplate($tpl);
    $pdf->Image($qrPath, 150, 250, 40, 40);
    $pdf->Output('F', $savePath); // Overwrite PDF with QR

    unlink($qrPath); // Clean QR image

    // Save record to text/JSON (later use DB)
    $records = json_decode(file_get_contents('data.json'), true) ?? [];
    $records[] = [
        'name' => $name,
        'mobile' => $mobile,
        'report' => $uniqueName,
        'type' => $type,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    file_put_contents('data.json', json_encode($records, JSON_PRETTY_PRINT));

    echo "<script>alert('Report uploaded successfully'); window.location.href='admin.html';</script>";
} else {
    echo "<script>alert('Failed to upload report.'); window.history.back();</script>";
}
?>