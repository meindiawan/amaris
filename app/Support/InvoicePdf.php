<?php
namespace App\Support;

use App\Models\Invoice;

/** Membuat PDF invoice dengan dompdf (sudah dibundel di /lib/dompdf — tidak perlu composer). */
class InvoicePdf
{
    public static function html(Invoice $invoice): string
    {
        $invoice->load(['items', 'booking.payments']);
        return view('invoices.pdf', ['invoice' => $invoice])->render();
    }

    public static function render(Invoice $invoice): string
    {
        require_once base_path('lib/dompdf/autoload.inc.php');
        $dir = storage_path('app/dompdf');
        if (!is_dir($dir)) @mkdir($dir, 0775, true);

        $options = new \Dompdf\Options();
        $options->set('defaultFont', 'Helvetica');
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('chroot', base_path());
        $options->set('tempDir', $dir);
        $options->set('fontDir', $dir);
        $options->set('fontCache', $dir);

        $pdf = new \Dompdf\Dompdf($options);
        $pdf->loadHtml(self::html($invoice), 'UTF-8');
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();
        return $pdf->output();
    }

    public static function response(Invoice $invoice, bool $download = false)
    {
        return response(self::render($invoice), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => ($download ? 'attachment' : 'inline').'; filename="'.$invoice->file_name.'"',
            'Cache-Control' => 'private, max-age=0, must-revalidate',
        ]);
    }
}
