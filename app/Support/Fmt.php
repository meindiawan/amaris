<?php
namespace App\Support;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/** Pembantu format tampilan (rupiah, telepon, tanggal, masa berlaku dokumen). */
class Fmt
{
    protected static ?array $settings = null;

    public static function rp($n): string { return 'Rp '.number_format((float) $n, 0, ',', '.'); }
    public static function num($n): string { return number_format((float) $n, 0, ',', '.'); }

    /** Pengaturan situs (site_settings) – di-cache selama satu request. */
    public static function setting(string $key, $default = null)
    {
        if (self::$settings === null) {
            try { self::$settings = DB::table('site_settings')->pluck('value', 'key')->all(); } catch (\Throwable $e) { self::$settings = []; }
        }
        $v = self::$settings[$key] ?? null;
        return ($v === null || $v === '') ? $default : $v;
    }
    public static function forgetSettings(): void { self::$settings = null; }

    /** 0821-3621-562 → +62 821-3621-562 */
    public static function phoneIntl(?string $p): string
    {
        $n = preg_replace('/\D+/', '', (string) $p);
        if ($n === '') return '';
        if (Str::startsWith($n, '0')) $n = '62'.substr($n, 1);
        elseif (Str::startsWith($n, '8')) $n = '62'.$n;
        $rest = substr($n, 2);
        if (!Str::startsWith($n, '62') || strlen($rest) < 8) return '+'.$n;
        return '+62 '.substr($rest, 0, 3).'-'.substr($rest, 3, 4).'-'.substr($rest, 7);
    }

    /** Nomor untuk tautan wa.me */
    public static function waNumber(?string $p): string
    {
        $n = preg_replace('/\D+/', '', (string) $p);
        if (Str::startsWith($n, '0')) return '62'.substr($n, 1);
        if (Str::startsWith($n, '8')) return '62'.$n;
        return $n;
    }

    /** "7 Oktober 2026" */
    public static function date($d, string $fmt = 'j F Y'): string
    {
        return $d ? Carbon::parse($d)->translatedFormat($fmt) : '-';
    }

    /**
     * Status masa berlaku dokumen (STNK, pajak, SIM).
     * @return array{0:string,1:string}|null [kelas css, teks]
     */
    public static function expiry($date): ?array
    {
        if (!$date) return null;
        $d = Carbon::parse($date)->startOfDay();
        $days = (int) now()->startOfDay()->diffInDays($d, false);
        $txt = $d->translatedFormat('d M Y');
        if ($days < 0)   return ['bad',  $txt.' · lewat '.abs($days).' hr'];
        if ($days <= 30) return ['warn', $txt.' · '.$days.' hr lagi'];
        return ['ok', $txt];
    }

    public static function dataUri(string $publicPath): string
    {
        static $cache = [];
        if (isset($cache[$publicPath])) return $cache[$publicPath];
        $file = public_path($publicPath);
        if (!is_file($file)) return $cache[$publicPath] = '';
        $mime = mime_content_type($file) ?: 'image/png';
        return $cache[$publicPath] = 'data:'.$mime.';base64,'.base64_encode(file_get_contents($file));
    }

    /** Simpan upload ke public/uploads/{folder}, kembalikan path web. */
    public static function saveUpload($file, string $folder): string
    {
        $name = Str::uuid().'.'.strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $file->move(public_path('uploads/'.$folder), $name);
        return '/uploads/'.$folder.'/'.$name;
    }
}
