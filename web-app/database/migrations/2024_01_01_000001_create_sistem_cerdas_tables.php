<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Kelas & Rombel
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kelas'); // contoh: X AK, XI DKV-1, XII MM-1
            $table->string('tingkat'); // X, XI, XII
            $table->string('jurusan'); // AKL, DKV, BDG, BDP, MM, TKJ, dll
            $table->string('tahun_ajaran'); // 2023/2024, 2024/2025
            $table->timestamps();
        });

        // 2. Tabel Siswa (Sesuai Dapodik SMKN 1 Bungo)
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
            $table->string('nisn', 20)->unique();
            $table->string('nipd', 30)->nullable();
            $table->string('nama_lengkap');
            $table->enum('jenis_kelamin', ['L', 'P'])->default('L');
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->string('alat_transportasi')->nullable();
            $table->boolean('is_penerima_kps')->default(false);
            $table->boolean('is_penerima_kip')->default(false);
            $table->boolean('is_penerima_kks')->default(false);
            $table->boolean('is_layak_pip')->default(false);
            $table->string('pekerjaan_ayah')->nullable();
            $table->string('pekerjaan_ibu')->nullable();
            $table->decimal('penghasilan_ayah', 15, 2)->default(0);
            $table->decimal('penghasilan_ibu', 15, 2)->default(0);
            $table->integer('jml_saudara_kandung')->default(0);
            $table->decimal('jarak_rumah_km', 8, 2)->default(0);
            $table->enum('status_keaktifan', ['Aktif', 'Mutasi', 'Putus Sekolah', 'Lulus'])->default('Aktif');
            $table->timestamps();
        });

        // 3. Tabel Leger Rapor & Presensi
        Schema::create('rapor_semesters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->string('semester'); // Ganjil / Genap
            $table->string('tahun_ajaran'); // 2023/2024
            $table->decimal('rerata_nilai', 6, 2)->default(0);
            $table->integer('jml_mapel_dibawah_kkm')->default(0);
            $table->integer('total_sakit')->default(0);
            $table->integer('total_izin')->default(0);
            $table->integer('total_alfa')->default(0);
            $table->timestamps();
        });

        // 4. Tabel Fitur Dataset KDD (Preprocessed Features)
        Schema::create('fitur_datasets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->string('periode')->nullable();
            $table->decimal('rerata_nilai', 6, 2)->default(0);
            $table->integer('mapel_dibawah_kkm')->default(0);
            $table->integer('jml_alfa')->default(0);
            $table->integer('jml_izin_sakit')->default(0);
            $table->integer('kode_ekonomi')->default(1); // 1 = Rentan/Penerima Bansos, 2 = Menengah, 3 = Mampu
            $table->decimal('jarak_km', 8, 2)->default(0);
            $table->integer('jml_saudara')->default(0);
            $table->enum('label_aktual', ['Tinggi', 'Rendah'])->nullable();
            $table->timestamps();
        });

        // 5. Tabel Evaluasi Model (Random Forest & Naive Bayes)
        Schema::create('model_evaluasis', function (Blueprint $table) {
            $table->id();
            $table->enum('algoritma', ['Random_Forest', 'Naive_Bayes']);
            $table->decimal('akurasi', 6, 2)->default(0);
            $table->decimal('presisi', 6, 2)->default(0);
            $table->decimal('recall', 6, 2)->default(0);
            $table->decimal('f1_score', 6, 2)->default(0);
            $table->json('confusion_matrix')->nullable(); // {tp, fp, tn, fn}
            $table->json('hyperparameter_settings')->nullable();
            $table->longText('model_state')->nullable(); // Serialized / weights
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        // 6. Tabel Log Prediksi Deteksi Dini
        Schema::create('prediksi_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('model_evaluasi_id')->constrained('model_evaluasis')->cascadeOnDelete();
            $table->enum('hasil_prediksi', ['Tinggi', 'Rendah']);
            $table->decimal('probabilitas_risiko', 5, 4)->default(0);
            $table->text('faktor_kunci')->nullable();
            $table->date('tanggal_prediksi');
            $table->timestamps();
        });

        // 7. Tabel Tindakan & Intervensi Siswa (Single Actor: Admin)
        Schema::create('tindakan_intervensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('tgl_tindakan');
            $table->string('jenis_tindakan'); // Konseling Individual, Pemanggilan Orang Tua, Home Visit, Bantuan Afirmasi PIP, Remedial
            $table->text('catatan_penanganan')->nullable();
            $table->enum('status_intervensi', ['Pending', 'Dalam Pemantauan', 'Selesai/Aman'])->default('Pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tindakan_intervensis');
        Schema::dropIfExists('prediksi_logs');
        Schema::dropIfExists('model_evaluasis');
        Schema::dropIfExists('fitur_datasets');
        Schema::dropIfExists('rapor_semesters');
        Schema::dropIfExists('siswas');
        Schema::dropIfExists('kelas');
    }
};
