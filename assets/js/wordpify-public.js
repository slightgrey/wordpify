/* Wordpify Public JS */
(function () {
  'use strict';

  const cfg     = window.wordpifyPublic ?? {};
  const restUrl = (cfg.restUrl ?? '').replace(/\/$/, '') + '/wordpify/v1';
  const nonce   = cfg.nonce ?? '';
  const cartUrl = cfg.cartUrl ?? '/cart/';

  async function api(path, options = {}) {
    const res = await fetch(restUrl + path, {
      ...options,
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': nonce,
        ...(options.headers ?? {}),
      },
      body: options.body ? JSON.stringify(options.body) : undefined,
    });
    if (!res.ok) {
      const body = await res.json().catch(() => ({}));
      throw new Error(body.error || `HTTP ${res.status}`);
    }
    return res.json();
  }

  // --- Toast ---
  function showToast(message, type) {
    let el = document.getElementById('wordpify-toast');
    if (!el) {
      el = document.createElement('div');
      el.id = 'wordpify-toast';
      document.body.appendChild(el);
    }
    el.className = 'wordpify-toast' + (type === 'error' ? ' wordpify-toast--error' : '');
    el.textContent = message;
    void el.offsetWidth; // force reflow so transition fires
    el.classList.add('wordpify-toast--show');
    clearTimeout(el._t);
    el._t = setTimeout(() => el.classList.remove('wordpify-toast--show'), 2500);
  }

  // --- Checkout button loading ---
  function setCheckoutLoading(on) {
    const btn = document.querySelector('.wordpify-checkout-btn');
    if (!btn) return;
    if (on) {
      btn.dataset.origHref = btn.getAttribute('href') || '';
      btn.dataset.origText = btn.textContent.trim();
      btn.removeAttribute('href');
      btn.classList.add('is-updating');
      btn.textContent = 'Updating…';
    } else {
      if (btn.dataset.origHref) btn.setAttribute('href', btn.dataset.origHref);
      btn.classList.remove('is-updating');
      btn.textContent = btn.dataset.origText || 'Proceed to Checkout';
    }
  }

  // --- Add to cart ---
  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.wordpify-add-to-cart');
    if (!btn) return;
    e.preventDefault();

    const variantId = btn.dataset.variantId;
    if (!variantId) return;

    const productId = btn.dataset.productId;
    let activeVariantId = variantId;
    if (productId) {
      const sel = document.querySelector(`#wordpify-variant-select-${productId}`);
      if (sel) activeVariantId = sel.value;
    }

    btn.classList.add('is-loading');
    btn.disabled = true;
    const origText = btn.textContent;
    btn.textContent = 'Adding…';

    try {
      await api('/cart/items', {
        method: 'POST',
        body: { lines: [{ merchandiseId: activeVariantId, quantity: 1 }] },
      });
      refreshCartCount();
      showToast('Added to cart!');
    } catch (err) {
      console.error('Wordpify: add to cart failed', err);
      showToast(err.message || 'Could not add to cart.', 'error');
    } finally {
      btn.classList.remove('is-loading');
      btn.disabled = false;
      btn.textContent = origText;
    }
  });

  // --- Qty update (debounced 800ms) ---
  const qtyTimers  = {};
  const pendingQty = {};

  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.wordpify-qty-btn');
    if (!btn) return;
    e.preventDefault();

    const lineId = btn.dataset.lineId;
    const qty    = parseInt(btn.dataset.quantity, 10);

    // Update DOM immediately so clicks feel instant
    const item = btn.closest('.wordpify-cart-item');
    if (item) {
      const span  = item.querySelector('.wordpify-cart-item__qty span');
      const minus = item.querySelector('.wordpify-qty-minus');
      const plus  = item.querySelector('.wordpify-qty-plus');
      if (span)  span.textContent = Math.max(0, qty);
      if (minus) minus.dataset.quantity = String(Math.max(0, qty - 1));
      if (plus)  plus.dataset.quantity  = String(qty + 1);
    }

    pendingQty[lineId] = qty;
    clearTimeout(qtyTimers[lineId]);

    qtyTimers[lineId] = setTimeout(async () => {
      const finalQty = pendingQty[lineId];
      delete pendingQty[lineId];

      setCheckoutLoading(true);
      try {
        if (finalQty <= 0) {
          await api('/cart/items/remove', { method: 'POST', body: { lineId } });
        } else {
          await api('/cart/items/update', { method: 'POST', body: { lineId, quantity: finalQty } });
        }
        location.reload();
      } catch (err) {
        setCheckoutLoading(false);
        showToast(err.message || 'Could not update cart.', 'error');
        location.reload();
      }
    }, 800);
  });

  // --- Remove item ---
  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.wordpify-cart-item__remove');
    if (!btn) return;
    e.preventDefault();

    const lineId = btn.dataset.lineId;

    // Cancel any pending qty debounce for this line
    clearTimeout(qtyTimers[lineId]);
    delete pendingQty[lineId];

    setCheckoutLoading(true);
    try {
      await api('/cart/items/remove', { method: 'POST', body: { lineId } });
      location.reload();
    } catch (err) {
      setCheckoutLoading(false);
      showToast(err.message || 'Could not remove item.', 'error');
    }
  });

  // --- Account form ---
  document.addEventListener('submit', async (e) => {
    const form = e.target.closest('#wordpify-account-form');
    if (!form) return;
    e.preventDefault();

    const msg = document.getElementById('wordpify-account-message');
    const data = Object.fromEntries(new FormData(form));

    try {
      const res = await api('/account', { method: 'POST', body: data });
      if (msg) { msg.textContent = 'Saved successfully.'; msg.style.color = 'green'; }
    } catch (err) {
      if (msg) { msg.textContent = 'Error saving changes.'; msg.style.color = 'red'; }
    }
  });

  // --- Address form ---
  const addBtn    = document.getElementById('wordpify-add-address');
  const addrForm  = document.getElementById('wordpify-address-form');
  const cancelBtn = document.getElementById('wordpify-address-cancel');
  const saveBtn   = document.getElementById('wordpify-address-save');

  if (addBtn && addrForm) {
    addBtn.addEventListener('click', () => { addrForm.hidden = false; });
    cancelBtn?.addEventListener('click', () => { addrForm.hidden = true; });

    // Edit
    document.addEventListener('click', (e) => {
      const btn = e.target.closest('.wordpify-address-edit');
      if (!btn) return;
      const card = btn.closest('.wordpify-address-card');
      document.getElementById('wordpify-address-id').value = btn.dataset.addressId;
      // Populate fields from card text (simple approach — a real impl would store data attrs).
      addrForm.hidden = false;
    });

    // Delete
    document.addEventListener('click', async (e) => {
      const btn = e.target.closest('.wordpify-address-delete');
      if (!btn) return;
      if (!confirm('Delete this address?')) return;
      const addressId = btn.dataset.addressId;
      try {
        await api('/addresses/' + encodeURIComponent(addressId), { method: 'DELETE' });
        location.reload();
      } catch (err) {
        alert('Error deleting address.');
      }
    });

    saveBtn?.addEventListener('click', async () => {
      const addressId = document.getElementById('wordpify-address-id').value;
      const fields    = ['firstName','lastName','address1','address2','city','province','zip','country','phone'];
      const address   = {};
      fields.forEach(f => {
        const el = document.getElementById('wordpify-addr-' + f);
        if (el) address[f] = el.value;
      });
      const msg = document.getElementById('wordpify-address-message');

      try {
        const path = addressId ? `/addresses/${encodeURIComponent(addressId)}` : '/addresses';
        const method = addressId ? 'POST' : 'POST';
        await api(path, { method, body: { address } });
        location.reload();
      } catch (err) {
        if (msg) { msg.textContent = 'Error saving address.'; msg.style.color = 'red'; }
      }
    });
  }

  // --- Checkout redirect ---
  const checkoutRedirect = document.getElementById('wordpify-checkout-redirect');
  if (checkoutRedirect) {
    api('/cart/checkout').then(data => {
      if (!data.checkout_url || !data.total_quantity) {
        window.location.replace(cartUrl);
        return;
      }
      setTimeout(() => {
        window.location.href = data.checkout_url;
      }, 300);
    }).catch(() => {
      showToast('Could not load checkout.', 'error');
      setTimeout(() => window.location.replace(cartUrl), 1500);
    });
  }

  // --- Proceed to Checkout ---
  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.wordpify-checkout-btn');
    if (!btn) return;
    e.preventDefault();

    const origText = btn.textContent.trim();
    btn.classList.add('is-updating');
    btn.textContent = 'Redirecting…';

    try {
      const data = await api('/cart/checkout');
      if (data.checkout_url) {
        window.location.href = data.checkout_url;
      }
    } catch (err) {
      btn.classList.remove('is-updating');
      btn.textContent = origText;
      showToast(err.message || 'Could not proceed to checkout.', 'error');
    }
  });

  // --- Refresh cart count (mini-cart) ---
  async function refreshCartCount() {
    try {
      const cart  = await api('/cart');
      const count = document.getElementById('wordpify-cart-count');
      if (count) count.textContent = cart.totalQuantity ?? 0;
    } catch { /* silent */ }
  }

  // Init cart count on load.
  if (document.getElementById('wordpify-cart-count')) {
    refreshCartCount();
  }
})();
