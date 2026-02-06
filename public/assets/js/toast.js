document.addEventListener('DOMContentLoaded', function () {
  const VISIBLE_DURATION = 7000; // ms visible before exit
  const EXIT_ANIM_MS = 300; // wait for exit animation before removing DOM

  function closeToastElement(el) {
    if (!el) return;
    el.classList.remove('opacity-100', 'translate-y-0');
    setTimeout(() => {
      if (el && el.parentNode) el.remove();
    }, EXIT_ANIM_MS);
  }

  const toasts = Array.from(document.querySelectorAll('.toast-stack .toast'));
  toasts.forEach((t) => {
    // trigger enter animation
    requestAnimationFrame(() => t.classList.add('opacity-100', 'translate-y-0'));

    // auto close after duration
    const autoClose = setTimeout(() => closeToastElement(t), VISIBLE_DURATION);

    // handle manual close
    const btn = t.querySelector('.toast-close');
    if (btn) {
      btn.addEventListener('click', () => {
        clearTimeout(autoClose);
        closeToastElement(t);
      });
    }

    // also close on Escape when focused inside the toast
    t.addEventListener('keydown', (ev) => {
      if (ev.key === 'Escape') {
        clearTimeout(autoClose);
        closeToastElement(t);
      }
    });
  });
});
