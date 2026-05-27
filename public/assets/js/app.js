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

      // Obtener lista de IDs fuera de stock
      const table = document.getElementById('js-inventory-table');
      let outOfStockIds = [];
      if (table && table.dataset.outOfStockIds) {
        try {
          outOfStockIds = JSON.parse(table.dataset.outOfStockIds);
        } catch {
          outOfStockIds = [];
        }
      }

      const frag = document.createDocumentFragment();
      items.forEach((p) => {
        const tr = document.createElement('tr');
        const isOutOfStock = outOfStockIds.includes(Number(p.id));
        if (isOutOfStock) {
          tr.className = 'out-of-stock';
        }
        tr.setAttribute('data-product-id', String(p.id));
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
              <a class="btn" href="${u(`inventario/${Number(p.id)}`)}" ${isOutOfStock ? 'style="pointer-events: none; opacity: 0.5;" title="Producto sin stock"' : ''}>Ver</a>
              <a class="btn" href="${u(`inventario/${Number(p.id)}/editar`)}" ${isOutOfStock ? 'style="pointer-events: none; opacity: 0.5;" title="Producto sin stock"' : ''}>Editar</a>
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
  function remountTablePicker(root, ocupadas = null) {
    if (!root) return;
    
    delete root.dataset.equimacPickerMounted;
    
    const grid = root.querySelector('.js-picker-grid') || root.querySelector('[data-grid]');
    
    if (grid) {
      grid.innerHTML = '';
      grid.style.gridTemplateColumns = '';
    }
    
    try {
      mountOneTablePicker(root, ocupadas);
    } catch (e) {
      console.error('[EQUIMAC] Error mounting picker:', e.message);
    }
  }

  function mountOneTablePicker(root, ocupadas = null) {
    if (!root || root.dataset.equimacPickerMounted === '1') return;

    let max = parseInt(String(root.getAttribute('data-max') || '20'), 10);
    if (!Number.isFinite(max) || max < 2) max = 12;
    if (max > 40) max = 40;

    const grid = root.querySelector('.js-picker-grid') || root.querySelector('[data-grid]');
    const label = root.querySelector('.js-picker-label') || root.querySelector('[data-label]');
    const inpR = root.querySelector('.js-picker-filas') || root.querySelector('input[name="entrepaño"]');
    const inpC = root.querySelector('.js-picker-columnas') || root.querySelector('input[name="posicion"]');
    
    if (!grid || !inpR || !inpC) {
      console.error('[EQUIMAC] Missing elements in picker');
      return;
    }

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

    // Usar las dimensiones reales del estante
    const gridRows = dr;
    const gridCols = dc;
    
    console.log('[EQUIMAC] Grid dimensions:', { gridRows, gridCols });
    
    grid.style.gridTemplateColumns = `repeat(${gridCols}, 1fr)`;
    grid.innerHTML = '';
    const cells = [];
    
    for (let r = 1; r <= gridRows; r++) {
      for (let c = 1; c <= gridCols; c++) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'table-size-picker-cell';
        btn.dataset.r = String(r);
        btn.dataset.c = String(c);
        btn.title = `Fila ${r}, Posición ${c}`;
        
        // Marcar si está ocupada
        if (ocupadas && ocupadas[r] && ocupadas[r][c]) {
          btn.classList.add('is-occupied');
          btn.disabled = true;
        }
        
        grid.appendChild(btn);
        cells.push(btn);
      }
    }
    
    const paint = () => {
      cells.forEach((btn) => {
        const r = parseInt(btn.dataset.r, 10);
        const c = parseInt(btn.dataset.c, 10);
        const isSelected = r === selR && c === selC;
        const isHover = r === hoverR && c === hoverC && !btn.classList.contains('is-occupied');
        btn.classList.toggle('is-selected', isSelected);
        btn.classList.toggle('is-hover', isHover && !isSelected);
      });
      if (label) {
        label.textContent = `Seleccionado: Fila ${selR} · Posición ${selC}`;
      }
    };

    grid.addEventListener('mouseover', (e) => {
      const t = e.target.closest('.table-size-picker-cell');
      if (!t || !grid.contains(t) || t.classList.contains('is-occupied')) return;
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
      if (!t || !grid.contains(t) || t.classList.contains('is-occupied')) return;
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

  // Manejar selector de estante dinámico en formulario
  function initEstanteSelector() {
    const select = document.getElementById('js-estante-select');
    if (!select) {
      console.log('[EQUIMAC] js-estante-select not found');
      return;
    }

    const container = document.getElementById('js-picker-container');
    const placeholder = document.getElementById('js-picker-placeholder');
    if (!container || !placeholder) {
      console.log('[EQUIMAC] picker container or placeholder not found');
      return;
    }

    const updatePicker = async () => {
      const selected = select.options[select.selectedIndex];
      const filas = parseInt(selected.getAttribute('data-filas') || '5', 10);
      const columnas = parseInt(selected.getAttribute('data-columnas') || '5', 10);
      const estante = select.value;

      if (estante === '0') {
        container.style.display = 'none';
        placeholder.style.display = '';
        return;
      }

      placeholder.style.display = 'none';
      container.style.display = 'block';

      const picker = container.querySelector('[data-table-picker]');
      
      if (picker) {
        picker.setAttribute('data-default-rows', String(filas));
        picker.setAttribute('data-default-cols', String(columnas));
        picker.setAttribute('data-max', String(Math.max(filas, columnas, 12)));
        
        // Cargar ocupadas desde API
        let ocupadas = {};
        try {
          const resp = await fetch(u(`api/estante/${estante}/posiciones`));
          if (!resp.ok) throw new Error('Error en la respuesta');
          const data = await resp.json();
          if (data.ok && data.ocupadas) {
            ocupadas = data.ocupadas;
          }
        } catch (err) {
          console.warn('No se pudo cargar posiciones ocupadas:', err);
        }
        
        remountTablePicker(picker, ocupadas);
      }
    };

    // Attach listener with explicit binding
    const handleChange = () => {
      updatePicker();
    };
    
    select.addEventListener('change', handleChange);
    
    // Ejecutar al cargar si hay un estante seleccionado
    if (select && select.value !== '0') {
      updatePicker();
    }
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
      // Solo montar si no es el picker dinámico del formulario
      if (!root.closest('#js-picker-container')) {
        mountOneTablePicker(root);
      }
    });
    // Inicializar selector dinámico
    initEstanteSelector();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initTableSizePickers);
  } else {
    initTableSizePickers();
  }

  // Stock bajo: notificación y desactivación de productos sin stock
  function initStockNotifications() {
    const dataEl = document.getElementById('js-low-stock-data');
    if (!dataEl) return;

    let data;
    try {
      data = JSON.parse(dataEl.textContent);
    } catch {
      return;
    }

    const lowStockItems = data.lowStockItems || [];
    const outOfStockIds = data.outOfStockIds || [];

    // Desactivar filas de productos sin stock
    outOfStockIds.forEach((id) => {
      const row = document.querySelector(`tr[data-product-id="${id}"]`);
      if (row) {
        // Desactivar botones Ver y Editar
        const links = row.querySelectorAll('a.btn:not(.danger)');
        links.forEach((link) => {
          link.style.pointerEvents = 'none';
          link.style.opacity = '0.5';
          link.title = 'Producto sin stock';
        });
      }
    });

    // Mostrar notificación de stock bajo si hay
    if (lowStockItems.length > 0) {
      const plural = lowStockItems.length === 1 ? 'producto' : 'productos';
      const items = lowStockItems
        .slice(0, 3)
        .map((item) => `${item.descripcion} (${item.cantidad}/${item.stock_minimo})`)
        .join(', ');
      const more = lowStockItems.length > 3 ? ` y ${lowStockItems.length - 3} más` : '';

      const toastEl = document.createElement('div');
      toastEl.className = 'toast-low-stock';
      toastEl.innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px;">
          <div style="flex: 1;">
            <div style="font-weight: 800; font-size: 13px;">⚠️ Stock bajo</div>
            <div style="margin-top: 6px; color: rgba(255,255,255,.72); font-size: 12px;">
              ${lowStockItems.length} ${plural} con inventario bajo: ${items}${more}
            </div>
          </div>
          <button class="toast-close-btn" aria-label="Cerrar notificación" style="background: none; border: none; color: rgba(255,255,255,.6); cursor: pointer; font-size: 18px; padding: 0; min-width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">×</button>
        </div>
      `;
      toastEl.style.cssText = `
        width: min(420px, calc(100vw - 32px));
        border-radius: 16px;
        padding: 12px 12px;
        background: linear-gradient(135deg, rgba(245, 158, 11, .15), rgba(239, 68, 68, .1));
        border: 1px solid rgba(245, 158, 11, .35);
        box-shadow: 0 2px 8px rgba(59, 130, 246, .08);
        backdrop-filter: blur(10px);
        transform: translateY(10px);
        opacity: 0;
        animation: toastIn 260ms ease forwards;
        margin-bottom: 10px;
      `;

      stack().appendChild(toastEl);

      // Cerrar notificación al hacer clic en el botón
      const closeBtn = toastEl.querySelector('.toast-close-btn');
      closeBtn.addEventListener('click', () => {
        toastEl.style.opacity = '0';
        toastEl.style.transform = 'translateY(8px)';
        toastEl.style.transition = 'all 220ms ease';
        setTimeout(() => toastEl.remove(), 260);
      });

      // Auto-cerrar después de 8 segundos (más tiempo que los toasts normales)
      setTimeout(() => {
        if (toastEl.parentNode) {
          toastEl.style.opacity = '0';
          toastEl.style.transform = 'translateY(8px)';
          toastEl.style.transition = 'all 220ms ease';
          setTimeout(() => toastEl.remove(), 260);
        }
      }, 8000);
    }
  }

  // Inicializar notificaciones de stock cuando el DOM está listo
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initStockNotifications);
  } else {
    initStockNotifications();
  }
})();

