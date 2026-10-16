<?php
namespace App\Support;

use ZipArchive;

/**
 * Penulis .xlsx minimal (tanpa library tambahan) — cukup untuk laporan tabel sederhana.
 * Sel: string biasa, ['date' => 'Y-m-d'] untuk tanggal, atau angka.
 */
class SimpleXlsx
{
    /** @param array<int,string> $headers  @param array<int,array<int,mixed>> $rows  @param array<int,int> $widths */
    public static function build(string $path, string $sheet, array $headers, array $rows, array $widths = [], ?string $title = null): bool
    {
        if (!class_exists(ZipArchive::class)) return false;
        $zip = new ZipArchive();
        if ($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) return false;

        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/></Types>');
        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>');
        $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="'.self::x(mb_substr(preg_replace('/[\\\\\/\?\*\[\]:]/', ' ', $sheet), 0, 31)).'" sheetId="1" r:id="rId1"/></sheets></workbook>');
        $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/></Relationships>');
        // style 0 = normal, 1 = header (tebal, putih di atas ungu), 2 = tanggal, 3 = judul
        $zip->addFromString('xl/styles.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><numFmts count="1"><numFmt numFmtId="164" formatCode="dd\ mmm\ yyyy"/></numFmts><fonts count="3"><font><sz val="11"/><name val="Calibri"/></font><font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font><font><b/><sz val="14"/><name val="Calibri"/></font></fonts><fills count="3"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FF38205B"/><bgColor indexed="64"/></patternFill></fill></fills><borders count="2"><border><left/><right/><top/><bottom/><diagonal/></border><border><left style="thin"><color rgb="FFD9CCB9"/></left><right style="thin"><color rgb="FFD9CCB9"/></right><top style="thin"><color rgb="FFD9CCB9"/></top><bottom style="thin"><color rgb="FFD9CCB9"/></bottom><diagonal/></border></borders><cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs><cellXfs count="4"><xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment vertical="top" wrapText="1"/></xf><xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"/><xf numFmtId="164" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="top"/></xf><xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1"/></cellXfs><cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles></styleSheet>');

        $cols = '';
        foreach ($widths as $i => $w) $cols .= '<col min="'.($i + 1).'" max="'.($i + 1).'" width="'.$w.'" customWidth="1"/>';
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'.($cols ? '<cols>'.$cols.'</cols>' : '').'<sheetData>';
        $r = 1;
        if ($title !== null) { $xml .= '<row r="'.$r.'">'.self::cell(0, $r, $title, 3).'</row>'; $r += 2; }
        $xml .= '<row r="'.$r.'">'; foreach ($headers as $c => $h) $xml .= self::cell($c, $r, $h, 1); $xml .= '</row>';
        foreach ($rows as $row) {
            $r++; $xml .= '<row r="'.$r.'">';
            foreach (array_values($row) as $c => $v) $xml .= self::cell($c, $r, $v, 0);
            $xml .= '</row>';
        }
        $xml .= '</sheetData></worksheet>';
        $zip->addFromString('xl/worksheets/sheet1.xml', $xml);
        return $zip->close();
    }

    private static function cell(int $c, int $r, $v, int $style): string
    {
        $ref = self::col($c).$r;
        if (is_array($v) && isset($v['date'])) {
            $ts = strtotime($v['date'].' 00:00:00 UTC');
            if ($ts === false) return '<c r="'.$ref.'" s="0"/>';
            return '<c r="'.$ref.'" s="2"><v>'.intdiv($ts - strtotime('1899-12-30 00:00:00 UTC'), 86400).'</v></c>';
        }
        if ($v === null || $v === '') return '<c r="'.$ref.'" s="'.$style.'"/>';
        if (is_int($v) || is_float($v)) return '<c r="'.$ref.'" s="'.$style.'"><v>'.$v.'</v></c>';
        return '<c r="'.$ref.'" s="'.$style.'" t="inlineStr"><is><t xml:space="preserve">'.self::x((string) $v).'</t></is></c>';
    }

    private static function col(int $i): string
    {
        $s = ''; $i++;
        while ($i > 0) { $m = ($i - 1) % 26; $s = chr(65 + $m).$s; $i = intdiv($i - 1, 26); }
        return $s;
    }

    private static function x(string $s): string
    {
        $s = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $s);
        return htmlspecialchars($s, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
