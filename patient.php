<?php
// patient.php — Show patient reports

$name = $_POST['patient_name'] ?? '';
$mobile = $_POST['mobile'] ?? '';

$records = json_decode(file_get_contents('data.json'), true) ?? [];
$matched = [];

foreach ($records as $record) {
    if (strtolower(trim($record['name'])) == strtolower(trim($name)) && trim($record['mobile']) == trim($mobile)) {
        $matched[] = $record;
    }
}

if (count($matched) === 0) {
    echo "<script>alert('No reports found.'); window.location.href='index.html';</script>";
    exit;
}
?><!DOCTYPE html><html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Reports</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        h2 { color: #2c3e50; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #ccc; text-align: left; }
        th { background-color: #f2f2f2; }
        a.download { color: green; font-weight: bold; text-decoration: none; }
        .logout { margin-top: 20px; display: inline-block; color: red; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($name); ?>!</h2>
    <p>Here are your uploaded reports:</p>
    <table>
        <tr>
            <th>Report Type</th>
            <th>Download</th>
            <th>Date</th>
        </tr>
        <?php foreach ($matched as $row): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['type']); ?></td>
            <td><a class="download" href="reports/<?php echo $row['report']; ?>" target="_blank">Download</a></td>
            <td><?php echo $row['timestamp']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <a class="logout" href="index.html">Logout</a>
</body>
</html>