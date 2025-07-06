<!DOCTYPE html>
<html>
<head>
    <title>Notifikasi Maintenance Baru</title>
</head>
<body>
    <h2>Halo Admin,</h2>
    <p>Sebuah jadwal maintenance baru telah dicatat dalam sistem. Berikut adalah detailnya:</p>
    <ul>
        <li><strong>Truk:</strong> {{ $maintenanceHistory->truck->nopol }}</li>
        <li><strong>Jenis Servis:</strong> {{ $maintenanceHistory->maintenanceSchedule->nama_servis }}</li>
        <li><strong>Tanggal Servis:</strong> {{ $maintenanceHistory->tanggal_servis->format('d F Y') }}</li>
        <li><strong>Status:</strong> {{ $maintenanceHistory->status->value }}</li>
    </ul>
    <p>Silakan periksa aplikasi untuk detail lebih lanjut.</p>
    <p>Terima kasih.</p>
</body>
</html>