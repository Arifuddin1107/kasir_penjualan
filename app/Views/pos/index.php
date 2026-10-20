<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Hotkey Info Bar -->
<div style="display: flex; justify-content: space-between; align-items: center; background: #FFFFFF; border: 1px solid var(--accent-green-border); border-radius: 10px; padding: 10px 16px; margin-bottom: 20px; font-size: 12.5px; color: var(--text-muted); flex-wrap: wrap; gap: 8px;">
    <div style="display: flex; gap: 12px; align-items: center;">
        <span style="font-weight: 700; color: var(--text-primary);"><i class="fa-solid fa-keyboard"></i> Shortcut Cepat:</span>
        <span><kbd style="background: #F3F4F6; border: 1px solid #D1D5DB; padding: 2px 6px; border-radius: 4px; font-weight: 700;">F2</kbd> Fokus Barcode</span>
        <span><kbd style="background: #F3F4F6; border: 1px solid #D1D5DB; padding: 2px 6px; border-radius: 4px; font-weight: 700;">F4</kbd> Bayar / Checkout</span>
        <span><kbd style="background: #F3F4F6; border: 1px solid #D1D5DB; padding: 2px 6px; border-radius: 4px; font-weight: 700;">ESC</kbd> Tutup Modal</span>
    </div>
    <div style="display: flex; align-items: center; gap: 8px;">
        <span class="badge badge-success"><i class="fa-solid fa-signal"></i> Sistem Online</span>
        <span>Kasir: <b><?= esc(session()->get('nama_lengkap')) ?></b></span>
    </div>
</div>

<!-- POS Main Grid (2 Columns: Left Input & Catalog, Right Shopping Cart & Total) -->
<div style="display: grid; grid-template-columns: 1fr 420px; gap: 20px; align-items: start;">

    <!-- Left Column: Barcode Scanner & Search Autocomplete -->
    <div>
        <!-- Barcode & Search Box -->
        <div class="antigravity-card" style="margin-bottom: 20px;">
            <div style="position: relative;">
                <label class="form-label" style="display: flex; justify-content: space-between;">
                    <span><i class="fa-solid fa-barcode"></i> Scan Barcode Scanner atau Cari Nama Produk</span>
                    <span style="color: var(--accent-green); font-weight: 600;">Response < 200ms</span>
                </label>
                <div style="display: flex; gap: 10px;">
                    <div style="position: relative; flex: 1;">
                        <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #9CA3AF;"></i>
                        <input type="text" id="posSearchInput" class="form-control" style="padding-left: 40px; font-size: 15px; height: 48px; border-width: 2px;" placeholder="Scan barcode fisik (tekan Enter) atau ketik nama produk..." autocomplete="off" autofocus>
                    </div>
                    <button type="button" class="antigravity-btn" onclick="triggerSearch()" style="height: 48px; padding: 0 20px;">
                        <i class="fa-solid fa-arrow-down-to-bracket"></i> Cari
                    </button>
                </div>

                <!-- Autocomplete Dropdown List -->
                <div id="searchDropdown" style="display: none; position: absolute; left: 0; right: 0; top: 100%; background: #FFFFFF; border: 1px solid var(--accent-green-border); border-radius: 10px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); z-index: 40; max-height: 280px; overflow-y: auto; margin-top: 4px;">
                    <!-- Items rendered dynamically via JS -->
                </div>
            </div>
        </div>

        <!-- Table Keranjang Belanja (Active Cart) -->
        <div class="antigravity-card" style="margin-bottom: 20px;">
            <div class="antigravity-card-header">
                <h2 class="card-title">
                    <i class="fa-solid fa-cart-shopping" style="color: var(--accent-green);"></i> Keranjang Belanja Pelanggan
                </h2>
                <button type="button" class="antigravity-btn-outline btn-sm" onclick="clearCart()">
                    <i class="fa-solid fa-trash-can"></i> Kosongkan
                </button>
            </div>

            <div class="table-responsive" style="min-height: 250px;">
                <table class="antigravity-table" id="cartTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Produk</th>
                            <th style="text-align: right;">Harga</th>
                            <th style="width: 140px; text-align: center;">Qty</th>
                            <th style="text-align: right;">Subtotal</th>
                            <th style="width: 50px; text-align: center;">Hapus</th>
                        </tr>
                    </thead>
                    <tbody id="cartTableBody">
                        <tr id="emptyCartRow">
                            <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 48px 16px;">
                                <i class="fa-solid fa-basket-shopping" style="font-size: 32px; color: #D1D5DB; margin-bottom: 10px; display: block;"></i>
                                Keranjang belanja masih kosong.<br>Scan barcode atau ketik nama produk di atas untuk menambahkan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section: Riwayat Transaksi Hari Ini (Cetak Ulang Struk Cepat) -->
        <div class="antigravity-card">
            <div class="antigravity-card-header">
                <h2 class="card-title" style="font-size: 15px;">
                    <i class="fa-solid fa-clock-rotate-left" style="color: var(--accent-green);"></i> Riwayat Transaksi Kasir Hari Ini
                </h2>
                <span style="font-size: 12px; color: var(--text-muted);"><?= count($todayTransactions) ?> Transaksi</span>
            </div>

            <div class="table-responsive" style="max-height: 220px; overflow-y: auto;">
                <table class="antigravity-table">
                    <thead>
                        <tr>
                            <th>Faktur</th>
                            <th>Waktu</th>
                            <th>Total Item</th>
                            <th>Total Omzet</th>
                            <th>Metode</th>
                            <th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($todayTransactions)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 20px;">
                                    Belum ada transaksi pada shift ini.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($todayTransactions as $tx): ?>
                                <tr>
                                    <td><strong><?= esc($tx['nomor_faktur']) ?></strong></td>
                                    <td><?= date('H:i', strtotime($tx['tanggal'])) ?></td>
                                    <td><?= $tx['total_item'] ?></td>
                                    <td><b>Rp <?= number_format($tx['total_omzet'], 0, ',', '.') ?></b></td>
                                    <td><span class="badge badge-success"><?= strtoupper(esc($tx['metode_pembayaran'])) ?></span></td>
                                    <td style="text-align: right;">
                                        <a href="<?= base_url('transaksi/struk/' . $tx['id']) ?>" target="_blank" class="antigravity-btn-outline btn-sm">
                                            <i class="fa-solid fa-print"></i> Struk
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Grand Total Display & Checkout Panel -->
    <div>
        <div class="antigravity-card" style="position: sticky; top: 84px; border-width: 2px;">
            <!-- Grand Total Big Screen -->
            <div style="background: linear-gradient(135deg, #059669 0%, #064E3B 100%); border-radius: 12px; padding: 22px; color: #FFFFFF; margin-bottom: 20px; box-shadow: 0 8px 16px rgba(5, 150, 105, 0.25);">
                <div style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; opacity: 0.9;">Total Pembayaran</div>
                <div id="displayTotalGrand" style="font-size: 34px; font-weight: 800; letter-spacing: -0.02em; margin-top: 4px;">
                    Rp 0
                </div>
                <div style="display: flex; justify-content: space-between; margin-top: 10px; font-size: 12px; opacity: 0.85; border-top: 1px solid rgba(255,255,255,0.2); padding-top: 8px;">
                    <span>Total Item: <b id="displayTotalItem">0</b></span>
                    <span>Toko: <b><?= esc($settings['nama_toko'] ?? 'Antigravity Retail') ?></b></span>
                </div>
            </div>

            <!-- Quick Checkout Inputs -->
            <div class="form-group">
                <label class="form-label">Metode Pembayaran</label>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px;">
                    <button type="button" class="antigravity-btn payment-method-btn active" data-method="cash" onclick="setPaymentMethod('cash')">
                        <i class="fa-solid fa-money-bill-wave"></i> Tunai
                    </button>
                    <button type="button" class="antigravity-btn-outline payment-method-btn" data-method="qris" onclick="setPaymentMethod('qris')">
                        <i class="fa-solid fa-qrcode"></i> QRIS
                    </button>
                    <button type="button" class="antigravity-btn-outline payment-method-btn" data-method="transfer" onclick="setPaymentMethod('transfer')">
                        <i class="fa-solid fa-building-columns"></i> Transfer
                    </button>
                </div>
            </div>

            <!-- Cash Input section (only visible if Tunai) -->
            <div id="cashInputSection">
                <div class="form-group">
                    <label class="form-label">Uang Diterima Pelanggan (Rp)</label>
                    <input type="number" id="inputBayar" class="form-control" style="font-size: 18px; font-weight: 700; height: 48px; color: var(--text-primary);" placeholder="0" min="0" step="500" oninput="calcChange()">
                </div>

                <!-- Quick Cash Presets -->
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px; margin-bottom: 16px;">
                    <button type="button" class="antigravity-btn-outline btn-sm" onclick="setExactCash()">Uang Pas</button>
                    <button type="button" class="antigravity-btn-outline btn-sm" onclick="addPresetCash(20000)">+ 20.000</button>
                    <button type="button" class="antigravity-btn-outline btn-sm" onclick="addPresetCash(50000)">+ 50.000</button>
                    <button type="button" class="antigravity-btn-outline btn-sm" onclick="setPresetCash(50000)">50.000</button>
                    <button type="button" class="antigravity-btn-outline btn-sm" onclick="setPresetCash(100000)">100.000</button>
                    <button type="button" class="antigravity-btn-outline btn-sm" onclick="setPresetCash(200000)">200.000</button>
                </div>

                <!-- Kembalian Display -->
                <div style="background: #F9FAFB; border: 1px solid var(--border-color); border-radius: 10px; padding: 14px; margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 13px; font-weight: 600; color: #4B5563;">Kembalian:</span>
                        <span id="displayKembalian" style="font-size: 20px; font-weight: 800; color: #059669;">Rp 0</span>
                    </div>
                </div>
            </div>

            <!-- Catatan Transaksi -->
            <div class="form-group">
                <label class="form-label">Catatan Tambahan (Opsional)</label>
                <input type="text" id="inputCatatan" class="form-control" placeholder="Contoh: Meja 02 / Pelanggan Langganan">
            </div>

            <!-- Submit Button (FR-3.3) -->
            <button type="button" id="btnSubmitCheckout" class="antigravity-btn" style="width: 100%; height: 50px; font-size: 16px; font-weight: 800;" onclick="processCheckout()">
                <i class="fa-solid fa-check-double"></i> Selesaikan Transaksi (F4)
            </button>
        </div>
    </div>
</div>

<!-- Modal Transaksi Berhasil (Checkout Success & Thermal Print Trigger) -->
<div id="modalSuccess" class="modal-overlay">
    <div class="modal-card" style="max-width: 460px; text-align: center;">
        <div class="modal-body" style="padding: 32px 24px;">
            <div style="width: 64px; height: 64px; background: #ECFDF5; border: 2px solid #A7F3D0; color: #059669; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 28px; margin-bottom: 16px;">
                <i class="fa-solid fa-check"></i>
            </div>
            <h3 style="font-size: 20px; font-weight: 800; color: var(--text-primary); margin-bottom: 4px;">Transaksi Berhasil!</h3>
            <p style="font-size: 13px; color: var(--text-muted);">Pembayaran telah diproses dengan integritas data ACID.</p>

            <div style="background: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 10px; padding: 16px; margin: 20px 0; text-align: left; font-size: 13px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                    <span style="color: var(--text-muted);">No. Faktur:</span>
                    <strong id="successFaktur">-</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                    <span style="color: var(--text-muted);">Total Belanja:</span>
                    <strong id="successTotal">-</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                    <span style="color: var(--text-muted);">Dibayar:</span>
                    <strong id="successBayar">-</strong>
                </div>
                <div style="display: flex; justify-content: space-between; border-top: 1px dashed #D1D5DB; padding-top: 8px;">
                    <span style="color: var(--text-primary); font-weight: 700;">Kembalian:</span>
                    <strong id="successKembalian" style="color: #059669; font-size: 16px;">-</strong>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 10px;">
                <button type="button" class="antigravity-btn" style="height: 44px;" onclick="printStrukCurrent()">
                    <i class="fa-solid fa-print"></i> Cetak Struk Belanja (Thermal)
                </button>
                <button type="button" class="antigravity-btn-outline" style="height: 44px;" onclick="newTransaction()">
                    <i class="fa-solid fa-cart-plus"></i> Transaksi Baru (Enter)
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // State Keranjang Belanja
    let cart = [];
    let selectedMethod = 'cash';
    let currentPenjualanId = null;

    const csrfTokenName = document.querySelector('meta[name="csrf-token-name"]').getAttribute('content');
    let csrfHash = document.querySelector('meta[name="csrf-hash"]').getAttribute('content');

    const searchInput = document.getElementById('posSearchInput');
    const searchDropdown = document.getElementById('searchDropdown');
    const cartTableBody = document.getElementById('cartTableBody');
    const emptyCartRow = document.getElementById('emptyCartRow');
    const displayTotalGrand = document.getElementById('displayTotalGrand');
    const displayTotalItem = document.getElementById('displayTotalItem');
    const inputBayar = document.getElementById('inputBayar');
    const displayKembalian = document.getElementById('displayKembalian');

    // 1. Keyboard Shortcuts (F2, F4, Enter)
    document.addEventListener('keydown', function(e) {
        if (e.key === 'F2') {
            e.preventDefault();
            searchInput.focus();
            searchInput.select();
        } else if (e.key === 'F4') {
            e.preventDefault();
            processCheckout();
        } else if (e.key === 'Enter') {
            // Jika modal success aktif, tekan enter untuk transaksi baru
            const modalSuccess = document.getElementById('modalSuccess');
            if (modalSuccess.classList.contains('active')) {
                e.preventDefault();
                newTransaction();
            }
        }
    });

    // 2. Barcode Physical Scanner & Search Autocomplete (FR-3.2, < 200ms)
    let searchDebounceTimer;
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            triggerSearch(true); // Direct enter dari barcode scanner
        }
    });

    searchInput.addEventListener('input', function() {
        clearTimeout(searchDebounceTimer);
        const query = searchInput.value.trim();
        if (query.length < 2) {
            searchDropdown.style.display = 'none';
            return;
        }

        searchDebounceTimer = setTimeout(() => {
            fetchProducts(query);
        }, 150); // Debounce responsif 150ms (< 200ms)
    });

    function triggerSearch(isScannerEnter = false) {
        const query = searchInput.value.trim();
        if (!query) return;

        fetch(`<?= base_url('pos/search') ?>?q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success' && res.data.length > 0) {
                    // Jika barcode scanner persis (exact match), langsung tambahkan item pertama ke keranjang
                    if (res.exact || (isScannerEnter && res.data.length === 1)) {
                        addToCart(res.data[0]);
                        searchInput.value = '';
                        searchDropdown.style.display = 'none';
                        searchInput.focus();
                    } else {
                        renderDropdown(res.data);
                    }
                } else {
                    alert('Produk dengan barcode / kata kunci "' + query + '" tidak ditemukan.');
                    searchInput.select();
                }
            })
            .catch(err => console.error(err));
    }

    function fetchProducts(query) {
        fetch(`<?= base_url('pos/search') ?>?q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    renderDropdown(res.data);
                }
            });
    }

    function renderDropdown(items) {
        if (!items || items.length === 0) {
            searchDropdown.style.display = 'none';
            return;
        }

        let html = '';
        items.forEach(item => {
            const isOutOfStock = parseInt(item.stok) <= 0;
            html += `
                <div onclick="${isOutOfStock ? '' : 'selectItem(' + JSON.stringify(item).replace(/"/g, '&quot;') + ')'}" 
                     style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; border-bottom: 1px solid #F3F4F6; cursor: ${isOutOfStock ? 'not-allowed' : 'pointer'}; opacity: ${isOutOfStock ? '0.5' : '1'}; transition: 0.15s;"
                     onmouseover="this.style.backgroundColor='#F0FDF4'" onmouseout="this.style.backgroundColor='transparent'">
                    <div>
                        <strong style="color: var(--text-primary); font-size: 13.5px;">${item.nama_produk}</strong>
                        <div style="font-size: 11px; color: var(--text-muted);">Barcode: ${item.barcode} | Satuan: ${item.satuan}</div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-weight: 700; color: #059669; font-size: 14px;">Rp ${new Intl.NumberFormat('id-ID').format(item.harga_jual)}</div>
                        <div style="font-size: 11px; color: ${isOutOfStock ? '#DC2626' : 'var(--text-muted)'};">
                            Stok: <b>${item.stok}</b> ${isOutOfStock ? '(Habis)' : ''}
                        </div>
                    </div>
                </div>
            `;
        });

        searchDropdown.innerHTML = html;
        searchDropdown.style.display = 'block';
    }

    function selectItem(item) {
        addToCart(item);
        searchInput.value = '';
        searchDropdown.style.display = 'none';
        searchInput.focus();
    }

    // Tutup dropdown jika klik di luar
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
            searchDropdown.style.display = 'none';
        }
    });

    // 3. Cart Logic
    function addToCart(product) {
        const existing = cart.find(i => i.id === product.id);
        const maxStock = parseInt(product.stok);

        if (maxStock <= 0) {
            alert('Stok produk "' + product.nama_produk + '" sudah habis!');
            return;
        }

        if (existing) {
            if (existing.qty + 1 > maxStock) {
                alert('Stok tidak mencukupi! Maksimum stok tersedia: ' + maxStock);
                return;
            }
            existing.qty += 1;
            existing.subtotal = existing.qty * existing.harga_jual;
        } else {
            cart.push({
                id: product.id,
                barcode: product.barcode,
                nama_produk: product.nama_produk,
                harga_jual: parseFloat(product.harga_jual),
                harga_beli: parseFloat(product.harga_beli),
                qty: 1,
                maxStock: maxStock,
                satuan: product.satuan,
                subtotal: parseFloat(product.harga_jual)
            });
        }

        renderCart();
    }

    function updateQty(id, delta) {
        const item = cart.find(i => i.id === id);
        if (!item) return;

        const newQty = item.qty + delta;
        if (newQty <= 0) {
            removeFromCart(id);
            return;
        }

        if (newQty > item.maxStock) {
            alert('Stok produk tidak mencukupi! Maksimum: ' + item.maxStock);
            return;
        }

        item.qty = newQty;
        item.subtotal = item.qty * item.harga_jual;
        renderCart();
    }

    function setQtyDirect(id, value) {
        const item = cart.find(i => i.id === id);
        if (!item) return;

        let val = parseInt(value) || 1;
        if (val <= 0) val = 1;
        if (val > item.maxStock) {
            alert('Stok produk hanya tersedia: ' + item.maxStock);
            val = item.maxStock;
        }

        item.qty = val;
        item.subtotal = item.qty * item.harga_jual;
        renderCart();
    }

    function removeFromCart(id) {
        cart = cart.filter(i => i.id !== id);
        renderCart();
    }

    function clearCart() {
        if (cart.length === 0) return;
        if (confirm('Kosongkan semua produk dalam keranjang belanja?')) {
            cart = [];
            renderCart();
            searchInput.focus();
        }
    }

    function renderCart() {
        if (cart.length === 0) {
            emptyCartRow.style.display = '';
            // Hapus baris item selain emptyCartRow
            const rows = cartTableBody.querySelectorAll('tr:not(#emptyCartRow)');
            rows.forEach(r => r.remove());
            displayTotalGrand.innerText = 'Rp 0';
            displayTotalItem.innerText = '0';
            calcChange();
            return;
        }

        emptyCartRow.style.display = 'none';
        const rows = cartTableBody.querySelectorAll('tr:not(#emptyCartRow)');
        rows.forEach(r => r.remove());

        let totalOmzet = 0;
        let totalItems = 0;

        cart.forEach((item, index) => {
            totalOmzet += item.subtotal;
            totalItems += item.qty;

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${index + 1}</td>
                <td>
                    <strong>${item.nama_produk}</strong>
                    <div style="font-size: 11px; color: var(--text-muted);">${item.barcode}</div>
                </td>
                <td style="text-align: right;">Rp ${new Intl.NumberFormat('id-ID').format(item.harga_jual)}</td>
                <td style="text-align: center;">
                    <div style="display: inline-flex; align-items: center; border: 1px solid #D1D5DB; border-radius: 6px; overflow: hidden;">
                        <button type="button" onclick="updateQty(${item.id}, -1)" style="border: none; background: #F3F4F6; width: 28px; height: 28px; cursor: pointer; font-weight: bold;">-</button>
                        <input type="number" value="${item.qty}" min="1" max="${item.maxStock}" onchange="setQtyDirect(${item.id}, this.value)" style="width: 44px; text-align: center; border: none; font-size: 13px; font-weight: 700; outline: none;">
                        <button type="button" onclick="updateQty(${item.id}, 1)" style="border: none; background: #F3F4F6; width: 28px; height: 28px; cursor: pointer; font-weight: bold;">+</button>
                    </div>
                </td>
                <td style="text-align: right; font-weight: 800; color: var(--text-primary);">
                    Rp ${new Intl.NumberFormat('id-ID').format(item.subtotal)}
                </td>
                <td style="text-align: center;">
                    <button type="button" onclick="removeFromCart(${item.id})" style="border: none; background: transparent; color: #DC2626; cursor: pointer; font-size: 14px;" title="Hapus">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </td>
            `;
            cartTableBody.appendChild(tr);
        });

        displayTotalGrand.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalOmzet);
        displayTotalItem.innerText = totalItems;
        calcChange();
    }

    function getTotalCart() {
        return cart.reduce((acc, curr) => acc + curr.subtotal, 0);
    }

    // 4. Payment & Cash Handling
    function setPaymentMethod(method) {
        selectedMethod = method;
        document.querySelectorAll('.payment-method-btn').forEach(btn => {
            if (btn.dataset.method === method) {
                btn.classList.add('antigravity-btn');
                btn.classList.remove('antigravity-btn-outline');
            } else {
                btn.classList.remove('antigravity-btn');
                btn.classList.add('antigravity-btn-outline');
            }
        });

        const cashSec = document.getElementById('cashInputSection');
        if (method === 'cash') {
            cashSec.style.display = 'block';
        } else {
            cashSec.style.display = 'none';
        }
        calcChange();
    }

    function calcChange() {
        const total = getTotalCart();
        if (selectedMethod !== 'cash') {
            displayKembalian.innerText = 'Rp 0';
            return;
        }

        const bayar = parseFloat(inputBayar.value) || 0;
        const kembalian = bayar - total;

        if (kembalian >= 0) {
            displayKembalian.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(kembalian);
            displayKembalian.style.color = '#059669';
        } else {
            displayKembalian.innerText = 'Kurang Rp ' + new Intl.NumberFormat('id-ID').format(Math.abs(kembalian));
            displayKembalian.style.color = '#DC2626';
        }
    }

    function setExactCash() {
        inputBayar.value = getTotalCart();
        calcChange();
    }

    function setPresetCash(val) {
        inputBayar.value = val;
        calcChange();
    }

    function addPresetCash(val) {
        const cur = parseFloat(inputBayar.value) || 0;
        inputBayar.value = cur + val;
        calcChange();
    }

    // 5. Checkout ACID Execution (FR-3.3)
    function processCheckout() {
        if (cart.length === 0) {
            alert('Keranjang belanja masih kosong! Tambahkan produk terlebih dahulu.');
            searchInput.focus();
            return;
        }

        const total = getTotalCart();
        const bayar = (selectedMethod === 'cash') ? (parseFloat(inputBayar.value) || 0) : total;

        if (selectedMethod === 'cash' && bayar < total) {
            alert('Jumlah uang pembayaran tunai kurang dari total belanja.');
            inputBayar.focus();
            return;
        }

        const btnSubmit = document.getElementById('btnSubmitCheckout');
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan Transaksi...';

        const payload = {
            items: cart.map(i => ({ id: i.id, qty: i.qty })),
            bayar: bayar,
            metode_pembayaran: selectedMethod,
            catatan: document.getElementById('inputCatatan').value
        };

        fetch('<?= base_url('pos/checkout') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfHash
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(res => {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="fa-solid fa-check-double"></i> Selesaikan Transaksi (F4)';

            if (res.status === 'success') {
                currentPenjualanId = res.penjualan_id;

                // Tampilkan Modal Sukses
                document.getElementById('successFaktur').innerText = res.nomor_faktur;
                document.getElementById('successTotal').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(res.total_omzet);
                document.getElementById('successBayar').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(res.bayar);
                document.getElementById('successKembalian').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(res.kembalian);

                openModal('modalSuccess');

                // Buka otomatis print dialog struk
                printStrukCurrent();
            } else {
                alert('Gagal menyelesaikan transaksi: ' + res.message);
            }
        })
        .catch(err => {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="fa-solid fa-check-double"></i> Selesaikan Transaksi (F4)';
            alert('Terjadi kesalahan jaringan atau server: ' + err);
        });
    }

    function printStrukCurrent() {
        if (!currentPenjualanId) return;
        const printUrl = '<?= base_url('transaksi/struk') ?>/' + currentPenjualanId;
        window.open(printUrl, '_blank', 'width=420,height=600');
    }

    function newTransaction() {
        closeModal('modalSuccess');
        cart = [];
        inputBayar.value = '';
        document.getElementById('inputCatatan').value = '';
        renderCart();
        searchInput.focus();
    }
</script>

<?= $this->endSection() ?>

