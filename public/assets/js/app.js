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

  // Kardex: impresión (entradas / salidas / ambos)
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-print-kardex]');
    if (!btn) return;
    e.preventDefault();
    const mode = String(btn.getAttribute('data-print-kardex') || 'ambos');
    document.body.dataset.printKardex = mode;
    window.print();
    // Cleanup (algunos navegadores disparan afterprint)
    setTimeout(() => {
      delete document.body.dataset.printKardex;
    }, 250);
  });

  window.addEventListener('afterprint', () => {
    delete document.body.dataset.printKardex;
  });

  // Instant search (Inventario)
  const search = $('#js-search');
  const filterType = $('#js-filter-type');
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
          <td class="mono">
            <strong>${escapeHtml(p.codigo)}</strong>
            <div class="muted" style="margin-top:6px;font-size:12px;font-family: ui-sans-serif, system-ui;">${escapeHtml(p.nombre || '')}</div>
          </td>
          <td>
            <div>${escapeHtml(p.descripcion || '')}</div>
            <div class="muted" style="margin-top:6px;font-size:12px">${p.equipo ? '📌 ' + escapeHtml(p.equipo) : ''}</div>
          </td>
          <td>${p.marca ? `<span class="badge"><span class="dot"></span>${escapeHtml(p.marca)}</span>` : '<span class="muted">—</span>'}</td>
          <td><span class="badge"><span class="dot warn"></span>Est. ${Number(p.estante)} · F${Number(p.entrepaño)} · P${Number(p.posicion)}</span></td>
          <td><span class="badge"><span class="dot good"></span>${Number(p.cantidad)} ${escapeHtml(p.unidad)}</span></td>
          <td>
            <div class="row" style="gap:10px">
              <a class="btn" href="${u(`inventario/${Number(p.id)}`)}" ${isOutOfStock ? 'style="pointer-events: none; opacity: 0.5;" title="Producto sin stock"' : ''}>Ver</a>
              <a class="btn" href="${u(`inventario/${Number(p.id)}/editar`)}" ${isOutOfStock ? 'style="pointer-events: none; opacity: 0.5;" title="Producto sin stock"' : ''}>Editar</a>
              <button class="btn danger" data-quick-delete="${Number(p.id)}" data-quick-name="${escapeAttr(p.nombre || p.descripcion || '')}">Eliminar</button>
            </div>
          </td>
        `;
        frag.appendChild(tr);
      });
      tbody.appendChild(frag);
    };

    const doSearch = async () => {
      const q = search.value.trim();
      const type = filterType ? filterType.value : 'all';
      if (searchAbort) searchAbort.abort();
      searchAbort = new AbortController();
      try {
        const r = await fetch(u(`api/inventario/buscar?q=${encodeURIComponent(q)}&type=${encodeURIComponent(type)}`), { signal: searchAbort.signal });
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
    
    if (filterType) {
      filterType.addEventListener('change', () => {
        clearTimeout(t);
        t = setTimeout(doSearch, 180);
      });
    }
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

    // Varios productos pueden compartir la misma celda; mostramos conteo sin bloquear.
    const isSizeMode = String(inpR.getAttribute('name') || '') === 'filas' && String(inpC.getAttribute('name') || '') === 'columnas';

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

        if (!isSizeMode && ocupadas && ocupadas[r] && ocupadas[r][c]) {
          const count = typeof ocupadas[r][c] === 'number' ? ocupadas[r][c] : 1;
          btn.classList.add('is-shared');
          btn.dataset.count = String(count);
          btn.title = `${count} producto(s) aquí — puedes agregar otro`;
        }
        
        grid.appendChild(btn);
        cells.push(btn);
      }
    }
    
    const paint = () => {
      cells.forEach((btn) => {
        const r = parseInt(btn.dataset.r, 10);
        const c = parseInt(btn.dataset.c, 10);
        const isShared = btn.classList.contains('is-shared');
        const canHighlight = isSizeMode ? !btn.classList.contains('is-occupied') : true;

        let isSelected;
        let isHover;
        if (isSizeMode) {
          // Rectángulo anclado en (1,1): se nota cuántos cuadros incluye.
          isSelected = r <= selR && c <= selC;
          isHover = r <= hoverR && c <= hoverC;
        } else {
          // Selección puntual para ubicación exacta del producto.
          isSelected = r === selR && c === selC;
          isHover = r === hoverR && c === hoverC;
        }

        btn.classList.toggle('is-selected', isSelected && canHighlight);
        btn.classList.toggle('is-hover', isHover && !isSelected && canHighlight);
      });
      if (label) {
        if (isSizeMode) {
          const cuadros = selR * selC;
          label.textContent = `Filas: ${selR} · Paños: ${selC} · ${cuadros} cuadros`;
        } else {
          label.textContent = `Seleccionado: Fila ${selR} · Posición ${selC}${isSharedAt(selR, selC) ? ' (celda compartida)' : ''}`;
        }
      }
    };

    const isSharedAt = (r, c) => {
      const cell = cells.find((b) => parseInt(b.dataset.r, 10) === r && parseInt(b.dataset.c, 10) === c);
      return cell && cell.classList.contains('is-shared');
    };

    grid.addEventListener('mouseover', (e) => {
      const t = e.target.closest('.table-size-picker-cell');
      if (!t || !grid.contains(t)) return;
      if (isSizeMode && t.classList.contains('is-occupied')) return;
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
      if (isSizeMode && t.classList.contains('is-occupied')) return;
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

  // Reabastecimiento: filtro y vista previa del producto seleccionado
  function initReabastecerForm() {
    const select = document.getElementById('js-product-select');
    const filter = document.getElementById('js-reabastecer-filter');
    const preview = document.getElementById('js-product-preview');
    if (!select) return;

    const previewNombre = document.getElementById('js-preview-nombre');
    const previewCodigo = document.getElementById('js-preview-codigo');
    const previewStock = document.getElementById('js-preview-stock');
    const previewUbic = document.getElementById('js-preview-ubic');
    const hint = document.getElementById('js-reabastecer-hint');

    const allOptions = Array.from(select.options).filter((o) => o.value !== '');

    const updatePreview = () => {
      const opt = select.options[select.selectedIndex];
      if (!opt || !opt.value) {
        if (preview) preview.style.display = 'none';
        if (hint) hint.textContent = 'Elige el producto que vas a reabastecer.';
        return;
      }
      if (preview) preview.style.display = '';
      if (previewNombre) previewNombre.textContent = opt.dataset.nombre || opt.textContent;
      if (previewCodigo) previewCodigo.textContent = opt.dataset.codigo || '';
      if (previewStock) {
        previewStock.textContent = `${opt.dataset.cantidad || 0} ${opt.dataset.unidad || ''} (mín. ${opt.dataset.min || 5})`;
      }
      if (previewUbic) previewUbic.textContent = opt.dataset.ubicacion || '';
      if (hint) hint.textContent = '';
    };

    const applyFilter = () => {
      const q = (filter?.value || '').trim().toLowerCase();
      const current = select.value;
      select.innerHTML = '';
      const placeholder = document.createElement('option');
      placeholder.value = '';
      placeholder.textContent = '— Selecciona un producto —';
      select.appendChild(placeholder);

      let count = 0;
      allOptions.forEach((opt) => {
        const label = (opt.dataset.label || opt.textContent || '').toLowerCase();
        if (!q || label.includes(q)) {
          select.appendChild(opt.cloneNode(true));
          count++;
        }
      });

      if (current && select.querySelector(`option[value="${current}"]`)) {
        select.value = current;
      } else {
        select.value = '';
      }

      if (hint && q) {
        hint.textContent = count
          ? `${count} producto(s) coinciden con la búsqueda.`
          : 'Ningún producto coincide. Prueba otro nombre o código.';
      }
      updatePreview();
    };

    select.addEventListener('change', updatePreview);
    if (filter) {
      filter.addEventListener('input', applyFilter);
    }
    updatePreview();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initReabastecerForm);
  } else {
    initReabastecerForm();
  }

  // Stock bajo: notificación y desactivación de productos sin stock
  function initStockNotifications() {
    const dataEl = document.getElementById('js-low-stock-data');
    const bell = document.getElementById('js-notif-bell');
    const badge = document.getElementById('js-notif-badge');
    const panel = document.getElementById('js-notif-panel');
    const list = document.getElementById('js-notif-list');
    const empty = document.getElementById('js-notif-empty');
    const closeBtn = document.getElementById('js-notif-close');

    const setOpen = (open) => {
      if (!panel || !bell) return;
      panel.style.display = open ? '' : 'none';
      bell.setAttribute('aria-expanded', open ? 'true' : 'false');
    };

    if (bell && panel) {
      bell.addEventListener('click', (e) => {
        e.preventDefault();
        const isOpen = panel.style.display !== 'none';
        setOpen(!isOpen);
      });
      if (closeBtn) closeBtn.addEventListener('click', () => setOpen(false));
      document.addEventListener('click', (e) => {
        if (panel.style.display === 'none') return;
        const inside = e.target.closest('#js-notif-panel') || e.target.closest('#js-notif-bell');
        if (!inside) setOpen(false);
      });
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') setOpen(false);
      });
    }

    // Si la página no trae data de stock (solo inventario index), dejar el panel vacío.
    if (!dataEl) {
      if (bell) bell.classList.remove('is-active');
      if (badge) badge.style.display = 'none';
      if (empty) empty.textContent = 'No hay notificaciones.';
      if (list) list.innerHTML = '';
      return;
    }

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
    // En vez de spamear toasts al cargar, agrupar en un panel accesible con campana.
    if (bell && badge) {
      if (lowStockItems.length > 0) {
        bell.classList.add('is-active');
        badge.textContent = String(lowStockItems.length);
        badge.style.display = '';
      } else {
        bell.classList.remove('is-active');
        badge.style.display = 'none';
      }
    }

    const banner = document.getElementById('js-stock-banner');
    const bannerMsg = document.getElementById('js-stock-banner-msg');
    const bannerOpen = document.getElementById('js-stock-banner-open');
    if (banner && lowStockItems.length > 0) {
      banner.hidden = false;
      if (bannerMsg) {
        bannerMsg.textContent = `${lowStockItems.length} producto(s) con stock bajo o agotado. Revisa y reabastece a tiempo.`;
      }
      if (bannerOpen) {
        bannerOpen.addEventListener('click', () => {
          setOpen(true);
          bell?.focus();
        });
      }
    } else if (banner) {
      banner.hidden = true;
    }

    if (list && empty) {
      list.innerHTML = '';
      if (!lowStockItems.length) {
        empty.style.display = '';
      } else {
        empty.style.display = 'none';
        const frag = document.createDocumentFragment();
        lowStockItems.forEach((it) => {
          const codigo = it.codigo ? String(it.codigo) : '';
          const desc = it.nombre ? String(it.nombre) : (it.descripcion ? String(it.descripcion) : 'Producto');
          const cantidad = Number(it.cantidad ?? 0);
          const min = Number(it.stock_minimo ?? 0);
          const id = it.id != null ? Number(it.id) : null;

          const el = document.createElement('div');
          el.className = 'notif-item';
          el.innerHTML = `
            <div class="t">
              <span>${escapeHtml(codigo ? `${codigo} · ${desc}` : desc)}</span>
              <span style="color: var(--warn); font-weight: 900;">${cantidad}/${min}</span>
            </div>
            <div class="m">Inventario bajo. Recomendado reabastecer.</div>
          `;

          if (id && Number.isFinite(id)) {
            el.style.cursor = 'pointer';
            el.title = 'Reabastecer producto';
            el.addEventListener('click', () => {
              location.href = u(`inventario/reabastecer?id=${id}`);
            });
          }

          frag.appendChild(el);
        });
        list.appendChild(frag);
      }
    }
  }

  // Inicializar notificaciones de stock cuando el DOM está listo
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initStockNotifications);
  } else {
    initStockNotifications();
  }

  function initPageLoader() {
    const loader = document.getElementById('js-page-loader');
    if (!loader) return;

    const started = Date.now();
    const MIN_MS = 600;
    const MAX_MS = 5000;

    const hide = () => {
      const elapsed = Date.now() - started;
      const wait = Math.max(0, MIN_MS - elapsed);
      setTimeout(() => {
        loader.classList.add('is-hidden');
        loader.setAttribute('aria-busy', 'false');
      }, wait);
    };

    const forceTimer = setTimeout(hide, MAX_MS);
    window.addEventListener('load', () => {
      clearTimeout(forceTimer);
      hide();
    }, { once: true });

    document.querySelectorAll('.nav .chip[href], .brand[href]').forEach((link) => {
      link.addEventListener('click', (e) => {
        const href = link.getAttribute('href');
        if (!href || href.startsWith('#') || e.metaKey || e.ctrlKey) return;
        loader.classList.remove('is-hidden');
        loader.setAttribute('aria-busy', 'true');
      });
    });
  }

  function initMultiLineForms() {
    document.addEventListener('click', (e) => {
      const removeBtn = e.target.closest('[data-remove-line]');
      if (removeBtn) {
        const line = removeBtn.closest('[data-line]');
        const container = line?.parentElement;
        if (line && container && container.querySelectorAll('[data-line]').length > 1) {
          line.remove();
        }
      }
    });

    const salidaAdd = document.getElementById('js-add-salida-line');
    const salidaLines = document.getElementById('js-salida-lines');
    if (salidaAdd && salidaLines) {
      salidaAdd.addEventListener('click', () => {
        const row = document.createElement('div');
        row.className = 'mov-line';
        row.dataset.line = '';
        row.innerHTML = `
          <div class="form-grid" style="grid-template-columns:1fr 140px auto;align-items:end">
            <div class="field" style="margin:0">
              <label>Código</label>
              <input name="line_codigo[]" placeholder="EQ-001" autocomplete="off" />
            </div>
            <div class="field" style="margin:0">
              <label>Cantidad</label>
              <input type="number" min="1" name="line_cantidad[]" value="1" />
            </div>
            <button type="button" class="btn danger" data-remove-line title="Quitar">✕</button>
          </div>`;
        salidaLines.appendChild(row);
      });
    }

    const entradaAdd = document.getElementById('js-add-entrada-line');
    const entradaLines = document.getElementById('js-entrada-lines');
    const entradaTpl = document.getElementById('js-entrada-line-template');
    if (entradaAdd && entradaLines && entradaTpl) {
      entradaAdd.addEventListener('click', () => {
        entradaLines.appendChild(entradaTpl.content.cloneNode(true));
      });
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
      initPageLoader();
      initMultiLineForms();
    });
  } else {
    initPageLoader();
    initMultiLineForms();
  }
})();

