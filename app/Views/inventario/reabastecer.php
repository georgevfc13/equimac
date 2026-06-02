<?php
/** @var array $items */
/** @var array $errors */
/** @var string|null $selectedProductId */

function reabastecerFieldError(array $errors, string $key): string {
  if (!isset($errors[$key])) return '';
  return '<div class="help" style="color: rgba(239,68,68,.95)">'.e($errors[$key]).'</div>';
}

$content = '
<div class="page-head">
  <div>
    <h2 class="page-title">Reabastecer Producto</h2>
    <p class="page-sub">Añade más cantidad a un producto existente en el inventario.</p>
  </div>
  <div class="row">
    <a class="btn" href="'.e(url('inventario')).'">Inventario</a>
  </div>
</div>

<div class="card pad">
  <form method="POST" action="'.e(url('inventario/reabastecer')).'">
    <div class="form-grid">
      <div class="field" style="grid-column:1/-1">
        <label>Selecciona el producto *</label>
        <select name="inventario_id" required>
          <option value="">Selecciona un producto…</option>';

foreach (($items ?? []) as $p) {
    $sel = ($selectedProductId && (int)$selectedProductId === (int)$p['id']) ? 'selected' : '';
    $content .= '<option value="'.(int)$p['id'].'" '.$sel.'>'.e($p['codigo']).' · '.e($p['nombre'] ?? '').' (Stock: '.(int)$p['cantidad'].' '.e($p['unidad']).')</option>';
}

$content .= '
        </select>
        '.reabastecerFieldError($errors ?? [], 'inventario_id').'
      </div>

      <div class="field">
        <label>Cantidad a añadir *</label>
        <input type="number" min="1" name="cantidad" value="'.e((string)($_POST['cantidad'] ?? '1')).'" placeholder="1" required />
        '.reabastecerFieldError($errors ?? [], 'cantidad').'
      </div>

      <div class="field" style="grid-column:1/-1">
        <label>De quién llegó (proveedor/fuente) *</label>
        <input name="de_quien_llego" value="'.e((string)($_POST['de_quien_llego'] ?? '')).'" placeholder="Proveedor / Persona que entregó…" required />
        '.reabastecerFieldError($errors ?? [], 'de_quien_llego').'
      </div>

      <div class="field" style="grid-column:1/-1">
        <label>Quién recibió *</label>
        <input name="quien_recibio" value="'.e((string)($_POST['quien_recibio'] ?? '')).'" placeholder="Nombre de quién recibió…" required />
        '.reabastecerFieldError($errors ?? [], 'quien_recibio').'
      </div>

      <div class="field" style="grid-column:1/-1">
        <label>Observaciones</label>
        <textarea name="observaciones" placeholder="Notas adicionales sobre el reabastecer…" style="height:80px">'.e((string)($_POST['observaciones'] ?? '')).'</textarea>
      </div>
    </div>

    <div style="height:16px"></div>
    <div class="row" style="justify-content:flex-end">
      <button type="submit" class="btn primary">Registrar reabastecer</button>
    </div>
  </form>
</div>
';

echo view('layout', [
  'title' => $title ?? 'Reabastecer Producto',
  'active' => $active ?? 'inventario',
  'content' => $content
]);
