@php
 $waN = \App\Support\Fmt::waNumber(\App\Support\Fmt::setting('whatsapp', '6285157024883'));
@endphp
<div class="contact-float">
<a class="cf-btn cf-wa" href="https://wa.me/{{ $waN }}?text={{ urlencode('Halo Amaris Ren Car, saya ingin bertanya tentang rental mobil.') }}" target="_blank" rel="noopener" aria-label="Hubungi lewat WhatsApp">@include('partials.brand-icon',['n'=>'wa'])<span class="cf-text">Chat Admin</span></a>
</div>
