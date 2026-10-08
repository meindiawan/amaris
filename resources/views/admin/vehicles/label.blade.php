<!doctype html><html lang="id"><head><meta charset="utf-8"><title>Label Barcode Unit</title>
<style>body{font-family:Arial,sans-serif;margin:16px;background:#fff}.bar{margin-bottom:14px}.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:10px}
.lbl{border:1.5px dashed #888;border-radius:8px;padding:10px;text-align:center;break-inside:avoid}.lbl .p{font:800 26px Arial;letter-spacing:2px}.lbl .n{font-size:12px;color:#444;margin-bottom:6px}.lbl svg{width:100%;height:auto}.lbl code{font-size:13px;font-weight:700;letter-spacing:2px}
@media print{.bar{display:none}body{margin:6mm}}</style></head><body>
<div class="bar"><button onclick="window.print()" style="padding:10px 18px;font-size:15px">🖨 Cetak</button></div>
<div class="grid">@foreach($vehicles as $v)<div class="lbl"><div class="p">{{ $v->plate_number }}</div><div class="n">{{ $v->label }} · AMARIS RENT CAR</div>{!! \App\Support\Code128::svg($v->barcode, 60, 2, false) !!}<code>{{ $v->barcode }}</code></div>@endforeach</div>
</body></html>
