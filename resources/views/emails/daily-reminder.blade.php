<!DOCTYPE html>
<html>
<head>
    <title>Pengingat Jadwal Maintenance</title>
</head>
<body>
    <h2>Halo Admin,</h2>
    <p>Ini adalah pengingat untuk jadwal maintenance yang jatuh tempo hari ini:</p>

    <table border="1" cellpadding="10" cellspacing="0" style="width:100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color:#f2f2f2;">
                <th>Truk</th>
                <th>Jenis Servis</th>
                <th>Driver</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($upcomingMaintenances as $maintenance)
                <tr>
                    <td>{{ $maintenance->truck->nopol }}</td>
                    <td>{{ $maintenance->maintenanceSchedule->nama_servis }}</td>
                    <td>{{ $maintenance->truck->driver->nama ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p>Mohon pastikan semua jadwal di atas ditangani. Terima kasih.</p>
</body>
</html>