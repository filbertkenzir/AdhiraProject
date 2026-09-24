<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cashier - Print & Copy</title>
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
            <a href="/" class="flex items-center gap-3 px-3 py-2 rounded-lg bg-white text-blue-700 font-semibold">
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
            <h1 class="font-semibold text-lg text-slate-700">Cashier</h1>
            <div class="text-sm text-slate-400" id="clock"></div>
        </header>

        <main class="flex-1 p-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- FORM INPUT -->
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                    <h2 class="font-semibold text-slate-700 mb-4">Detail Pesanan</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">Jenis Metode</label>
                            <select id="methodSelect" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih Metode --</option>
                                <!-- masih data dummy, nnt tolong bikin functionnya -->
                                <option data-price="500" data-name="Print Hitam Putih">Print Hitam Putih (Rp 500/lbr)</option>
                                <option data-price="1500" data-name="Print Berwarna">Print Berwarna (Rp 1.500/lbr)</option>
                                <option data-price="300" data-name="Fotocopy Hitam Putih">Fotocopy Hitam Putih (Rp 300/lbr)</option>
                                <option data-price="1200" data-name="Fotocopy Berwarna">Fotocopy Berwarna (Rp 1.200/lbr)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">Jenis Kertas</label>
                            <select id="paperSelect" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih Kertas --</option>
                                <option>F4</option>
                                <option>A4</option>
                                <option>A3</option>
                                <option>A5</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">Banyak Kertas (lembar)</label>
                            <input type="number" id="qtyInput" min="1" value="1"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="flex items-end">
                            <button type="button" id="addItemBtn"
                                    class="w-full bg-blue-700 hover:bg-blue-800 text-white font-medium py-2.5 rounded-lg transition">
                                + Tambah ke Pesanan
                            </button>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-4">
                        <h3 class="text-sm font-medium text-slate-500 mb-2">Daftar Pesanan Berjalan</h3>
                        <div id="cartTable" class="space-y-2">
                            <p id="emptyCartMsg" class="text-sm text-slate-400">Belum ada item ditambahkan.</p>
                        </div>
                    </div>
                </div>

                <!-- SUMMARY & PEMBAYARAN -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 h-fit">
                    <h2 class="font-semibold text-slate-700 mb-4">Ringkasan Pembayaran</h2>

                    <div id="summaryList" class="space-y-2 mb-4 text-sm text-slate-600">
                        <p class="text-slate-400">Ringkasan akan muncul di sini.</p>
                    </div>

                    <div class="border-t border-slate-100 pt-3 flex justify-between font-semibold text-slate-800 mb-4">
                        <span>Total</span>
                        <span id="grandTotal">Rp 0</span>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-600 mb-1">Metode Pembayaran</label>
                        <select id="paymentMethod" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="cash" selected>Cash</option>
                            <option value="qris">Qris</option>
                        </select>
                    </div>

                    <div id="cashFields" class="grid grid-cols-2 gap-3 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">Uang Bayar</label>
                            <input type="number" id="paidAmount" min="0" placeholder="0"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">Kembalian</label>
                            <input type="text" id="changeAmount" readonly value="Rp 0"
                                   class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm">
                        </div>
                    </div>

                    <button type="button" id="payBtn" disabled
                            class="w-full bg-blue-700 hover:bg-blue-800 disabled:bg-slate-300 disabled:cursor-not-allowed text-white font-medium py-2.5 rounded-lg transition">
                        Pembayaran Selesai
                    </button>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- MODAL QRIS -->
<div id="qrisModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 px-4">
    <div class="bg-white w-full max-w-xs rounded-2xl shadow-xl p-6 text-center relative">
        <button type="button" id="closeQrisModal" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">✕</button>
        <h3 class="font-semibold text-slate-700 mb-4">Scan Qris untuk Membayar</h3>
        <img src="https://nurosoft.id/blog/wp-content/uploads/2025/09/Apa-itu-QRIS.jpeg" alt="QRIS" class="mx-auto rounded-lg border border-slate-200 mb-4">
        <p class="text-sm text-slate-500 mb-4">Total: <span id="qrisTotal" class="font-semibold text-slate-700">Rp 0</span></p>
        <button type="button" id="qrisPayBtn"
                class="w-full bg-blue-700 hover:bg-blue-800 text-white font-medium py-2.5 rounded-lg transition">
            Pembayaran Selesai
        </button>
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
    // ---------- jam ----------
    function updateClock() {
        document.getElementById('clock').textContent = new Date().toLocaleString('id-ID');
    }
    setInterval(updateClock, 1000);
    updateClock();

    // ---------- submenu inventory ----------
    document.getElementById('inventoryToggle').addEventListener('click', function () {
        const submenu = document.getElementById('inventorySubmenu');
        const arrow = document.getElementById('inventoryArrow');
        submenu.classList.toggle('hidden');
        arrow.textContent = submenu.classList.contains('hidden') ? '▸' : '▾';
    });

    // ---------- login modal ----------
    const loginModal = document.getElementById('loginModal');
    document.getElementById('openLoginBtn').addEventListener('click', () => loginModal.classList.remove('hidden'));
    document.getElementById('closeLoginModal').addEventListener('click', () => loginModal.classList.add('hidden'));
    loginModal.addEventListener('click', (e) => { if (e.target === loginModal) loginModal.classList.add('hidden'); });

    // ---------- cart logic ----------
    const cart = []; // { methodName, price, paperType, qty, subtotal }

    const methodSelect  = document.getElementById('methodSelect');
    const paperSelect   = document.getElementById('paperSelect');
    const qtyInput      = document.getElementById('qtyInput');
    const addItemBtn    = document.getElementById('addItemBtn');
    const cartTable      = document.getElementById('cartTable');
    const emptyCartMsg   = document.getElementById('emptyCartMsg');
    const summaryList    = document.getElementById('summaryList');
    const grandTotalEl   = document.getElementById('grandTotal');
    const paymentMethod  = document.getElementById('paymentMethod');
    const cashFields      = document.getElementById('cashFields');
    const paidAmountInput = document.getElementById('paidAmount');
    const changeAmountEl  = document.getElementById('changeAmount');
    const payBtn           = document.getElementById('payBtn');
    const qrisModal         = document.getElementById('qrisModal');
    const qrisTotalEl        = document.getElementById('qrisTotal');

    function formatRupiah(num) {
        return 'Rp ' + Number(num).toLocaleString('id-ID');
    }

    function getTotal() {
        return cart.reduce((sum, item) => sum + item.subtotal, 0);
    }

    function renderCart() {
        cartTable.innerHTML = '';
        summaryList.innerHTML = '';

        if (cart.length === 0) {
            cartTable.appendChild(emptyCartMsg);
            summaryList.innerHTML = '<p class="text-slate-400">Ringkasan akan muncul di sini.</p>';
            grandTotalEl.textContent = formatRupiah(0);
            payBtn.disabled = true;
            updateChange();
            return;
        }

        payBtn.disabled = false;

        cart.forEach((item, idx) => {
            const row = document.createElement('div');
            row.className = 'flex items-center justify-between bg-slate-50 rounded-lg px-3 py-2 text-sm';
            row.innerHTML = `
                <div>
                    <p class="font-medium text-slate-700">${item.methodName}</p>
                    <p class="text-slate-400">${item.paperType} &times; ${item.qty}</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="font-medium text-slate-700">${formatRupiah(item.subtotal)}</span>
                    <button type="button" data-idx="${idx}" class="removeItemBtn text-red-400 hover:text-red-600">✕</button>
                </div>
            `;
            cartTable.appendChild(row);

            const summaryRow = document.createElement('div');
            summaryRow.className = 'flex justify-between';
            summaryRow.innerHTML = `<span>${item.methodName} - ${item.paperType} (${item.qty} lbr)</span><span>${formatRupiah(item.subtotal)}</span>`;
            summaryList.appendChild(summaryRow);
        });

        grandTotalEl.textContent = formatRupiah(getTotal());

        document.querySelectorAll('.removeItemBtn').forEach(btn => {
            btn.addEventListener('click', function () {
                cart.splice(Number(this.dataset.idx), 1);
                renderCart();
            });
        });

        updateChange();
    }

    addItemBtn.addEventListener('click', function () {
        const methodOpt = methodSelect.selectedOptions[0];
        const paper     = paperSelect.value;
        const qty       = parseInt(qtyInput.value, 10);

        if (!methodOpt || !methodOpt.value && !methodOpt.dataset.name) { alert('Pilih jenis metode dulu.'); return; }
        if (!methodOpt.dataset.name) { alert('Pilih jenis metode dulu.'); return; }
        if (!paper) { alert('Pilih jenis kertas dulu.'); return; }
        if (!qty || qty < 1) { alert('Jumlah kertas tidak valid.'); return; }

        const price = parseInt(methodOpt.dataset.price, 10);

        cart.push({
            methodName: methodOpt.dataset.name,
            paperType: paper,
            qty: qty,
            price: price,
            subtotal: price * qty,
        });

        qtyInput.value = 1;
        renderCart();
    });

    function updateChange() {
        const total = getTotal();
        const paid  = parseInt(paidAmountInput.value || 0, 10);
        const change = Math.max(paid - total, 0);
        changeAmountEl.value = formatRupiah(change);
    }

    paidAmountInput.addEventListener('input', updateChange);

    paymentMethod.addEventListener('change', function () {
        cashFields.classList.toggle('hidden', this.value !== 'cash');
    });

    payBtn.addEventListener('click', function () {
        if (cart.length === 0) return;

        if (paymentMethod.value === 'qris') {
            qrisTotalEl.textContent = formatRupiah(getTotal());
            qrisModal.classList.remove('hidden');
            return;
        }
        finishTransaction();
    });

    document.getElementById('qrisPayBtn').addEventListener('click', finishTransaction);
    document.getElementById('closeQrisModal').addEventListener('click', () => qrisModal.classList.add('hidden'));

    function finishTransaction() {
        // Di sini nanti kamu ganti dengan fetch() ke endpoint backend Laravel kamu
        alert('Transaksi selesai (demo frontend). Total: ' + formatRupiah(getTotal()));
        cart.length = 0;
        paidAmountInput.value = '';
        renderCart();
        qrisModal.classList.add('hidden');
    }

    renderCart();
});
</script>
</body>
</html>