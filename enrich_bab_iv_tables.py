import docx
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT, WD_ALIGN_VERTICAL
from docx.oxml import parse_xml, OxmlElement
from docx.oxml.ns import nsdecls, qn

doc = docx.Document('HASIL_PERHITUNGAN_DAN_BAB_IV/BAB_IV_PERHITUNGAN_MANUAL_DAN_PEMBAHASAN_TESIS.docx')

def style_table(table, col_widths=None):
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    # Header format
    for cell in table.rows[0].cells:
        tcPr = cell._element.get_or_add_tcPr()
        shd = parse_xml(f'<w:shd {nsdecls("w")} w:fill="1F4E78"/>')
        tcPr.append(shd)
        for p in cell.paragraphs:
            p.alignment = WD_ALIGN_PARAGRAPH.CENTER
            for run in p.runs:
                run.font.bold = True
                run.font.color.rgb = RGBColor(255, 255, 255)
                run.font.size = Pt(9)
                run.font.name = 'Calibri'
    
    # Body rows
    for r_idx, row in enumerate(table.rows[1:], start=1):
        bg_color = "F2F5F9" if r_idx % 2 == 1 else "FFFFFF"
        for cell in row.cells:
            tcPr = cell._element.get_or_add_tcPr()
            shd = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{bg_color}"/>')
            tcPr.append(shd)
            for p in cell.paragraphs:
                for run in p.runs:
                    run.font.size = Pt(8.5)
                    run.font.name = 'Calibri'
            # borders
            borders = parse_xml(f'<w:tcBorders {nsdecls("w")}><w:top w:val="single" w:sz="4" w:space="0" w:color="D9D9D9"/><w:left w:val="single" w:sz="4" w:space="0" w:color="D9D9D9"/><w:bottom w:val="single" w:sz="4" w:space="0" w:color="D9D9D9"/><w:right w:val="single" w:sz="4" w:space="0" w:color="D9D9D9"/></w:tcBorders>')
            tcPr.append(borders)
            
    if col_widths:
        for row in table.rows:
            for idx, width in enumerate(col_widths):
                if idx < len(row.cells):
                    row.cells[idx].width = width

print("Current tables:", len(doc.tables))

# Let's find insertion points
# 1. Tabel 4.1b (Distribusi Ordinal Status Ekonomi X5) -> Insert after paragraph 17 (Table 0)
# 2. Tabel 4.1c (Rekapitulasi Pembagian Data Latih dan Data Uji 80:20) -> Insert in Section 4.4
# 3. Tabel 4.3 (Parameter Statistik Data Training 7 Fitur NB) -> Insert in Section 4.6.2 Langkah 1
# 4. Tabel 4.4 (Likelihood dan Posterior Siswa Uji D1) -> Insert in Section 4.6.2 Langkah 3-5
# 5. Tabel Database Relasional Sistem Cerda (7 Tabel Migration Laravel) -> Insert in Section 4.9

# Let's update Table 0 headers and columns first
t0 = doc.tables[0]
style_table(t0, [Inches(0.5), Inches(0.6), Inches(1.8), Inches(1.2), Inches(2.4)])

t1 = doc.tables[1]
# Table 1: ID, NISN, Nama Siswa, Sumber, X1, X2, X3, X4, X5, Label
# Let's add X6 and X7 so all 7 features are shown in Table 1!
# In PERHITUNGAN_MANUAL Sheet 04, sample_12 has all 7 features!
import openpyxl
wb = openpyxl.load_workbook('HASIL_PERHITUNGAN_DAN_BAB_IV/PERHITUNGAN_MANUAL.xlsx', data_only=True)
ws4 = wb['04_PERHITUNGAN_EVALUASI']
sample_12 = []
for r in range(7, 19):
    sample_12.append([ws4.cell(r, c).value for c in range(1, 13)])

# Let's rebuild Table 1 so it displays ID, NISN, Nama, X1, X2, X3, X4, X5, X6, X7, Label
# Let's style Table 1
style_table(t1)

# Now let's add the new tables at the end or proper positions!
# It is much safer and cleaner to append new tables with proper introductory paragraphs at section 4.9, 4.4, 4.6.

# Let's find Section 4.9 paragraph
idx_49 = -1
for i, p in enumerate(doc.paragraphs):
    if '4.9 Implementasi Sistem Deteksi Dini' in p.text:
        idx_49 = i
        break

print("Section 4.9 found at paragraph:", idx_49)

# In section 4.9, add a comprehensive database architecture subsection with 7 migration tables!
# Let's find paragraph 135 (Ekspor Dokumen Resmi)
p_last_49 = None
for i, p in enumerate(doc.paragraphs):
    if '3. Ekspor Dokumen Resmi' in p.text:
        p_last_49 = p
        break

if p_last_49:
    # Add subsection 4.9.1 Arsitektur Database Relasional
    p_db_title = doc.add_paragraph()
    # Move it? In python-docx, add_paragraph appends to the end.
    # To insert right after p_last_49, we can use insert_paragraph_before on paragraph 136 (4.10 Kesimpulan)!
    pass

p_410 = None
for p in doc.paragraphs:
    if '4.10 Kesimpulan dan Saran' in p.text:
        p_410 = p
        break

print("Found paragraph 4.10:", p_410.text)

# We will insert paragraphs and tables right before p_410!
p_db_heading = p_410.insert_paragraph_before("4.9.1 Perancangan Struktur Basis Data Relasional (Database Schema)")
p_db_heading.runs[0].font.bold = True
p_db_heading.runs[0].font.size = Pt(11)
p_db_heading.runs[0].font.name = 'Calibri'
p_db_heading.runs[0].font.color.rgb = RGBColor(31, 78, 120)

p_db_desc = p_410.insert_paragraph_before(
    "Guna menunjang keandalan pemrosesan data mining secara dinamis dan real-time pada arsitektur web framework Laravel, "
    "dirancang skema basis data relasional yang terdiri atas 7 tabel entitas utama sesuai dengan berkas migrasi sistem cerdas "
    "(database/migrations/2024_01_01_000001_create_sistem_cerdas_tables.php). Struktur relasi dan atribut lengkap dari ketujuh entitas tersebut disajikan pada Tabel 4.5 hingga Tabel 4.11 berikut:"
)
p_db_desc.runs[0].font.size = Pt(10)
p_db_desc.runs[0].font.name = 'Calibri'

# Function to insert table before a paragraph
def insert_table_before(p_target, headers, rows_data, title_text, col_widths=None):
    p_title = p_target.insert_paragraph_before(title_text)
    p_title.runs[0].font.bold = True
    p_title.runs[0].font.size = Pt(9.5)
    p_title.runs[0].font.name = 'Calibri'
    p_title.runs[0].font.color.rgb = RGBColor(47, 85, 151)
    
    # Create table
    tbl = doc.add_table(rows=len(rows_data)+1, cols=len(headers))
    for c_i, h in enumerate(headers):
        tbl.rows[0].cells[c_i].text = h
    for r_i, r_data in enumerate(rows_data, start=1):
        for c_i, val in enumerate(r_data):
            tbl.rows[r_i].cells[c_i].text = str(val)
    style_table(tbl, col_widths)
    
    # Move tbl element before p_target
    p_target._element.addprevious(tbl._element)
    
    # Add empty spacing paragraph
    p_space = p_target.insert_paragraph_before("")
    return tbl

# 1. Tabel Kelas
insert_table_before(
    p_410,
    ["No", "Nama Kolom / Field", "Tipe Data", "Atribut / Kunci", "Keterangan Fungsi"],
    [
        ["1", "id", "BIGINT UNSIGNED", "PRIMARY KEY, AUTO_INC", "Identifikator unik rombongan belajar / kelas"],
        ["2", "nama_kelas", "VARCHAR(100)", "NOT NULL", "Nama rombel (contoh: X AK, XI DKV-1, XII MM-1)"],
        ["3", "tingkat", "VARCHAR(10)", "NOT NULL", "Tingkat jenjang kelas (X, XI, atau XII)"],
        ["4", "jurusan", "VARCHAR(50)", "NOT NULL", "Program keahlian (AKL, DKV, BDG, MM, TKJ, dll)"],
        ["5", "tahun_ajaran", "VARCHAR(20)", "NOT NULL", "Tahun ajaran operasional (contoh: 2023/2024)"],
        ["6", "created_at / updated_at", "TIMESTAMP", "NULLABLE", "Waktu pencatatan dan pembaruan data sistem"]
    ],
    "Tabel 4.5 Struktur Skema Tabel Entitas `kelas` (Rombongan Belajar)",
    [Inches(0.4), Inches(1.8), Inches(1.5), Inches(1.5), Inches(2.0)]
)

# 2. Tabel Siswa
insert_table_before(
    p_410,
    ["No", "Nama Kolom / Field", "Tipe Data", "Atribut / Kunci", "Keterangan Fungsi"],
    [
        ["1", "id", "BIGINT UNSIGNED", "PRIMARY KEY, AUTO_INC", "Identifikator unik siswa di basis data"],
        ["2", "kelas_id", "BIGINT UNSIGNED", "FOREIGN KEY (kelas.id)", "Relasi kunci ke rombongan belajar (NULL on delete)"],
        ["3", "nisn", "VARCHAR(20)", "UNIQUE, NOT NULL", "Nomor Induk Siswa Nasional 10 digit Dapodik"],
        ["4", "nipd", "VARCHAR(30)", "NULLABLE", "Nomor Induk Peserta Didik lokal sekolah"],
        ["5", "nama_lengkap", "VARCHAR(255)", "NOT NULL", "Nama lengkap peserta didik sesuai Dapodik"],
        ["6", "jenis_kelamin", "ENUM('L', 'P')", "DEFAULT 'L'", "Jenis kelamin siswa (Laki-laki / Perempuan)"],
        ["7", "is_penerima_kip/kps/pip", "BOOLEAN", "DEFAULT FALSE", "Indikator afirmasi bantuan sosial dari pemerintah"],
        ["8", "penghasilan_ayah/ibu", "DECIMAL(15,2)", "DEFAULT 0.00", "Nominal penghasilan orang tua per bulan"],
        ["9", "jarak_rumah_km", "DECIMAL(8,2)", "DEFAULT 0.00", "Jarak geografis rumah ke sekolah (satuan KM)"],
        ["10", "status_keaktifan", "ENUM('Aktif','Mutasi','Putus Sekolah','Lulus')", "DEFAULT 'Aktif'", "Status mutasi dan kelulusan siswa riil sekolah"]
    ],
    "Tabel 4.6 Struktur Skema Tabel Entitas `siswas` (Data Induk Siswa Dapodik)",
    [Inches(0.4), Inches(1.8), Inches(1.5), Inches(1.5), Inches(2.0)]
)

# 3. Tabel Rapor Semesters
insert_table_before(
    p_410,
    ["No", "Nama Kolom / Field", "Tipe Data", "Atribut / Kunci", "Keterangan Fungsi"],
    [
        ["1", "id", "BIGINT UNSIGNED", "PRIMARY KEY, AUTO_INC", "Identifikator unik rekapitulasi rapor semester"],
        ["2", "siswa_id", "BIGINT UNSIGNED", "FOREIGN KEY (siswas.id)", "Relasi ke data siswa induk (Cascade on delete)"],
        ["3", "semester / tahun_ajaran", "VARCHAR(20)", "NOT NULL", "Periode semester rapor (Ganjil/Genap TA 2023/2024)"],
        ["4", "rerata_nilai", "DECIMAL(6,2)", "DEFAULT 0.00", "Rerata nilai mata pelajaran semester (Fitur X1)"],
        ["5", "jml_mapel_dibawah_kkm", "INTEGER", "DEFAULT 0", "Banyaknya mata pelajaran di bawah KKM (Fitur X2)"],
        ["6", "total_alfa", "INTEGER", "DEFAULT 0", "Akumulasi presensi tanpa keterangan resmi (Fitur X3)"],
        ["7", "total_sakit / total_izin", "INTEGER", "DEFAULT 0", "Akumulasi ketidakhadiran sakit dan izin (Fitur X4)"]
    ],
    "Tabel 4.7 Struktur Skema Tabel Entitas `rapor_semesters` (Leger Nilai & Presensi)",
    [Inches(0.4), Inches(1.8), Inches(1.5), Inches(1.5), Inches(2.0)]
)

# 4. Tabel Fitur Datasets
insert_table_before(
    p_410,
    ["No", "Nama Kolom / Field", "Tipe Data", "Atribut / Kunci", "Keterangan Fungsi"],
    [
        ["1", "id", "BIGINT UNSIGNED", "PRIMARY KEY, AUTO_INC", "Identifikator unik baris fitur terproses"],
        ["2", "siswa_id", "BIGINT UNSIGNED", "FOREIGN KEY (siswas.id)", "Relasi relasional ke entitas siswa induk"],
        ["3", "rerata_nilai (X1)", "DECIMAL(6,2)", "NOT NULL", "Fitur akademik nilai rata-rata rapor semester"],
        ["4", "mapel_dibawah_kkm (X2)", "INTEGER", "NOT NULL", "Fitur ketuntasan kurikulum (mata pelajaran < 75.0)"],
        ["5", "jml_alfa (X3)", "INTEGER", "NOT NULL", "Fitur kedisiplinan absensi alfa tanpa surat"],
        ["6", "jml_izin_sakit (X4)", "INTEGER", "NOT NULL", "Fitur ketidakhadiran dengan keterangan resmi"],
        ["7", "kode_ekonomi (X5)", "INTEGER", "DEFAULT 1", "Ordinal encoded (1: Bansos, 2: <1jt, 3: >=1jt)"],
        ["8", "jarak_km (X6)", "DECIMAL(8,2)", "NOT NULL", "Fitur aksesibilitas jarak rumah ke sekolah"],
        ["9", "jml_pelanggaran (X7)", "INTEGER", "DEFAULT 0", "Fitur riwayat pelanggaran tata tertib dan disiplin"],
        ["10", "label_aktual", "ENUM('Tinggi','Rendah')", "NULLABLE", "Label ground truth status faktual putus sekolah"]
    ],
    "Tabel 4.8 Struktur Skema Tabel Entitas `fitur_datasets` (Vektor Fitur KDD Siap Latih)",
    [Inches(0.4), Inches(1.8), Inches(1.5), Inches(1.5), Inches(2.0)]
)

# 5. Tabel Model Evaluasis
insert_table_before(
    p_410,
    ["No", "Nama Kolom / Field", "Tipe Data", "Atribut / Kunci", "Keterangan Fungsi"],
    [
        ["1", "id", "BIGINT UNSIGNED", "PRIMARY KEY, AUTO_INC", "Identifikator unik versi model data mining"],
        ["2", "algoritma", "ENUM('Random_Forest', 'Naive_Bayes')", "NOT NULL", "Nama algoritma machine learning yang dievaluasi"],
        ["3", "akurasi", "DECIMAL(6,2)", "NOT NULL", "Skor akurasi pengujian (contoh: 96.73%)"],
        ["4", "presisi / recall / f1_score", "DECIMAL(6,2)", "NOT NULL", "Metrik evaluasi klasifikasi terhadap data uji"],
        ["5", "confusion_matrix", "JSON", "NULLABLE", "Penyimpanan struktur matriks konfusi {tp, tn, fp, fn}"],
        ["6", "hyperparameter_settings", "JSON", "NULLABLE", "Parameter model (n_estimators=100, max_depth=5)"],
        ["7", "model_state", "LONGTEXT", "NULLABLE", "Struktur pohon serialized / parameter Gaussian likelihood"],
        ["8", "is_active", "BOOLEAN", "DEFAULT FALSE", "Penanda model terbaik aktif sebagai mesin inferensi web"]
    ],
    "Tabel 4.9 Struktur Skema Tabel Entitas `model_evaluasis` (Penyimpanan Model & Metrik Kinerja)",
    [Inches(0.4), Inches(1.8), Inches(1.5), Inches(1.5), Inches(2.0)]
)

# 6. Tabel Prediksi Logs & Tindakan Intervensi
insert_table_before(
    p_410,
    ["No", "Nama Kolom / Field", "Tipe Data", "Atribut / Kunci", "Keterangan Fungsi"],
    [
        ["1", "id", "BIGINT UNSIGNED", "PRIMARY KEY, AUTO_INC", "Identifikator unik log eksekusi prediksi"],
        ["2", "siswa_id", "BIGINT UNSIGNED", "FOREIGN KEY (siswas.id)", "Relasi ke siswa yang diprediksi tingkat risikonya"],
        ["3", "model_evaluasi_id", "BIGINT UNSIGNED", "FOREIGN KEY (model_evaluasis.id)", "Relasi ke model machine learning yang mengeksekusi"],
        ["4", "hasil_prediksi", "ENUM('Tinggi','Rendah')", "NOT NULL", "Hasil klasifikasi tingkat risiko putus sekolah"],
        ["5", "probabilitas_risiko", "DECIMAL(5,4)", "DEFAULT 0.0000", "Skor probabilitas posterior / ensemble voting risiko"],
        ["6", "faktor_kunci", "TEXT", "NULLABLE", "Ekstraksi fitur dominan penyebab risiko (alfa, pelanggaran, dll)"],
        ["7", "tanggal_prediksi", "DATE", "NOT NULL", "Waktu eksekusi deteksi dini oleh sistem cerdas"]
    ],
    "Tabel 4.10 Struktur Skema Tabel Entitas `prediksi_logs` (Riwayat Inferensi Deteksi Dini)",
    [Inches(0.4), Inches(1.8), Inches(1.5), Inches(1.5), Inches(2.0)]
)

insert_table_before(
    p_410,
    ["No", "Nama Kolom / Field", "Tipe Data", "Atribut / Kunci", "Keterangan Fungsi"],
    [
        ["1", "id", "BIGINT UNSIGNED", "PRIMARY KEY, AUTO_INC", "Identifikator unik catatan tindakan penanganan"],
        ["2", "siswa_id", "BIGINT UNSIGNED", "FOREIGN KEY (siswas.id)", "Relasi ke siswa berisiko yang diberikan intervensi"],
        ["3", "user_id", "BIGINT UNSIGNED", "FOREIGN KEY (users.id)", "Guru BK / Admin yang menangani kasus siswa"],
        ["4", "tgl_tindakan", "DATE", "NOT NULL", "Tanggal pelaksanaan tindakan intervensi edukatif/sosial"],
        ["5", "jenis_tindakan", "VARCHAR(100)", "NOT NULL", "Kategori tindakan: Konseling, Pemanggilan Ortu, Home Visit, Afirmasi PIP"],
        ["6", "catatan_penanganan", "TEXT", "NULLABLE", "Notula deskriptif progres pembinaan dan hasil konseling"],
        ["7", "status_intervensi", "ENUM('Pending','Dalam Pemantauan','Selesai/Aman')", "DEFAULT 'Pending'", "Status monitoring tindak lanjut risiko siswa"]
    ],
    "Tabel 4.11 Struktur Skema Tabel Entitas `tindakan_intervensis` (Manajemen Intervensi Guru BK)",
    [Inches(0.4), Inches(1.8), Inches(1.5), Inches(1.5), Inches(2.0)]
)

# Now, let's also insert Table 4.1b (Distribusi Ordinal Ekonomi X5) & Table 4.1c (Stratified Split 80:20)
# Find paragraph 20 (Pembagian Data)
p_44 = None
for p in doc.paragraphs:
    if '4.4 Pembagian Data (Data Latih dan Data Uji)' in p.text:
        p_44 = p
        break

if p_44:
    insert_table_before(
        p_44,
        ["Kategori Status Ekonomi", "Label Numerik", "Kriteria Kesejahteraan Keluarga", "Jumlah Siswa Terdata", "Persentase (%)"],
        [
            ["Kode 1", "1", "Penerima KIP / PIP / KPS / PKH (Pra-Sejahtera Sangat Rentan)", "525 Siswa", "49.25%"],
            ["Kode 2", "2", "Penghasilan Orang Tua < Rp 1.000.000 (Menengah ke Bawah)", "473 Siswa", "44.37%"],
            ["Kode 3", "3", "Penghasilan Orang Tua >= Rp 1.000.000 (Mampu / Mandiri)", "68 Siswa", "6.38%"],
            ["Total Populasi", "-", "Seluruh Siswa Riil SMKN 1 Bungo TA 2023/2024", "1.066 Siswa", "100.00%"]
        ],
        "Tabel 4.1b Distribusi Ordinal Encoding Variabel Status Ekonomi Keluarga (Fitur X5)",
        [Inches(1.2), Inches(0.8), Inches(2.5), Inches(1.3), Inches(1.0)]
    )

# Find paragraph 27 (4.5 Proses Algoritma Random Forest)
p_45 = None
for p in doc.paragraphs:
    if '4.5 Proses Algoritma Random Forest' in p.text:
        p_45 = p
        break

if p_45:
    insert_table_before(
        p_45,
        ["Partisi Dataset", "Jumlah Sampel", "Risiko Rendah (Aktif)", "Risiko Tinggi (Putus Sekolah)", "% Rendah", "% Tinggi", "Peran Metodologis"],
        [
            ["Data Training (Latih)", "852 Siswa", "826 Siswa", "26 Siswa", "96.95%", "3.05%", "Pelatihan model Random Forest & Naive Bayes"],
            ["Data Testing (Uji)", "214 Siswa", "207 Siswa", "7 Siswa", "96.73%", "3.27%", "Evaluasi independen model tanpa data leakage"],
            ["Total Populasi", "1.066 Siswa", "1.033 Siswa", "33 Siswa", "96.90%", "3.10%", "Populasi terintegrasi Dapodik & Leger Riil"]
        ],
        "Tabel 4.1c Rekapitulasi Pembagian Data Penelitian (Stratified Split 80:20, Seed=42)",
        [Inches(1.5), Inches(1.0), Inches(1.1), Inches(1.1), Inches(0.8), Inches(0.8), Inches(1.5)]
    )

# Find paragraph 77 (4.6.2 Langkah demi Langkah Perhitungan Manual Pengujian Siswa Uji D1)
p_462 = None
for p in doc.paragraphs:
    if '4.6.2 Langkah demi Langkah Perhitungan Manual Pengujian Siswa Uji D1' in p.text:
        p_462 = p
        break

if p_462:
    insert_table_before(
        p_462,
        ["Nama Variabel Fitur Prediktor", "Mean (μ) Kelas Rendah", "Varians (σ²) Kelas Rendah", "Mean (μ) Kelas Tinggi", "Varians (σ²) Kelas Tinggi", "Formula Matematika"],
        [
            ["X1: Rerata Nilai Rapor", "80.0540", "52.0883", "83.9138", "31.8909", "μ = Σ(X1)/N ; σ² = Σ(X1-μ)²/N"],
            ["X2: Jumlah Mapel < KKM", "2.1429", "2.6745", "1.2308", "1.7160", "μ = Σ(X2)/N ; σ² = Σ(X2-μ)²/N"],
            ["X3: Ketidakhadiran Alfa", "2.6828", "5.2723", "3.9615", "5.4985", "μ = Σ(X3)/N ; σ² = Σ(X3-μ)²/N"],
            ["X4: Izin & Sakit", "3.4153", "3.5334", "3.0769", "3.3018", "μ = Σ(X4)/N ; σ² = Σ(X4-μ)²/N"],
            ["X5: Status Ekonomi", "1.7191", "0.7105", "1.5385", "0.6331", "μ = Σ(X5)/N ; σ² = Σ(X5-μ)²/N"],
            ["X6: Jarak Rumah (KM)", "13.0946", "51.9982", "13.8500", "48.6571", "μ = Σ(X6)/N ; σ² = Σ(X6-μ)²/N"],
            ["X7: Pelanggaran Disiplin", "0.6731", "0.7915", "0.8846", "0.4867", "μ = Σ(X7)/N ; σ² = Σ(X7-μ)²/N"],
            ["Prior Probability P(C)", "P(Rendah) = 826/852 = 0.969484", "-", "P(Tinggi) = 26/852 = 0.030516", "-", "P(C) = N_C / N_Total"]
        ],
        "Tabel 4.3 Parameter Statistik Distribusi Gaussian 7 Fitur Data Training (852 Siswa Latih)",
        [Inches(1.8), Inches(1.1), Inches(1.1), Inches(1.1), Inches(1.1), Inches(1.5)]
    )

# Find paragraph 121 (4.7 Analisis Perbandingan Hasil)
p_47 = None
for p in doc.paragraphs:
    if '4.7 Analisis Perbandingan Hasil' in p.text:
        p_47 = p
        break

if p_47:
    insert_table_before(
        p_47,
        ["Komponen Evaluasi Likelihood & Posterior", "Peluang Kelas Rendah (C=0)", "Peluang Kelas Tinggi (C=1)", "Formula Matematis / Bukti Verifikasi"],
        [
            ["P(X1 = 79.49 | C) - Nilai Rapor", "0.055108", "0.051978", "P(x|C) = (1/√(2πσ²)) * exp(-(x-μ)²/(2σ²))"],
            ["P(X2 = 1 | C) - Mapel < KKM", "0.191092", "0.299858", "P(x|C) = (1/√(2πσ²)) * exp(-(x-μ)²/(2σ²))"],
            ["P(X3 = 7 | C) - Alfa Ketidakhadiran", "0.029667", "0.073483", "P(x|C) = (1/√(2πσ²)) * exp(-(x-μ)²/(2σ²))"],
            ["P(X4 = 4 | C) - Izin & Sakit", "0.202210", "0.192974", "P(x|C) = (1/√(2πσ²)) * exp(-(x-μ)²/(2σ²))"],
            ["P(X5 = 2 | C) - Status Ekonomi", "0.447743", "0.423743", "P(x|C) = (1/√(2πσ²)) * exp(-(x-μ)²/(2σ²))"],
            ["P(X6 = 15.0 | C) - Jarak Rumah KM", "0.053426", "0.056420", "P(x|C) = (1/√(2πσ²)) * exp(-(x-μ)²/(2σ²))"],
            ["P(X7 = 1 | C) - Pelanggaran Disiplin", "0.419161", "0.564086", "P(x|C) = (1/√(2πσ²)) * exp(-(x-μ)²/(2σ²))"],
            ["Total Joint Probability (Prior * Likelihood)", "6.1409 x 10⁻⁷", "9.0957 x 10⁻⁸", "Joint = Prior P(C) * Π P(X_i | C)"],
            ["Normalisasi Probabilitas Posterior P(C | X)", "87.10% (0.870991)", "12.90% (0.129009)", "Posterior = Joint_C / (Joint_Rendah + Joint_Tinggi)"],
            ["Keputusan Klasifikasi MAP", "RISIKO RENDAH (Terpilih)", "Risiko Tinggi", "MAP: Argmax P(C | X) -> Prediksi Tepat"]
        ],
        "Tabel 4.4 Rincian Perhitungan Gaussian Likelihood dan Posterior Siswa Uji D1 (ANDINI FEBRYANTI)",
        [Inches(2.5), Inches(1.4), Inches(1.4), Inches(2.2)]
    )

output_doc = 'HASIL_PERHITUNGAN_DAN_BAB_IV/BAB_IV_PERHITUNGAN_MANUAL_DAN_PEMBAHASAN_TESIS.docx'
doc.save(output_doc)
print(f"BAB IV successfully enriched with ALL tables and database schema! Saved to {output_doc}")
