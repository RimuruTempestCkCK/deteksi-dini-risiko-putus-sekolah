import docx
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT
from docx.oxml import parse_xml
from docx.oxml.ns import nsdecls

doc = docx.Document('HASIL_PERHITUNGAN_DAN_BAB_IV/BAB_IV_PERHITUNGAN_MANUAL_DAN_PEMBAHASAN_TESIS.docx')

def style_table(table, col_widths=None):
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
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
    for r_idx, row in enumerate(table.rows[1:], start=1):
        bg_color = 'F2F5F9' if r_idx % 2 == 1 else 'FFFFFF'
        for cell in row.cells:
            tcPr = cell._element.get_or_add_tcPr()
            shd = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{bg_color}"/>')
            tcPr.append(shd)
            for p in cell.paragraphs:
                for run in p.runs:
                    run.font.size = Pt(8.5)
                    run.font.name = 'Calibri'
            borders = parse_xml(f'<w:tcBorders {nsdecls("w")}><w:top w:val="single" w:sz="4" w:space="0" w:color="D9D9D9"/><w:left w:val="single" w:sz="4" w:space="0" w:color="D9D9D9"/><w:bottom w:val="single" w:sz="4" w:space="0" w:color="D9D9D9"/><w:right w:val="single" w:sz="4" w:space="0" w:color="D9D9D9"/></w:tcBorders>')
            tcPr.append(borders)
    if col_widths:
        for row in table.rows:
            for idx, width in enumerate(col_widths):
                if idx < len(row.cells):
                    row.cells[idx].width = width

def insert_table_before(p_target, headers, rows_data, title_text, col_widths=None):
    p_title = p_target.insert_paragraph_before(title_text)
    p_title.runs[0].font.bold = True
    p_title.runs[0].font.size = Pt(9.5)
    p_title.runs[0].font.name = 'Calibri'
    p_title.runs[0].font.color.rgb = RGBColor(47, 85, 151)
    
    tbl = doc.add_table(rows=len(rows_data)+1, cols=len(headers))
    for c_i, h in enumerate(headers):
        tbl.rows[0].cells[c_i].text = h
    for r_i, r_data in enumerate(rows_data, start=1):
        for c_i, val in enumerate(r_data):
            tbl.rows[r_i].cells[c_i].text = str(val)
    style_table(tbl, col_widths)
    p_target._element.addprevious(tbl._element)
    p_target.insert_paragraph_before('')
    return tbl

# 1. Insert Preprocessing Audit Table before Section 4.3 P16 (Transformasi Fitur)
p_trans = None
for p in doc.paragraphs:
    if '3. Data Transformation (Transformasi Fitur)' in p.text:
        p_trans = p
        break

if p_trans:
    insert_table_before(
        p_trans,
        ['Tahapan Preprocessing Data', 'Data Awal', 'Data Bermasalah', 'Tindakan Metodologis', 'Jumlah Data Akhir'],
        [
            ['Tahap 1: Pembersihan String & Spasi', '1.066 Baris Dapodik', '0 Nilai Kosong', 'Fungsi TRIM & UPPER pada nama siswa, standardisasi NISN 10 digit', 'Format teks terstandardisasi'],
            ['Tahap 2: Integrasi Variabel Multidimensi', '1.066 Siswa', 'Struktur terpisah', 'Penggabungan demografi Dapodik, nilai rapor, absensi, ekonomi, dan pelanggaran', '1.066 Siswa terintegrasi'],
            ['Tahap 3: Pembentukan Fitur (Feature Engineering)', '37 Kolom Mentah', 'Dimensi tinggi', 'Ekstraksi 7 fitur prediktor utama: X1 s.d. X7 (termasuk X7 Pelanggaran Disiplin)', '7 Fitur Siap Model'],
            ['Tahap 4: Ordinal Encoding Sosial Ekonomi', 'Teks bansos/penghasilan', 'Data kategorik', 'Transformasi ke Kode 1 (Bansos), Kode 2 (<1 Jt), Kode 3 (>=1 Jt)', 'Fitur X5 terstandar'],
            ['Tahap 5: Penetapan Label Target Faktual', 'Kolom Status_Putus_Sekolah', 'Imbalance kelas', 'Label Tinggi (33 Siswa Putus Sekolah), Label Rendah (1.033 Siswa Aktif)', '1.066 SISWA VALID']
        ],
        'Tabel 4.0 Rekapitulasi Tahapan Audit Preprocessing Data Penelitian SMKN 1 Bungo',
        [Inches(2.0), Inches(1.3), Inches(1.2), Inches(2.2), Inches(1.3)]
    )

# 2. Insert Gini Evaluation Summary Table in Section 4.5.2
p_l4 = None
for p in doc.paragraphs:
    if 'Langkah 4: Pengujian Model Ensemble' in p.text:
        p_l4 = p
        break

if p_l4:
    insert_table_before(
        p_l4,
        ['Komponen Evaluasi Gini Impurity', 'Cabang Kiri (X7 >= 1)', 'Cabang Kanan (X7 < 1)', 'Perhitungan Keseluruhan', 'Keterangan Metodologis'],
        [
            ['Jumlah Sampel Terbagi (N)', '6 Siswa', '6 Siswa', 'Total: 12 Siswa Latih Bootstrap', 'Partisi seimbang antara cabang kiri dan kanan'],
            ['Komposisi Kelas (Tinggi / Rendah)', '6 Tinggi, 0 Rendah', '0 Tinggi, 6 Rendah', 'Root: 6 Tinggi, 6 Rendah', 'Pemisahan kelas sempurna 100% tanpa salah tempat'],
            ['Gini Impurity Simpul Cabang', 'Gini(L) = 0.0000', 'Gini(R) = 0.0000', 'Gini(Root) = 0.5000', 'Simpul anak berstatus murni sempurna (Pure Node)'],
            ['Gini Split Tertimbang', '-', '-', 'Gini_Split = (6/12)*0 + (6/12)*0 = 0.0000', 'Formula: (N_L/N)*G_L + (N_R/N)*G_R'],
            ['Gini Gain (Delta Gini)', '-', '-', 'Gini_Gain = 0.5000 - 0.0000 = 0.5000', 'Penurunan ketidakmurnian maksimal (Gain = 0.5000)']
        ],
        'Tabel 4.2b Rekapitulasi Perhitungan Gini Impurity dan Gini Gain Simpul Akar (Tree 1)',
        [Inches(2.0), Inches(1.3), Inches(1.3), Inches(2.0), Inches(1.6)]
    )

output_file = 'HASIL_PERHITUNGAN_DAN_BAB_IV/BAB_IV_PERHITUNGAN_MANUAL_DAN_PEMBAHASAN_TESIS.docx'
doc.save(output_file)
print("Tabel 4.0 and Tabel 4.2b successfully inserted into BAB IV!")
