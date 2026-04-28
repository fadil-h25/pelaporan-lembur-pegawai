<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Lembur</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f7f6;
            padding: 40px;
        }

        .form-container {
            background: white;
            max-width: 600px;
            margin: auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #555;
        }

        input[type="text"],
        input[type="date"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        textarea {
            height: 100px;
            resize: vertical;
        }

        .btn-submit {
            background-color: #ffc107;
            color: black;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            width: 100%;
        }

        .btn-submit:hover {
            background-color: #e0a800;
        }

        .btn-back {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #666;
            text-decoration: none;
            font-size: 14px;
        }
    </style>
</head>

<body>

    <div class="form-container">
        <h2>Edit Laporan Lembur</h2>
        <p>Admin Mode: Mengubah data milik <strong>{{ $lembur->nama }}</strong></p>

        <form action="{{ route('lembur.update', $lembur->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Tanggal Lembur:</label>
                <input type="date" name="tanggal_lembur" value="{{ $lembur->tanggal_lembur }}" required>
            </div>

            <div class="form-group">
                <label>Jumlah Jam:</label>
                <input type="number" name="jumlah_jam" value="{{ $lembur->jumlah_jam }}" required>
            </div>

            <div class="form-group">
                <label>Pembebanan Anggaran:</label>
                <input type="text" name="pembebanan_anggaran" value="{{ $lembur->pembebanan_anggaran }}" required>
            </div>

            <div class="form-group">
                <label>Rencana Kerja (Untuk SPK):</label>
                <textarea name="rencana_kerja" required>{{ $lembur->rencana_kerja }}</textarea>
            </div>

            <div class="form-group">
                <label>Hasil Kerja (Untuk LPJ):</label>
                <textarea name="hasil_kerja" required>{{ $lembur->hasil_kerja }}</textarea>
            </div>

            <button type="submit" class="btn-submit">Update Data Lembur</button>
            <a href="{{ route('dashboard') }}" class="btn-back"> Kembali ke Dashboard</a>
        </form>
    </div>

</body>

</html>
