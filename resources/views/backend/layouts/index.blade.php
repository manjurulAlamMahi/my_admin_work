@extends('backend.master')

@section('page_title', 'Dashboard')

@section('content')

    {{-- ===================== MAIN ROW ===================== --}}
    <div class="row g-4">

        {{-- Welcome Card --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-body p-4 d-flex flex-column justify-content-center position-relative"
                    style="min-height: 200px;">

                    {{-- Decorative circles --}}
                    <div class="position-absolute top-0 end-0 opacity-25"
                        style="width:220px;height:220px;border-radius:50%;background:var(--bs-primary);transform:translate(60px,-60px);pointer-events:none;"></div>
                    <div class="position-absolute bottom-0 end-0 opacity-10"
                        style="width:120px;height:120px;border-radius:50%;background:var(--bs-primary);transform:translate(30px,30px);pointer-events:none;"></div>

                    <div class="position-relative">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <img src="{{ asset(Auth::user()->avatar == 'user.png' ? 'admin.png' : Auth::user()->avatar) }}"
                                class="rounded-circle border border-3 border-white shadow"
                                width="56" height="56"
                                style="object-fit:cover;" />
                            <div>
                                <h3 class="fw-bold mb-0" id="greeting-text">Hello,</h3>
                                <h3 class="fw-bold mb-0 text-primary">{{ Auth::user()->name }} 👋</h3>
                            </div>
                        </div>
                        <p class="text-muted mb-0 fs-md" id="dashboard-quote" style="max-width:480px;"></p>
                    </div>

                </div>
            </div>
        </div>

        {{-- Right column: Clock + Calendar stacked --}}
        <div class="col-lg-4 d-flex flex-column gap-4">

            {{-- Live Clock Card --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex flex-column align-items-center justify-content-center p-4 text-center">
                    <p class="text-muted mb-1 fs-sm text-uppercase fw-semibold" id="clock-day-date"></p>
                    <div class="fw-bold text-primary" id="clock-time"
                        style="font-size:2.4rem;letter-spacing:3px;font-variant-numeric:tabular-nums;line-height:1;">
                        00:00:00
                    </div>
                    <p class="text-muted mt-1 mb-0 fs-xs" id="clock-ampm" style="letter-spacing:2px;"></p>
                </div>
            </div>

            {{-- Calendar Card --}}
            <div class="card border-0 shadow-sm">

                <div class="card-header bg-transparent border-bottom-0 pt-3 pb-0 px-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <button class="btn btn-sm btn-ghost-primary px-2" id="cal-prev" title="Previous month">
                            <i class="ti ti-chevron-left fs-lg"></i>
                        </button>
                        <h6 class="fw-bold mb-0" id="cal-month-year"></h6>
                        <button class="btn btn-sm btn-ghost-primary px-2" id="cal-next" title="Next month">
                            <i class="ti ti-chevron-right fs-lg"></i>
                        </button>
                    </div>
                </div>

                <div class="card-body px-3 pb-3 pt-2">
                    <div class="d-grid mb-1" style="grid-template-columns:repeat(7,1fr);gap:2px;" id="cal-day-headers"></div>
                    <div class="d-grid" style="grid-template-columns:repeat(7,1fr);gap:2px;" id="cal-grid"></div>
                    <div class="text-center mt-2">
                        <button class="btn btn-xs btn-outline-primary px-3" id="cal-today-btn" style="font-size:11px;">
                            Today
                        </button>
                    </div>
                </div>

            </div>

        </div>

    </div>

@endsection

@push('scripts')
<script>
(function () {

    // ── QUOTES ───────────────────────────────────────────────────────────────
    const quotes = [
        "What's on the agenda today? Let's make it count.",
        "Every great day starts with a clear focus. You've got this!",
        "Ready to crush it? Your dashboard is standing by.",
        "Small progress is still progress. Keep moving forward.",
        "The best time to start is now. Let's get things done!",
        "Your work today shapes tomorrow. Make it meaningful.",
        "Stay consistent, stay focused — results follow.",
        "Another day, another chance to build something great.",
        "Great things never come from comfort zones. Push forward!",
        "You're one decision away from a completely different outcome.",
    ];

    // ── GREETING ─────────────────────────────────────────────────────────────
    function getGreeting() {
        const h = new Date().getHours();
        if (h >= 5  && h < 12) return 'Good Morning,';
        if (h >= 12 && h < 17) return 'Good Afternoon,';
        if (h >= 17 && h < 21) return 'Good Evening,';
        return 'Good Night,';
    }

    document.getElementById('greeting-text').textContent = getGreeting();
    document.getElementById('dashboard-quote').textContent =
        quotes[Math.floor(Math.random() * quotes.length)];

    // ── LIVE CLOCK ────────────────────────────────────────────────────────────
    const days   = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

    function pad(n) { return String(n).padStart(2, '0'); }

    function tickClock() {
        const now  = new Date();
        let   h    = now.getHours();
        const m    = now.getMinutes();
        const s    = now.getSeconds();
        const ampm = h >= 12 ? 'PM' : 'AM';
        const h12  = h % 12 || 12;

        document.getElementById('clock-time').textContent =
            pad(h12) + ':' + pad(m) + ':' + pad(s);
        document.getElementById('clock-ampm').textContent = ampm;
        document.getElementById('clock-day-date').textContent =
            days[now.getDay()] + ', ' + months[now.getMonth()] + ' ' + now.getDate() + ' ' + now.getFullYear();
    }
    tickClock();
    setInterval(tickClock, 1000);

    // ── MINI CALENDAR ─────────────────────────────────────────────────────────
    const today   = new Date();
    let   current = new Date(today.getFullYear(), today.getMonth(), 1);

    const dayNames = ['Su','Mo','Tu','We','Th','Fr','Sa'];

    // Build day-header row once
    const headerGrid = document.getElementById('cal-day-headers');
    dayNames.forEach(function(d, i) {
        const cell = document.createElement('div');
        cell.textContent = d;
        cell.className   = 'text-center fw-semibold text-muted';
        cell.style.cssText = 'font-size:10px;padding:4px 0;' + (i === 0 || i === 6 ? 'color:var(--bs-danger)!important;' : '');
        headerGrid.appendChild(cell);
    });

    function renderCalendar() {
        const grid  = document.getElementById('cal-grid');
        grid.innerHTML = '';

        const year  = current.getFullYear();
        const month = current.getMonth();

        document.getElementById('cal-month-year').textContent =
            months[month] + ' ' + year;

        const firstDay   = new Date(year, month, 1).getDay();
        const totalDays  = new Date(year, month + 1, 0).getDate();
        const isThisMonth = year === today.getFullYear() && month === today.getMonth();

        // Empty cells before first day
        for (let i = 0; i < firstDay; i++) {
            grid.appendChild(emptyCell());
        }

        for (let d = 1; d <= totalDays; d++) {
            const isToday    = isThisMonth && d === today.getDate();
            const isWeekend  = (new Date(year, month, d).getDay() % 6 === 0);
            const cell       = document.createElement('div');
            cell.textContent = d;
            cell.style.cssText =
                'text-align:center;padding:5px 2px;font-size:12px;border-radius:6px;cursor:default;' +
                'font-weight:' + (isToday ? '700' : '400') + ';' +
                'transition:background .15s;';

            if (isToday) {
                cell.style.background   = 'var(--bs-primary)';
                cell.style.color        = '#fff';
            } else if (isWeekend) {
                cell.style.color = 'var(--bs-danger)';
                cell.style.opacity = '0.7';
            }

            cell.addEventListener('mouseover', function () {
                if (!isToday) this.style.background = 'var(--bs-light)';
            });
            cell.addEventListener('mouseout', function () {
                if (!isToday) this.style.background = '';
            });

            grid.appendChild(cell);
        }
    }

    function emptyCell() {
        const c = document.createElement('div');
        return c;
    }

    document.getElementById('cal-prev').addEventListener('click', function () {
        current = new Date(current.getFullYear(), current.getMonth() - 1, 1);
        renderCalendar();
    });
    document.getElementById('cal-next').addEventListener('click', function () {
        current = new Date(current.getFullYear(), current.getMonth() + 1, 1);
        renderCalendar();
    });
    document.getElementById('cal-today-btn').addEventListener('click', function () {
        current = new Date(today.getFullYear(), today.getMonth(), 1);
        renderCalendar();
    });

    renderCalendar();

})();
</script>
@endpush
