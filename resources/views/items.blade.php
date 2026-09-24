<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tambah Barang - Print & Copy</title>
<script src="https://cdn.tailwindcss.com"></script>
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
            <a href="/" class="flex items-center gap-3 px-3 py-2 rounded-lg text-blue-100 hover:bg-blue-600 transition">
                Cashier
            </a>
 
            <div>
                <button type="button" id="inventoryToggle"
                        class="w-full flex items-center justify-between px-3 py-2 rounded-lg bg-blue-600 text-white font-semibold">
                    <span class="flex items-center gap-3">Inventory</span>
                    <span id="inventoryArrow">▾</span>
                </button>
                <div id="inventorySubmenu" class="ml-8 mt-1 space-y-1">
                    <a href="/item" class="block px-3 py-1.5 rounded-md text-sm bg-white text-blue-700 font-semibold">Tambah Barang</a>
                    <a href="/method" class="block px-3 py-1.5 rounded-md text-sm text-blue-100 hover:bg-blue-600">Tambah Metode</a>
                </div>
            </div>
 
            <a href="/admin" class="flex items-center gap-3 px-3 py-2 rounded-lg text-blue-100 hover:bg-blue-600 transition">
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
            <h1 class="font-semibold text-lg text-slate-700">Inventory - Tambah Barang</h1>
            <div class="text-sm text-slate-400" id="clock"></div>
        </header>

        <main class="flex-1 p-6 space-y-6">

            <!-- FORM -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h2 class="font-semibold text-slate-700 mb-4">Tambah Barang</h2>

                <form id="itemForm" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-slate-600 mb-1">Nama Barang</label>
                        <input type="text" id="itemName" required placeholder="ex: Kertas F4"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1">Stok</label>
                        <input type="number" id="itemStock" min="0" required placeholder="0"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1">Harga / Satuan</label>
                        <input type="number" id="itemPrice" min="0" required placeholder="0"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="sm:col-span-4">
                        <button type="submit"
                                class="bg-blue-700 hover:bg-blue-800 text-white font-medium px-6 py-2.5 rounded-lg transition">
                            Simpan Barang
                        </button>
                    </div>
                </form>
            </div>

            <!-- TABEL -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h2 class="font-semibold text-slate-700 mb-4">Daftar Barang</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-slate-400 border-b border-slate-100">
                                <th class="py-2 pr-4">Nama Barang</th>
                                <th class="py-2 pr-4">Stok</th>
                                <th class="py-2 pr-4">Harga</th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody">
                            <!-- masih data dummy, nnt dibikin backendnya -->
                            <tr class="border-b border-slate-50">
                                <td class="py-3 pr-4 font-medium text-slate-700">Kertas F4</td>
                                <td class="py-3 pr-4"><span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700">120</span></td>
                                <td class="py-3 pr-4">Rp 500</td>
                            </tr>
                            <tr class="border-b border-slate-50">
                                <td class="py-3 pr-4 font-medium text-slate-700">Kertas A4</td>
                                <td class="py-3 pr-4"><span class="px-2 py-1 rounded-full text-xs font-medium bg-red-50 text-red-600">8</span></td>
                                <td class="py-3 pr-4">Rp 450</td>
                            </tr>
                        </tbody>
                    </table>
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

    // ---------- tambah barang ke tabel (client-side saja, belum tersambung backend) ----------
    document.getElementById('itemForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const name  = document.getElementById('itemName').value;
        const stock = parseInt(document.getElementById('itemStock').value, 10);
        const price = parseInt(document.getElementById('itemPrice').value, 10);

        const tbody = document.getElementById('itemsTableBody');
        const row = document.createElement('tr');
        row.className = 'border-b border-slate-50';
        row.innerHTML = `
            <td class="py-3 pr-4 font-medium text-slate-700">${name}</td>
            <td class="py-3 pr-4">
                <span class="px-2 py-1 rounded-full text-xs font-medium ${stock <= 10 ? 'bg-red-50 text-red-600' : 'bg-blue-50 text-blue-700'}">${stock}</span>
            </td>
            <td class="py-3 pr-4">Rp ${price.toLocaleString('id-ID')}</td>
        `;
        tbody.prepend(row);

        this.reset();
    });
});
</script>
</body>
</html>