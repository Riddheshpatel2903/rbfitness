@extends('frontend.layout')

@section('content')
  <!-- HERO SECTION -->
  <section id="home">
    <video id="hero-bg-video" autoplay muted loop playsinline preload="auto">
      <source id="hero-video-src" src="{{ asset('assets/gym_bg.mp4') }}" type="video/mp4" />
    </video>
    <div class="hero-overlay"></div>
    <div class="hero-content">
      <div class="reveal">
        <h1 class="hero-title">
          <span class="white">ELEVATE YOUR</span><br />
          <span class="red">FITNESS</span>
        </h1>
        <div class="hero-cta">
          <a href="#contact" class="btn-primary" onclick="smoothScroll('#contact');return false;">START TODAY</a>
        </div>
      </div>
      <div class="hero-stats-bar reveal" style="transition-delay:0.5s;">
        <div class="stats-inner">
          <div class="stats-grid">
            <div>
              <div class="stat-value">500+</div>
              <div class="stat-label">Members</div>
            </div>
            <div>
              <div class="stat-value">15+</div>
              <div class="stat-label">Trainers</div>
            </div>
            <div>
              <div class="stat-value">50+</div>
              <div class="stat-label">Equipment</div>
            </div>
            <div>
              <div class="stat-value">7 Days</div>
              <div class="stat-label">Open</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ABOUT SECTION -->
  <section id="about">
    <div class="section-inner">
      <div class="section-header reveal">
        <h2 class="section-title">MORE THAN A <span class="red">GYM</span></h2>
        <p class="section-subtitle">
          RB Fitness is Gandevi's most complete fitness destination. Located in the heart of Atmiya Complex, we offer
          world-class equipment, expert trainers, and a community that pushes you to be your best — every single day.
        </p>
      </div>
      <div class="highlights-grid">
        <div class="highlight-card reveal reveal-delay-1">
          <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="6" /><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11" /></svg>
          <p class="highlight-label">Expert Coaching</p>
        </div>
        <div class="highlight-card reveal reveal-delay-2">
          <svg viewBox="0 0 24 24"><path d="M6.5 6.5h11M6.5 17.5h11M3 9.5h2.5v5H3zM18.5 9.5H21v5h-2.5z" /><rect x="5.5" y="8.5" width="1.5" height="7" rx="0.5" /><rect x="17" y="8.5" width="1.5" height="7" rx="0.5" /><line x1="7" y1="12" x2="17" y2="12" /></svg>
          <p class="highlight-label">Modern Equipment</p>
        </div>
        <div class="highlight-card reveal reveal-delay-2">
          <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" /><circle cx="9" cy="7" r="4" /><path d="M23 21v-2a4 4 0 0 0-3-3.87" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /></svg>
          <p class="highlight-label">All Fitness Levels</p>
        </div>
        <div class="highlight-card reveal reveal-delay-3">
          <svg viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18" /><polyline points="17 6 23 6 23 12" /></svg>
          <p class="highlight-label">Proven Results</p>
        </div>
      </div>
    </div>
  </section>

  <!-- FACILITIES SECTION -->
  <section id="facilities">
    <div class="section-inner-wide">
      <div class="section-header reveal">
        <h2 class="section-title">OUR <span class="red">FACILITIES</span></h2>
      </div>
      <div class="facilities-grid">
        @forelse($facilities as $index => $facility)
        <div class="facility-card reveal reveal-delay-{{ ($index % 3) + 1 }}">
          @if(Str::endsWith($facility->image, ['.mp4', '.mov', '.MOV']))
            <video autoplay muted loop playsinline preload="auto">
              <source src="{{ asset('storage/' . $facility->image) }}" type="video/mp4" />
            </video>
          @else
            <img src="{{ asset('storage/' . $facility->image) }}" alt="{{ $facility->title }}" loading="lazy" />
          @endif
          <div class="facility-glass-overlay"></div>
          <div class="facility-inner-border"></div>
          <div class="facility-top-blur"></div>
          <span id="facilities_name">{{ $facility->title }}</span>
        </div>
        @empty
          <div class="reveal" style="grid-column: 1 / -1; text-align: center; color: rgba(255,255,255,0.5);">
            No facilities data found in database.
          </div>
        @endforelse
      </div>
    </div>
  </section>

  <!-- TRAINING VIDEOS SECTION -->
  <section id="training">
    <div class="section-inner-wide">
      <div class="section-header reveal">
        <h2 class="section-title">TRAINING IN <span class="red">ACTION</span></h2>
      </div>
      <div class="videos-grid">
        <div class="video-card reveal reveal-delay-1">
          <div class="video-wrapper">
            <video autoplay muted loop playsinline preload="auto">
              <source src="{{ asset('assets/video1.mp4') }}" type="video/mp4" />
            </video>
          </div>
          <p class="video-label">Strength Training</p>
        </div>
        <div class="video-card reveal reveal-delay-2">
          <div class="video-wrapper">
            <video autoplay muted loop playsinline preload="auto">
              <source src="{{ asset('assets/video-cardio.mp4') }}" type="video/mp4" />
            </video>
          </div>
          <p class="video-label">Cardio Blast</p>
        </div>
        <div class="video-card reveal reveal-delay-3">
          <div class="video-wrapper">
            <video autoplay muted loop playsinline preload="auto">
              <source src="{{ asset('assets/video3.mp4') }}" type="video/mp4" />
            </video>
          </div>
          <p class="video-label">Free Weights</p>
        </div>
      </div>
    </div>
  </section>

  <!-- TRAINERS SECTION -->
  <section id="trainers">
    <div class="section-inner">
      <div class="section-header reveal">
        <h2 class="section-title">MEET YOUR <span class="red">TRAINER</span></h2>
      </div>
      <div class="trainers-grid">
        @foreach($trainers as $trainer)
        <div class="trainer-card reveal">
          <div class="trainer-photo">
            <img src="{{ $trainer->image ? asset('storage/' . $trainer->image) : asset('assets/TRAINER.JPEG') }}" alt="{{ $trainer->name }}" loading="lazy" />
          </div>
          <div class="trainer-info">
            <h3 class="trainer-name">{{ $trainer->name }}</h3>
            <p class="trainer-role">{{ $trainer->specialization }}</p>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </section>

  <!-- PLANS SECTION -->
  <section id="plans">
    <div class="section-inner">
      <div class="section-header reveal">
        <h2 class="section-title">MEMBERSHIP <span class="red">PLANS</span></h2>
        <p class="section-subtitle">Invest in your health with our flexible membership options.</p>
      </div>
      <div class="plans-tabs">
        @foreach($categories as $index => $category)
        <button class="tab-btn {{ $index == 0 ? 'active' : '' }}" onclick="loadPlans('{{ $category->slug }}', this)">
          {{ $category->name }}
        </button>
        @endforeach
      </div>

      <div id="plans-wrapper" class="plans-container">
        @if($categories->count() > 0)
          @include('frontend.partials.plans_list', ['category' => $categories->first()])
        @else
          <div style="text-align: center; color: rgba(255,255,255,0.5); padding: 4rem;">No categories found.</div>
        @endif
      </div>
    </div>
  </section>

  <!-- CONTACT SECTION -->
  <section id="contact">
    <div class="section-inner">
      <div class="section-header reveal">
        <h2 class="section-title">GET IN <span class="red">TOUCH</span></h2>
      </div>
      <div class="contact-grid">
        <form class="contact-form reveal-left" onsubmit="handleContactSubmit(event)">
          <div><input id="name" name="name" type="text" placeholder="Your Name" required /></div>
          <div><input id="phone" name="phone" type="tel" placeholder="Your Phone" required /></div>
          <div><textarea id="message" name="message" placeholder="Your Message" rows="4" required></textarea></div>
          <button type="submit" class="btn-primary" style="width:100%;font-size:1.125rem;padding:1rem;border-radius:9999px;letter-spacing:0.1em;">SEND MESSAGE</button>
        </form>
        <div class="contact-info reveal-right">
          <div class="contact-info-item">
            <svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" /><circle cx="12" cy="10" r="3" /></svg>
            <div>
              <h4 class="contact-info-label">Address</h4>
              <p class="contact-info-text">{!! nl2br(e($settings['contact_address'] ?? 'Atmiya Complex, Gandevi, Navsari, Gujarat - 396360')) !!}</p>
            </div>
          </div>
          <div class="contact-info-item">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /><polyline points="12 6 12 12 16 14" /></svg>
            <div>
              <h4 class="contact-info-label">Hours</h4>
              <div class="hours-grid">
                <div class="hours-row">
                  <span class="hours-day">Morning</span>
                  <span class="hours-time">{{ $settings['hours_morning'] ?? '5:30 AM – 11:00 AM' }}</span>
                </div>
                <div class="hours-row">
                  <span class="hours-day">Evening</span>
                  <span class="hours-time">{{ $settings['hours_evening'] ?? '4:00 PM – 8:30 PM' }}</span>
                </div>
                <div class="hours-row sun">
                  <span class="hours-day">Sunday</span>
                  <span class="hours-time">{{ $settings['hours_sun'] ?? 'OFF ( GYM CLOSED )' }}</span>
                </div>
              </div>
            </div>
          </div>
          <div class="contact-info-item">
            <svg viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 1.27h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L7.91 8.93a16 16 0 0 0 6 6l.95-.95a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" /></svg>
            <div>
              <h4 class="contact-info-label">Phone</h4>
              <p class="contact-info-text">{{ $settings['contact_phone'] ?? '+91 91730 82488' }}</p>
            </div>
          </div>
        </div>
        <div class="map-container">
          <iframe title="RB Fitness location" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3729.31029557831!2d72.99385637635147!3d20.819172794942055!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3be0f31d4d10f019%3A0x2929a64c20ebbecd!2sRBFitness!5e0!3m2!1sen!2sin!4v1774346954690!5m2!1sen!2sin" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
      </div>
    </div>
  </section>
@endsection

@push('scripts')
<script>
    /* Hero video update */
    (function () {
      var src = document.getElementById('hero-video-src');
      var vid = document.getElementById('hero-bg-video');
      function updateHeroVideo() {
        var newSrc = '{{ asset("assets/gym_bg.mp4") }}';
        if (src.getAttribute('src') !== newSrc) {
          src.setAttribute('src', newSrc);
          vid.load();
          vid.play().catch(function () { });
        }
        vid.style.transform = 'translate(-50%, -50%)';
      }
      updateHeroVideo();
      window.addEventListener('resize', updateHeroVideo, { passive: true });
    })();

    /* AJAX Plan Loading */
    function loadPlans(slug, btn) {
      const wrapper = document.getElementById('plans-wrapper');
      
      // Update active tab
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      // Loading state
      wrapper.style.opacity = '0.5';
      wrapper.style.pointerEvents = 'none';

      fetch(`/plans/category/${slug}`)
        .then(response => response.text())
        .then(html => {
          wrapper.innerHTML = html;
          wrapper.style.opacity = '1';
          wrapper.style.pointerEvents = 'all';
          
          // Add animation
          const grid = wrapper.querySelector('.plans-grid');
          if (grid) {
            grid.classList.add('fade-in');
          }
        })
        .catch(error => {
          console.error('Error loading plans:', error);
          wrapper.style.opacity = '1';
          wrapper.style.pointerEvents = 'all';
        });
    }

    /* WhatsApp redirect */
    function redirectToWhatsApp(planName) {
      var phone = '919173082488';
      var message = 'Hello RB Fitness, I am interested in the ' + planName + ' plan. Please provide more details.';
      var url = 'https://wa.me/' + phone + '?text=' + encodeURIComponent(message);
      window.open(url, '_blank');
    }

    function handleContactSubmit(event) {
      event.preventDefault();
      var name = document.getElementById('name').value;
      var phone = document.getElementById('phone').value;
      var message = document.getElementById('message').value;
      var whatsappMessage = "Hello RB Fitness,\n" + "My Name: " + name + "\n" + "Phone: " + phone + "\n" + "Message: " + message;
      var phoneNum = '919173082488';
      var url = 'https://wa.me/' + phoneNum + '?text=' + encodeURIComponent(whatsappMessage);
      window.open(url, '_blank');
    }
</script>
@endpush
