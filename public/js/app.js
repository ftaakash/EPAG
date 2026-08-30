/* public/js/app.js — EPAG Frontend Utilities */

// ── 1. Double-submit guard ────────────────────────────────────────────────────
// Disables the submit button on any form submission to prevent duplicate POSTs.
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function (e) {
      const btn = this.querySelector('button[type="submit"]');
      if (!btn) return;

      // If already submitted once, block subsequent clicks
      if (btn.dataset.submitted === 'true') {
        e.preventDefault();
        return;
      }

      btn.dataset.submitted = 'true';
      btn.disabled = true;
      btn.textContent = 'Processing…';

      // Re-enable after 5 s as a fallback (e.g. validation failure)
      setTimeout(() => {
        btn.disabled = false;
        btn.dataset.submitted = 'false';
        btn.textContent = btn.dataset.originalText || 'Submit';
      }, 5000);
    });

    // Store original button text before any change
    const btn = form.querySelector('button[type="submit"]');
    if (btn) btn.dataset.originalText = btn.textContent.trim();
  });

  // ── 2. Auto-dismiss alerts ─────────────────────────────────────────────────
  document.querySelectorAll('.alert').forEach(alert => {
    setTimeout(() => {
      alert.style.transition = 'opacity 0.5s ease';
      alert.style.opacity = '0';
      setTimeout(() => alert.remove(), 500);
    }, 4000);
  });

  // ── 3. Toast helper (can also be triggered programmatically) ───────────────
  window.showToast = function (msg, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = msg;
    document.body.appendChild(toast);
    setTimeout(() => {
      toast.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(20px)';
      setTimeout(() => toast.remove(), 400);
    }, 3000);
  };
});
