<?php
/** @var array<array> $items */
/** @var array<string, string> $errors */
/** @var int|string|null $selectedProductId */
/** @var array $old */

function reabastecerFieldError(array $errors, string $key): string {
  if (!isset($errors[$key])) return '';
  return '<div class="help" style="color: rgba(239,68,68,.95)">'.e($errors[$key]).'</div>';
}

$items = $items ?? [];
$errors = $errors ?? [];
$old = $old ?? [];
$selectedProductId = $selectedProductId ?? ($old['inventario_id'] ?? null);

$selId = (int)($selectedProductId ?? 0);
$cantidadVal = (string)($old['cantidad'] ?? '1');
$deQuienVal = (string)($old['de_quien_llego'] ?? '');
$quienRecibioVal = (string)($old['quien_recibio'] ?? '');
$obsVal = (string)($old['observaciones'] ?? '');

$content = '
<div class="page-head">
  <div>
    <h2 class="page-title">Reabastecer producto</h2>
    <p class="page-sub">Selecciona un producto del inventario y registra la entrada de material.</p>
  </div>
  <div class="row">
    <a class="btn" href="'.e(url('inventario')).'">Inventario</a>
    <a class="btn" href="'.e(url('inventario/entrada')).'">← Atrás</a>
  </div>
</div>';

if (empty($items)) {
  $content .= '
<div class="card pad">
  <div class="empty">
    <h3>No hay productos en inventario</h3>
    <p>Crea un producto primero o registra una entrada nueva.</p>
    <a class="btn primary" href="'.e(url('inventario/nuevo')).'">+ Crear producto</a>
  </div>
</div>';
} else {
  $content .= '
<div class="card pad">
  <form method="POST" action="'.e(url('inventario/reabastecer')).'" id="js-reabastecer-form">
    <div class="form-grid">
      <div class="field" style="grid-column:1/-1">
        <label>Buscar producto</label>
        <div class="search" style="min-width:100%">
          <span aria-hidden="true">⌕</span>
          <input type="search" id="js-reabastecer-filter" placeholder="Escribe nombre o código…" autocomplete="off" />
        </div>
      </div>

      <div class="field" style="grid-column:1/-1">
        <label for="js-product-select">Producto del inventario *</label>
        <select name="inventario_id" id="js-product-select" required>
          <option value="">— Selecciona un producto —</option>';

  foreach ($items as $p) {
    $pid = (int)$p['id'];
    $sel = ($selId === $pid) ? ' selected' : '';
    $codigo = e($p['codigo']);
    $nombre = e($p['nombre'] ?? '');
    $cantidad = (int)$p['cantidad'];
    $min = (int)($p['stock_minimo'] ?? 5);
    $unidad = e($p['unidad']);
    $ubic = 'Est. '.(int)$p['estante'].' · F'.(int)$p['entrepaño'].' · P'.(int)$p['posicion'];
    $label = $nombre !== '' ? "{$nombre} · {$codigo}" : $codigo;
    $content .= '<option value="'.$pid.'"'.$sel
      .' data-codigo="'.$codigo.'"'
      .' data-nombre="'.$nombre.'"'
      .' data-cantidad="'.(int)$cantidad.'"'
      .' data-min="'.(int)$min.'"'
      .' data-unidad="'.$unidad.'"'
      .' data-ubicacion="'.e($ubic).'"'
      .' data-label="'.e(strtolower($label.' '.$codigo)).'">'
      .e($label).' (Stock: '.$cantidad.' '.$unidad.')</option>';
  }

  $content .= '
        </select>
        '.reabastecerFieldError($errors, 'inventario_id').'
        <p class="help" id="js-reabastecer-hint" style="margin-top:8px">'.($selId ? '' : 'Elige el producto que vas a reabastecer.').'</p>
      </div>

      <div class="field" style="grid-column:1/-1;display:none" id="js-product-preview">
        <div class="card pad" style="background:rgba(59,130,246,.06);border-color:rgba(59,130,246,.25);margin:0">
          <div class="row" style="justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:12px">
            <div>
              <div class="muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.1em">Producto seleccionado</div>
              <div style="font-weight:800;font-size:16px;margin-top:4px" id="js-preview-nombre">—</div>
              <div class="mono muted" style="font-size:12px;margin-top:4px" id="js-preview-codigo">—</div>
            </div>
            <div class="row" style="gap:10px">
              <span class="badge"><span class="dot good"></span><span id="js-preview-stock">—</span></span>
              <span class="badge"><span class="dot warn"></span><span id="js-preview-ubic">—</span></span>
            </div>
          </div>
        </div>
      </div>

      <div class="field">
        <label>Cantidad a añadir *</label>
        <input type="number" min="1" name="cantidad" id="js-cantidad" value="'.e($cantidadVal).'" placeholder="1" required />
        '.reabastecerFieldError($errors, 'cantidad').'
      </div>

      <div class="field" style="grid-column:1/-1">
        <label>De quién llegó (proveedor) *</label>
        <input name="de_quien_llego" value="'.e($deQuienVal).'" placeholder="Ej. Distribuidor XYZ, Almacén central…" required />
        '.reabastecerFieldError($errors, 'de_quien_llego').'
      </div>

      <div class="field" style="grid-column:1/-1">
        <label>Quién recibió *</label>
        <input name="quien_recibio" value="'.e($quienRecibioVal).'" placeholder="Nombre de quien recibió el material…" required />
        '.reabastecerFieldError($errors, 'quien_recibio').'
      </div>

      <div class="field" style="grid-column:1/-1">
        <label>Observaciones</label>
        <textarea name="observaciones" placeholder="Notas adicionales (opcional)" style="min-height:80px">'.e($obsVal).'</textarea>
      </div>
    </div>

    <div style="height:16px"></div>
    <div class="row" style="justify-content:flex-end;gap:10px">
      <a class="btn" href="'.e(url('inventario/entrada')).'">Cancelar</a>
      <button type="submit" class="btn primary">Registrar reabastecimiento</button>
    </div>
  </form>
</div>';
}

echo view('layout', [
  'title' => $title ?? 'Reabastecer Producto',
  'active' => $active ?? 'inventario',
  'content' => $content
]);
