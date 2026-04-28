@extends('layouts.app')
@section('content')
    <div class="row justify-content-center mt-5">
        <div class="col-md-5">
            <div class="card p-4 border-warning shadow">
                <h5 class="fw-bold mb-3 text-warning">Edit Akun Pegawai</h5>
                <form action="{{ route('pegawai.update', $pegawai->id) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="mb-2"><label class="small fw-bold">Nama</label><input type="text" name="name"
                            class="form-control" value="{{ $pegawai->name }}" required></div>
                    <div class="mb-2"><label class="small fw-bold">NIP</label><input type="text" name="nip"
                            class="form-control" value="{{ $pegawai->nip }}" required></div>
                    <div class="mb-2"><label class="small fw-bold">Jabatan</label><input type="text" name="jabatan"
                            class="form-control" value="{{ $pegawai->jabatan }}"></div>
                    <div class="mb-2"><label class="small fw-bold">Golongan</label><input type="text" name="golongan"
                            class="form-control" value="{{ $pegawai->golongan }}"></div>
                    <div class="mb-2"><label class="small fw-bold">Email</label><input type="email" name="email"
                            class="form-control" value="{{ $pegawai->email }}" required></div>
                    <div class="mb-3"><label class="small fw-bold">Password Baru (Opsional)</label><input type="password"
                            name="password" class="form-control" placeholder="Kosongkan jika tidak diganti"></div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">Batal</a>
                        <button type="submit" class="btn btn-primary btn-sm px-4">Update Profil</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
