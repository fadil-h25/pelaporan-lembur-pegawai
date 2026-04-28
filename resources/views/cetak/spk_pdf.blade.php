<!DOCTYPE html>
<html>

<head>
    <title>Cetak SPK</title>
    <style>
        @page {
            margin: 1cm;
            size: A4;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.3;
        }

        .header-logo {
            text-align: center;
            margin-bottom: 10px;
        }

        .header-logo img {
            width: 100%;
            max-width: 500px;
        }

        .judul {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            margin-top: 10px;
        }

        .nomor {
            text-align: center;
            margin-bottom: 20px;
        }

        .tabel-pegawai {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .tabel-pegawai th,
        .tabel-pegawai td {
            border: 1px solid black;
            padding: 5px;
            text-align: left;
        }

        .ttd-container {
            margin-top: 30px;
            float: right;
            width: 300px;
            text-align: center;
        }

        .footer-space {
            margin-top: 80px;
        }
    </style>
</head>

<body>
    <div class="header-logo">
        <p style="color: red; font-weight: bold; font-size: 20px;">BAWASLU</p>
        <small>BADAN PENGAWAS PEMILIHAN UMUM PROVINSI SULAWESI SELATAN</small>
        <hr>
    </div>

    <div class="judul">SURAT PERINTAH KERJA LEMBUR (SPKL)</div>
    <div class="nomor">Nomor : {{ $no_surat }}</div>

    <p>Yang bertanda tangan di bawah ini, Kepala Sekretariat Bawaslu Provinsi Sulawesi Selatan memerintahkan kerja
        lembur para pegawai yang namanya tersebut di bawah ini terhitung mulai tanggal {{ $tanggal }} selama
        {{ $jam }} ({{ $terbilang }}) jam untuk melaksanakan pekerjaan yang penyelesaiannya tidak dapat
        ditangguhkan.</p>

    <table class="tabel-pegawai">
        <thead>
            <tr>
                <th>NO.</th>
                <th>NAMA</th>
                <th>JABATAN</th>
                <th>GOL</th>
                <th>JENIS PEKERJAAN</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>{{ $nama }}<br>NIP. {{ $nip }}</td>
                <td>{{ $jabatan }}</td>
                <td>{{ $golongan }}</td>
                <td>{{ $pekerjaan }}</td>
            </tr>
        </tbody>
    </table>

    <p>Demikian agar dilaksanakan dengan penuh tanggung jawab.</p>

    <div class="ttd-container">
        Makassar, {{ $tanggal }}<br>
        <strong>KEPALA SEKRETARIAT,</strong>
        <div class="footer-space"></div>
        <strong><u>AWALUDDIN MUSTAFA, S.E., M.Si</u></strong><br>
        NIP. 19740712 200212 1 006
    </div>
</body>

</html>
