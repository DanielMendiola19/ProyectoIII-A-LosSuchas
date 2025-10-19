(function () {
  const $$ = (sel, ctx = document) => Array.from((ctx || document).querySelectorAll(sel));
  const $  = (sel, ctx = document) => (ctx || document).querySelector(sel);

  

  const alertTop = document.getElementById('inv-alert');
  let alertTimer = null;

  function showAlertTop(message, type = 'err', ms = 2500) {
    if (!alertTop) return;
    alertTop.className = `inv-alert ${type}`;
    alertTop.textContent = message;
    alertTop.classList.add('show');
    clearTimeout(alertTimer);
    alertTimer = setTimeout(() => alertTop.classList.remove('show'), ms);
  }


  const overlay   = $('#inv-overlay');   
  const pop       = $('#inv-confirm');     
  const popText   = $('.confirm-text', pop);
  const btnOK     = $('.btn-ok', pop);
  const btnCancel = $('.btn-cancel', pop);

  let pending = null; 

  function openConfirmFor(btn, data) {
    pending = { button: btn, ...data };
    if (popText) {
      popText.innerHTML = `¿Seguro de <b>${data.action}</b> <b>${data.cantidad}</b> de <b style="color:var(--dorado)">${data.name}</b>?`;
    }
    overlay?.classList.remove('hidden');
    if (pop) {
      pop.classList.remove('hidden');
      $('.btn-ok', pop)?.focus();
    }
  }

  function closeConfirm() {
    overlay?.classList.add('hidden');
    pop?.classList.add('hidden');
    pending = null;
  }

  btnCancel?.addEventListener('click', closeConfirm);
  overlay?.addEventListener('click', closeConfirm);
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && pop && !pop.classList.contains('hidden')) closeConfirm();
  });

  /* =========================
   *  Contadores + / -
   * ========================= */
  $$('.contador').forEach(cont => {
    const input = $('.input-cant', cont);
    if (!input) return;

    $('.mas', cont)?.addEventListener('click', () => {
      const maxAdd = parseInt(input.dataset.maxAdd);
      const v = parseInt(input.value || '1');
      input.value = isNaN(maxAdd) ? (v + 1) : Math.min(v + 1, Math.max(1, maxAdd));
    });

    $('.menos', cont)?.addEventListener('click', () => {
      const v = parseInt(input.value || '1');
      input.value = Math.max(1, v - 1);
    });

    input.addEventListener('input', () => {
      let v = parseInt(input.value || '1');
      if (isNaN(v) || v < 1) input.value = 1;
    });
  });


  $$('.js-accion').forEach(btn => {
    btn.addEventListener('click', () => {
      const row   = btn.closest('tr');
      const input = $('.input-cant', row);
      if (!row || !input) return;

      const name     = btn.dataset.name;
      const url      = btn.dataset.url;
      const action   = btn.dataset.action; // 'aumentar' | 'disminuir'
      const cantidad = parseInt(input.value || '1');

      const maxAdd = parseInt(input.dataset.maxAdd);
      const maxSub = parseInt(input.dataset.maxSub);

      // Validaciones con avisos arriba (claros)
      if (action === 'aumentar' && (isNaN(maxAdd) || maxAdd <= 0)) {
        showAlertTop('El máximo de stock es 50. No se puede aumentar más.', 'err'); 
        return;
      }
      if (action === 'aumentar' && cantidad > maxAdd) {
        input.value = maxAdd;
        showAlertTop('No puedes exceder el stock máximo (50). Se Ajusto la cantidad permitida.', 'err');
        return;
      }
      if (action === 'disminuir' && (isNaN(maxSub) || maxSub <= 0)) {
  showAlertTop('No puedes disminuir; el inventario actual ya está en 0 unidades.', 'err');
  return;
}
if (action === 'disminuir' && cantidad > maxSub) {
  input.value = maxSub;
  showAlertTop(`Solo puedes reducir hasta ${maxSub} unidades, que es el stock disponible actualmente.`, 'err');
  return;
}

      openConfirmFor(btn, { row, input, url, action, name, cantidad });
    });
  });


  btnOK?.addEventListener('click', async () => {
    if (!pending) return;
    const { row, input, url, cantidad } = pending;
    closeConfirm();

    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    try {
      const res = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': token
        },
        credentials: 'same-origin', 
        body: JSON.stringify({ cantidad })
      });

      let data = null;
      try { data = await res.json(); } catch (_) {}

      if (res.ok) {
        if (data && typeof data.nuevoStock !== 'undefined') {
          const badge = $('[data-stock]', row);
          const nuevo = parseInt(data.nuevoStock);
          if (badge && !isNaN(nuevo)) {
            badge.textContent = nuevo;
           
            input.dataset.maxAdd = Math.max(0, 50 - nuevo);
            input.dataset.maxSub = Math.max(0, nuevo);
          }
          row.classList.add('flash-ok'); 
          setTimeout(() => row.classList.remove('flash-ok'), 800);
          showAlertTop('Inventario actualizado correctamente.', 'ok');
        } else {
          location.reload();
        }
      } else {
        if (res.status === 419) showAlertTop('Sesión expirada. Vuelve a cargar la página.', 'err');
        else showAlertTop('No se pudo actualizar el inventario.', 'err');
      }
    } catch (e) {
      showAlertTop('Intenta nuevamente.', 'err');
    }
  });

})();
