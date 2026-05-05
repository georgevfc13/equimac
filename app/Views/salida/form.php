<?php
/** @var array $errors */
/** @var array $old */

function salidaFieldError(array $errors, string $key): string {
  if (!isset($errors[$key])) return '';
  return '<div class="help" style="color: rgba(239,68,68,.95)">'.e($errors[$key]).'</div>';
}

$o = $old ?? [];

$content = '
<div class="page-head">
  <div>
    <h2 class="page-title">Salida de material</h2>
    <p class="page-sub">Registra consumo o entrega desde bodega. Se descuenta del inventario y queda historial.</p>
  </div>
  <div class="row">
    <a class="btn" href="'.e(url('inventario')).'">Inventario</a>
  </div>
</div>

<div class="card pad">
  <form method="POST" action="'.e(url('salida/guardar')).'">
    <div class="form-grid">
      <div class="field">
        <label>Código del producto *</label>
        <input name="codigo" value="'.e((string)($o['codigo'] ?? '')).'" placeholder="EQ-001" autocomplete="off" />
        '.salidaFieldError($errors ?? [], 'codigo').'
      </div>
      <div class="field">
        <label>Cantidad usada *</label>
        <input type="number" min="1" name="cantidad_usada" value="'.e((string)($o['cantidad_usada'] ?? '')).'" placeholder="1" />
        '.salidaFieldError($errors ?? [], 'cantidad_usada').'
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
    </div>
    <div style="height:16px"></div>
    <div class="row" style="justify-content:flex-end">
      <button type="submit" class="btn primary">Registrar salida</button>
    </div>
  </form>
</div>
';

echo view('layout', [
  'title' => $title ?? 'Salida',
  'active' => $active ?? 'salida',
  'content' => $content
]);
