<?php
/** @var array<array> $items */
/** @var array<string, string> $errors */
/** @var array $old */

function loteFieldError(array $errors, string $key): string {
  if (!isset($errors[$key])) return '';
  return '<div class="help" style="color: rgba(239,68,68,.95)">'.e($errors[$key]).'</div>';
}

$items = $items ?? [];
$errors = $errors ?? [];
$o = $old ?? [];
$lines = $o['lines'] ?? [['inventario_id' => '', 'cantidad' => '1']];
if (!$lines) {
  $lines = [['inventario_id' => '', 'cantidad' => '1']];
}

$flash = '';
if (isset($_GET['entrada_ok']) && $_GET['entrada_ok'] === '1') {
  $ordenMsg = isset($_GET['orden']) ? ' · '.e((string)$_GET['orden']) : '';
  $flash = '<div class="card pad flash-good" style="margin-bottom:14px"><strong>Entrada registrada</strong>'.$ordenMsg.'</div>';
}

$optionsHtml = '<option value="">— Selecciona —</option>';
foreach ($items as $p) {
  $pid = (int)$p['id'];
  $label = ($p['nombre'] ?? '') !== '' ? e($p['nombre']).' · '.e($p['codigo']) : e($p['codigo']);
  $optionsHtml .= '<option value="'.$pid.'">'.$label.' (Stock: '.(int)$p['cantidad'].')</option>';
}

$content = $flash.'
<div class="page-head">
  <div>
    <h2 class="page-title">Entrada múltiple</h2>
    <p class="page-sub">Registra varios productos en una sola orden de entrada (000, 001…).</p>
  </div>
  <div class="row">
    <a class="btn" href="'.e(url('inventario/entrada')).'">← Atrás</a>
  </div>
</div>';

if (empty($items)) {
  $content .= '<div class="card pad"><div class="empty"><h3>No hay productos</h3><p>Crea productos primero.</p></div></div>';
} else {
  $content .= '
<div class="card pad">
  <form method="POST" action="'.e(url('inventario/entrada-lote')).'" id="js-entrada-lote-form">
    <div class="form-grid">
      <div class="field">
        <label>Fecha de entrada</label>
        <input type="date" name="fecha_entrada" value="'.e((string)($o['fecha_entrada'] ?? date('Y-m-d'))).'" />
      </div>
      <div class="field">
        <label>Hora de entrada</label>
        <input type="time" name="hora_entrada" value="'.e((string)($o['hora_entrada'] ?? date('H:i'))).'" />
      </div>
      <div class="field" style="grid-column:1/-1">
        <label>De quién llegó *</label>
        <input name="de_quien_llego" value="'.e((string)($o['de_quien_llego'] ?? '')).'" required />
        '.loteFieldError($errors, 'de_quien_llego').'
      </div>
      <div class="field" style="grid-column:1/-1">
        <label>Quién recibió *</label>
        <input name="quien_recibio" value="'.e((string)($o['quien_recibio'] ?? '')).'" required />
        '.loteFieldError($errors, 'quien_recibio').'
      </div>
      <div class="field" style="grid-column:1/-1">
        <label>Observaciones</label>
        <textarea name="observaciones" style="min-height:70px">'.e((string)($o['observaciones'] ?? '')).'</textarea>
      </div>
    </div>

    <div style="height:20px"></div>
    <div class="row" style="justify-content:space-between;align-items:center">
      <h3 style="margin:0;font-size:18px">Productos</h3>
      <button type="button" class="btn" id="js-add-entrada-line">+ Agregar producto</button>
    </div>
    '.loteFieldError($errors, 'lines').'
    '.loteFieldError($errors, 'general').'

    <div id="js-entrada-lines" style="margin-top:12px;display:flex;flex-direction:column;gap:10px">';

  foreach ($lines as $i => $line) {
    $selId = (int)($line['inventario_id'] ?? 0);
    $lineOptions = '<option value="">— Selecciona —</option>';
    foreach ($items as $p) {
      $pid = (int)$p['id'];
      $sel = ($selId === $pid) ? ' selected' : '';
      $label = ($p['nombre'] ?? '') !== '' ? e($p['nombre']).' · '.e($p['codigo']) : e($p['codigo']);
      $lineOptions .= '<option value="'.$pid.'"'.$sel.'>'.$label.' (Stock: '.(int)$p['cantidad'].')</option>';
    }
    $content .= '
      <div class="mov-line" data-line>
        <div class="form-grid" style="grid-template-columns:1fr 140px auto;align-items:end">
          <div class="field" style="margin:0">
            <label>Producto</label>
            <select name="line_inventario_id[]" required>'.$lineOptions.'</select>
          </div>
          <div class="field" style="margin:0">
            <label>Cantidad</label>
            <input type="number" min="1" name="line_cantidad[]" value="'.e((string)($line['cantidad'] ?? '1')).'" required />
          </div>
          <button type="button" class="btn danger" data-remove-line title="Quitar">✕</button>
        </div>
        '.loteFieldError($errors, "line_{$i}").'
      </div>';
  }

  $content .= '
    </div>
    <template id="js-entrada-line-template">
      <div class="mov-line" data-line>
        <div class="form-grid" style="grid-template-columns:1fr 140px auto;align-items:end">
          <div class="field" style="margin:0">
            <label>Producto</label>
            <select name="line_inventario_id[]" required>'.$optionsHtml.'</select>
          </div>
          <div class="field" style="margin:0">
            <label>Cantidad</label>
            <input type="number" min="1" name="line_cantidad[]" value="1" required />
          </div>
          <button type="button" class="btn danger" data-remove-line title="Quitar">✕</button>
        </div>
      </div>
    </template>

    <div style="height:16px"></div>
    <div class="row" style="justify-content:flex-end">
      <button type="submit" class="btn primary">Registrar orden de entrada</button>
    </div>
  </form>
</div>';
}

echo view('layout', [
  'title' => $title ?? 'Entrada múltiple',
  'active' => $active ?? 'inventario',
  'content' => $content
]);
