<!DOCTYPE html>
<html>

<head>
    <title>Cetak LPJ</title>
    <style>
        @page {
            margin: 1.5cm;
            size: A4;
        }

        body {
            font-family: 'Bookman Old Style', serif;
            font-size: 12pt;
            line-height: 1.4;
        }

        .judul {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 20px;
        }

        .list-data {
            width: 100%;
            margin-top: 20px;
        }

        .list-data td {
            vertical-align: top;
            padding: 5px 0;
        }

        .ttd-box {
            margin-top: 50px;
            float: right;
            width: 250px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="judul">LAPORAN HASIL PEKERJAAN LEMBUR</div>

    <div style="text-align: center; font-weight: bold; margin-bottom: 20px;">
        ATAS NAMA : {{ strtoupper($nama) }}
    </div>

    <table class="list-data">
        <tr>
            <td width="5%">1.</td>
            <td width="35%">Nomor Surat Perintah Kerja Lembur</td>
            <td width="5%">:</td>
            <td>{{ $no_surat }}</td>
        </tr>
        <tr>
            <td>2.</td>
            <td>Hari Tanggal Lembur</td>
            <td>:</td>
            <td>{{ $hari_tanggal }}</td>
        </tr>
        <tr>
            <td>3.</td>
            <td>Jumlah Jam Kerja Lembur</td>
            <td>:</td>
            <td>{{ $jam }} ({{ $terbilang }}) jam</td>
        </tr>
        <tr>
            <td>4.</td>
            <td>Pembebanan Anggaran</td>
            <td>:</td>
            <td>{{ $anggaran }}</td>
        </tr>
        <tr>
            <td>5.</td>
            <td>Deskripsi Hasil Pekerjaan Lembur</td>
            <td>:</td>
            <td>{{ $hasil }}</td>
        </tr>
    </table>

    <p style="margin-top: 30px;">Demikian laporan pekerjaan ini dibuat untuk dapat dipertanggungjawabkan.</p>

    <div class="ttd-box">
        Makassar, {{ $tanggal }}<br>
        Pelaksana Kerja Lembur
        <br><br><br><br>
        ( {{ $nama }} )
    </div>
</body>

</html>
