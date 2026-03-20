/* BG Biglang Gwapo Barbershop – main.js */
document.addEventListener('DOMContentLoaded', () => {

  /* Page Loader */
  const loader = document.getElementById('page-loader');
  if (loader) setTimeout(() => loader.classList.add('hidden'), 2100);

  /* Navbar scroll + hero-nav */
  const nav = document.getElementById('mainNav');
  const updateNav = () => {
    if (!nav) return;
    const scrolled = window.scrollY > 60;
    nav.classList.toggle('scrolled', scrolled);
    nav.classList.toggle('hero-nav', !scrolled);
  };
  updateNav();
  window.addEventListener('scroll', updateNav, { passive: true });

  /* Fade-up observer */
  const obs = new IntersectionObserver(entries => {
    entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); obs.unobserve(e.target); } });
  }, { threshold: 0.12 });
  document.querySelectorAll('.fade-up').forEach(el => obs.observe(el));

  /* Smooth scroll */
  document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
      const tgt = document.querySelector(a.getAttribute('href'));
      if (tgt) { e.preventDefault(); window.scrollTo({ top: tgt.offsetTop - 80, behavior: 'smooth' }); }
    });
  });

  /* Counter animation */
  document.querySelectorAll('.stat-num[data-target]').forEach(el => {
    new IntersectionObserver(([entry], ob) => {
      if (!entry.isIntersecting) return;
      ob.unobserve(el);
      const target = +el.dataset.target, suffix = el.dataset.suffix || '';
      let cur = 0;
      const t = setInterval(() => {
        cur += target / (2000 / 16);
        if (cur >= target) { el.textContent = target + suffix; clearInterval(t); }
        else el.textContent = Math.floor(cur) + suffix;
      }, 16);
    }, { threshold: 0.5 }).observe(el);
  });

  /* Duplicate marquee */
  const track = document.querySelector('.marquee-track');
  if (track) track.innerHTML += track.innerHTML;

  /* Close mobile nav on link click */
  document.querySelectorAll('.navbar-nav .nav-link').forEach(l => {
    l.addEventListener('click', () => {
      const collapse = document.querySelector('.navbar-collapse.show');
      if (collapse) bootstrap.Collapse.getInstance(collapse)?.hide();
    });
  });

});
