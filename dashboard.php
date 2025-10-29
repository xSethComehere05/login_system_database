<?php
// dashboard.php (View)

// 1. เรียกใช้ไฟล์ process เพื่อดึงข้อมูลและฟังก์ชันที่จำเป็น
require_once 'csrf.php'; 
require_once 'dashboard_process.php';

?>

<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Retail DW Dashboard (Live)</title>

    <!-- Favicon รูปกล่อง -->
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2360a5fa' class='bi bi-box' viewBox='0 0 16 16'%3E%3Cpath d='M8.186 1.113a.5.5 0 0 0-.372 0L1.814 4.267a.5.5 0 0 0-.223.434V11.3a.5.5 0 0 0 .223.434l6 3.15a.5.5 0 0 0 .372 0l6-3.15a.5.5 0 0 0 .223-.434V4.7a.5.5 0 0 0-.223-.434L8.186 1.113zM14.5 4.673l-5.5 2.895L3.5 4.673m-3 0V11.3l5.5 2.894L11.5 11.3V4.673L8 6.273 3.5 4.673z'/%3E%3C/svg%3E">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <meta name="csrf-token" content="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

    <style>
        /* --- CSS Variables --- */
        :root {
            --bg-main: #0f172a; --card-bg: #1e293b; --border-color: #334155;
            --text-primary: #e2e8f0; --text-secondary: #94a3b8; --text-heading: #f1f5f9; --kpi-text: #f8fafc; /* Dark mode texts */
            --link-color: #60a5fa; --chart-grid: rgba(255, 255, 255, 0.1); --chart-ticks: #cbd5e1;
            --danger-color: #f87171; --danger-hover-bg: #ef4444; --danger-active-bg: #dc2626;
            --kpi-icon-color: rgba(255, 255, 255, 0.05); /* Color for faint KPI icon */
             /* Chart Colors (Dark) */
             --chart-bar-bg: #3b82f6; --chart-line-border: #3b82f6; --chart-line-bg: rgba(59, 130, 246, 0.3);
             --chart-pie-bg-1: #3b82f6; --chart-pie-bg-2: #ef4444; --chart-pie-bg-3: #10b981; --chart-pie-bg-4: #ea580c; --chart-pie-bg-5: #8b5cf6;
             --chart-line-new: #10b981; --chart-line-return: #ef4444; --chart-bar-top: #10b981; --chart-bar-hourly: #ea580c;
        }
        .light-mode {
            --bg-main: #f8fafc; --card-bg: #ffffff; --border-color: #e2e8f0;
            --text-primary: #334155; --text-secondary: #64748b; --text-heading: #0f172a; --kpi-text: #0f172a; /* Light mode texts (FIXED) */
            --link-color: #2563eb; --chart-grid: rgba(0, 0, 0, 0.1); --chart-ticks: #475569;
            --danger-color: #dc2626; --danger-hover-bg: #b91c1c; --danger-active-bg: #991b1b;
            --kpi-icon-color: rgba(0, 0, 0, 0.04); /* Color for faint KPI icon in light mode */
             /* Chart Colors (Light) */
             --chart-bar-bg: #2563eb; --chart-line-border: #2563eb; --chart-line-bg: rgba(37, 99, 235, 0.2);
             --chart-pie-bg-1: #2563eb; --chart-pie-bg-2: #dc2626; --chart-pie-bg-3: #059669; --chart-pie-bg-4: #ea580c; --chart-pie-bg-5: #7c3aed;
             --chart-line-new: #059669; --chart-line-return: #dc2626; --chart-bar-top: #059669; --chart-bar-hourly: #ea580c;
        }

        /* --- General Styles --- */
        body { background-color: var(--bg-main); color: var(--text-primary); font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; padding: 1.5rem; transition: background-color 0.3s ease, color 0.3s ease; }
        .container-fluid { max-width: 1400px; margin: 0 auto; }
        h2 { color: var(--text-heading); font-weight: 600; margin-bottom: 0.25rem; }
        .sub { color: var(--text-secondary); font-size: 0.9rem; }
        .card {
            background-color: var(--card-bg); border: 1px solid var(--border-color);
            border-radius: 0.75rem; padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: background-color 0.3s ease, border-color 0.3s ease;
            display: flex; flex-direction: column;
            position: relative; /* [!] Needed for icon positioning */
            overflow: hidden;   /* [!] Keep icon inside card */
        }
        .card h5 { color: var(--text-heading); margin-bottom: 1rem; font-size: 1.1rem; font-weight: 500; position: relative; z-index: 1; /* Keep text above icon */ }
        .kpi {
            font-size: 1.75rem; font-weight: 700; color: var(--kpi-text);
            word-wrap: break-word; white-space: normal; flex-grow: 1;
            display: flex; align-items: center; justify-content: center; text-align: center;
            position: relative; z-index: 1; /* Keep text above icon */
        }

        /* --- KPI Layout --- */
        .kpi-grid { display: flex; flex-wrap: wrap; gap: 1.5rem; margin-bottom: 1.5rem; }
        .kpi-grid .card { flex: 1 1 0; min-width: 250px; text-align: center; }
        .kpi-grid .card h5 { margin-bottom: 0.5rem; }

        /* --- [!] KPI Background Icon --- */
        .kpi-card::after {
            content: '';
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            font-family: 'bootstrap-icons'; /* Use Bootstrap Icons font */
            font-size: 4.5rem; /* Adjust size */
            color: var(--kpi-icon-color); /* Use variable for faint color */
            line-height: 1;
            z-index: 0; /* Behind text */
            transition: color 0.3s ease;
        }
        .kpi-sales::after { content: '\F227'; } /* bi-cash-coin */
        .kpi-qty::after { content: '\F1C8'; }    /* bi-box-seam */
        .kpi-buyers::after { content: '\F4DA'; } /* bi-people-fill */


        /* --- Charts Layout --- */
        .charts-grid { display: flex; flex-wrap: wrap; gap: 1.5rem; }
        .charts-grid .card { flex: 1 1 calc(50% - 0.75rem); min-width: 300px; margin-bottom: 0; }
        .charts-grid .chart-large { flex: 2 1 calc(66.66% - 0.75rem); min-width: 400px; }
        .charts-grid .chart-small { flex: 1 1 calc(33.33% - 1rem); min-width: 250px; }
        .charts-grid .chart-full-width { flex-basis: 100%; }
        @media (max-width: 991px) { .charts-grid .card { flex-basis: calc(50% - 0.75rem); } .charts-grid .chart-large, .charts-grid .chart-small { flex-basis: calc(50% - 0.75rem); } }
        @media (max-width: 767px) { body { padding: 1rem; } h2 { font-size: 1.5rem; } .kpi { font-size: 1.5rem; } .kpi-grid .card { min-width: 100%; } .charts-grid .card { flex-basis: 100%; min-width: unset; } .header-actions { margin-top: 1rem; } .welcome-text { display: none; } }
        canvas { max-height: 350px; width: 100% !important; }

        /* --- Header & Dropdown --- */
        .header-actions { display: flex; align-items: center; }
        .welcome-text { color: var(--text-secondary); margin-right: 1.5rem; white-space: nowrap; }
        .dropdown-toggle::after { display: none; }
        .btn-icon { background: none; border: 1px solid var(--border-color); color: var(--text-secondary); padding: 0.5rem 0.8rem; border-radius: 0.5rem; cursor: pointer; transition: background-color 0.2s ease, color 0.2s ease; }
        .btn-icon:hover { background-color: var(--card-bg); color: var(--text-primary); }
        .btn-icon i { font-size: 1.2rem; vertical-align: middle; line-height: 1; }
        .dropdown-menu { background-color: var(--card-bg); border-color: var(--border-color); border-radius: 0.5rem; padding: 0.5rem 0; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05); --bs-dropdown-link-color: var(--text-primary); --bs-dropdown-link-hover-color: var(--text-heading); --bs-dropdown-link-hover-bg: rgba(255, 255, 255, 0.05); --bs-dropdown-link-active-color: var(--text-heading); --bs-dropdown-link-active-bg: rgba(255, 255, 255, 0.1); min-width: 10rem; }
        .light-mode .dropdown-menu { --bs-dropdown-link-color: var(--text-primary); --bs-dropdown-link-hover-color: var(--text-heading); --bs-dropdown-link-hover-bg: rgba(0, 0, 0, 0.05); --bs-dropdown-link-active-color: var(--text-heading); --bs-dropdown-link-active-bg: rgba(0, 0, 0, 0.1); }
        .dropdown-item { padding: 0.6rem 1.2rem; display: flex; align-items: center; }
        .dropdown-item i { margin-right: 0.75rem; width: 1.2em; text-align: center; color: var(--text-secondary); }
        .dropdown-divider { border-top: 1px solid var(--border-color); }
        .theme-toggle-btn { background: none; border: none; width: 100%; text-align: left; }
    </style>
</head>
<body class="p-3 p-md-4">
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap">
            <div>
                <h2>ยอดขาย (Retail DW) — Dashboard</h2>
                <span class="sub">แหล่งข้อมูล: MySQL (Live Data)</span>
            </div>
            <div class="header-actions">
                 <span class="welcome-text">
                     สวัสดี, <?= htmlspecialchars($display_name, ENT_QUOTES, 'UTF-8') ?>
                 </span>
                 <div class="dropdown">
                     <button class="btn btn-icon dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" title="Menu">
                         <i class="bi bi-list"></i>
                     </button>
                     <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                         <li>
                             <button id="theme-toggle" class="dropdown-item theme-toggle-btn" type="button">
                                 <i id="theme-icon-light" class="bi bi-sun-fill" style="display: none;"></i>
                                 <i id="theme-icon-dark" class="bi bi-moon-stars-fill"></i>
                                 <span>สลับโหมด</span>
                             </button>
                         </li>
                         <li><hr class="dropdown-divider"></li>
                         <li>
                             <a class="dropdown-item" href="logout.php">
                                 <i class="bi bi-box-arrow-right"></i>
                                 ออกจากระบบ
                             </a>
                         </li>
                     </ul>
                 </div>
            </div>
        </div>

        <!-- KPI Grid -->
        <div class="kpi-grid">
            <!-- [!] Add kpi-card and specific icon class -->
            <div class="card kpi-card kpi-sales"><h5>ยอดขาย 30 วัน</h5><div class="kpi">฿<?= nf($kpi['sales_30d'] ?? 0) ?></div></div>
            <div class="card kpi-card kpi-qty"><h5>จำนวนชิ้นขาย 30 วัน</h5><div class="kpi"><?= number_format((int)($kpi['qty_30d'] ?? 0)) ?> ชิ้น</div></div>
            <div class="card kpi-card kpi-buyers"><h5>จำนวนผู้ซื้อ 30 วัน</h5><div class="kpi"><?= number_format((int)($kpi['buyers_30d'] ?? 0)) ?> คน</div></div>
        </div>

        <!-- Charts grid -->
        <div class="charts-grid">
            <div class="card chart-large"> <h5 class="mb-2">ยอดขายรายเดือน (2 ปี)</h5><canvas id="chartMonthly"></canvas></div>
            <div class="card chart-small"> <h5 class="mb-2">สัดส่วนยอดขายตามหมวด</h5><canvas id="chartCategory"></canvas></div>
            <div class="card"> <h5 class="mb-2">Top 10 สินค้าขายดี</h5><canvas id="chartTopProducts"></canvas></div>
            <div class="card"> <h5 class="mb-2">ยอดขายตามภูมิภาค</h5><canvas id="chartRegion"></canvas></div>
            <div class="card"> <h5 class="mb-2">วิธีการชำระเงิน</h5><canvas id="chartPayment"></canvas></div>
            <div class="card"> <h5 class="mb-2">ยอดขายรายชั่วโมง</h5><canvas id="chartHourly"></canvas></div>
            <div class="card chart-full-width"> <h5 class="mb-2">ลูกค้าใหม่ vs ลูกค้าเดิม (90 วันล่าสุด)</h5><canvas id="chartNewReturning"></canvas></div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// --- ส่วน JavaScript (เหมือนเดิม) ---
const monthly = <?= json_encode($monthly ?? [], JSON_UNESCAPED_UNICODE) ?>;
const category = <?= json_encode($category ?? [], JSON_UNESCAPED_UNICODE) ?>;
const region = <?= json_encode($region ?? [], JSON_UNESCAPED_UNICODE) ?>;
const topProducts = <?= json_encode($topProducts ?? [], JSON_UNESCAPED_UNICODE) ?>;
const payment = <?= json_encode($payment ?? [], JSON_UNESCAPED_UNICODE) ?>;
const hourly = <?= json_encode($hourly ?? [], JSON_UNESCAPED_UNICODE) ?>;
const newReturning = <?= json_encode($newReturning ?? [], JSON_UNESCAPED_UNICODE) ?>;

// Theme Toggle Logic
const themeToggleBtn = document.getElementById('theme-toggle');
const bodyElement = document.body;
const lightIcon = document.getElementById('theme-icon-light');
const darkIcon = document.getElementById('theme-icon-dark');
let currentTheme = localStorage.getItem('theme') || 'dark';

const applyTheme = (theme) => {
    if (theme === 'light') {
        bodyElement.classList.add('light-mode');
        lightIcon.style.display = 'inline-block'; darkIcon.style.display = 'none';
    } else {
        bodyElement.classList.remove('light-mode');
        lightIcon.style.display = 'none'; darkIcon.style.display = 'inline-block';
    }
    setTimeout(() => updateChartDefaults(theme), 50);
};

const updateChartDefaults = (theme) => {
    const computedStyle = getComputedStyle(document.documentElement);
    const getCssVar = (varName, defaultValue) => computedStyle.getPropertyValue(varName)?.trim() || defaultValue;
    Chart.defaults.color = getCssVar('--text-primary', '#e2e8f0');
    Chart.defaults.borderColor = getCssVar('--chart-grid', 'rgba(255, 255, 255, 0.1)');
    Chart.defaults.plugins.legend.labels.color = getCssVar('--text-heading', '#f1f5f9');
    Chart.defaults.scale.ticks.color = getCssVar('--chart-ticks', '#cbd5e1');
    Chart.defaults.scale.grid.color = getCssVar('--chart-grid', 'rgba(255, 255, 255, 0.1)');

     Object.values(window.myCharts || {}).forEach(chart => {
         if (chart && typeof chart.update === 'function') {
             try {
                 if(chart.options.scales) {
                     if(chart.options.scales.x) { chart.options.scales.x.ticks.color = Chart.defaults.scale.ticks.color; chart.options.scales.x.grid.color = Chart.defaults.scale.grid.color; }
                     if(chart.options.scales.y) { chart.options.scales.y.ticks.color = Chart.defaults.scale.ticks.color; chart.options.scales.y.grid.color = Chart.defaults.scale.grid.color; }
                 }
                 if(chart.options.plugins && chart.options.plugins.legend) { chart.options.plugins.legend.labels.color = Chart.defaults.plugins.legend.labels.color; }
                 const colors = getChartColors();
                 chart.data.datasets.forEach((dataset, index) => {
                     if (chart.config.type === 'line') {
                         if (dataset.label.includes('ลูกค้าใหม่')) dataset.borderColor = colors.lineNew;
                         else if (dataset.label.includes('ลูกค้าเดิม')) dataset.borderColor = colors.lineReturn;
                         else {
                            dataset.borderColor = colors.lineBorder;
                            dataset.backgroundColor = colors.lineBg;
                         }
                     }
                     else if (chart.config.type === 'bar') {
                         if (chart.canvas.id === 'chartTopProducts') dataset.backgroundColor = colors.barTop;
                         else if (chart.canvas.id === 'chartHourly') dataset.backgroundColor = colors.barHourly;
                         else if (chart.canvas.id === 'chartRegion') dataset.backgroundColor = colors.pieBg.slice(0, chart.data.labels.length);
                         else dataset.backgroundColor = colors.barBg;
                     }
                     else if (chart.config.type === 'doughnut' || chart.config.type === 'pie') {
                         dataset.backgroundColor = colors.pieBg.slice(0, chart.data.labels.length);
                         dataset.borderColor = colors.cardBg;
                         dataset.borderWidth = 2;
                     }
                 });
                 chart.update('none');
             } catch (e) { console.error("Error updating chart:", chart.canvas.id, e); }
         }
     });
};
applyTheme(currentTheme);
themeToggleBtn.addEventListener('click', () => {
    currentTheme = bodyElement.classList.contains('light-mode') ? 'dark' : 'light';
    localStorage.setItem('theme', currentTheme);
    applyTheme(currentTheme);
});

// Chart.js Initialization
window.myCharts = {};
const toXY = (arr, x, y) => {
    if (!Array.isArray(arr)) return { labels: [], values: [] };
    return { labels: arr.map(o => o?.[x]), values: arr.map(o => parseFloat(o?.[y] || 0)) };
};
const getStandardScales = () => ({ x: { grid: { color: Chart.defaults.scale.grid.color } }, y: { grid: { color: Chart.defaults.scale.grid.color } } });
const getLineScales = () => ({ x: { ticks: { maxTicksLimit: 10 }, grid: { color: Chart.defaults.scale.grid.color } }, y: { grid: { color: Chart.defaults.scale.grid.color } } });
const getChartColors = () => { const computedStyle = getComputedStyle(document.documentElement); const getCssVar = (varName, defaultValue) => computedStyle.getPropertyValue(varName)?.trim() || defaultValue; return { barBg: getCssVar('--chart-bar-bg', '#3b82f6'), lineBorder: getCssVar('--chart-line-border', '#3b82f6'), lineBg: getCssVar('--chart-line-bg', 'rgba(59, 130, 246, 0.3)'), pieBg: [ getCssVar('--chart-pie-bg-1', '#3b82f6'), getCssVar('--chart-pie-bg-2', '#ef4444'), getCssVar('--chart-pie-bg-3', '#10b981'), getCssVar('--chart-pie-bg-4', '#f9716'), getCssVar('--chart-pie-bg-5', '#8b5cf6') ], lineNew: getCssVar('--chart-line-new', '#10b981'), lineReturn: getCssVar('--chart-line-return', '#ef4444'), barTop: getCssVar('--chart-bar-top', '#10b981'), barHourly: getCssVar('--chart-bar-hourly', '#f9716'), cardBg: getCssVar('--card-bg', '#1e293b') }; };

// Monthly
(() => { if (!monthly || monthly.length === 0) { console.warn("No data for Monthly chart"); return; } const {labels, values} = toXY(monthly, 'ym', 'net_sales'); const colors = getChartColors(); try { window.myCharts.monthly = new Chart(document.getElementById('chartMonthly'), { type: 'line', data: { labels, datasets: [{ label: 'ยอดขาย (฿)', data: values, tension: .25, fill: true, borderColor: colors.lineBorder, backgroundColor: colors.lineBg }] }, options: { scales: getStandardScales() } }); } catch(e) { console.error("Error creating Monthly chart:", e); } })();
// Category
(() => { if (!category || category.length === 0) { console.warn("No data for Category chart"); return; } const {labels, values} = toXY(category, 'category', 'net_sales'); const colors = getChartColors(); try { window.myCharts.category = new Chart(document.getElementById('chartCategory'), { type: 'doughnut', data: { labels, datasets: [{ data: values, backgroundColor: colors.pieBg.slice(0, labels.length), borderColor: colors.cardBg, borderWidth: 2 }] }, options: { plugins: { legend: { position: 'bottom', labels: { padding: 15 } } } } }); } catch(e) { console.error("Error creating Category chart:", e); } })();
// Top products
(() => { if (!topProducts || topProducts.length === 0) { console.warn("No data for Top Products chart"); return; } const labels = topProducts.map(o => o?.product_name); const qty = topProducts.map(o => parseInt(o?.qty_sold || 0)); const colors = getChartColors(); try { window.myCharts.topProducts = new Chart(document.getElementById('chartTopProducts'), { type: 'bar', data: { labels, datasets: [{ label: 'ชิ้นที่ขาย', data: qty, backgroundColor: colors.barTop }] }, options: { indexAxis: 'y', scales: getStandardScales(), plugins: { legend: { display: false } } } }); } catch(e) { console.error("Error creating Top Products chart:", e); } })();
// Region
(() => { if (!region || region.length === 0) { console.warn("No data for Region chart"); return; } const {labels, values} = toXY(region, 'region', 'net_sales'); const colors = getChartColors(); try { window.myCharts.region = new Chart(document.getElementById('chartRegion'), { type: 'bar', data: { labels, datasets: [{ label: 'ยอดขาย (฿)', data: values, backgroundColor: colors.pieBg.slice(0, labels.length), borderWidth: 0 }] }, options: { scales: getStandardScales(), plugins: { legend: { display: false } } } }); } catch(e) { console.error("Error creating Region chart:", e); } })();
// Payment
(() => { if (!payment || payment.length === 0) { console.warn("No data for Payment chart"); return; } const {labels, values} = toXY(payment, 'payment_method', 'net_sales'); const colors = getChartColors(); try { window.myCharts.payment = new Chart(document.getElementById('chartPayment'), { type: 'pie', data: { labels, datasets: [{ data: values, backgroundColor: colors.pieBg.slice(0, labels.length), borderColor: colors.cardBg, borderWidth: 2 }] }, options: { plugins: { legend: { position: 'bottom', labels: { padding: 15 } } } } }); } catch(e) { console.error("Error creating Payment chart:", e); } })();
// Hourly
(() => { if (!hourly || hourly.length === 0) { console.warn("No data for Hourly chart"); return; } const {labels, values} = toXY(hourly, 'hour_of_day', 'net_sales'); const colors = getChartColors(); try { window.myCharts.hourly = new Chart(document.getElementById('chartHourly'), { type: 'bar', data: { labels, datasets: [{ label: 'ยอดขาย (฿)', data: values, backgroundColor: colors.barHourly }] }, options: { scales: getStandardScales(), plugins: { legend: { display: false } } } }); } catch(e) { console.error("Error creating Hourly chart:", e); } })();
// New vs Returning
(() => { if (!newReturning || newReturning.length === 0) { console.warn("No data for New vs Returning chart"); return; } const labels = newReturning.map(o => o?.date_key); const newC = newReturning.map(o => parseFloat(o?.new_customer_sales || 0)); const retC = newReturning.map(o => parseFloat(o?.returning_sales || 0)); const colors = getChartColors(); try { window.myCharts.newReturning = new Chart(document.getElementById('chartNewReturning'), { type: 'line', data: { labels, datasets: [ { label: 'ลูกค้าใหม่ (฿)', data: newC, tension: .25, fill: false, borderColor: colors.lineNew }, { label: 'ลูกค้าเดิม (฿)', data: retC, tension: .25, fill: false, borderColor: colors.lineReturn } ] }, options: { scales: getLineScales(), plugins: { legend: { position: 'bottom', labels: { padding: 15 } } } } }); } catch(e) { console.error("Error creating New vs Returning chart:", e); } })();

</script>

</body>
</html>

