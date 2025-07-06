<!DOCTYPE html>
<html>
<body>
    <h2>Halo Admin,</h2>
    <p>Ini adalah peringatan untuk dokumen yang akan segera kedaluwarsa dalam 30 hari ke depan:</p>

    @if($expiringSims->isNotEmpty())
        <h3>SIM Driver</h3>
        <table border="1" cellpadding="5" cellspacing="0" style="width:100%; border-collapse: collapse;">
            <thead><tr style="background-color:#f2f2f2;"><th>Nama Driver</th><th>Masa Berlaku SIM</th></tr></thead>
            <tbody>
                @foreach ($expiringSims as $driver)
                    <tr><td>{{ $driver->nama }}</td><td>{{ $driver->sim_tanggal_kadaluarsa->format('d F Y') }}</td></tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($expiringKirs->isNotEmpty())
        <h3>KIR Truk</h3>
        <table border="1" cellpadding="5" cellspacing="0" style="width:100%; border-collapse: collapse;">
            <thead><tr style="background-color:#f2f2f2;"><th>Nomor Polisi</th><th>Masa Berlaku KIR</th></tr></thead>
            <tbody>
                @foreach ($expiringKirs as $truck)
                    <tr><td>{{ $truck->nopol }}</td><td>{{ $truck->kir_tanggal_kadaluarsa->format('d F Y') }}</td></tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($expiringTaxes->isNotEmpty())
        <h3>Pajak Tahunan Truk</h3>
        <table border="1" cellpadding="5" cellspacing="0" style="width:100%; border-collapse: collapse;">
            <thead><tr style="background-color:#f2f2f2;"><th>Nomor Polisi</th><th>Jatuh Tempo Pajak</th></tr></thead>
            <tbody>
                @foreach ($expiringTaxes as $truck)
                    <tr><td>{{ $truck->nopol }}</td><td>{{ $truck->pajak_tanggal_kadaluarsa->format('d F Y') }}</td></tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <p>Mohon segera diurus. Terima kasih.</p>
</body>
</html>