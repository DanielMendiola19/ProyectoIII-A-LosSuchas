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

//Inicio filtros
//Inicio filtros
//Inicio filtros
/* ===== Filtros/Búsqueda/Orden – SOLO FRAGMENTO, pegado al final de inventario.js ===== */
(function () {
  // Usa los helpers existentes si están; si no, crea mínimos.
  const $  = window.$  || ((s, c=document) => c.querySelector(s));
  const $$ = window.$$ || ((s, c=document) => Array.from(c.querySelectorAll(s)));

  const body    = document.getElementById('inv-body') || document.querySelector('.tabla-inventario tbody');
  if (!body) return;

  // Controles esperados en la toolbar
  const qInput  = $('#f-q');
  const btnClr  = $('#f-clear');
  const sortSel = $('#f-sort');
  const lowChk  = $('#f-low');
  const lowVal  = $('#f-low-val');
  const btnRst  = $('#f-reset');
  const chipCnt = $('#f-count');

  // Si no existe la toolbar, no hacemos nada:
  if (!qInput || !sortSel || !lowVal) return;

  // Construcción de filas: toma nombre desde .nombre-prod y stock desde [data-stock] o texto
  const rows = $$('#inv-body tr, .tabla-inventario tbody tr').map(tr => {
    const nameEl  = tr.querySelector('.nombre-prod');
    const stockEl = tr.querySelector('[data-stock]') || tr.querySelector('[data-stock-badge]');
    const name  = (nameEl?.textContent || '').trim().toLowerCase();
    const stock = parseInt((stockEl?.textContent || '0').replace(/\D+/g,''), 10) || 0;
    return { el: tr, name, stock };
  });

  const KEY = 'inv.filters.v1';
  const state = loadState() || { q:'', sort:'name-asc', lowEnabled:false, lowValue:5 };

  // UI inicial
  applyStateToUI();
  render();
  toggleClear();

  // Eventos
  qInput.addEventListener('input', debounce(() => {
    state.q = (qInput.value || '').trim().toLowerCase();
    saveState(); render(); toggleClear();
  }, 120));

  btnClr?.addEventListener('click', () => {
    qInput.value = '';
    state.q = '';
    saveState(); render(); toggleClear(); qInput.focus();
  });

  sortSel.addEventListener('change', () => {
    state.sort = sortSel.value;
    saveState(); render();
  });

  lowChk?.addEventListener('change', () => {
    state.lowEnabled = !!lowChk.checked;
    saveState(); render();
  });

  lowVal.addEventListener('input', () => {
    const n = Math.max(0, parseInt(lowVal.value || '0', 10) || 0);
    state.lowValue = n;
    saveState(); render();
  });

  btnRst?.addEventListener('click', () => {
    state.q = '';
    state.sort = 'name-asc';
    state.lowEnabled = false;
    state.lowValue = 5;
    saveState(); applyStateToUI(); render(); toggleClear();
  });

  // Render
  function render(){
    const q = (state.q || '').toLowerCase();
    const lowOn = !!state.lowEnabled;
    const lowNum = Number(state.lowValue || 0);

    // Filtrar visibilidad
    let visible = 0;
    rows.forEach(r => {
      const matchQ = !q || r.name.includes(q);
      const matchLow = !lowOn || (r.stock <= lowNum);
      const show = matchQ && matchLow;
      r.el.style.display = show ? '' : 'none';
      if (show) visible++;
    });

    // Orden solo sobre visibles
    const visibles = rows.filter(r => r.el.style.display !== 'none');
    const [key, dir] = (state.sort || 'name-asc').split('-'); // name|stock - asc|desc
    visibles.sort((a, b) => {
      let cmp = 0;
      if (key === 'name')  cmp = a.name.localeCompare(b.name, 'es', { sensitivity:'base' });
      if (key === 'stock') cmp = a.stock - b.stock;
      return dir === 'desc' ? -cmp : cmp;
    });

    // Reinsertar para reflejar orden
    const frag = document.createDocumentFragment();
    visibles.forEach(r => frag.appendChild(r.el));
    body.appendChild(frag);

    if (chipCnt) chipCnt.textContent = `${visible} de ${rows.length}`;
  }

  function applyStateToUI(){
    qInput.value = state.q || '';
    if (sortSel) sortSel.value = state.sort || 'name-asc';
    if (lowChk)  lowChk.checked = !!state.lowEnabled;
    lowVal.value = Number(state.lowValue || 5);
  }

  function toggleClear(){ if (btnClr) btnClr.style.visibility = qInput.value ? 'visible' : 'hidden'; }
  function saveState(){ try { localStorage.setItem(KEY, JSON.stringify(state)); } catch(_){} }
  function loadState(){ try { const raw = localStorage.getItem(KEY); return raw ? JSON.parse(raw) : null; } catch(_){ return null; } }

  function debounce(fn, wait){
    let t; return (...args) => { clearTimeout(t); t = setTimeout(() => fn.apply(null, args), wait); };
  }
})();
//Fin filtros
//Fin filtros
//Fin filtros

