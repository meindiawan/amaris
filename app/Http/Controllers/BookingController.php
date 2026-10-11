<?php
namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Car;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function store(Request $r, Car $car)
    {
        // Honeypot anti-spam: field ini harus kosong (disembunyikan dari pengunjung asli).
        if ($r->filled('website')) return redirect('/');

        if (!$car->is_available) {
            return back()->withInput()->withErrors(['car' => 'Maaf, mobil ini sedang tidak tersedia untuk disewa.']);
        }

        $d = $r->validate([
            'service_type'   => 'required|in:self_drive,with_driver,all_in',
            'start_date'     => 'required|date|after_or_equal:today',
            'start_time'     => 'required|date_format:H:i',
            'end_date'       => 'required|date|after_or_equal:start_date',
            'end_time'       => 'required|date_format:H:i',
            'pickup_method'  => 'required|in:office,delivery',
            'pickup_address' => 'required_if:pickup_method,delivery|nullable|string|max:500',
            'return_method'  => 'required|in:office,pickup',
            'return_address' => 'required_if:return_method,pickup|nullable|string|max:500',
            'customer_name'  => 'required|string|max:120',
            'phone'          => ['required', 'regex:/^[0-9+\-\s]{9,18}$/'],
            'email'          => 'nullable|email|max:120',
            'nik'            => 'required|digits:16',
            'address'        => 'required|string|max:500',
            'sim_number'     => 'nullable|string|max:40',
            'destination'    => 'nullable|string|max:200',
            'passengers'     => 'nullable|integer|min:1|max:30',
            'notes'          => 'nullable|string|max:1000',
            'agree'          => 'accepted',
        ], [
            'service_type.required'   => 'Pilih paket sewa terlebih dahulu.',
            'start_date.required'     => 'Tanggal ambil (check-in) wajib diisi.',
            'start_date.after_or_equal' => 'Tanggal ambil tidak boleh sebelum hari ini.',
            'end_date.required'       => 'Tanggal kembali (check-out) wajib diisi.',
            'end_date.after_or_equal' => 'Tanggal kembali tidak boleh sebelum tanggal ambil.',
            'start_time.required'     => 'Jam ambil wajib diisi.',
            'end_time.required'       => 'Jam kembali wajib diisi.',
            'pickup_address.required_if' => 'Alamat pengantaran mobil wajib diisi.',
            'return_address.required_if' => 'Alamat penjemputan mobil wajib diisi.',
            'customer_name.required'  => 'Nama lengkap wajib diisi.',
            'phone.required'          => 'Nomor WhatsApp wajib diisi.',
            'phone.regex'             => 'Nomor WhatsApp tidak valid.',
            'nik.required'            => 'NIK (nomor KTP) wajib diisi.',
            'nik.digits'              => 'NIK harus 16 digit angka.',
            'address.required'        => 'Alamat penyewa wajib diisi.',
            'agree.accepted'          => 'Centang persetujuan syarat & ketentuan untuk melanjutkan.',
        ]);

        $start = Carbon::parse($d['start_date'].' '.$d['start_time']);
        $end   = Carbon::parse($d['end_date'].' '.$d['end_time']);
        if ($end->lessThanOrEqualTo($start)) {
            return back()->withInput()->withErrors(['end_date' => 'Waktu kembali harus setelah waktu ambil.']);
        }
        if (Booking::conflicts($car->id, $start, $end)->exists()) {
            return back()->withInput()->withErrors(['start_date' => 'Mobil ini sudah dibooking pada tanggal tersebut. Silakan pilih tanggal lain atau hubungi admin.']);
        }

        // Hitung biaya (estimasi) — dibulatkan per 24 jam, minimal 1 hari.
        $settings = DB::table('site_settings')->pluck('value', 'key');
        $days = max(1, (int) ceil($start->diffInMinutes($end) / 1440));
        $feePerDay = match ($d['service_type']) {
            'with_driver' => (int) ($settings['driver_fee'] ?? 0),
            'all_in'      => (int) ($settings['allin_fee'] ?? 0),
            default       => 0,
        };
        $legs = ($d['pickup_method'] === 'delivery' ? 1 : 0) + ($d['return_method'] === 'pickup' ? 1 : 0);
        $delivery = $legs * (int) ($settings['delivery_fee'] ?? 0);
        $total = $days * ($car->price + $feePerDay) + $delivery;

        $booking = Booking::create([
            'code' => Booking::makeCode(), 'car_id' => $car->id, 'car_name' => $car->name,
            'service_type' => $d['service_type'], 'start_at' => $start, 'end_at' => $end, 'days' => $days,
            'pickup_method' => $d['pickup_method'], 'pickup_address' => $d['pickup_method'] === 'delivery' ? $d['pickup_address'] : null,
            'return_method' => $d['return_method'], 'return_address' => $d['return_method'] === 'pickup' ? $d['return_address'] : null,
            'customer_name' => $d['customer_name'], 'phone' => $d['phone'], 'email' => $d['email'] ?? null,
            'nik' => $d['nik'], 'address' => $d['address'],
            'sim_number' => $d['service_type'] === 'self_drive' ? ($d['sim_number'] ?? null) : null,
            'destination' => $d['destination'] ?? null, 'passengers' => $d['passengers'] ?? null, 'notes' => $d['notes'] ?? null,
            'price_per_day' => $car->price, 'service_fee_per_day' => $feePerDay, 'delivery_fee' => $delivery,
            'total_estimate' => $total, 'status' => 'pending',
        ]);

        // Booking hanya lewat WhatsApp: data tersimpan di sistem, lalu pelanggan langsung diarahkan ke WA admin.
        return redirect()->away($this->whatsappUrl($booking, $car, $d));
    }

    /** Tautan wa.me ke admin dengan pesan booking yang sudah terisi. */
    private function whatsappUrl(Booking $b, Car $car, array $d): string
    {
        $rp = fn ($n) => 'Rp '.number_format($n, 0, ',', '.');
        $lines = [
            'Halo Amaris Rent Car, saya ingin booking mobil:',
            '',
            'Kode booking : '.$b->code,
            'Mobil : '.$car->name,
            'Paket : '.$b->service_label,
            'Ambil : '.$b->start_at->translatedFormat('d M Y').' jam '.$b->start_at->format('H:i'),
            'Kembali : '.$b->end_at->translatedFormat('d M Y').' jam '.$b->end_at->format('H:i').' ('.$b->days.' hari)',
            'Serah terima : '.$b->pickup_label,
            'Pengembalian : '.$b->return_label,
        ];
        if (!empty($d['destination'])) $lines[] = 'Tujuan : '.$d['destination'];
        if (!empty($d['passengers'])) $lines[] = 'Penumpang : '.$d['passengers'].' orang';
        array_push($lines, '', 'Nama : '.$b->customer_name, 'No. WA : '.$b->phone, 'NIK : '.$b->nik, 'Alamat : '.$b->address);
        if ($b->sim_number) $lines[] = 'No. SIM : '.$b->sim_number;
        if (!empty($d['notes'])) $lines[] = 'Catatan : '.$d['notes'];
        array_push($lines, '', 'Estimasi total : '.$rp($b->total_estimate), 'Mohon konfirmasinya. Terima kasih.');

        $wa = \App\Support\Fmt::waNumber(\App\Support\Fmt::setting('whatsapp', '6285157024883'));
        return 'https://wa.me/'.$wa.'?text='.rawurlencode(implode("\n", $lines));
    }

    public function success(string $code)
    {
        $booking = Booking::with('car')->where('code', $code)->firstOrFail();
        $settings = DB::table('site_settings')->pluck('value', 'key');
        return view('booking-success', compact('booking', 'settings'));
    }
}
