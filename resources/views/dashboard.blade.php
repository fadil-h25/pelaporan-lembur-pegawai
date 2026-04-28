@extends('layouts.app')
@section('content')
    <div class="row g-4">
        <div class="col-12">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <div class="card p-3 bg-primary text-white shadow-sm">
                <h5 class="fw-bold mb-3">Input Data Lembur Baru</h5>
                <form action="{{ route('lembur.store') }}" method="POST" class="row g-2">
                    @csrf
                    <div class="col-md-2"><input type="date" name="tanggal_lembur" class="form-control" required></div>
                    <div class="col-md-1"><input type="number" name="jumlah_jam" class="form-control" placeholder="Jam"
                            required></div>
                    <div class="col-md-3"><input type="text" name="pembebanan_anggaran" class="form-control"
                            placeholder="Anggaran" required></div>
                    <div class="col-md-4"><input type="text" name="rencana_kerja" class="form-control"
                            placeholder="Hasil Kerja" required></div>
                    <div class="col-md-2"><button type="submit" class="btn btn-light w-100 fw-bold">Simpan</button></div>
                </form>
            </div>
        </div>

        <div class="col-12">
            <div class="card p-3 shadow-sm">
                <h5 class="fw-bold mb-3">Riwayat Laporan</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>No. Surat</th>
                                <th>Nama</th>
                                <th>Hasil Kerja</th>
                                <th>Jam</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dataLembur as $l)
                                @php
                                    /** * LOGIKA SINKRONISASI NOMOR:
                                     * Mencari urutan ID data ini di seluruh tabel lembur yang diurutkan ASC (paling lama ke baru).
                                     * Ini memastikan nomor 0001, 0002, dst tetap konsisten bagi Admin maupun Pegawai.
                                     */
                                    $nomorDatabase =
                                        \App\Models\Lembur::orderBy('created_at', 'asc')->pluck('id')->search($l->id) +
                                        1;
                                @endphp
                                <tr>
                                    <td class="text-danger fw-bold">{{ str_pad($nomorDatabase, 4, '0', STR_PAD_LEFT) }}</td>
                                    <td>{{ $l->nama }}</td>
                                    <td class="text-success fw-bold">{{ $l->hasil_kerja }}</td>
                                    <td>{{ $l->jumlah_jam }} Jam</td>
                                    <td>
                                        <a href="{{ route('lembur.cetak', ['spk', $l->id, $nomorDatabase]) }}"
                                            class="btn btn-sm btn-success">SPK</a>
                                        <a href="{{ route('lembur.cetak', ['lpj', $l->id, $nomorDatabase]) }}"
                                            class="btn btn-sm btn-info text-white">LPJ</a>
                                        @if (Auth::user()->role == 'admin')
                                            <a href="{{ route('lembur.edit', $l->id) }}"
                                                class="btn btn-sm btn-warning">Edit</a>
                                            <form action="{{ route('lembur.destroy', $l->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Hapus?')">Hapus</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if (Auth::user()->role == 'admin')
            <div class="col-12">
                <div class="card p-3 shadow-sm border-primary">
                    <h5 class="fw-bold text-primary mb-3">Manajemen Pegawai</h5>
                    <form action="{{ route('pegawai.store') }}" method="POST" class="row g-2 mb-4">
                        @csrf
                        <div class="col-md-2"><input type="text" name="name" class="form-control" placeholder="Nama"
                                required></div>
                        <div class="col-md-2"><input type="text" name="nip" class="form-control" placeholder="NIP"
                                required></div>
                        <div class="col-md-2"><input type="text" name="jabatan" class="form-control"
                                placeholder="Jabatan"></div>
                        <div class="col-md-1"><input type="text" name="golongan" class="form-control" placeholder="Gol">
                        </div>
                        <div class="col-md-2"><input type="email" name="email" class="form-control" placeholder="Email"
                                required></div>
                        <div class="col-md-2"><input type="password" name="password" class="form-control" placeholder="Pass"
                                required></div>
                        <div class="col-md-1"><button type="submit" class="btn btn-primary w-100 fw-bold">Add</button>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm border">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama</th>
                                    <th>NIP</th>
                                    <th>Jabatan (Gol)</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dataPegawai as $p)
                                    <tr>
                                        <td>{{ $p->name }}</td>
                                        <td>{{ $p->nip }}</td>
                                        <td>{{ $p->jabatan }} ({{ $p->golongan }})</td>
                                        <td>
                                            <a href="{{ route('pegawai.edit', $p->id) }}"
                                                class="btn btn-sm btn-warning">Edit</a>
                                            <form action="{{ route('pegawai.destroy', $p->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Hapus?')">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
