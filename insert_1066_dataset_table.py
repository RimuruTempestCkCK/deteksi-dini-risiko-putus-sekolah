import os
import openpyxl
import pandas as pd
import docx
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT
from docx.oxml import parse_xml, OxmlElement
from docx.oxml.ns import nsdecls, qn

print("=== MEMULAI PENAMBAHAN TABEL DATASET LENGKAP 1.066 SISWA KE BAB IV ===")

excel_path = 'HASIL_PERHITUNGAN_DAN_BAB_IV/PERHITUNGAN_MANUAL.xlsx'
wb = openpyxl.load_workbook(excel_path, data_only=True)
ws1 = wb['01_DATASET']

rows_data = []
# Sheet 01 rows from row 12 to 1077 (1.066 rows)
for r in range(12, 1078):
    row_vals = [ws1.cell(r, c).value for c in range(1, 12)]
    rows_data.append(row_vals)

print(f"Total baris data terbaca dari Sheet 01_DATASET: {len(rows_data)} baris")

doc_path = 'HASIL_PERHITUNGAN_DAN_BAB_IV/BAB_IV_PERHITUNGAN_MANUAL_DAN_PEMBAHASAN_TESIS.docx'
doc = docx.Document(doc_path)

# Let's find Section 4.10 (Kesimpulan) or add as Appendix (Lampiran) or Subbab 4.3.1
# Let's check where to place it:
# Usually in thesis, a 1066-row table is placed as a special subsection in 4.3 (Data Selection & Preprocessing)
# or as "Tabel 4.1d: Dataset Lengkap Penelitian 1.066 Siswa SMKN 1 Bungo TA 2023/2024" right under Subbab 4.3!

# Let's find Subbab 4.4 paragraph
p_44 = None
for p in doc.paragraphs:
    if '4.4 Pembagian Data (Data Latih dan Data Uji)' in p.text:
        p_44 = p
        break

if not p_44:
    print("Paragraph 4.4 not found, falling back to end of document")
    p_44 = doc.paragraphs[-1]

# Insert Introductory Paragraph
p_heading = p_44.insert_paragraph_before("4.3.1 Tabel Dataset Penelitian Riil Lengkap (Populasi 1.066 Siswa SMKN 1 Bungo)")
p_heading.runs[0].font.bold = True
p_heading.runs[0].font.size = Pt(11)
p_heading.runs[0].font.name = 'Calibri'
p_heading.runs[0].font.color.rgb = RGBColor(31, 78, 120)

p_desc = p_44.insert_paragraph_before(
    "Sebagai bukti keterbukaan data ilmiah, validitas metodologis, dan reproduksibilitas eksperimen machine learning, "
    "Tabel 4.1d berikut menyajikan populasi lengkap 1.066 siswa riil SMK Negeri 1 Bungo Tahun Ajaran 2023/2024 hasil integrasi data "
    "Buku Induk kependidikan Dapodik dan 32 file Leger rapor semester. Dataset ini memuat 10 digit NISN asli, nama siswa, 7 atribut prediktor "
    "(X1 Nilai Rapor, X2 Mapel < KKM, X3 Alfa, X4 Izin & Sakit, X5 Status Ekonomi Ordinal, X6 Jarak KM, dan X7 Catatan Pelanggaran Disiplin), "
    "serta Label Target Ground Truth status faktual putus sekolah di lapangan (33 Siswa Risiko Tinggi dan 1.033 Siswa Risiko Rendah):"
)
p_desc.runs[0].font.size = Pt(10)
p_desc.runs[0].font.name = 'Calibri'

p_tbl_title = p_44.insert_paragraph_before("Tabel 4.1d Dataset Lengkap Penelitian 1.066 Siswa SMKN 1 Bungo TA 2023/2024")
p_tbl_title.runs[0].font.bold = True
p_tbl_title.runs[0].font.size = Pt(9.5)
p_tbl_title.runs[0].font.name = 'Calibri'
p_tbl_title.runs[0].font.color.rgb = RGBColor(47, 85, 151)

headers = ["No", "NISN Asli", "Nama Siswa (Riil)", "X1 (Nilai)", "X2 (KKM)", "X3 (Alfa)", "X4 (I+S)", "X5 (Eko)", "X6 (KM)", "X7 (Plgr)", "Status Target"]
col_widths = [Inches(0.4), Inches(0.85), Inches(1.8), Inches(0.6), Inches(0.55), Inches(0.55), Inches(0.55), Inches(0.6), Inches(0.6), Inches(0.6), Inches(0.85)]

print("Membangun tabel Word 1.067 baris x 11 kolom...")
table = doc.add_table(rows=len(rows_data) + 1, cols=len(headers))
table.alignment = WD_TABLE_ALIGNMENT.CENTER

# Set repeat table header across pages
header_tr = table.rows[0]._element.get_or_add_trPr()
header_tr.append(parse_xml(f'<w:tblHeader {nsdecls("w")}/>'))

# Format Header
for c_i, h in enumerate(headers):
    cell = table.rows[0].cells[c_i]
    cell.text = h
    tcPr = cell._element.get_or_add_tcPr()
    tcPr.append(parse_xml(f'<w:shd {nsdecls("w")} w:fill="1F4E78"/>'))
    p = cell.paragraphs[0]
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    if p.runs:
        run = p.runs[0]
        run.font.bold = True
        run.font.color.rgb = RGBColor(255, 255, 255)
        run.font.size = Pt(8)
        run.font.name = 'Calibri'

# Fill rows efficiently
border_xml = parse_xml(f'<w:tcBorders {nsdecls("w")}><w:top w:val="single" w:sz="4" w:space="0" w:color="D9D9D9"/><w:left w:val="single" w:sz="4" w:space="0" w:color="D9D9D9"/><w:bottom w:val="single" w:sz="4" w:space="0" w:color="D9D9D9"/><w:right w:val="single" w:sz="4" w:space="0" w:color="D9D9D9"/></w:tcBorders>')
shd_zebra = parse_xml(f'<w:shd {nsdecls("w")} w:fill="F9FAFB"/>')
shd_white = parse_xml(f'<w:shd {nsdecls("w")} w:fill="FFFFFF"/>')
shd_high = parse_xml(f'<w:shd {nsdecls("w")} w:fill="FCE4D6"/>')

print("Mengisi data 1.066 siswa ke dalam sel tabel...")
for idx, r_val in enumerate(rows_data):
    row = table.rows[idx + 1]
    is_high = str(r_val[10]) == 'Tinggi'
    
    # Format values
    vals = [
        str(r_val[0]), # No
        str(r_val[1]), # NISN
        str(r_val[2]), # Nama
        f"{float(r_val[3]):.2f}", # X1
        str(r_val[4]), # X2
        str(r_val[5]), # X3
        str(r_val[6]), # X4
        str(r_val[7]), # X5
        f"{float(r_val[8]):.1f}", # X6
        str(r_val[9]), # X7
        str(r_val[10]) # Label
    ]
    
    for c_i, v in enumerate(vals):
        cell = row.cells[c_i]
        cell.text = v
        tcPr = cell._element.get_or_add_tcPr()
        
        # Border
        tcPr.append(parse_xml(f'<w:tcBorders {nsdecls("w")}><w:top w:val="single" w:sz="4" w:space="0" w:color="D9D9D9"/><w:left w:val="single" w:sz="4" w:space="0" w:color="D9D9D9"/><w:bottom w:val="single" w:sz="4" w:space="0" w:color="D9D9D9"/><w:right w:val="single" w:sz="4" w:space="0" w:color="D9D9D9"/></w:tcBorders>'))
        
        # Fill
        if is_high:
            tcPr.append(parse_xml(f'<w:shd {nsdecls("w")} w:fill="FCE4D6"/>'))
        elif idx % 2 == 1:
            tcPr.append(parse_xml(f'<w:shd {nsdecls("w")} w:fill="F9FAFB"/>'))
        else:
            tcPr.append(parse_xml(f'<w:shd {nsdecls("w")} w:fill="FFFFFF"/>'))
            
        p = cell.paragraphs[0]
        if c_i == 2:
            p.alignment = WD_ALIGN_PARAGRAPH.LEFT
        elif c_i in [3, 8]:
            p.alignment = WD_ALIGN_PARAGRAPH.RIGHT
        else:
            p.alignment = WD_ALIGN_PARAGRAPH.CENTER
            
        if p.runs:
            run = p.runs[0]
            run.font.size = Pt(7.5)
            run.font.name = 'Calibri'
            if is_high:
                run.font.bold = True
                if c_i in [0, 1, 2, 9, 10]:
                    run.font.color.rgb = RGBColor(192, 0, 0)

# Apply column widths
for row in table.rows:
    for idx, width in enumerate(col_widths):
        row.cells[idx].width = width

# Move table before p_44
p_44._element.addprevious(table._element)
p_44.insert_paragraph_before("") # Spacing paragraph

print("Menyimpan dokumen Word...")
doc.save(doc_path)
print(f"=== BERHASIL! Tabel 1.066 Siswa Lengkap Telah Disimpan di {doc_path} ===")
