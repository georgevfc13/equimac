(() => {
  const $ = (sel, root = document) => root.querySelector(sel);
  const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));
  const BASE = (window.__EQUIMAC_BASE__ || '').replace(/\/+$/, '');
  const u = (path) => {
    path = String(path || '').replace(/^\/+/, '');
    return (BASE ? BASE + '/' : '/') + path;
  };

  // Toasts
  const stack = () => {
    let el = $('.toast-stack');
    if (!el) {
      el = document.createElement('div');
      el.className = 'toast-stack';
      document.body.appendChild(el);
    }
    return el;
  };

  function toast(title, message, type = 'good', ttl = 3200) {
    const el = document.createElement('div');
    el.className = `toast ${type === 'bad' ? 'bad' : 'good'}`;
    el.innerHTML = `<div class="t"></div><div class="m"></div>`;
    el.querySelector('.t').textContent = title;
    el.querySelector('.m').textContent = message;
    stack().appendChild(el);
    setTimeout(() => {
      el.style.opacity = '0';
      el.style.transform = 'translateY(8px)';
      el.style.transition = 'all 220ms ease';
      setTimeout(() => el.remove(), 260);
    }, ttl);
  }

  // Modal (data-modal open/close)
  function openModal(id) {
    const m = document.getElementById(id);
    if (!m) return;
    m.classList.add('is-open');
    m.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    const first = m.querySelector('input,select,textarea,button');
    if (first) first.focus();
    // Cuadrícula del modal: montar tras mostrar (layout correcto; evita max NaN sin celdas).
    if (id === 'modal-nuevo-estante') {
      requestAnimationFrame(() => {
        const root = m.querySelector('[data-table-picker]');
        if (root) remountTablePicker(root);
      });
    }
  }
  function closeModal(modalEl) {
    modalEl.classList.remove('is-open');
    modalEl.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }

  document.addEventListener('click', (e) => {
    const openBtn = e.target.closest('[data-open-modal]');
    if (openBtn) {
      e.preventDefault();
      openModal(openBtn.getAttribute('data-open-modal'));
      return;
    }
    const closeBtn = e.target.closest('[data-close-modal]');
    if (closeBtn) {
      e.preventDefault();
      const m = closeBtn.closest('.modal');
      if (m) closeModal(m);
      return;
    }
    const backdrop = e.target.classList.contains('modal') ? e.target : null;
    if (backdrop && backdrop.classList.contains('is-open')) {
      closeModal(backdrop);
    }
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      const m = $('.modal.is-open');
      if (m) closeModal(m);
    }
  });

  // Instant search (Inventario)
  const search = $('#js-search');
  let searchAbort = null;
  if (search) {
    const tbody = $('#js-table-body');
    const empty = $('#js-empty');
    const counter = $('#js-counter');

    const renderRows = (items) => {
      tbody.innerHTML = '';
      if (!items.length) {
        empty.style.display = '';
        if (counter) counter.textContent = '0';
        return;
      }
      empty.style.display = 'none';
      if (counter) counter.textContent = String(items.length);

      const frag = document.createDocumentFragment();
      items.forEach((p) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td class="mono"><strong>${escapeHtml(p.codigo)}</strong></td>
          <td>
            <div>${escapeHtml(p.descripcion)}</div>
            <div class="muted" style="margin-top:6px;font-size:12px">${p.equipo ? '📌 ' + escapeHtml(p.equipo) : ''}</div>
          </td>
          <td>${p.marca ? `<span class="badge"><span class="dot"></span>${escapeHtml(p.marca)}</span>` : '<span class="muted">—</span>'}</td>
          <td><span class="badge"><span class="dot warn"></span>Est. ${Number(p.estante)} · F${Number(p.entrepaño)} · P${Number(p.posicion)}</span></td>
          <td><span class="badge"><span class="dot good"></span>${Number(p.cantidad)} ${escapeHtml(p.unidad)}</span></td>
          <td>
            <div class="row" style="gap:10px">
              <a class="btn" href="${u(`inventario/${Number(p.id)}`)}">Ver</a>
              <a class="btn" href="${u(`inventario/${Number(p.id)}/editar`)}">Editar</a>
              <button class="btn danger" data-quick-delete="${Number(p.id)}" data-quick-name="${escapeAttr(p.descripcion)}">Eliminar</button>
            </div>
          </td>
        `;
        frag.appendChild(tr);
      });
      tbody.appendChild(frag);
    };

    const doSearch = async () => {
      const q = search.value.trim();
      if (searchAbort) searchAbort.abort();
      searchAbort = new AbortController();
      try {
        const r = await fetch(u(`api/inventario/buscar?q=${encodeURIComponent(q)}`), { signal: searchAbort.signal });
        const data = await r.json();
        if (!data.ok) throw new Error(data.message || 'Error');
        renderRows(data.items || []);
      } catch (err) {
        if (err.name === 'AbortError') return;
        toast('Búsqueda', 'No se pudo buscar. Revisa el servidor local.', 'bad');
      }
    };

    let t = null;
    search.addEventListener('input', () => {
      clearTimeout(t);
      t = setTimeout(doSearch, 180);
    });
  }

  // Quick delete
  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('[data-quick-delete]');
    if (!btn) return;
    const id = btn.getAttribute('data-quick-delete');
    const name = btn.getAttribute('data-quick-name') || 'este producto';
    if (!confirm(`¿Eliminar "${name}"?\n\nEsta acción no se puede deshacer.`)) return;

    try {
      const fd = new FormData();
      fd.append('id', id);
      const r = await fetch(u('api/inventario/eliminar'), { method: 'POST', body: fd });
      const data = await r.json();
      if (!data.ok) throw new Error(data.message || 'Error');
      toast('Eliminado', 'Producto eliminado correctamente.', 'good');
      // Trigger refresh in search page if present.
      const s = document.getElementById('js-search');
      if (s) s.dispatchEvent(new Event('input'));
      else location.reload();
    } catch {
      toast('Error', 'No se pudo eliminar.', 'bad');
    }
  });

  function escapeHtml(str) {
    return String(str ?? '').replace(/[&<>"']/g, (m) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));
  }
  function escapeAttr(str) {
    return escapeHtml(str).replace(/`/g, '&#096;');
  }

  // Estantes: selector de tamaño (filas × columnas) estilo Word
  function remountTablePicker(root) {
    if (!root) return;
    delete root.dataset.equimacPickerMounted;
    const oldGrid = root.querySelector('.js-picker-grid') || root.querySelector('[data-grid]');
    if (oldGrid && oldGrid.parentNode) {
      const fresh = oldGrid.cloneNode(false);
      oldGrid.parentNode.replaceChild(fresh, oldGrid);
    }
    mountOneTablePicker(root);
  }

  function mountOneTablePicker(root) {
    if (!root || root.dataset.equimacPickerMounted === '1') return;

    let max = parseInt(String(root.getAttribute('data-max') || '20'), 10);
    if (!Number.isFinite(max) || max < 2) max = 12;
    if (max > 40) max = 40;

    const grid = root.querySelector('.js-picker-grid') || root.querySelector('[data-grid]');
    const label = root.querySelector('.js-picker-label') || root.querySelector('[data-label]');
    const inpR = root.querySelector('.js-picker-filas') || root.querySelector('input[name="filas"]');
    const inpC = root.querySelector('.js-picker-columnas') || root.querySelector('input[name="columnas"]');
    if (!grid || !inpR || !inpC) return;

    let dr = parseInt(String(root.getAttribute('data-default-rows') || inpR.value || '5'), 10);
    let dc = parseInt(String(root.getAttribute('data-default-cols') || inpC.value || '5'), 10);
    if (!Number.isFinite(dr) || dr < 1) dr = 5;
    if (!Number.isFinite(dc) || dc < 1) dc = 5;

    let selR = Math.min(max, Math.max(1, dr));
    let selC = Math.min(max, Math.max(1, dc));
    let hoverR = selR;
    let hoverC = selC;
    inpR.value = String(selR);
    inpC.value = String(selC);

    grid.style.gridTemplateColumns = `repeat(${max}, 14px)`;
    grid.innerHTML = '';
    const cells = [];
    for (let r = 1; r <= max; r++) {
      for (let c = 1; c <= max; c++) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'table-size-picker-cell';
        btn.dataset.r = String(r);
        btn.dataset.c = String(c);
        grid.appendChild(btn);
        cells.push(btn);
      }
    }

    const paint = () => {
      cells.forEach((btn) => {
        const r = parseInt(btn.dataset.r, 10);
        const c = parseInt(btn.dataset.c, 10);
        btn.classList.toggle('is-hover', r <= hoverR && c <= hoverC);
      });
      if (label) {
        label.textContent = `Seleccionado: ${hoverR} fila(s) × ${hoverC} posición(es)`;
      }
    };

    grid.addEventListener('mouseover', (e) => {
      const t = e.target.closest('.table-size-picker-cell');
      if (!t || !grid.contains(t)) return;
      hoverR = parseInt(t.dataset.r, 10);
      hoverC = parseInt(t.dataset.c, 10);
      paint();
    });

    grid.addEventListener('mouseleave', () => {
      hoverR = selR;
      hoverC = selC;
      paint();
    });

    grid.addEventListener('click', (e) => {
      const t = e.target.closest('.table-size-picker-cell');
      if (!t || !grid.contains(t)) return;
      e.preventDefault();
      selR = parseInt(t.dataset.r, 10);
      selC = parseInt(t.dataset.c, 10);
      hoverR = selR;
      hoverC = selC;
      inpR.value = String(selR);
      inpC.value = String(selC);
      paint();
    });

    root.dataset.equimacPickerMounted = '1';
    paint();
  }

  function initTableSizePickers() {
    $$('[data-table-picker]').forEach((root) => {
      const modal = root.closest('#modal-nuevo-estante');
      if (modal) {
        if (modal.classList.contains('is-open')) {
          remountTablePicker(root);
        }
        return;
      }
      mountOneTablePicker(root);
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initTableSizePickers);
  } else {
    initTableSizePickers();
  }
})();

