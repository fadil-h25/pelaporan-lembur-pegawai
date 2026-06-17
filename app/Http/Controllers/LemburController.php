<?php

namespace App\Http\Controllers;

use App\Models\Lembur;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpWord\TemplateProcessor;

class LemburController extends Controller
{
    public function index()
    {
        Carbon::setLocale('id');
        $dataLembur = Auth::user()->role == 'admin' ? Lembur::orderBy('created_at', 'asc')->get() : Lembur::where('user_id', Auth::id())->orderBy('created_at', 'asc')->get();
        $dataPegawai = Auth::user()->role == 'admin' ? User::where('role', 'pegawai')->get() : [];

        return view('dashboard', compact('dataLembur', 'dataPegawai'));
    }

    // --- FITUR LEMBUR ---
    public function store(Request $request)
    {
        Lembur::create([
            'user_id' => Auth::id(), 'nama' => Auth::user()->name, 'nip' => Auth::user()->nip,
            'golongan' => Auth::user()->golongan, 'jabatan' => Auth::user()->jabatan,
            'tanggal_lembur' => $request->tanggal_lembur, 'jumlah_jam' => $request->jumlah_jam,
            'pembebanan_anggaran' => $request->pembebanan_anggaran, 'rencana_kerja' => $request->rencana_kerja,
            'hasil_kerja' => $request->rencana_kerja, 'status' => 'Selesai',
        ]);

        return redirect()->back()->with('success', 'Data lembur berhasil disimpan!');
    }

    public function edit($id)
    {
        $lembur = Lembur::findOrFail($id);

        return view('lembur.edit', compact('lembur'));
    }

    public function update(Request $request, $id)
    {
        Lembur::findOrFail($id)->update($request->all());

        return redirect('/dashboard')->with('success', 'Data lembur diperbarui!');
    }

    public function destroy($id)
    {
        Lembur::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Data berhasil dihapus!');
    }

    // --- MANAJEMEN PEGAWAI ---
    public function storePegawai(Request $request)
    {
        User::create([
            'name' => $request->name, 'nip' => $request->nip, 'email' => $request->email,
            'jabatan' => $request->jabatan, 'golongan' => $request->golongan, 'role' => 'pegawai',
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'Pegawai didaftarkan!');
    }

    public function editPegawai($id)
    {
        $pegawai = User::findOrFail($id);

        return view('pegawai.edit', compact('pegawai'));
    }

    public function updatePegawai(Request $request, $id)
    {
        $pegawai = User::findOrFail($id);
        $data = $request->only(['name', 'nip', 'email', 'jabatan', 'golongan']);
        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }
        $pegawai->update($data);

        return redirect('/dashboard')->with('success', 'Akun pegawai diperbarui!');
    }

    public function destroyPegawai($id)
    {
        User::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Pegawai dihapus!');
    }

    // --- FITUR CETAK ---
    private function terbilang($n)
    {
        $b = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];
        if ($n < 12) {
            return ' '.$b[$n];
        } elseif ($n < 20) {
            return $this->terbilang($n - 10).' belas';
        } elseif ($n < 100) {
            return $this->terbilang($n / 10).' puluh'.$this->terbilang($n % 10);
        }

        return '';
    }

    public function cetak($type, $id, $nomor)
    {
        $l = Lembur::findOrFail($id);
        $tp = new TemplateProcessor(public_path("templates/template_$type.docx"));
        Carbon::setLocale('id');
        $t = Carbon::parse($l->tanggal_lembur);
        $tp->setValue('no_surat', str_pad($nomor, 4, '0', STR_PAD_LEFT).'/SL/SPKL/SN/'.$t->format('m/Y'));
        $tp->setValue('nama', $l->nama);
        $tp->setValue('nip', $l->nip);
        $tp->setValue('jabatan', $l->jabatan);
        $tp->setValue('golongan', $l->golongan);
        $tp->setValue('hari_tanggal', $t->translatedFormat('l / d F Y')); // Hasil: Jumat / 06 Februari 2026
        $tp->setValue('tanggal', $t->translatedFormat('d F Y'));
        $tp->setValue('jam', $l->jumlah_jam);
        $tp->setValue('terbilang', trim($this->terbilang($l->jumlah_jam)));
        $tp->setValue('pekerjaan', $l->rencana_kerja);
        $tp->setValue('hasil', $l->hasil_kerja);
        $tp->setValue('anggaran', $l->pembebanan_anggaran);
        $tp->setValue('nama_kasek', 'AWALUDDIN MUSTAFA, S.E., M.Si');
        $tp->setValue('nip_kasek', '19740712 200212 1 006');
        $path = storage_path('app/public/'.$type.'_'.time().'.docx');
        $tp->saveAs($path);

        return response()->download($path)->deleteFileAfterSend(true);
    }
}
