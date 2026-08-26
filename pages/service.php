<?php
require_once __DIR__ . '/../includes/auth_check.php';
?>
<div id="servicePage">

    <div class="service-header-block">
        <div class="page-head mb-3">
            <div>
                <h4>Mobile Servicing</h4>
                <p>Manage mobile repair jobs, parts used, and service charges</p>
            </div>
            <button class="ck-btn ck-btn-primary" id="btnNewService">
                <i class="fa-solid fa-plus"></i> New Service Entry
            </button>
        </div>

        <div class="ck-card mb-3">
            <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
                <div class="input-group-search flex-fill" style="min-width:220px;">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="serviceSearch" placeholder="Search customer, mobile, brand or model...">
                </div>
                <div class="service-status-tabs" id="serviceStatusTabs">
                    <div class="sst-item active" data-status="">All</div>
                    <div class="sst-item" data-status="pending">Pending</div>
                    <div class="sst-item" data-status="in_progress">In Progress</div>
                    <div class="sst-item" data-status="completed">Completed</div>
                    <div class="sst-item" data-status="delivered">Delivered</div>
                </div>
            </div>
        </div>
    </div>

    <div class="ck-card p-0 service-table-card">
        <div class="table-responsive table-scroll-box" id="serviceTableScroll">
            <table class="ck-table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Mobile</th>
                        <th>Problem</th>
                        <th>Service Charge</th>
                        <th>Parts Total</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th><?php echo lang('action'); ?></th>
                    </tr>
                </thead>
                <tbody id="serviceTableBody">
                    <tr><td colspan="10" class="text-center py-4 text-muted">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ============ MODAL: NEW SERVICE ENTRY ============ -->
<div class="ck-modal-overlay" id="serviceFormOverlay" style="display:none;">
    <div class="ck-modal-box" style="max-width:620px;">
        <div class="ck-modal-header">
            <h5>New Service Entry</h5>
            <i class="fa-solid fa-xmark ck-modal-close" data-close="serviceFormOverlay"></i>
        </div>

        <form id="serviceForm">
            <label class="ck-label">Customer</label>
            <div class="d-flex gap-2">
                <select class="ck-select" id="sCustomerSelect">
                    <option value="">-- Walk-in Customer --</option>
                </select>
                <button type="button" class="ck-btn ck-btn-outline" id="btnQuickAddServiceCustomer" style="padding:10px 14px;" title="Add New Customer">
                    <i class="fa-solid fa-plus"></i>
                </button>
            </div>

            <label class="ck-label mt-2">Mobile Name / Brand</label>
            <input type="text" class="ck-input" id="sMobileBrand" placeholder="e.g. Vivo Y20" required>

            <label class="ck-label mt-2">Problem Description</label>
            <textarea class="ck-input" id="sProblem" rows="2" placeholder="e.g. Display broken, not charging..." required></textarea>

            <label class="ck-label mt-2">Service Charge</label>
            <input type="number" step="0.01" min="0" class="ck-input" id="sServiceCharge" placeholder="0.00">

            <hr class="my-3">

            <label class="ck-label">Add Parts Used <span class="text-muted">(only from Parts category stock)</span></label>
            <div class="d-flex gap-2">
                <div class="ck-search-select flex-fill" id="sPartSearchWrap">
                    <input type="text" class="ck-input" id="sPartSearchInput" placeholder="Search part by name..." autocomplete="off">
                    <input type="hidden" id="sPartSelect" value="">
                    <div class="ck-search-dropdown" id="sPartDropdown" style="display:none;"></div>
                </div>
                <input type="number" class="ck-input" id="sPartQty" min="1" value="1" style="max-width:80px;">
                <button type="button" class="ck-btn ck-btn-outline" id="btnAddPart" style="padding:10px 14px;">
                    <i class="fa-solid fa-plus"></i>
                </button>
            </div>

            <div id="sPartsListBox" class="mt-2" style="display:none;">
                <table class="ck-table" style="font-size:12px;">
                    <thead>
                        <tr>
                            <th>Part</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="sPartsListBody"></tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between mt-2" style="font-size:12px;">
                <span class="text-muted">Parts Total</span>
                <span id="sPartsTotalDisplay" style="font-weight:600;">৳0.00</span>
            </div>

            <label class="ck-label mt-3">Discount <span class="text-muted">(optional)</span></label>
            <input type="number" step="0.01" min="0" class="ck-input" id="sDiscount" placeholder="0.00">

            <div class="ck-total-box mt-3">
                <span><?php echo lang('total_amount'); ?></span>
                <span id="sTotalDisplay">৳0.00</span>
            </div>

            <label class="ck-label mt-3">Pay Amount</label>
            <input type="number" step="0.01" min="0" class="ck-input" id="sPaidAmount" placeholder="0.00">

            <div class="d-flex justify-content-between mt-2" style="font-size:12px;">
                <span class="text-muted">Due Amount</span>
                <span id="sDueDisplay" style="font-weight:600;color:var(--danger);">৳0.00</span>
            </div>

            <div class="d-flex gap-2 mt-3">
                <button type="button" class="ck-btn ck-btn-outline flex-fill justify-content-center" data-close="serviceFormOverlay"><?php echo lang('cancel'); ?></button>
                <button type="submit" class="ck-btn ck-btn-primary flex-fill justify-content-center" id="serviceSaveBtn"><?php echo lang('save'); ?></button>
            </div>
        </form>
    </div>
</div>

<!-- ============ MODAL: QUICK ADD CUSTOMER (Service Page) ============ -->
<div class="ck-modal-overlay" id="serviceQuickCustomerOverlay" style="display:none;">
    <div class="ck-modal-box" style="max-width:380px;">
        <div class="ck-modal-header">
            <h5><?php echo lang('add_customer'); ?></h5>
            <i class="fa-solid fa-xmark ck-modal-close" data-close="serviceQuickCustomerOverlay"></i>
        </div>
        <form id="serviceQuickCustomerForm">
            <label class="ck-label"><?php echo lang('name'); ?></label>
            <input type="text" class="ck-input" id="sqcName" required>

            <label class="ck-label mt-2"><?php echo lang('mobile'); ?></label>
            <input type="text" class="ck-input" id="sqcMobile" required>

            <label class="ck-label mt-2"><?php echo lang('address'); ?> <span class="text-muted">(optional)</span></label>
            <input type="text" class="ck-input" id="sqcAddress">

            <div class="d-flex gap-2 mt-3">
                <button type="button" class="ck-btn ck-btn-outline flex-fill justify-content-center" data-close="serviceQuickCustomerOverlay"><?php echo lang('cancel'); ?></button>
                <button type="submit" class="ck-btn ck-btn-primary flex-fill justify-content-center"><?php echo lang('save'); ?></button>
            </div>
        </form>
    </div>
</div>

<!-- ============ MODAL: VIEW PARTS DETAIL ============ -->
<div class="ck-modal-overlay" id="servicePartsOverlay" style="display:none;">
    <div class="ck-modal-box" style="max-width:420px;">
        <div class="ck-modal-header">
            <h5>Parts Used</h5>
            <i class="fa-solid fa-xmark ck-modal-close" data-close="servicePartsOverlay"></i>
        </div>
        <div id="servicePartsDetailBody" style="font-size:13px;"></div>
    </div>
</div>

<!-- ============ MODAL: PAY DUE ============ -->
<div class="ck-modal-overlay" id="servicePayDueOverlay" style="display:none;">
    <div class="ck-modal-box" style="max-width:380px;">
        <div class="ck-modal-header">
            <h5>Pay Due</h5>
            <i class="fa-solid fa-xmark ck-modal-close" data-close="servicePayDueOverlay"></i>
        </div>
        <form id="servicePayDueForm">
            <input type="hidden" id="servicePayDueId">
            <p style="font-size:13px;">Customer: <strong id="servicePayDueCustomer"></strong></p>
            <p class="text-muted" style="font-size:12px;">Due Amount: <span id="servicePayDueAmountText" style="font-weight:600;color:var(--danger);"></span></p>

            <label class="ck-label mt-2"><?php echo lang('amount'); ?></label>
            <input type="number" step="0.01" min="0.01" class="ck-input" id="servicePayDueAmountInput" required>

            <div class="d-flex gap-2 mt-3">
                <button type="button" class="ck-btn ck-btn-outline flex-fill justify-content-center" data-close="servicePayDueOverlay"><?php echo lang('cancel'); ?></button>
                <button type="submit" class="ck-btn ck-btn-primary flex-fill justify-content-center">Pay</button>
            </div>
        </form>
    </div>
</div>

<!-- ============ PAGE-SPECIFIC STYLES ============ -->
<style>
    #servicePage {
        display: flex;
        flex-direction: column;
        height: calc(100vh - var(--topbar-height) - 52px);
        height: calc(100svh - var(--topbar-height) - 52px);
    }

    @media (max-width: 991px) {
        #servicePage {
            height: calc(100vh - var(--topbar-height) - 108px);
            height: calc(100svh - var(--topbar-height) - 108px);
        }
    }

    .service-header-block { flex-shrink: 0; }

    .service-table-card {
        flex: 1 1 auto;
        min-height: 0;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .table-scroll-box {
        flex: 1 1 auto;
        min-height: 0;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }
    .table-scroll-box::-webkit-scrollbar { display: none; }

    .table-scroll-box .ck-table thead th {
        position: sticky;
        top: 0;
        z-index: 5;
    }

    @media (max-width: 767px) {
        .table-scroll-box { padding: 4px 4px 12px; }
    }

    .service-status-tabs {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }
    .sst-item {
        font-size: 11px;
        font-weight: 500;
        padding: 6px 12px;
        border-radius: 8px;
        background: var(--body-bg);
        color: var(--text-muted);
        cursor: pointer;
        white-space: nowrap;
        border: 1px solid var(--border-color);
    }
    .sst-item.active {
        background: var(--primary-blue);
        color: #fff;
        border-color: var(--primary-blue);
    }

    .status-select {
        font-size: 11px;
        padding: 5px 8px;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        background: #fff;
        cursor: pointer;
    }

    .ck-search-select { position: relative; }
    .ck-search-dropdown {
        position: absolute;
        top: calc(100% + 4px);
        left: 0;
        right: 0;
        background: #fff;
        border: 1.5px solid var(--border-color);
        border-radius: 10px;
        max-height: 240px;
        overflow-y: auto;
        z-index: 60;
        box-shadow: 0 10px 28px rgba(0,0,0,0.14);
    }
    .ck-search-item {
        padding: 9px 14px;
        font-size: 13px;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        border-bottom: 1px solid var(--body-bg);
    }
    .ck-search-item:last-child { border-bottom: none; }
    .ck-search-item:hover,
    .ck-search-item.ck-search-item-active { background: var(--light-blue); }
    .ck-search-item .csi-name { font-weight: 500; }
    .ck-search-item .csi-stock {
        font-size: 11px;
        color: var(--text-muted);
        white-space: nowrap;
        flex-shrink: 0;
    }
    .ck-search-item.ck-search-item-disabled { opacity: 0.5; cursor: not-allowed; }
    .ck-search-empty {
        padding: 16px 14px;
        font-size: 12px;
        color: var(--text-muted);
        text-align: center;
    }
</style>

<script>
(function () {
    let partsCache = [];
    let customersCache = [];
    let selectedParts = []; // [{product_id, name, price, stock, quantity}]
    let currentStatusFilter = '';

    function money(v) {
        return '৳' + parseFloat(v || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function formatDate(dateStr) {
        const d = new Date(dateStr.replace(' ', 'T'));
        return d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    }

    function statusLabel(status) {
        const map = {
            pending: { label: 'Pending', bg: '#fff7ed', color: '#d97706' },
            in_progress: { label: 'In Progress', bg: '#eff6ff', color: '#2563eb' },
            completed: { label: 'Completed', bg: '#f0fdf4', color: '#16a34a' },
            delivered: { label: 'Delivered', bg: '#f0fdf4', color: '#15803d' }
        };
        return map[status] || map.pending;
    }

    /* ============ LOAD SERVICE LIST ============ */
    async function loadServiceList(search = '') {
        const tbody = document.getElementById('serviceTableBody');
        try {
            const url = 'api/service/list.php?search=' + encodeURIComponent(search) + '&status=' + encodeURIComponent(currentStatusFilter);
            const res = await fetch(url);
            const result = await res.json();

            if (result.status !== 'success') {
                tbody.innerHTML = `<tr><td colspan="10" class="text-center py-4 text-danger">Failed to load</td></tr>`;
                return;
            }
            if (result.data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="10" class="text-center py-4 text-muted"><?php echo lang('no_data'); ?></td></tr>`;
                return;
            }

            tbody.innerHTML = result.data.map(s => {
                let paymentHtml;
                if (s.due_amount > 0 && s.paid_amount > 0) {
                    paymentHtml = `<span class="badge-due">Partial</span><div style="font-size:10px;color:var(--danger);margin-top:2px;">Due: ${money(s.due_amount)}</div>`;
                } else if (s.due_amount > 0) {
                    paymentHtml = `<span class="badge-due"><?php echo lang('due'); ?></span><div style="font-size:10px;color:var(--danger);margin-top:2px;">Due: ${money(s.due_amount)}</div>`;
                } else {
                    paymentHtml = `<span class="badge-cash">Paid</span>`;
                }

                const st = statusLabel(s.status);
                const problemShort = s.problem_description.length > 40 ? s.problem_description.slice(0, 40) + '…' : s.problem_description;

                return `
                <tr>
                    <td data-label="Customer" style="font-weight:500;">${s.customer_name}<div style="font-size:10px;color:var(--text-muted);">${s.customer_mobile}</div></td>
                    <td data-label="Mobile">${s.mobile_brand}${s.mobile_model ? ' ' + s.mobile_model : ''}</td>
                    <td data-label="Problem" title="${s.problem_description.replace(/"/g, '&quot;')}">${problemShort}</td>
                    <td data-label="Service Charge">${money(s.service_charge)}</td>
                    <td data-label="Parts Total">
                        ${money(s.parts_total)}
                        ${s.parts && s.parts.length ? `<div><a href="javascript:void(0)" style="font-size:10px;" onclick='showPartsDetail(${JSON.stringify(s.parts)})'>${s.parts.length} item(s)</a></div>` : ''}
                    </td>
                    <td data-label="Total" style="font-weight:600;">${money(s.total_amount)}</td>
                    <td data-label="Payment">${paymentHtml}</td>
                    <td data-label="Status">
                        <select class="status-select" style="background:${st.bg};color:${st.color};" onchange='changeServiceStatus(${s.id}, this.value)'>
                            <option value="pending" ${s.status === 'pending' ? 'selected' : ''}>Pending</option>
                            <option value="in_progress" ${s.status === 'in_progress' ? 'selected' : ''}>In Progress</option>
                            <option value="completed" ${s.status === 'completed' ? 'selected' : ''}>Completed</option>
                            <option value="delivered" ${s.status === 'delivered' ? 'selected' : ''}>Delivered</option>
                        </select>
                    </td>
                    <td data-label="<?php echo lang('date'); ?>">${formatDate(s.created_at)}</td>
                    <td data-label="<?php echo lang('action'); ?>">
                        <div class="d-flex gap-2 justify-content-end">
                            ${s.due_amount > 0 ? `<button class="ck-btn ck-btn-success-soft" style="padding:6px 10px;font-size:11px;" onclick='openServicePayDue(${JSON.stringify({ id: s.id, customer_name: s.customer_name, due_amount: s.due_amount })})'><i class="fa-solid fa-hand-holding-dollar"></i> Pay</button>` : ''}
                            <button class="icon-btn ck-btn-outline" onclick='deleteService(${s.id})' title="Delete"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
            `;
            }).join('');
        } catch (err) {
            tbody.innerHTML = `<tr><td colspan="10" class="text-center py-4 text-danger">Error loading data</td></tr>`;
        }
    }

    let searchTimer;
    document.getElementById('serviceSearch').addEventListener('input', function () {
        clearTimeout(searchTimer);
        const val = this.value;
        searchTimer = setTimeout(() => loadServiceList(val), 350);
    });

    document.querySelectorAll('#serviceStatusTabs .sst-item').forEach(el => {
        el.addEventListener('click', function () {
            document.querySelectorAll('#serviceStatusTabs .sst-item').forEach(x => x.classList.remove('active'));
            this.classList.add('active');
            currentStatusFilter = this.dataset.status;
            loadServiceList(document.getElementById('serviceSearch').value);
        });
    });

    /* ============ CHANGE STATUS INLINE ============ */
    window.changeServiceStatus = async function (id, status) {
        try {
            const res = await fetch('api/service/update_status.php', {
                method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ id, status })
            });
            const result = await res.json();
            if (result.status === 'success') {
                ckToast('success', result.message);
            } else {
                ckToast('error', result.message);
                loadServiceList(document.getElementById('serviceSearch').value);
            }
        } catch (err) {
            ckToast('error', 'Failed to update status');
        }
    };

    /* ============ SHOW PARTS DETAIL ============ */
    window.showPartsDetail = function (parts) {
        const box = document.getElementById('servicePartsDetailBody');
        box.innerHTML = `
            <table class="ck-table">
                <thead><tr><th>Part</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
                <tbody>
                    ${parts.map(p => `<tr><td>${p.product_name}</td><td>${p.quantity}</td><td>${money(p.price)}</td><td>${money(p.total)}</td></tr>`).join('')}
                </tbody>
            </table>
        `;
        document.getElementById('servicePartsOverlay').style.display = 'flex';
    };

    /* ============ DELETE SERVICE ============ */
    window.deleteService = async function (id) {
        const result = await ckConfirm('This will delete the service entry, restore part stock, and reverse cash/due amounts.');
        if (!result.isConfirmed) return;

        try {
            const res = await fetch('api/service/delete.php', {
                method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ id })
            });
            const data = await res.json();
            if (data.status === 'success') {
                ckToast('success', data.message);
                updateCashBalance(data.cash_balance);
                loadServiceList(document.getElementById('serviceSearch').value);
                if (typeof loadLowStockAlert === 'function') loadLowStockAlert();
            } else {
                ckToast('error', data.message);
            }
        } catch (err) {
            ckToast('error', 'Failed to delete service entry');
        }
    };

    /* ============ LOAD FORM DATA (Parts + Customers) ============ */
    async function loadFormData() {
        try {
            const res = await fetch('api/service/form_data.php');
            const result = await res.json();
            if (result.status === 'success') {
                partsCache = result.data.parts;
                customersCache = result.data.customers;

                document.getElementById('sPartSearchInput').value = '';
                document.getElementById('sPartSelect').value = '';

                const custSelect = document.getElementById('sCustomerSelect');
                custSelect.innerHTML = '<option value="">-- Walk-in Customer --</option>' +
                    customersCache.map(c => `<option value="${c.id}">${c.name} - ${c.mobile}</option>`).join('');
            }
        } catch (err) { /* silent */ }
    }

    /* ============ SEARCHABLE PARTS DROPDOWN ============ */
    let partSearchActiveIndex = -1;
    let partSearchCurrentResults = [];

    function getFilteredParts(term) {
        const t = term.trim().toLowerCase();
        if (!t) return partsCache;
        return partsCache.filter(p => p.name.toLowerCase().includes(t));
    }

    function renderPartDropdown(term) {
        const dropdown = document.getElementById('sPartDropdown');
        const results = getFilteredParts(term);
        partSearchCurrentResults = results;
        partSearchActiveIndex = -1;

        if (results.length === 0) {
            dropdown.innerHTML = '<div class="ck-search-empty">No parts found</div>';
        } else {
            dropdown.innerHTML = results.map((p, idx) => `
                <div class="ck-search-item" data-idx="${idx}" data-id="${p.id}">
                    <span class="csi-name">${p.name}</span>
                    <span class="csi-stock">Stock: ${p.stock} - ${money(p.sale_price)}</span>
                </div>
            `).join('');
        }

        dropdown.style.display = 'block';
    }

    function closePartDropdown() {
        document.getElementById('sPartDropdown').style.display = 'none';
        partSearchActiveIndex = -1;
    }

    function selectPart(part) {
        document.getElementById('sPartSelect').value = part.id;
        document.getElementById('sPartSearchInput').value = `${part.name} (Stock: ${part.stock}) - ${money(part.sale_price)}`;
        closePartDropdown();
    }

    function highlightPartItem(index) {
        const items = document.querySelectorAll('#sPartDropdown .ck-search-item');
        items.forEach(el => el.classList.remove('ck-search-item-active'));
        if (items[index]) {
            items[index].classList.add('ck-search-item-active');
            items[index].scrollIntoView({ block: 'nearest' });
        }
    }

    document.getElementById('sPartSearchInput').addEventListener('input', function () {
        document.getElementById('sPartSelect').value = '';
        renderPartDropdown(this.value);
    });

    document.getElementById('sPartSearchInput').addEventListener('focus', function () {
        renderPartDropdown(this.value);
    });

    document.getElementById('sPartSearchInput').addEventListener('keydown', function (e) {
        const dropdown = document.getElementById('sPartDropdown');
        if (dropdown.style.display === 'none') return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (partSearchCurrentResults.length === 0) return;
            partSearchActiveIndex = (partSearchActiveIndex + 1) % partSearchCurrentResults.length;
            highlightPartItem(partSearchActiveIndex);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (partSearchCurrentResults.length === 0) return;
            partSearchActiveIndex = (partSearchActiveIndex - 1 + partSearchCurrentResults.length) % partSearchCurrentResults.length;
            highlightPartItem(partSearchActiveIndex);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (partSearchActiveIndex >= 0 && partSearchCurrentResults[partSearchActiveIndex]) {
                selectPart(partSearchCurrentResults[partSearchActiveIndex]);
            } else if (partSearchCurrentResults.length === 1) {
                selectPart(partSearchCurrentResults[0]);
            }
        } else if (e.key === 'Escape') {
            closePartDropdown();
        }
    });

    document.getElementById('sPartDropdown').addEventListener('click', function (e) {
        const item = e.target.closest('.ck-search-item');
        if (!item) return;
        const idx = parseInt(item.getAttribute('data-idx'));
        const part = partSearchCurrentResults[idx];
        if (part) selectPart(part);
    });

    document.addEventListener('click', function (e) {
        const wrap = document.getElementById('sPartSearchWrap');
        if (wrap && !wrap.contains(e.target)) {
            closePartDropdown();
        }
    });

    /* ============ QUICK ADD CUSTOMER (Service Page) ============ */
    document.getElementById('btnQuickAddServiceCustomer').addEventListener('click', () => {
        document.getElementById('serviceQuickCustomerForm').reset();
        document.getElementById('serviceQuickCustomerOverlay').style.display = 'flex';
    });

    document.querySelectorAll('#serviceQuickCustomerOverlay [data-close], #serviceQuickCustomerOverlay .ck-modal-close').forEach(el => {
        el.addEventListener('click', () => document.getElementById('serviceQuickCustomerOverlay').style.display = 'none');
    });

    document.getElementById('serviceQuickCustomerForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        const payload = {
            name: document.getElementById('sqcName').value.trim(),
            mobile: document.getElementById('sqcMobile').value.trim(),
            address: document.getElementById('sqcAddress').value.trim()
        };
        try {
            const res = await fetch('api/customer/add.php', {
                method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload)
            });
            const result = await res.json();
            if (result.status === 'success') {
                ckToast('success', result.message);
                document.getElementById('serviceQuickCustomerOverlay').style.display = 'none';
                await loadFormData();
                document.getElementById('sCustomerSelect').value = result.data.id;
            } else {
                ckToast('error', result.message);
            }
        } catch (err) {
            ckToast('error', 'Failed to add customer');
        }
    });

    /* ============ ADD PART ROW ============ */
    document.getElementById('btnAddPart').addEventListener('click', function () {
        const partId = document.getElementById('sPartSelect').value;
        const qty = parseInt(document.getElementById('sPartQty').value) || 0;

        if (!partId) { ckToast('warning', 'Please select a part'); return; }
        if (qty <= 0) { ckToast('warning', 'Quantity must be greater than 0'); return; }

        const part = partsCache.find(p => p.id == partId);
        if (!part) return;

        const existing = selectedParts.find(sp => sp.product_id == partId);
        const alreadyQty = existing ? existing.quantity : 0;

        if ((alreadyQty + qty) > part.stock) {
            ckToast('error', `Insufficient Stock. Available: ${part.stock}`);
            return;
        }

        if (existing) {
            existing.quantity += qty;
        } else {
            selectedParts.push({
                product_id: part.id,
                name: part.name,
                price: parseFloat(part.sale_price),
                stock: part.stock,
                quantity: qty
            });
        }

        document.getElementById('sPartSelect').value = '';
        document.getElementById('sPartSearchInput').value = '';
        document.getElementById('sPartQty').value = 1;
        closePartDropdown();
        renderPartsList();
    });

    function renderPartsList() {
        const box = document.getElementById('sPartsListBox');
        const body = document.getElementById('sPartsListBody');

        if (selectedParts.length === 0) {
            box.style.display = 'none';
            body.innerHTML = '';
        } else {
            box.style.display = 'block';
            body.innerHTML = selectedParts.map((p, idx) => `
                <tr>
                    <td>${p.name}</td>
                    <td>${p.quantity}</td>
                    <td>${money(p.price)}</td>
                    <td>${money(p.price * p.quantity)}</td>
                    <td><i class="fa-solid fa-trash text-danger" style="cursor:pointer;" onclick="removePart(${idx})"></i></td>
                </tr>
            `).join('');
        }

        recalcTotals();
    }

    window.removePart = function (idx) {
        selectedParts.splice(idx, 1);
        renderPartsList();
    };

    /* ============ RECALCULATE TOTALS ============ */
    function recalcTotals() {
        const partsTotal = selectedParts.reduce((sum, p) => sum + (p.price * p.quantity), 0);
        const serviceCharge = parseFloat(document.getElementById('sServiceCharge').value) || 0;
        let discount = parseFloat(document.getElementById('sDiscount').value) || 0;

        const gross = serviceCharge + partsTotal;
        if (discount > gross) {
            discount = gross;
            document.getElementById('sDiscount').value = gross.toFixed(2);
        }
        const total = gross - discount;

        let paid = parseFloat(document.getElementById('sPaidAmount').value) || 0;
        if (paid > total) {
            paid = total;
            document.getElementById('sPaidAmount').value = total.toFixed(2);
        }
        const due = total - paid;

        document.getElementById('sPartsTotalDisplay').textContent = money(partsTotal);
        document.getElementById('sTotalDisplay').textContent = money(total);
        document.getElementById('sDueDisplay').textContent = money(due);
    }

    document.getElementById('sServiceCharge').addEventListener('input', recalcTotals);
    document.getElementById('sDiscount').addEventListener('input', recalcTotals);
    document.getElementById('sPaidAmount').addEventListener('input', recalcTotals);

    /* ============ OPEN NEW SERVICE MODAL ============ */
    document.getElementById('btnNewService').addEventListener('click', async () => {
        document.getElementById('serviceForm').reset();
        selectedParts = [];
        renderPartsList();
        closePartDropdown();
        await loadFormData();
        document.getElementById('serviceFormOverlay').style.display = 'flex';
    });

    /* ============ SUBMIT SERVICE FORM ============ */
    document.getElementById('serviceForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        const saveBtn = document.getElementById('serviceSaveBtn');
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';

        const payload = {
            customer_id: document.getElementById('sCustomerSelect').value,
            mobile_brand: document.getElementById('sMobileBrand').value.trim(),
            problem_description: document.getElementById('sProblem').value.trim(),
            service_charge: document.getElementById('sServiceCharge').value || 0,
            discount_amount: document.getElementById('sDiscount').value || 0,
            paid_amount: document.getElementById('sPaidAmount').value || 0,
            parts: selectedParts.map(p => ({ product_id: p.product_id, quantity: p.quantity }))
        };

        try {
            const res = await fetch('api/service/add.php', {
                method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload)
            });
            const result = await res.json();
            if (result.status === 'success') {
                ckToast('success', result.message);
                document.getElementById('serviceFormOverlay').style.display = 'none';
                updateCashBalance(result.cash_balance);
                loadServiceList();
                if (typeof loadLowStockAlert === 'function') loadLowStockAlert();
            } else {
                ckToast('error', result.message);
            }
        } catch (err) {
            ckToast('error', 'Failed to save service entry');
        } finally {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<?php echo lang('save'); ?>';
        }
    });

    /* ============ PAY DUE ============ */
    window.openServicePayDue = function (s) {
        document.getElementById('servicePayDueId').value = s.id;
        document.getElementById('servicePayDueCustomer').textContent = s.customer_name;
        document.getElementById('servicePayDueAmountText').textContent = money(s.due_amount);
        document.getElementById('servicePayDueAmountInput').value = '';
        document.getElementById('servicePayDueAmountInput').max = s.due_amount;
        document.getElementById('servicePayDueOverlay').style.display = 'flex';
    };

    document.getElementById('servicePayDueForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        const payload = {
            id: document.getElementById('servicePayDueId').value,
            amount: document.getElementById('servicePayDueAmountInput').value
        };
        try {
            const res = await fetch('api/service/pay_due.php', {
                method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload)
            });
            const result = await res.json();
            if (result.status === 'success') {
                ckToast('success', result.message);
                document.getElementById('servicePayDueOverlay').style.display = 'none';
                updateCashBalance(result.cash_balance);
                loadServiceList(document.getElementById('serviceSearch').value);
            } else {
                ckToast('error', result.message);
            }
        } catch (err) {
            ckToast('error', 'Failed to pay due');
        }
    });

    /* ============ CLOSE MODALS ============ */
    document.querySelectorAll('[data-close], .ck-modal-close').forEach(el => {
        el.addEventListener('click', function () {
            const target = this.dataset.close;
            if (target) document.getElementById(target).style.display = 'none';
        });
    });

    loadServiceList();
})();
</script>