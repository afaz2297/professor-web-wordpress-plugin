/* Professor Web — front-end interactions for The Jolly Joiner (no dependencies) */
(function () {
  'use strict';

  var CONFIG = window.PW_CONFIG || { animations: true, hideOnScroll: true };
  var body = document.body;
  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var animate = CONFIG.animations && !reduceMotion;
  var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };
  var once = function (el, key) {
    if (el.dataset['pw' + key]) return false;
    el.dataset['pw' + key] = '1';
    return true;
  };

  /* ---------- Page load ---------- */
  var markLoaded = function () { body.classList.add('is-loaded'); };
  if (document.fonts && document.fonts.ready) {
    document.fonts.ready.then(function () { requestAnimationFrame(markLoaded); });
    setTimeout(markLoaded, 1200);
  } else {
    setTimeout(markLoaded, 150);
  }

  /* ---------- Header, mega menu & popover menu ---------- */
  var header = null;
  var megaItems = [];

  function initHeader(scope) {
    $$('[data-pw-header]', scope).forEach(function (h) {
      if (!once(h, 'Header')) return;
      header = h;
      var root = h.parentElement;

      $$('.has-mega', h).forEach(function (item) {
        megaItems.push(item);
        var btn = item.querySelector('.nav__trigger');
        var set = function (open) {
          item.classList.toggle('is-open', open);
          btn.setAttribute('aria-expanded', String(open));
        };
        item._pwSet = set;
        btn.addEventListener('click', function (e) {
          e.stopPropagation();
          set(!item.classList.contains('is-open'));
        });
        document.addEventListener('click', function (e) { if (!item.contains(e.target)) set(false); });
        item.addEventListener('focusout', function (e) { if (!item.contains(e.relatedTarget)) set(false); });
      });

      var pop = root.querySelector('.popmenu');
      var burger = h.querySelector('.burger');
      if (!pop || !burger) return;
      var close = pop.querySelector('.popmenu__close');
      var timer;

      function openMenu() {
        clearTimeout(timer);
        var r = burger.getBoundingClientRect();
        pop.style.setProperty('--cx', (r.left + r.width / 2) + 'px');
        pop.style.setProperty('--cy', (r.top + r.height / 2) + 'px');
        pop.hidden = false;
        void pop.offsetWidth;
        pop.classList.add('is-open');
        body.classList.add('no-scroll');
        burger.setAttribute('aria-expanded', 'true');
        setTimeout(function () { close.focus(); }, 300);
      }
      function closeMenu(returnFocus) {
        if (pop.hidden) return;
        pop.classList.remove('is-open');
        body.classList.remove('no-scroll');
        burger.setAttribute('aria-expanded', 'false');
        timer = setTimeout(function () { pop.hidden = true; }, animate ? 750 : 0);
        if (returnFocus !== false) burger.focus();
      }
      burger.addEventListener('click', openMenu);
      close.addEventListener('click', function () { closeMenu(); });
      $$('a', pop).forEach(function (a) {
        a.addEventListener('click', function () {
          // Same-page links need the menu out of the way; other links navigate anyway.
          if (a.hash && a.pathname === location.pathname) closeMenu(false);
        });
      });
      pop.addEventListener('keydown', function (e) {
        if (e.key !== 'Tab') return;
        var f = $$('a, button', pop);
        var first = f[0], last = f[f.length - 1];
        if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
        else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
      });
      document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') return;
        closeMenu();
        megaItems.forEach(function (m) { m._pwSet(false); });
      });
      window.addEventListener('resize', function () {
        if (window.innerWidth >= 1100 && !pop.hidden) closeMenu(false);
      });
    });
  }

  /* ---------- Home hero video reel ---------- */
  function initHero(scope) {
    $$('[data-pw-hero]', scope).forEach(function (hero) {
      if (!once(hero, 'Hero')) return;
      var vids = $$('.hero__video', hero);
      var reels = $$('.reel', hero);
      if (!vids.length) return;
      var SLIDE_MS = parseInt(hero.dataset.slide, 10) || 9000;
      var cur = 0, timer = null, running = animate;

      function load(v) {
        if (v.dataset.loaded || !v.dataset.src) return;
        v.dataset.loaded = '1';
        v.addEventListener('error', function () { v.dataset.failed = '1'; });
        v.src = v.dataset.src;
      }
      function play(v) {
        if (v.dataset.failed || !v.dataset.src) return;
        load(v);
        try { v.currentTime = 0; } catch (err) { /* not ready yet */ }
        var p = v.play();
        if (p && p.catch) p.catch(function () {});
      }
      function schedule() {
        clearTimeout(timer);
        if (!running || vids.length < 2) return;
        timer = setTimeout(function () { show((cur + 1) % vids.length); }, SLIDE_MS);
      }
      function show(i) {
        vids.forEach(function (v, n) {
          var active = n === i;
          v.classList.toggle('is-active', active);
          if (active) { if (running) play(v); }
          else { setTimeout(function () { if (!v.classList.contains('is-active')) v.pause(); }, 1600); }
        });
        reels.forEach(function (r, n) {
          r.classList.remove('is-active', 'is-done');
          void r.offsetWidth;
          if (n === i) r.classList.add('is-active');
          if (n < i) r.classList.add('is-done');
        });
        cur = i;
        schedule();
      }
      reels.forEach(function (r) {
        r.style.setProperty('--reel-dur', SLIDE_MS + 'ms');
        r.addEventListener('click', function () { show(+r.dataset.reel); });
      });

      if (!animate) { reels.forEach(function (r) { r.hidden = true; }); return; }

      play(vids[0]);
      schedule();
      setTimeout(function () { vids.slice(1).forEach(function (v) { v.preload = 'auto'; load(v); }); }, 2500);

      var setRunning = function (on) {
        if (on === running) return;
        running = on;
        if (on) { play(vids[cur]); schedule(); }
        else { clearTimeout(timer); vids.forEach(function (v) { v.pause(); }); }
      };
      if ('IntersectionObserver' in window) {
        new IntersectionObserver(function (en) { setRunning(en[0].isIntersecting && !document.hidden); }, { threshold: 0.05 }).observe(hero);
      }
      document.addEventListener('visibilitychange', function () {
        var r = hero.getBoundingClientRect();
        setRunning(!document.hidden && r.bottom > 0);
      });
    });
  }

  /* ---------- Scroll reveal ---------- */
  var io = null;
  function initReveal(scope) {
    var els = $$('[data-reveal], .footer__giant', scope).filter(function (el) { return once(el, 'Reveal'); });
    if (!animate || !('IntersectionObserver' in window)) {
      els.forEach(function (el) { el.classList.add('is-in'); });
      return;
    }
    // A fully clipped element never counts as intersecting, so clip reveals watch their parent.
    var target = function (el) { return el.dataset.reveal === 'clip' ? el.parentElement : el; };
    if (!io) {
      io = new IntersectionObserver(function (entries) {
        entries.forEach(function (en) {
          if (!en.isIntersecting) return;
          (en.target._pwReveal || []).forEach(function (el) {
            if (el.classList.contains('is-in')) return;
            if (el.dataset.delay) el.style.transitionDelay = el.dataset.delay + 'ms';
            el.classList.add('is-in');
            setTimeout(function () { el.style.transitionDelay = ''; }, (+el.dataset.delay || 0) + 1400);
          });
          io.unobserve(en.target);
        });
      }, { threshold: 0.14, rootMargin: '0px 0px -6% 0px' });
    }
    els.forEach(function (el) {
      var t = target(el);
      (t._pwReveal = t._pwReveal || []).push(el);
      io.observe(t);
    });
  }

  /* ---------- Counters ---------- */
  function runCounter(el) {
    var to = +el.dataset.count, from = +(el.dataset.from || 0), dur = 1800, t0 = null;
    var step = function (t) {
      if (!t0) t0 = t;
      var p = Math.min((t - t0) / dur, 1);
      el.textContent = Math.round(from + (to - from) * (1 - Math.pow(1 - p, 3)));
      if (p < 1) requestAnimationFrame(step);
    };
    requestAnimationFrame(step);
  }
  function initCounters(scope) {
    var els = $$('[data-count]', scope).filter(function (el) { return once(el, 'Count'); });
    if (!animate || !('IntersectionObserver' in window)) {
      els.forEach(function (el) { el.textContent = el.dataset.count; });
      return;
    }
    var cio = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (!en.isIntersecting) return;
        runCounter(en.target);
        cio.unobserve(en.target);
      });
    }, { threshold: 0.6 });
    els.forEach(function (el) { el.textContent = el.dataset.from || 0; cio.observe(el); });
  }

  /* ---------- Back to top links ---------- */
  function initTopLinks(scope) {
    $$('[data-pw-top]', scope).forEach(function (a) {
      if (!once(a, 'Top')) return;
      a.addEventListener('click', function (e) {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: animate ? 'smooth' : 'auto' });
      });
    });
  }

  /* ---------- Elementor forms: phone/email keyboards & autofill ---------- */
  function initForms(scope) {
    var hints = { name: ['text', 'name'], phone: ['tel', 'tel'], email: ['email', 'email'] };
    $$('.pw-form form, form.elementor-form', scope).forEach(function (form) {
      if (!once(form, 'Form')) return;
      Object.keys(hints).forEach(function (key) {
        var input = form.querySelector('[name="form_fields[' + key + ']"]');
        if (!input) return;
        input.setAttribute('inputmode', hints[key][0]);
        input.setAttribute('autocomplete', hints[key][1]);
      });
    });
  }

  /* ---------- Scroll-driven effects (single rAF loop) ---------- */
  var parallaxEls = [], spinEls = [], ticking = false, lastY = window.scrollY;

  function collectScrollEls() {
    parallaxEls = animate ? $$('[data-parallax]') : [];
    spinEls = animate ? $$('[data-spin]') : [];
  }

  function onScroll() {
    var y = window.scrollY;
    var vh = window.innerHeight;
    var menuOpen = megaItems.some(function (m) { return m.classList.contains('is-open'); });

    if (header) {
      header.classList.toggle('is-scrolled', y > 40);
      if (CONFIG.hideOnScroll && y > 400 && y > lastY + 4 && !menuOpen) header.classList.add('is-hidden');
      else if (y < lastY - 4 || y <= 400) header.classList.remove('is-hidden');
    }
    lastY = y;

    parallaxEls.forEach(function (img) {
      var box = img.parentElement.getBoundingClientRect();
      if (box.bottom < -100 || box.top > vh + 100) return;
      var offset = (box.top + box.height / 2) - vh / 2;
      img.style.setProperty('--py', (-offset * parseFloat(img.dataset.parallax)).toFixed(1) + 'px');
    });
    spinEls.forEach(function (el) {
      el.style.transform = 'rotate(' + (y * parseFloat(el.dataset.spin)).toFixed(1) + 'deg)';
    });

    var toTop = document.querySelector('[data-pw-totop]');
    if (toTop) {
      var max = document.documentElement.scrollHeight - vh;
      toTop.style.setProperty('--dash', (132 * (1 - (max > 0 ? y / max : 0))).toFixed(1));
      toTop.classList.toggle('is-visible', y > 700);
    }
    var bar = document.querySelector('[data-pw-actionbar]');
    if (bar) {
      var hero = document.querySelector('.pw .hero, .pw .page-hero');
      bar.classList.toggle('is-visible', y > (hero ? hero.offsetHeight * 0.55 : 300));
    }
    ticking = false;
  }

  window.addEventListener('scroll', function () {
    if (!ticking) { ticking = true; requestAnimationFrame(onScroll); }
  }, { passive: true });
  window.addEventListener('resize', onScroll);

  /* ---------- Boot ---------- */
  function init(scope) {
    scope = scope || document;
    initHeader(scope);
    initHero(scope);
    initReveal(scope);
    initCounters(scope);
    initTopLinks(scope);
    initForms(scope);
    collectScrollEls();
    onScroll();
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', function () { init(); });
  else init();

  // Elementor editor: widgets are re-rendered one by one, so initialise each as it appears.
  window.addEventListener('elementor/frontend/init', function () {
    if (!window.elementorFrontend || !window.elementorFrontend.hooks) return;
    window.elementorFrontend.hooks.addAction('frontend/element_ready/global', function ($scope) {
      var el = $scope && $scope[0];
      if (el && el.querySelector('.pw')) init(el);
    });
  });
})();
