<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'RB Fitness')</title>
  <meta name="description"
    content="Join RB Fitness at Atmiya Complex, Gandevi, Navsari. World-class equipment, expert trainers, and a community that elevates your fitness." />

  <meta property="og:title" content="RB Fitness" />
  <meta property="og:description"
    content="Join RB Fitness at Atmiya Complex, Gandevi, Navsari. World-class equipment, expert trainers, and a community that elevates your fitness." />
  <meta property="og:type" content="website" />
  <link rel="icon" type="image/png" href="{{ asset('assets/fevicon.png') }}">
  <link rel="apple-touch-icon" href="{{ asset('assets/fevicon.png') }}">
  <link rel="shortcut icon" href="{{ asset('assets/fevicon.png') }}" type="image/x-icon">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
  @stack('styles')
</head>

<body>
  <div id="scroll-progress" style="position: fixed; top: 0; left: 0; height: 3px; background: linear-gradient(90deg, #FFD700, #B8860B); width: 0%; z-index: 1000; transition: width 0.1s ease-out;"></div>


  <!-- NAVBAR -->
  <header id="navbar">
    <div class="nav-inner">
      <a href="#home" class="nav-logo" onclick="smoothScroll('#home');return false;">
        <img src="{{ asset('assets/logo.png') }}" alt="RB FITNESS Logo" class="nav-logo-img">
      </a>

      <ul class="nav-links">
        <li><button onclick="smoothScroll('#home')">Home</button></li>
        <li><button onclick="smoothScroll('#about')">About</button></li>
        <li><button onclick="smoothScroll('#facilities')">Facilities</button></li>
        <li><button onclick="smoothScroll('#trainers')">Trainers</button></li>
        <li><button onclick="smoothScroll('#plans')">Plans</button></li>
        <li><button onclick="smoothScroll('#contact')">Contact</button></li>
      </ul>

      <button class="nav-burger" id="burger-btn" aria-label="Toggle menu">
        <svg id="icon-menu" viewBox="0 0 24 24">
          <line x1="3" y1="6" x2="21" y2="6" />
          <line x1="3" y1="12" x2="21" y2="12" />
          <line x1="3" y1="18" x2="21" y2="18" />
        </svg>
        <svg id="icon-x" viewBox="0 0 24 24" style="display:none;">
          <line x1="18" y1="6" x2="6" y2="18" />
          <line x1="6" y1="6" x2="18" y2="18" />
        </svg>
      </button>
    </div>

    <!-- Mobile menu -->
    <div id="mobile-menu">
      <nav>
        <button onclick="mobileNav('#home')">Home</button>
        <button onclick="mobileNav('#about')">About</button>
        <button onclick="mobileNav('#facilities')">Facilities</button>
        <button onclick="mobileNav('#trainers')">Trainers</button>
        <button onclick="mobileNav('#plans')">Plans</button>
        <button onclick="mobileNav('#contact')">Contact</button>
      </nav>
    </div>
  </header>

  <main>
    @yield('content')
  </main>

  <!-- FOOTER -->
  <footer>
    <div class="footer-inner">
      <div class="footer-logo">
        <img src="{{ asset('assets/logo.png') }}" alt="RB FITNESS Logo" class="footer-logo-img">
      </div>

      <div class="footer-socials">
        <a href="https://www.instagram.com/rbfitness__2488?igsh=MW9kbTZjamF0OXFw" target="_blank"
          rel="noopener noreferrer" aria-label="Instagram">
          <svg viewBox="0 0 24 24">
            <rect x="2" y="2" width="20" height="20" rx="5" ry="5" />
            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" />
            <line x1="17.5" y1="6.5" x2="17.51" y2="6.5" />
          </svg>
        </a>
        <a href="https://www.facebook.com/rbfitness2488" target="_blank" rel="noopener noreferrer"
          aria-label="Facebook">
          <svg viewBox="0 0 24 24">
            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" />
          </svg>
        </a>
      </div>

      <p class="footer-copy" id="footer-year">© {{ date('Y') }} RB Fitness. All rights reserved.</p>
    </div>
  </footer>

  <script>
    function redirectToWhatsApp(planName) {
      const settings = @json($settings ?? []);
      const phone = settings['whatsapp_number'] || '919173082488';
      const message = `Hi RB Fitness! I'm interested in the ${planName}. Can you provide more details?`;
      window.open(`https://wa.me/${phone}?text=${encodeURIComponent(message)}`, '_blank');
    }

    /* smooth scroll */
    function smoothScroll(selector) {
      const el = document.querySelector(selector);
      if (el) {
        if (selector === '#home') {
          window.scrollTo({ top: 0, behavior: 'smooth' });
        } else {
          el.scrollIntoView({ behavior: 'smooth' });
        }
      }
    }

    /* mobile nav */
    function mobileNav(selector) {
      smoothScroll(selector);
      closeMobileMenu();
    }

    function closeMobileMenu() {
      const menu = document.getElementById('mobile-menu');
      if(menu) menu.classList.remove('open');
      const menuIcon = document.getElementById('icon-menu');
      const xIcon = document.getElementById('icon-x');
      if(menuIcon) menuIcon.style.display = '';
      if(xIcon) xIcon.style.display = 'none';
    }

    const burgerBtn = document.getElementById('burger-btn');
    if(burgerBtn) {
      burgerBtn.addEventListener('click', function () {
        const menu = document.getElementById('mobile-menu');
        if(!menu) return;
        const isOpen = menu.classList.toggle('open');
        document.getElementById('icon-menu').style.display = isOpen ? 'none' : '';
        document.getElementById('icon-x').style.display = isOpen ? '' : 'none';
      });
    }

    /* navbar scroll effect */
    window.addEventListener('scroll', function () {
      const navbar = document.getElementById('navbar');
      if (navbar) {
        if (window.scrollY > 50) {
          navbar.classList.add('scrolled');
        } else {
          navbar.classList.remove('scrolled');
        }
      }
    }, { passive: true });

    /* scroll reveal */
    (function () {
      var revealEls = document.querySelectorAll('.reveal, .reveal-left, .reveal-right');
      
      function showAll() {
        revealEls.forEach(function (el) { el.classList.add('visible'); });
      }

      if (!('IntersectionObserver' in window)) {
        showAll();
        return;
      }

      var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            observer.unobserve(entry.target);
          }
        });
      }, { 
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
      });

      revealEls.forEach(function (el) { observer.observe(el); });

      // Fail-safe: if after 2 seconds elements aren't visible, show them
      setTimeout(showAll, 2000);
    })();

    /* Scroll Progress */
    window.addEventListener('scroll', () => {
      const scrollProgress = document.getElementById('scroll-progress');
      if(scrollProgress) {
        const scrollTotal = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const scrollPercent = (window.scrollY / scrollTotal) * 100;
        scrollProgress.style.width = scrollPercent + '%';
      }
    });
  </script>
  @stack('scripts')
</body>
</html>
