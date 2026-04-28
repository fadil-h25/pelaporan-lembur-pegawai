<h2>Form Pengajuan Lembur</h2>

<form action="/lembur/store" method="POST">
    @csrf
    <label>Nama:</label><br>
    <input type="text" value="{{ $user->name }}" readonly><br><br>

    <label>NIP:</label><br>
    <input type="text" value="{{ $user->nip }}" readonly><br><br>

    <label>Tanggal Lembur:</label><br>
    <input type="date" name="tanggal_lembur" required><br><br>

    <label>Jumlah Jam:</label><br>
    <input type="number" name="jumlah_jam" placeholder="Contoh: 2" required><br><br>

    <label>Pembebanan Anggaran:</label><br>
    <input type="text" name="pembebanan_anggaran" value="DIPA TA 2025" required><br><br>

    <label>Rencana Kerja:</label><br>
    <textarea name="rencana_kerja" placeholder="Contoh: Menindaklanjuti arahan atasan..." required></textarea><br><br>

    <button type="submit">Simpan & Ajukan Lembur</button>
</form>
