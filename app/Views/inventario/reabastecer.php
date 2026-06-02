<?php
/** @var array<array> $items */
/** @var array<string, string> $errors */
/** @var int|string|null $selectedProductId */

function reabastecerFieldError(array $errors, string $key): string {
  if (!isset($errors[$key])) return '';
  return '<div class="help" style="color: rgba(239,68,68,.95)">'.e($errors[$key]).'</div>';
}

$items = $items ?? [];
$errors = $errors ?? [];
$selectedProductId = $selectedProductId ?? null;

$content = '
<div class="page-head">
  <div>
    <h2 class="page-title">Reabastecer Producto</h2>
    <p class="page-sub">Incrementa el stock de un producto existente en tu inventario.</p>
  </div>
  <div class="row">
    <a class="btn" href="'.e(url('inventario')).'">Inventario</a>
    <a class="btn" href="'.e(url('inventario/entrada')).'">← Atrás</a>
  </div>
</div>

<div class="card pad">
  <form method="POST" action="'.e(url('inventario/reabastecer')).'">
    <div class="form-grid">
      <div class="field" style="grid-column:1/-1">
        <label>Selecciona el producto *</label>
        <select name="inventario_id" id="js-product-select" required style="padding:10px 12px; font-size:14px">
          <option value="">-- Selecciona un producto --</option>';

foreach (($items ?? []) as $p) {
    $sel = ($selectedProductId && (int)$selectedProductId === (int)$p['id']) ? 'selected' : '';
    $nombre = e($p['nombre'] ?? '');
    $codigo = e($p['codigo']);
    $cantidad = (int)$p['cantidad'];
    $unidad = e($p['unidad']);
    $content .= '<option value="'.(int)$p['id'].'" '.$sel.'>'.$codigo.' · '.$nombre.' (Stock: '.$cantidad.' '.$unidad.')</option>';
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
        <label>De quién llegó (proveedor) *</label>
        <input name="de_quien_llego" value="'.e((string)($_POST['de_quien_llego'] ?? '')).'" placeholder="Ej. Distribuidor XYZ, Almacén central…" required />
        '.reabastecerFieldError($errors ?? [], 'de_quien_llego').'
      </div>

      <div class="field" style="grid-column:1/-1">
        <label>Quién recibió *</label>
        <input name="quien_recibio" value="'.e((string)($_POST['quien_recibio'] ?? '')).'" placeholder="Nombre de quién recibió el material…" required />
        '.reabastecerFieldError($errors ?? [], 'quien_recibio').'
      </div>

      <div class="field" style="grid-column:1/-1">
        <label>Observaciones</label>
        <textarea name="observaciones" placeholder="Notas adicionales (opcional). Ej: Producto con descuento, entrega parcial, etc." style="height:80px">'.e((string)($_POST['observaciones'] ?? '')).'</textarea>
      </div>
    </div>

    <div style="height:16px"></div>
    <div class="row" style="justify-content:flex-end; gap:10px">
      <a class="btn" href="'.e(url('inventario/entrada')).'">Cancelar</a>
      <button type="submit" class="btn primary">Registrar Reabastecer</button>
    </div>
  </form>
</div>

<script>
  // Hacer el select más visual
  const select = document.getElementById("js-product-select");
  if (select) {
    select.addEventListener("change", function() {
      if (this.value) {
        this.style.borderColor = "rgba(59, 130, 246, 0.5)";
        this.style.boxShadow = "0 0 0 3px rgba(59, 130, 246, 0.1)";
      } else {
        this.style.borderColor = "";
        this.style.boxShadow = "";
      }
    });
  }
</script>
';

echo view('layout', [
  'title' => $title ?? 'Reabastecer Producto',
  'active' => $active ?? 'inventario',
  'content' => $content
]);
