<?php
/** @var array $item */
/** @var array $entradas */
/** @var array $salidas */

$content = '
<div class="page-head kardex-screen-only">
  <div>
    <h2 class="page-title">Detalle</h2>
    <p class="page-sub">'.e($item['codigo']).' · '.e($item['nombre'] ?? '').'</p>
  </div>
  <div class="row">
    <a class="btn" href="'.e(url('inventario')).'">Volver</a>
    <a class="btn primary" href="'.e(url('inventario/'.(int)$item['id'].'/editar')).'">Editar</a>
    <form method="POST" action="'.e(url('inventario/'.(int)$item['id'].'/eliminar')).'" style="display:inline" onsubmit="return confirm(\'¿Eliminar este producto?\')">
      <button class="btn danger" type="submit">Eliminar</button>
    </form>
  </div>
</div>

<div class="card pad kardex-screen-only">
  <div class="form-grid">
    <div class="field">
      <label>Código</label>
      <input readonly value="'.e($item['codigo']).'" />
    </div>
    <div class="field">
      <label>Nombre</label>
      <input readonly value="'.e($item['nombre'] ?? '').'" />
    </div>
    <div class="field">
      <label>Unidad</label>
      <input readonly value="'.e($item['unidad']).'" />
    </div>
    <div class="field" style="grid-column:1/-1">
      <label>Descripción</label>
      <textarea readonly>'.e($item['descripcion']).'</textarea>
    </div>

    <div class="field">
      <label>Marca</label>
      <input readonly value="'.e($item['marca'] ?? '').'" placeholder="—" />
    </div>
    <div class="field">
      <label>Equipo</label>
      <input readonly value="'.e($item['equipo'] ?? '').'" placeholder="—" />
    </div>

    <div class="field">
      <label>Ubicación</label>
      <input readonly value="Estante '.(int)$item['estante'].' · Fila '.(int)$item['entrepaño'].' · Posición '.(int)$item['posicion'].'" />
    </div>
    <div class="field">
      <label>Stock Actual</label>
      <input readonly value="'.(int)$item['cantidad'].' '.e($item['unidad']).'" />
    </div>
    <div class="field">
      <label>Stock Mínimo</label>
      <input readonly value="'.(int)($item['stock_minimo'] ?? 5).'" />
    </div>
  </div>

  <div style="height:14px"></div>
  <div class="muted" style="font-size:12px">
    Creado: '.e((string)($item['fecha_creacion'] ?? '—')).' · Actualizado: '.e((string)($item['fecha_actualizacion'] ?? '—')).'
  </div>
</div>

<div class="kardex-screen-only" style="height:24px"></div>

<!-- KARDEX (solo esta zona se imprime) -->
<div id="kardex-print-root" class="kardex-print-area" style="margin-bottom:24px">
  <div class="kardex-print-header print-only">
    <div style="font-size:11px;letter-spacing:.12em;text-transform:uppercase;color:#64748b">EQUIMAC · Kardex</div>
    <div style="font-size:18px;font-weight:800;margin-top:4px;color:#000">'.e($item['codigo']).' · '.e($item['nombre'] ?? '').'</div>
    <div style="font-size:12px;margin-top:4px;color:#334155">'.e($item['descripcion']).'</div>
  </div>
  <div class="row kardex-screen-only" style="justify-content:space-between;align-items:center;margin-bottom:16px">
    <h3 style="font-size:18px;margin:0;font-weight:700">Kardex</h3>
    <div class="row no-print" style="gap:10px">
      <button type="button" class="btn" data-print-kardex="entradas">Imprimir entradas</button>
      <button type="button" class="btn" data-print-kardex="salidas">Imprimir salidas</button>
      <button type="button" class="btn primary" data-print-kardex="ambos">Imprimir ambos</button>
    </div>
  </div>
  
  <!-- ENTRADAS -->
  <div class="card pad" id="kardex-entradas" style="margin-bottom:14px">
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px">
      <div class="kardex-icon" style="width:24px;height:24px;background:rgba(34,197,94,.2);border-radius:8px;display:flex;align-items:center;justify-content:center;font-weight:700;color:rgb(34,197,94);font-size:14px">↓</div>
      <h4 style="margin:0;font-size:14px;font-weight:700">Entradas</h4>
    </div>
    
    '.($entradas ? '<div class="table-wrap">
      <table style="min-width:100%">
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Cantidad</th>
            <th>De quién llegó</th>
            <th>Quién recibió</th>
          </tr>
        </thead>
        <tbody>' : '<div style="color:var(--muted);font-size:13px">Sin registro de entradas</div>').'
        
        '.($entradas ? implode('', array_map(fn($e) => '
          <tr>
            <td style="font-size:12px">'.e(substr((string)($e['created_at'] ?? ''), 0, 16)).'</td>
            <td><strong>'.(int)$e['cantidad'].' '.e($item['unidad']).'</strong></td>
            <td>'.e($e['quien_entrego'] ?? '—').'</td>
            <td>'.e($e['quien_recibio'] ?? '—').'</td>
          </tr>
        ', $entradas)) : '').'
        
        '.($entradas ? '</tbody>
      </table>
    </div>' : '').'
  </div>

  <!-- SALIDAS -->
  <div class="card pad" id="kardex-salidas">
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px">
      <div class="kardex-icon" style="width:24px;height:24px;background:rgba(239,68,68,.2);border-radius:8px;display:flex;align-items:center;justify-content:center;font-weight:700;color:rgb(239,68,68);font-size:14px">↑</div>
      <h4 style="margin:0;font-size:14px;font-weight:700">Salidas</h4>
    </div>
    
    '.($salidas ? '<div class="table-wrap">
      <table style="min-width:100%">
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Cantidad</th>
            <th>Quién entregó</th>
            <th>Quién recibió</th>
          </tr>
        </thead>
        <tbody>' : '<div style="color:var(--muted);font-size:13px">Sin registro de salidas</div>').'
        
        '.($salidas ? implode('', array_map(fn($s) => '
          <tr>
            <td style="font-size:12px">'.e(substr((string)($s['created_at'] ?? ''), 0, 16)).'</td>
            <td><strong>'.(int)$s['cantidad_usada'].' '.e($item['unidad']).'</strong></td>
            <td>'.e($s['quien_entrego'] ?? '—').'</td>
            <td>'.e($s['quien_recibio'] ?? '—').'</td>
          </tr>
        ', $salidas)) : '').'
        
        '.($salidas ? '</tbody>
      </table>
    </div>' : '').'
  </div>
</div>';

echo view('layout', [
  'title' => $title ?? 'Detalle',
  'active' => $active ?? 'inventario',
  'content' => $content
]);

