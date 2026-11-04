<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $rombel = [
            ['nama_kelas' => 'X AK', 'tingkat' => 'X', 'jurusan' => 'Akuntansi dan Keuangan Lembaga', 'tahun_ajaran' => '2023/2024'],
            ['nama_kelas' => 'X BDG-1', 'tingkat' => 'X', 'jurusan' => 'Bisnis Digital', 'tahun_ajaran' => '2023/2024'],
            ['nama_kelas' => 'X BDG-2', 'tingkat' => 'X', 'jurusan' => 'Bisnis Digital', 'tahun_ajaran' => '2023/2024'],
            ['nama_kelas' => 'X DKV-1', 'tingkat' => 'X', 'jurusan' => 'Desain Komunikasi Visual', 'tahun_ajaran' => '2023/2024'],
            ['nama_kelas' => 'X DKV-2', 'tingkat' => 'X', 'jurusan' => 'Desain Komunikasi Visual', 'tahun_ajaran' => '2023/2024'],
            ['nama_kelas' => 'X TKJ-1', 'tingkat' => 'X', 'jurusan' => 'Teknik Komputer dan Jaringan', 'tahun_ajaran' => '2023/2024'],
            ['nama_kelas' => 'XI AKL', 'tingkat' => 'XI', 'jurusan' => 'Akuntansi dan Keuangan Lembaga', 'tahun_ajaran' => '2023/2024'],
            ['nama_kelas' => 'XI BDG', 'tingkat' => 'XI', 'jurusan' => 'Bisnis Digital', 'tahun_ajaran' => '2023/2024'],
            ['nama_kelas' => 'XI DKV-1', 'tingkat' => 'XI', 'jurusan' => 'Desain Komunikasi Visual', 'tahun_ajaran' => '2023/2024'],
            ['nama_kelas' => 'XI TKJ-1', 'tingkat' => 'XI', 'jurusan' => 'Teknik Komputer dan Jaringan', 'tahun_ajaran' => '2023/2024'],
            ['nama_kelas' => 'XII AKL', 'tingkat' => 'XII', 'jurusan' => 'Akuntansi dan Keuangan Lembaga', 'tahun_ajaran' => '2023/2024'],
            ['nama_kelas' => 'XII BDP-1', 'tingkat' => 'XII', 'jurusan' => 'Bisnis Daring dan Pemasaran', 'tahun_ajaran' => '2023/2024'],
            ['nama_kelas' => 'XII MM-1', 'tingkat' => 'XII', 'jurusan' => 'Multimedia', 'tahun_ajaran' => '2023/2024'],
            ['nama_kelas' => 'XII TKJ-1', 'tingkat' => 'XII', 'jurusan' => 'Teknik Komputer dan Jaringan', 'tahun_ajaran' => '2023/2024'],
        ];

        foreach ($rombel as $r) {
            Kelas::firstOrCreate(['nama_kelas' => $r['nama_kelas'], 'tahun_ajaran' => $r['tahun_ajaran']], $r);
        }
    }
}
