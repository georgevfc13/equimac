<?php
/** @var array $errors */
/** @var array $old */

function salidaFieldError(array $errors, string $key): string {
  if (!isset($errors[$key])) return '';
  return '<div class="help" style="color: rgba(239,68,68,.95)">'.e($errors[$key]).'</div>';
}

$o = $old ?? [];
$lines = $o['lines'] ?? [['codigo' => '', 'cantidad_usada' => '1']];
if (!$lines) {
  $lines = [['codigo' => '', 'cantidad_usada' => '1']];
}

$flash = '';
if (isset($_GET['ok']) && $_GET['ok'] === '1') {
  $ordenMsg = isset($_GET['orden']) ? ' · '.e((string)$_GET['orden']) : '';
  $flash = '<div class="card pad flash-good" style="margin-bottom:14px"><strong>Salida registrada</strong>'.$ordenMsg.'</div>';
}

$content = $flash.'
<div class="page-head">
  <div>
    <h2 class="page-title">Salida de material</h2>
    <p class="page-sub">Registra una o varias salidas bajo la misma orden de salida (000, 001…).</p>
  </div>
  <div class="row">
    <a class="btn" href="'.e(url('inventario')).'">Inventario</a>
  </div>
</div>

<div class="card pad">
  <form method="POST" action="'.e(url('salida/guardar')).'" id="js-salida-form">
    <div class="form-grid">
      <div class="field">
        <label>Fecha de salida *</label>
        <input type="date" name="fecha_salida" value="'.e((string)($o['fecha_salida'] ?? date('Y-m-d'))).'" required />
        '.salidaFieldError($errors ?? [], 'fecha_salida').'
      </div>
      <div class="field">
        <label>Hora de salida *</label>
        <input type="time" name="hora_salida" value="'.e((string)($o['hora_salida'] ?? date('H:i'))).'" required />
        '.salidaFieldError($errors ?? [], 'hora_salida').'
      </div>
      <div class="field" style="grid-column:1/-1">
        <label>Quién recibió *</label>
        <input name="quien_recibio" value="'.e((string)($o['quien_recibio'] ?? '')).'" placeholder="Nombre de quien se lleva el material" />
        '.salidaFieldError($errors ?? [], 'quien_recibio').'
      </div>
      <div class="field" style="grid-column:1/-1">
        <label>Quién entregó *</label>
        <input name="quien_entrego" value="'.e((string)($o['quien_entrego'] ?? '')).'" placeholder="Nombre de quien entrega desde bodega" />
        '.salidaFieldError($errors ?? [], 'quien_entrego').'
      </div>
      <div class="field" style="grid-column:1/-1">
        <label>Observaciones</label>
        <textarea name="observaciones" placeholder="Notas de la orden de salida…" style="height:60px">'.e((string)($o['observaciones'] ?? '')).'</textarea>
      </div>
    </div>

    <div style="height:20px"></div>
    <div class="row" style="justify-content:space-between;align-items:center">
      <h3 style="margin:0;font-size:18px">Productos</h3>
      <button type="button" class="btn" id="js-add-salida-line">+ Agregar producto</button>
    </div>
    '.salidaFieldError($errors ?? [], 'lines').'
    '.salidaFieldError($errors ?? [], 'general').'

    <div id="js-salida-lines" style="margin-top:12px;display:flex;flex-direction:column;gap:10px">';

foreach ($lines as $i => $line) {
    $content .= '
      <div class="mov-line" data-line>
        <div class="form-grid" style="grid-template-columns:1fr 140px auto;align-items:end">
          <div class="field" style="margin:0">
            <label>Código</label>
            <input name="line_codigo[]" value="'.e((string)($line['codigo'] ?? '')).'" placeholder="EQ-001" autocomplete="off" />
          </div>
          <div class="field" style="margin:0">
            <label>Cantidad</label>
            <input type="number" min="1" name="line_cantidad[]" value="'.e((string)($line['cantidad_usada'] ?? $line['cantidad'] ?? '1')).'" />
          </div>
          <button type="button" class="btn danger" data-remove-line title="Quitar">✕</button>
        </div>
        '.salidaFieldError($errors ?? [], "line_{$i}").'
      </div>';
}

$content .= '
    </div>

    <div style="height:16px"></div>
    <div class="row" style="justify-content:flex-end">
      <button type="submit" class="btn primary">Registrar orden de salida</button>
    </div>
  </form>
</div>
';

echo view('layout', [
  'title' => $title ?? 'Salida',
  'active' => $active ?? 'salida',
  'content' => $content
]);
