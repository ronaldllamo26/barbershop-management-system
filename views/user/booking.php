<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!defined('BASE_PATH')) define('BASE_PATH', '/bg-barbershop/');
$isLoggedIn    = !empty($_SESSION['customer_id']);
$customerName  = $_SESSION['customer_name']  ?? '';
$customerEmail = $_SESSION['customer_email'] ?? '';
$customerPhone = ''; // pre-fill if we have it
if ($isLoggedIn) {
    require_once __DIR__ . '/../../config/db.php';
    $cid = intval($_SESSION['customer_id']);
    $cres = $conn->query("SELECT phone FROM customers WHERE id=$cid LIMIT 1");
    if ($cres) { $crow = $cres->fetch_assoc(); $customerPhone = $crow['phone'] ?? ''; }
}
$pageTitle = 'Book Appointment';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';
?>

<section class="page-hero page-hero-sm">
  <div class="page-hero-overlay"></div>
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center page-hero-content">
        <span class="sec-label">Reserve Your Slot</span>
        <div class="divider center"></div>
        <h1 class="page-hero-title">Book an <em style="color:var(--gold)">Appointment</em></h1>
        <p class="page-hero-sub">Pick your services, choose your barber, select your time</p>
      </div>
    </div>
  </div>
</section>

<section class="sec-pad bg-light-sec">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-10">

        <!-- Step Indicators -->
        <div class="booking-steps mb-5">
          <div class="bstep active" id="si-1"><div class="bstep-num">1</div><div class="bstep-label">Services</div></div>
          <div class="bstep-line"></div>
          <div class="bstep" id="si-2"><div class="bstep-num">2</div><div class="bstep-label">Barber</div></div>
          <div class="bstep-line"></div>
          <div class="bstep" id="si-3"><div class="bstep-num">3</div><div class="bstep-label">Schedule</div></div>
          <div class="bstep-line"></div>
          <div class="bstep" id="si-4"><div class="bstep-num">4</div><div class="bstep-label">Your Info</div></div>
        </div>

        <form id="bookingForm" novalidate>

          <!-- ══ STEP 1: Services ══ -->
          <div class="booking-step-panel" id="step-1">
            <div class="booking-card">
              <h3 class="booking-card-title">Choose Your Services</h3>
              <p class="booking-card-sub">Select one or more services. You can pick from different categories.</p>

              <?php
              // conflict_group: services in the same group cannot be selected together
              // 'hair' = Haircut & Style, Kids, Packages all conflict with each other
              // 'package' = Packages conflict with ALL other groups too
              $categories = [
                'Haircut & Style' => [
                  'conflict_group' => 'hair',
                  'note'           => 'Pick 1 — conflicts with Kids & Packages',
                  'items'          => [
                    ['Regular Haircut',   '₱350', 30],
                    ['Skin Fade',         '₱400', 35],
                    ['Textured Crop',     '₱400', 35],
                    ['Haircut + Blowdry', '₱500', 45],
                  ],
                ],
                'Shave' => [
                  'conflict_group' => 'shave',
                  'note'           => 'Pick 1',
                  'items'          => [
                    ['Hot Towel Shave', '₱450', 30],
                    ['Clean Shave',     '₱250', 20],
                  ],
                ],
                'Beard' => [
                  'conflict_group' => 'beard',
                  'note'           => 'Pick 1',
                  'items'          => [
                    ['Beard Trim & Shape',   '₱250', 20],
                    ['Beard Trim + Line Up', '₱350', 30],
                  ],
                ],
                'Treatment' => [
                  'conflict_group' => 'treatment',
                  'note'           => 'Pick 1',
                  'items'          => [
                    ['Hot Oil Treatment',       '₱350', 30],
                    ['Scalp Massage',           '₱300', 20],
                    ['Anti-Dandruff Treatment', '₱400', 30],
                  ],
                ],
                'Kids' => [
                    'conflict_group' => 'kids',
                    'note'           => 'Pick 1 — conflicts with Haircut, Beard, Shave & Packages',
                  'items'          => [
                    ['Kids Haircut (12 & below)', '₱250', 25],
                  ],
                ],
                'Packages' => [
                  'conflict_group' => 'package',
                  'note'           => 'All-in-one — cannot combine with other services',
                  'items'          => [
                    ['BG Classic Package',  '₱750',  55],
                    ['BG Premium Package',  '₱999',  90],
                    ['BG Glow-Up Package',  '₱900',  75],
                  ],
                ],
              ];
              foreach ($categories as $catName => $catData): ?>
              <div class="svc-pick-group" data-group="<?= $catData['conflict_group'] ?>">
                <div class="svc-pick-cat">
                  <?= $catName ?>
                  <span class="svc-pick-hint">— <?= $catData['note'] ?></span>
                </div>
                <div class="row g-3">
                  <?php foreach ($catData['items'] as $svc): ?>
                  <div class="col-md-6 col-lg-4">
                    <label class="svc-pick-card">
                      <input type="checkbox" name="services[]"
                        class="svc-checkbox"
                        data-category="<?= htmlspecialchars($catName) ?>"
                        data-conflict-group="<?= $catData['conflict_group'] ?>"
                        value="<?= htmlspecialchars($svc[0]) ?>"
                        data-price="<?= $svc[1] ?>"
                        data-duration="<?= $svc[2] ?>">
                      <div class="svc-pick-inner">
                        <span class="svc-pick-name"><?= $svc[0] ?></span>
                        <div class="svc-pick-meta">
                          <span class="svc-pick-price"><?= $svc[1] ?></span>
                          <span class="svc-pick-dur"><i class="far fa-clock"></i> <?= $svc[2] ?> mins</span>
                        </div>
                      </div>
                    </label>
                  </div>
                  <?php endforeach; ?>
                </div>
              </div>
              <?php endforeach; ?>

              <!-- Selected summary -->
              <div class="selected-services-bar" id="selectedBar" style="display:none;">
                <div class="ssb-label">Selected:</div>
                <div class="ssb-items" id="selectedList"></div>
                <div class="ssb-total">Total: <strong id="totalPrice">₱0</strong> · <strong id="totalDur">0 mins</strong></div>
              </div>

              <div class="booking-nav mt-4">
                <div></div>
                <button type="button" class="btn-gold" onclick="nextStep(1)">
                  Next: Choose Barber <i class="fas fa-arrow-right ms-2"></i>
                </button>
              </div>
            </div>
          </div>

          <!-- ══ STEP 2: Barber ══ -->
          <div class="booking-step-panel d-none" id="step-2">
            <div class="booking-card">
              <h3 class="booking-card-title">Choose Your Barber</h3>
              <p class="booking-card-sub">Select who you want — availability is checked automatically.</p>

              <div class="row g-3" id="barberGrid">
                <?php
                if (!isset($conn)) require_once __DIR__ . '/../../config/db.php';
                $barbersRes = $conn->query("SELECT * FROM barbers WHERE is_active=1 ORDER BY sort_order, id");
                while ($b = $barbersRes->fetch_assoc()):
                  $bid    = $b['id'];
                  $bname  = htmlspecialchars($b['first_name'] . ' ' . $b['last_name']);
                  $brole  = htmlspecialchars($b['role_title']);
                  $bphoto = htmlspecialchars($b['photo'] ?? '');
                ?>
                <div class="col-6 col-md-3">
                  <label class="barber-pick-card" data-barber-id="<?= $bid ?>">
                    <input type="radio" name="barber" value="<?= $bname ?>" data-id="<?= $bid ?>">
                    <div class="barber-pick-inner">
                      <?php if ($bphoto): ?>
                      <img src="<?= $bphoto ?>" alt="<?= $bname ?>">
                      <?php else: ?>
                      <div style="width:90px;height:90px;border-radius:50%;background:var(--light-2);margin:0 auto 14px;display:flex;align-items:center;justify-content:center;color:var(--gray);font-size:2rem;">
                        <i class="fas fa-user-tie"></i>
                      </div>
                      <?php endif; ?>
                      <h5><?= $bname ?></h5>
                      <span><?= $brole ?></span>
                      <div class="barber-availability" id="avail-<?= $bid ?>"></div>
                    </div>
                  </label>
                </div>
                <?php endwhile; ?>
                <div class="col-6 col-md-3">
                  <label class="barber-pick-card">
                    <input type="radio" name="barber" value="No Preference" data-id="0" checked>
                    <div class="barber-pick-inner barber-any">
                      <div class="barber-any-icon"><i class="fas fa-random"></i></div>
                      <h5>No Preference</h5>
                      <span>Any available barber</span>
                    </div>
                  </label>
                </div>
              </div>

              <div class="booking-nav mt-4">
                <button type="button" class="btn-outline-dark" onclick="prevStep(2)">
                  <i class="fas fa-arrow-left me-2"></i> Back
                </button>
                <button type="button" class="btn-gold" onclick="nextStep(2)">
                  Next: Schedule <i class="fas fa-arrow-right ms-2"></i>
                </button>
              </div>
            </div>
          </div>

          <!-- ══ STEP 3: Schedule ══ -->
          <div class="booking-step-panel d-none" id="step-3">
            <div class="booking-card">
              <h3 class="booking-card-title">Pick Your Schedule</h3>
              <p class="booking-card-sub">Choose your date — then pick from available time slots.</p>

              <div class="row g-4">
                <div class="col-md-5">
                  <label class="form-label-dark">Preferred Date</label>
                  <input type="date" id="appt-date" name="date" class="input-dark" required>
                  <p class="mt-2" style="font-size:.78rem;color:var(--gray)">
                    <i class="fas fa-info-circle me-1" style="color:var(--gold-d)"></i>
                    Booked slots will be grayed out automatically.
                  </p>
                </div>
                <div class="col-md-7">
                  <label class="form-label-dark">Available Time Slots</label>
                  <div id="timeSlotWrap">
                    <p style="color:var(--gray);font-size:.85rem;">Select a date first.</p>
                  </div>
                </div>
              </div>

              <div class="booking-nav mt-4">
                <button type="button" class="btn-outline-dark" onclick="prevStep(3)">
                  <i class="fas fa-arrow-left me-2"></i> Back
                </button>
                <button type="button" class="btn-gold" onclick="nextStep(3)">
                  Next: Your Info <i class="fas fa-arrow-right ms-2"></i>
                </button>
              </div>
            </div>
          </div>

          <!-- ══ STEP 4: Info ══ -->
          <div class="booking-step-panel d-none" id="step-4">
            <div class="booking-card">
              <h3 class="booking-card-title">Your Information</h3>
              <p class="booking-card-sub">Almost done! Fill in your details to confirm.</p>

              <div class="booking-summary mb-4" id="bookingSummary"></div>

              <?php if ($isLoggedIn): ?>
              <!-- LOGGED IN: show pre-filled info -->
              <div class="logged-in-banner mb-4">
                <div class="lib-left">
                  <div class="lib-avatar"><i class="fas fa-user-check"></i></div>
                  <div>
                    <strong><?= htmlspecialchars($customerName) ?></strong>
                    <span><?= htmlspecialchars($customerEmail) ?></span>
                  </div>
                </div>
                <a href="/bg-barbershop/views/user/logout.php" class="lib-logout">
                  <i class="fas fa-sign-out-alt me-1"></i>Not you?
                </a>
              </div>
              <!-- Hidden fields pre-filled from session -->
              <input type="hidden" name="first_name" value="<?= htmlspecialchars(explode(' ', $customerName)[0]) ?>">
              <input type="hidden" name="last_name"  value="<?= htmlspecialchars(implode(' ', array_slice(explode(' ', $customerName), 1))) ?>">
              <input type="hidden" name="email"      value="<?= htmlspecialchars($customerEmail) ?>">
              <input type="hidden" name="customer_id" value="<?= intval($_SESSION['customer_id']) ?>">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label-dark">Phone / Viber *</label>
                  <input type="tel" name="phone" class="input-dark"
                         placeholder="+63 9XX XXX XXXX"
                         value="<?= htmlspecialchars($customerPhone) ?>" required>
                </div>
                <div class="col-12">
                  <label class="form-label-dark">Notes (optional)</label>
                  <textarea name="notes" class="input-dark" rows="3" placeholder="Any special requests..."></textarea>
                </div>
              </div>

              <?php else: ?>
              <!-- GUEST: login prompt + guest form -->
              <div class="guest-login-prompt mb-4">
                <div class="glp-inner">
                  <div class="glp-left">
                    <i class="fas fa-user-circle"></i>
                    <div>
                      <strong>Already have an account?</strong>
                      <span>Login to auto-fill your info and track this booking.</span>
                    </div>
                  </div>
                  <a href="/bg-barbershop/views/user/login.php?redirect=<?= urlencode('/bg-barbershop/views/user/booking.php') ?>"
                     class="btn-black" style="white-space:nowrap;">
                    <i class="fas fa-sign-in-alt me-1"></i> Login
                  </a>
                </div>
                <div class="glp-divider"><span>or continue as guest</span></div>
              </div>

              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label-dark">First Name *</label>
                  <input type="text" name="first_name" class="input-dark" placeholder="Juan" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label-dark">Last Name *</label>
                  <input type="text" name="last_name" class="input-dark" placeholder="Dela Cruz" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label-dark">Phone / Viber *</label>
                  <input type="tel" name="phone" class="input-dark" placeholder="+63 9XX XXX XXXX" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label-dark">Email (optional)</label>
                  <input type="email" name="email" class="input-dark" placeholder="juan@email.com">
                </div>
                <div class="col-12">
                  <label class="form-label-dark">Notes (optional)</label>
                  <textarea name="notes" class="input-dark" rows="3" placeholder="Any special requests..."></textarea>
                </div>
              </div>
              <?php endif; ?>

              <div class="booking-nav mt-4">
                <button type="button" class="btn-outline-dark" onclick="prevStep(4)">
                  <i class="fas fa-arrow-left me-2"></i> Back
                </button>
                <button type="submit" class="btn-gold" id="submitBtn">
                  <i class="fas fa-calendar-check"></i> Confirm Booking
                </button>
              </div>
            </div>
          </div>

          <!-- ══ SUCCESS ══ -->
          <div class="booking-step-panel d-none" id="step-success">
            <div class="booking-card text-center py-5">
              <div class="success-icon mb-4"><i class="fas fa-check-circle"></i></div>
              <h2 class="sec-title mb-3">Booking <em style="color:var(--gold-d)">Confirmed!</em></h2>
              <p class="sec-sub mx-auto mb-4">
                Salamat! Your appointment has been received.<br>
                We'll reach out to confirm your slot shortly.
              </p>
              <p class="booking-ref mb-4" id="refNumber"></p>
              <?php if (!$isLoggedIn): ?>
              <div class="success-register-prompt mb-4">
                <p>Want to track this booking?</p>
                <a href="/bg-barbershop/views/user/register.php" class="btn-black me-2">
                  <i class="fas fa-user-plus me-1"></i> Create Account
                </a>
              </div>
              <?php else: ?>
              <a href="/bg-barbershop/views/user/my_bookings.php" class="btn-black me-2">
                <i class="fas fa-list me-1"></i> View My Bookings
              </a>
              <?php endif; ?>
              <a href="/bg-barbershop/index.php" class="btn-outline-dark">Back to Home</a>
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>
</section>

<script>
const BASE = '<?= BASE_PATH ?>';
let currentStep = 1;
const bk = { services: [], barber: 'No Preference', barberId: 0, date: '', time: '' };

// ── Conflict group rules ──
// 'hair'    : Haircut & Style, Kids — conflict with each other
// 'package' : Packages — conflict with EVERYTHING
// 'shave','beard','treatment' : only conflict within own category
const CONFLICT_MAP = {
  hair:      ['hair', 'kids', 'package'],
  kids:      ['hair', 'kids', 'shave', 'beard', 'package'],
  package:   ['hair', 'kids', 'shave', 'beard', 'treatment', 'package'],
  shave:     ['shave', 'kids', 'package'],
  beard:     ['beard', 'kids', 'package'],
  treatment: ['treatment', 'package'],
};

document.querySelectorAll('.svc-checkbox').forEach(cb => {
  cb.addEventListener('change', function() {
    const group = this.dataset.conflictGroup;
    const cat   = this.dataset.category;

    if (this.checked) {
      const conflicts = CONFLICT_MAP[group] || [group];

      // Uncheck all conflicting checkboxes
      document.querySelectorAll('.svc-checkbox').forEach(o => {
        if (o === this) return;
        const oGroup = o.dataset.conflictGroup;
        // Same category = always uncheck (max 1 per category)
        // Conflicting group = also uncheck
        if (o.dataset.category === cat || conflicts.includes(oGroup)) {
          o.checked = false;
          o.closest('label').querySelector('.svc-pick-inner').classList.remove('selected');
        }
      });

      // Dim (disable visually) conflicting options + mark group header
      document.querySelectorAll('.svc-checkbox').forEach(o => {
        if (o === this) return;
        const oGroup = o.dataset.conflictGroup;
        const card   = o.closest('label');
        if (o.dataset.category === cat || conflicts.includes(oGroup)) {
          card.classList.add('svc-conflict-dim');
        } else {
          card.classList.remove('svc-conflict-dim');
        }
      });
      // Mark conflicting group containers
      document.querySelectorAll('.svc-pick-group').forEach(grp => {
        const grpGroup = grp.dataset.group;
        const isSameCat = [...grp.querySelectorAll('.svc-checkbox')].some(o => o.dataset.category === cat && o !== this);
        if ((isSameCat || conflicts.includes(grpGroup)) && grpGroup !== group) {
          grp.classList.add('has-conflict');
        } else {
          grp.classList.remove('has-conflict');
        }
      });

      this.closest('label').querySelector('.svc-pick-inner').classList.add('selected');
    } else {
      this.closest('label').querySelector('.svc-pick-inner').classList.remove('selected');
      // Re-evaluate dims after uncheck
      document.querySelectorAll('.svc-pick-card').forEach(c => c.classList.remove('svc-conflict-dim'));
      document.querySelectorAll('.svc-pick-group').forEach(g => g.classList.remove('has-conflict'));
      const remaining = [...document.querySelectorAll('.svc-checkbox:checked')];
      remaining.forEach(checked => {
        const cGroup    = checked.dataset.conflictGroup;
        const cCat      = checked.dataset.category;
        const conflicts = CONFLICT_MAP[cGroup] || [cGroup];
        document.querySelectorAll('.svc-checkbox').forEach(o => {
          if (o === checked) return;
          if (o.dataset.category === cCat || conflicts.includes(o.dataset.conflictGroup)) {
            o.closest('label').classList.add('svc-conflict-dim');
          }
        });
        document.querySelectorAll('.svc-pick-group').forEach(grp => {
          const grpGroup = grp.dataset.group;
          const isSameCat = [...grp.querySelectorAll('.svc-checkbox')].some(o => o.dataset.category === cCat && o !== checked);
          if ((isSameCat || conflicts.includes(grpGroup)) && grpGroup !== cGroup) {
            grp.classList.add('has-conflict');
          }
        });
      });
    }
    updateSelectedBar();
  });
});

function updateSelectedBar() {
  const checked = [...document.querySelectorAll('.svc-checkbox:checked')];
  const bar = document.getElementById('selectedBar');
  const list = document.getElementById('selectedList');
  const totalPriceEl = document.getElementById('totalPrice');
  const totalDurEl = document.getElementById('totalDur');

  if (checked.length === 0) { bar.style.display = 'none'; return; }
  bar.style.display = 'flex';

  let totalPrice = 0, totalDur = 0;
  list.innerHTML = checked.map(cb => {
    const price = parseInt(cb.dataset.price.replace(/[^\d]/g, ''));
    totalPrice += price;
    totalDur   += parseInt(cb.dataset.duration);
    return `<span class="ssb-tag">${cb.value} <em>${cb.dataset.price}</em></span>`;
  }).join('');

  totalPriceEl.textContent = '₱' + totalPrice.toLocaleString();
  totalDurEl.textContent   = totalDur + ' mins';
}

// ── Date input ──
const dateInput = document.getElementById('appt-date');
const today = new Date().toISOString().split('T')[0];
dateInput.setAttribute('min', today);
dateInput.addEventListener('change', async function() {
  bk.date = this.value;
  bk.time = '';
  await loadTimeSlots();
  if (bk.barberId > 0) await checkBarberAvailability(bk.barberId, bk.date);
});

// ── Load time slots & check conflicts ──
async function loadTimeSlots() {
  const wrap = document.getElementById('timeSlotWrap');
  wrap.innerHTML = '<p style="color:var(--gray);font-size:.85rem;"><i class="fas fa-spinner fa-spin me-2"></i>Loading slots...</p>';

  let bookedSlots = [];
  let blockedRanges = [];
  let dayBlocked = false;

  if (bk.barberId > 0 && bk.date) {
    try {
      const res = await fetch(`${BASE}api/get_slots.php?barber_id=${bk.barberId}&date=${bk.date}`);
      const data = await res.json();
      bookedSlots    = data.booked         || [];
      blockedRanges  = data.blocked_ranges || [];
      dayBlocked     = data.blocked        || false;
    } catch(e) { bookedSlots = []; }
  }

  wrap.innerHTML = '';

  // Full day blocked
  if (dayBlocked) {
    wrap.innerHTML = `<div style="background:#fee2e2;border-left:4px solid #ef4444;color:#b91c1c;padding:14px 16px;font-size:.85rem;">
      <i class="fas fa-ban me-2"></i><strong>This day is fully blocked.</strong> Please choose another date.
    </div>`;
    return;
  }

  function isInBlockedRange(time) {
    return blockedRanges.some(r => time >= r.start && time < r.end);
  }

  const slots = [];
  for (let h = 9; h < 20; h++) {
    slots.push(`${String(h).padStart(2,'0')}:00`);
    if (h < 19) slots.push(`${String(h).padStart(2,'0')}:30`);
  }

  const container = document.createElement('div');
  container.className = 'time-slots';

  slots.forEach(time => {
    const isBooked  = bookedSlots.includes(time);
    const isBlocked = isInBlockedRange(time);
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'time-slot' + (isBooked || isBlocked ? ' booked' : '');
    btn.textContent = fmt12(time);
    btn.dataset.value = time;
    if (isBooked) {
      btn.disabled = true;
      btn.title = 'This slot is already booked';
    } else if (isBlocked) {
      btn.disabled = true;
      btn.title = 'This slot is blocked';
      btn.style.background = '#f1f5f9';
    } else {
      btn.addEventListener('click', () => {
        document.querySelectorAll('.time-slot').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        bk.time = time;
      });
    }
    container.appendChild(btn);
  });
  wrap.appendChild(container);
}

// ── Check barber availability on date ──
async function checkBarberAvailability(barberId, date) {
  if (!date || barberId === 0) return;
  try {
    const res = await fetch(`${BASE}api/get_slots.php?barber_id=${barberId}&date=${date}&summary=1`);
    const data = await res.json();
    const el = document.getElementById(`avail-${barberId}`);
    if (el) {
      const count = (data.booked || []).length;
      el.innerHTML = count > 0
        ? `<span class="avail-badge busy">${count} slot${count>1?'s':''} booked</span>`
        : `<span class="avail-badge free">Available</span>`;
    }
  } catch(e) {}
}

// ── Barber selection ──
document.querySelectorAll('input[name="barber"]').forEach(r => {
  r.addEventListener('change', async function() {
    bk.barber   = this.value;
    bk.barberId = parseInt(this.dataset.id) || 0;
    if (bk.date && bk.barberId > 0) await loadTimeSlots();
    if (bk.date && bk.barberId > 0) await checkBarberAvailability(bk.barberId, bk.date);
  });
});

function fmt12(t) {
  const [h, m] = t.split(':').map(Number);
  return `${h%12||12}:${String(m).padStart(2,'0')} ${h>=12?'PM':'AM'}`;
}

// ── Step Navigation ──
function nextStep(from) {
  if (from === 1) {
    const checked = [...document.querySelectorAll('.svc-checkbox:checked')];
    if (checked.length === 0) { showAlert(1, 'Please select at least one service.'); return; }
    bk.services = checked.map(cb => ({ name: cb.value, price: cb.dataset.price, duration: parseInt(cb.dataset.duration) }));
  }
  if (from === 2) {
    const sel = document.querySelector('input[name="barber"]:checked');
    bk.barber   = sel ? sel.value : 'No Preference';
    bk.barberId = sel ? parseInt(sel.dataset.id) : 0;
  }
  if (from === 3) {
    const todayCheck = new Date().toISOString().split('T')[0];
    const maxCheck = new Date();
    maxCheck.setDate(maxCheck.getDate() + 14);
    const maxCheckStr = maxCheck.toISOString().split('T')[0];
    if (!bk.date) { showAlert(3, 'Please select a date.'); return; }
    if (bk.date < todayCheck) { showAlert(3, 'Cannot book past dates!'); return; }
    if (bk.date > maxCheckStr) { showAlert(3, 'Cannot book more than 14 days in advance!'); return; }
    if (!bk.time) { showAlert(3, 'Please select a time slot.'); return; }
    buildSummary();
}
  goToStep(from + 1);
}

function prevStep(from) { goToStep(from - 1); }

function goToStep(n) {
  document.getElementById(`step-${currentStep}`).classList.add('d-none');
  document.getElementById(`step-${n}`).classList.remove('d-none');
  document.querySelectorAll('.bstep').forEach((el, i) => {
    el.classList.remove('active','done');
    if (i+1 < n) el.classList.add('done');
    if (i+1 === n) el.classList.add('active');
  });
  currentStep = n;
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function buildSummary() {
  const dateObj = new Date(bk.date + 'T00:00:00');
  const dateStr = dateObj.toLocaleDateString('en-PH',{weekday:'long',year:'numeric',month:'long',day:'numeric'});
  const svcList = bk.services.map(s => `${s.name} (${s.price})`).join(', ');
  const total   = bk.services.reduce((a,s)=>a+parseInt(s.price.replace(/[^\d]/g,'')),0);
  const dur     = bk.services.reduce((a,s)=>a+s.duration,0);
  document.getElementById('bookingSummary').innerHTML = `
    <div class="summary-title">Booking Summary</div>
    <div class="summary-row"><span>Services</span><strong>${svcList}</strong></div>
    <div class="summary-row"><span>Total Price</span><strong>₱${total.toLocaleString()}</strong></div>
    <div class="summary-row"><span>Est. Duration</span><strong>${dur} mins</strong></div>
    <div class="summary-row"><span>Barber</span><strong>${bk.barber}</strong></div>
    <div class="summary-row"><span>Date</span><strong>${dateStr}</strong></div>
    <div class="summary-row"><span>Time</span><strong>${fmt12(bk.time)}</strong></div>
  `;
}

function showAlert(step, msg) {
  document.querySelectorAll('.booking-alert').forEach(a => a.remove());
  const a = document.createElement('div');
  a.className = 'booking-alert';
  a.innerHTML = `<i class="fas fa-exclamation-circle me-2"></i>${msg}`;
  document.getElementById(`step-${step}`).querySelector('.booking-card').prepend(a);
  setTimeout(() => a.remove(), 4000);
}

// ── Submit ──
document.getElementById('bookingForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const fd = new FormData(this);
  // Get reCAPTCHA token
  let recaptchaToken = '';
  try {
    await new Promise(resolve => grecaptcha.ready(resolve));
    recaptchaToken = await grecaptcha.execute('6Lef9plsAAAADlpJ9LESMH2t4rZE_qiuDK6P9lT', {action: 'booking'});
  } catch(e) { recaptchaToken = ''; }

  const payload = {
    ...bk,
    first_name:      fd.get('first_name'),
    last_name:       fd.get('last_name'),
    phone:           fd.get('phone'),
    email:           fd.get('email') || '',
    notes:           fd.get('notes') || '',
    recaptcha_token: recaptchaToken,
  };
  if (!payload.first_name || !payload.last_name || !payload.phone) {
    showAlert(4, 'Please fill in all required fields.'); return;
  }
  const btn = document.getElementById('submitBtn');
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

  try {
    const res  = await fetch(`${BASE}api/book_appointment.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    });
    if (!res.ok) throw new Error('HTTP ' + res.status);
    const data = await res.json();
    if (data.success) {
      // Redirect to confirmation page
      window.location.href = '/bg-barbershop/views/user/booking_confirmation.php?ref=' + data.reference_no;
    } else {
      showAlert(4, data.message || 'Something went wrong. Please try again.');
      btn.disabled = false;
      btn.innerHTML = '<i class="fas fa-calendar-check me-2"></i>Confirm Booking';
    }
  } catch(err) {
    showAlert(4, 'Could not connect to server. Make sure XAMPP is running and the database is set up.');
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-calendar-check me-2"></i>Confirm Booking';
  }
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>