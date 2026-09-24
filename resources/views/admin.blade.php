<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard - Print & Copy</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

<div class="flex min-h-screen">

    <!-- SIDEBAR -->
    <aside class="w-64 shrink-0 bg-blue-700 text-white flex flex-col">
        <div class="h-16 flex items-center gap-2 px-6 border-b border-blue-600">
            <div class="px-3 h-8 rounded-lg bg-white/15 flex items-center justify-center font-bold">Adhira</div>
            <span class="font-semibold text-lg">Print & Copy</span>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
            <a href="/" class="flex items-center gap-3 px-3 py-2 rounded-lg text-blue-100 hover:bg-blue-600">
                Cashier
            </a>

            <div>
                <button type="button" id="inventoryToggle"
                        class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-blue-100 hover:bg-blue-600 transition">
                    <span class="flex items-center gap-3">Inventory</span>
                    <span id="inventoryArrow">▸</span>
                </button>
                <div id="inventorySubmenu" class="ml-8 mt-1 space-y-1 hidden">
                    <a href="/item" class="block px-3 py-1.5 rounded-md text-sm text-blue-100 hover:bg-blue-600">Tambah Barang</a>
                    <a href="/method" class="block px-3 py-1.5 rounded-md text-sm text-blue-100 hover:bg-blue-600">Tambah Metode</a>
                </div>
            </div>

            <a href="/admin" class="flex items-center gap-3 px-3 py-2 rounded-lg bg-white text-blue-700 font-semibold transition">
                Admin Dashboard
            </a>
        </nav>

        <div class="p-3 border-t border-blue-600">
            <button type="button" id="openLoginBtn"
                    class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-blue-100 hover:bg-blue-600 text-sm">
                Login
            </button>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col">
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6">
            <h1 class="font-semibold text-lg text-slate-700">Admin Dashboard</h1>
            <div class="text-sm text-slate-400" id="clock"></div>
        </header>

        <main class="flex-1 p-6 space-y-6">

            <!-- CARD STATISTIK -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
                    <p class="text-sm text-slate-400 mb-1">Total Penjualan Hari Ini</p>
                    <p class="text-2xl font-semibold text-blue-700">Rp 1.250.000</p>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
                    <p class="text-sm text-slate-400 mb-1">Total Pengunjung Hari Ini</p>
                    <p class="text-2xl font-semibold text-blue-700">38</p>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
                    <p class="text-sm text-slate-400 mb-1">Metode Terbanyak</p>
                    <p class="text-2xl font-semibold text-blue-700">Print Hitam Putih</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- GRAFIK -->
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                    <h2 class="font-semibold text-slate-700 mb-4">Grafik Penjualan (7 Hari Terakhir)</h2>
                    <canvas id="salesChart" height="120"></canvas>
                </div>

                <!-- KEPERLUAN PENGUNJUNG -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                    <h2 class="font-semibold text-slate-700 mb-4">Keperluan Pengunjung</h2>
                    <div class="space-y-3" id="visitorNeeds">
                        <!-- dummy data, nanti diisi dari backend -->
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- MODAL LOGIN -->
<div id="loginModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 px-4">
    <div class="bg-white w-full max-w-sm rounded-2xl shadow-xl p-6 relative">
        <button type="button" id="closeLoginModal" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">✕</button>
        <div class="text-center mb-6">
            <h2 class="text-lg font-semibold text-slate-800">Masuk ke Akun</h2>
            <p class="text-sm text-slate-400">Silakan login untuk mengakses dashboard</p>
        </div>
        <form onsubmit="event.preventDefault(); alert('Ini demo frontend, belum ada proses login.');" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Email / Username</label>
                <input type="text" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Password</label>
                <input type="password" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" class="w-full bg-blue-700 hover:bg-blue-800 text-white font-medium py-2.5 rounded-lg transition">Masuk</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    function updateClock() {
        document.getElementById('clock').textContent = new Date().toLocaleString('id-ID');
    }
    setInterval(updateClock, 1000);
    updateClock();

    document.getElementById('inventoryToggle').addEventListener('click', function () {
        const submenu = document.getElementById('inventorySubmenu');
        const arrow = document.getElementById('inventoryArrow');
        submenu.classList.toggle('hidden');
        arrow.textContent = submenu.classList.contains('hidden') ? '▸' : '▾';
    });

    const loginModal = document.getElementById('loginModal');
    document.getElementById('openLoginBtn').addEventListener('click', () => loginModal.classList.remove('hidden'));
    document.getElementById('closeLoginModal').addEventListener('click', () => loginModal.classList.add('hidden'));
    loginModal.addEventListener('click', (e) => { if (e.target === loginModal) loginModal.classList.add('hidden'); });

    // ---------- dummy data keperluan pengunjung ----------
    const visitorNeeds = [
        { name: 'Print Hitam Putih', percentage: 42 },
        { name: 'Fotocopy Hitam Putih', percentage: 28 },
        { name: 'Print Berwarna', percentage: 18 },
        { name: 'Fotocopy Berwarna', percentage: 12 },
    ];

    const container = document.getElementById('visitorNeeds');
    visitorNeeds.forEach(item => {
        const div = document.createElement('div');
        div.innerHTML = `
            <div class="flex justify-between text-sm mb-1">
                <span class="text-slate-600">${item.name}</span>
                <span class="text-slate-500">${item.percentage}%</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full" style="width: ${item.percentage}%"></div>
            </div>
        `;
        container.appendChild(div);
    });

    // ---------- dummy chart 7 hari terakhir ----------
    const chartLabels = ['17 Sep', '18 Sep', '19 Sep', '20 Sep', '21 Sep', '22 Sep', '23 Sep'];
    const chartData   = [850000, 920000, 700000, 1100000, 980000, 1300000, 1250000];

    new Chart(document.getElementById('salesChart'), {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Penjualan (Rp)',
                data: chartData,
                borderColor: '#1d4ed8',
                backgroundColor: 'rgba(29, 78, 216, 0.1)',
                tension: 0.35,
                fill: true,
                pointBackgroundColor: '#1d4ed8',
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    ticks: {
                        callback: function (value) {
                            return 'Rp ' + Number(value).toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });
});
</script>
</body>
</html>