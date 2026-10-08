<?php
namespace App\Support;

/** Generator barcode Code128 (set B) → SVG, tanpa library tambahan. */
class Code128
{
    private const PATTERNS = [
        '212222','222122','222221','121223','121322','131222','122213','122312','132212','221213',
        '221312','231212','112232','122132','122231','113222','123122','123221','223211','221132',
        '221231','213212','223112','312131','311222','321122','321221','312212','322112','322211',
        '212123','212321','232121','111323','131123','131321','112313','132113','132311','211313',
        '231113','231311','112133','112331','132131','113123','113321','133121','313121','211331',
        '231131','213113','213311','213131','311123','311321','331121','312113','312311','332111',
        '314111','221411','431111','111224','111422','121124','121421','141122','141221','112214',
        '112412','122114','122411','142112','142211','241211','221114','413111','241112','134111',
        '111242','121142','121241','114212','124112','124211','411212','421112','421211','212141',
        '214121','412121','111143','111341','131141','114113','114311','411113','411311','113141',
        '114131','311141','411131','211412','211214','211232',
    ];
    private const STOP = '2331112';

    /** Daftar lebar modul (bar, spasi, bar, …) untuk teks tertentu. */
    public static function modules(string $text): array
    {
        $text = preg_replace('/[^\x20-\x7E]/', '', $text);
        $codes = [104];                                  // Start B
        foreach (str_split($text) as $ch) $codes[] = ord($ch) - 32;
        $sum = $codes[0];
        for ($i = 1; $i < count($codes); $i++) $sum += $codes[$i] * $i;
        $codes[] = $sum % 103;                           // checksum
        $seq = '';
        foreach ($codes as $c) $seq .= self::PATTERNS[$c];
        $seq .= self::STOP;
        return array_map('intval', str_split($seq));
    }

    /** SVG siap tempel (inline) atau dijadikan data-URI. */
    public static function svg(string $text, int $height = 70, float $module = 2.0, bool $withText = false): string
    {
        $mods = self::modules($text);
        $quiet = 10;
        $x = $quiet; $bar = true; $rects = '';
        foreach ($mods as $w) {
            if ($bar) $rects .= '<rect x="'.($x * $module).'" y="0" width="'.($w * $module).'" height="'.$height.'"/>';
            $x += $w; $bar = !$bar;
        }
        $width = ($x + $quiet) * $module;
        $textH = $withText ? 16 : 0;
        $label = $withText ? '<text x="'.($width / 2).'" y="'.($height + 13).'" font-family="monospace" font-size="12" text-anchor="middle" fill="#000">'.htmlspecialchars($text).'</text>' : '';
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 '.$width.' '.($height + $textH).'" width="'.$width.'" height="'.($height + $textH).'"><rect width="100%" height="100%" fill="#fff"/><g fill="#000">'.$rects.'</g>'.$label.'</svg>';
    }

    public static function dataUri(string $text, int $height = 70, float $module = 2.0): string
    {
        return 'data:image/svg+xml;base64,'.base64_encode(self::svg($text, $height, $module));
    }
}
