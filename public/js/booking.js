(() => {
  const form = document.getElementById('bookingForm');
  if (!form) return;
  const $ = (id) => document.getElementById(id);
  const price = +form.dataset.price || 0, deliveryFee = +form.dataset.delivery || 0;
  const booked = JSON.parse(form.dataset.booked || '[]').map(([a, b]) => [new Date(a), new Date(b)]);
  const rp = (n) => 'Rp ' + Math.round(n).toLocaleString('id-ID');
  const val = (n) => (form.querySelector(`[name="${n}"]:checked`) || {}).value;
  const el = { sd: $('startDate'), st: $('startTime'), ed: $('endDate'), et: $('endTime') };

  // Tidak boleh memilih tanggal yang sudah lewat
  const d = new Date(); const today = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
  el.sd.min = today; el.ed.min = today;

  const toggleAddr = () => {
    const pick = val('pickup_method') === 'delivery', ret = val('return_method') === 'pickup';
    $('pickupWrap').hidden = !pick; $('returnWrap').hidden = !ret;
    $('pickupAddr').required = pick; $('returnAddr').required = ret;
  };

  const calc = () => {
    const svcInput = form.querySelector('[name="service_type"]:checked');
    const fee = +(svcInput?.dataset.fee || 0), svc = svcInput?.value;
    $('simWrap').hidden = svc !== 'self_drive';
    const label = { self_drive: 'Mobil saja', with_driver: 'Tambahan supir', all_in: 'Tambahan All In' }[svc];
    $('sumSvcLabel').textContent = label;

    let days = 0, conflict = false;
    if (el.sd.value && el.ed.value) {
      const s = new Date(`${el.sd.value}T${el.st.value || '00:00'}`), e = new Date(`${el.ed.value}T${el.et.value || '00:00'}`);
      if (e > s) {
        days = Math.max(1, Math.ceil((e - s) / 864e5));
        conflict = booked.some(([bs, be]) => bs < e && be > s);
        $('durationHint').textContent = `Durasi sewa: ${days} hari (dihitung per 24 jam).`;
      } else {
        $('durationHint').textContent = 'Waktu kembali harus setelah waktu ambil.';
      }
    }
    $('conflictHint').hidden = !conflict;
    $('submitBtn').disabled = conflict;
    const legs = (val('pickup_method') === 'delivery' ? 1 : 0) + (val('return_method') === 'pickup' ? 1 : 0);
    const deliv = legs * deliveryFee, d1 = Math.max(days, 1);
    $('sumDays').textContent = days ? `${days} hari` : '—';
    $('sumSvc').textContent = fee ? `${rp(fee)} × ${d1}` : 'Gratis';
    $('sumDelivRow').hidden = !deliv; $('sumDeliv').textContent = rp(deliv);
    $('sumTotal').textContent = rp(d1 * (price + fee) + deliv);
  };

  if (el.sd.value) el.ed.min = el.sd.value;
  el.sd.addEventListener('change', () => { el.ed.min = el.sd.value || today; if (el.ed.value && el.ed.value < el.sd.value) el.ed.value = el.sd.value; calc(); });
  form.addEventListener('change', () => { toggleAddr(); calc(); });
  form.addEventListener('input', (e) => { if (e.target.matches('input[type=time]')) calc(); });
  form.querySelector('[name="nik"]').addEventListener('input', (e) => { e.target.value = e.target.value.replace(/\D/g, '').slice(0, 16); });
  toggleAddr(); calc();

  // Bila ada error validasi dari server, langsung gulir ke form
  if ($('formAlert')) $('formAlert').scrollIntoView({ behavior: 'smooth', block: 'center' });
})();
