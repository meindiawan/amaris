(() => {
    const nav = document.getElementById('navbar');
    const toggle = document.getElementById('menuToggle');
    const links = document.getElementById('navLinks');
    window.addEventListener('scroll', () => nav?.classList.toggle('scrolled', window.scrollY > 15));
    toggle?.addEventListener('click', () => { toggle.classList.toggle('open'); links?.classList.toggle('open'); });
    links?.querySelectorAll('a').forEach(a => a.addEventListener('click', () => { toggle?.classList.remove('open'); links?.classList.remove('open'); }));

    const slides = [...document.querySelectorAll('.hero-slide')];
    const dots = [...document.querySelectorAll('.dot')];
    const current = document.getElementById('slideCurrent'); let active=0, timer;
    function showSlide(index){ if(!slides.length)return; active=(index+slides.length)%slides.length; slides.forEach((s,i)=>s.classList.toggle('active',i===active)); dots.forEach((d,i)=>d.classList.toggle('active',i===active)); if(current) current.textContent=String(active+1).padStart(2,'0'); }
    const next=()=>showSlide(active+1), prev=()=>showSlide(active-1);
    document.getElementById('nextSlide')?.addEventListener('click',next); document.getElementById('prevSlide')?.addEventListener('click',prev); dots.forEach(d=>d.addEventListener('click',()=>showSlide(+d.dataset.slide)));
    function autoplay(){clearInterval(timer); if(slides.length>1) timer=setInterval(next,6000)} autoplay(); document.getElementById('heroSlider')?.addEventListener('mouseenter',()=>clearInterval(timer)); document.getElementById('heroSlider')?.addEventListener('mouseleave',autoplay);

    const filters=[...document.querySelectorAll('.filter')], cars=[...document.querySelectorAll('.car-card')];
    filters.forEach(btn=>btn.addEventListener('click',()=>{filters.forEach(x=>x.classList.remove('active'));btn.classList.add('active');const f=btn.dataset.filter;cars.forEach(card=>card.classList.toggle('is-hidden',f!=='all'&&card.dataset.type!==f));}));

    const track=document.getElementById('testiTrack'); let testiIndex=0;
    function visible(){return window.innerWidth<=760?1:window.innerWidth<=1050?2:3}
    function moveTesti(dir){if(!track)return;const max=Math.max(0,track.children.length-visible());testiIndex=Math.max(0,Math.min(max,testiIndex+dir));const gap=18;const card=track.children[0];if(!card)return;const width=card.getBoundingClientRect().width;track.style.transform=`translateX(-${testiIndex*(width+gap)}px)`}
    document.getElementById('nextTesti')?.addEventListener('click',()=>moveTesti(1));document.getElementById('prevTesti')?.addEventListener('click',()=>moveTesti(-1));window.addEventListener('resize',()=>{testiIndex=0;if(track)track.style.transform='translateX(0)'});
})();
