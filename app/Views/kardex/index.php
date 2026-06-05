<?php
/** @var array $entradas */
/** @var array $salidas */

$content = '
<div class="page-head">
  <div>
    <h2 class="page-title">Kardex General</h2>
    <p class="page-sub">Registro de todas las entradas y salidas de productos.</p>
  </div>
  <div class="row">
    <a class="btn" href="'.e(url('inventario')).'">Inventario</a>
  </div>
</div>

<div style="height:24px"></div>

<!-- ENTRADAS -->
<div class="card pad" style="margin-bottom:24px">
  <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px">
    <div class="kardex-icon" style="width:24px;height:24px;background:rgba(34,197,94,.2);border-radius:8px;display:flex;align-items:center;justify-content:center;font-weight:700;color:rgb(34,197,94);font-size:14px">↓</div>
    <h3 style="margin:0;font-size:16px;font-weight:700">Entradas (Total: '.count($entradas).')</h3>
  </div>
  
  '.($entradas ? '<div class="table-wrap">
    <table style="min-width:100%">
      <thead>
        <tr>
          <th>Fecha</th>
          <th>Código</th>
          <th>Producto</th>
          <th>Cantidad</th>
          <th>De quién llegó</th>
          <th>Quién recibió</th>
          <th>Observaciones</th>
        </tr>
      </thead>
      <tbody>' : '').'';

foreach ($entradas as $e) {
    $content .= '
        <tr>
          <td><span style="font-size:12px">'.$e['created_at'].'</span></td>
          <td><strong class="mono">'.e($e['codigo'] ?? '—').'</strong></td>
          <td>'.e($e['nombre'] ?? '—').'</td>
          <td><strong>'.(int)$e['cantidad'].' '.e($e['unidad'] ?? '').'</strong></td>
          <td>'.e($e['quien_entrego'] ?? '—').'</td>
          <td>'.e($e['quien_recibio'] ?? '—').'</td>
          <td style="font-size:12px; color:#666">'.e($e['observaciones'] ?? '—').'</td>
        </tr>';
}

$content .= ($entradas ? '
      </tbody>
    </table>
  </div>' : '<p class="muted" style="margin:0">Sin registros de entrada</p>').'
</div>

<!-- SALIDAS -->
<div class="card pad">
  <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px">
    <div class="kardex-icon" style="width:24px;height:24px;background:rgba(239,68,68,.2);border-radius:8px;display:flex;align-items:center;justify-content:center;font-weight:700;color:rgb(239,68,68);font-size:14px">↑</div>
    <h3 style="margin:0;font-size:16px;font-weight:700">Salidas (Total: '.count($salidas).')</h3>
  </div>
  
  '.($salidas ? '<div class="table-wrap">
    <table style="min-width:100%">
      <thead>
        <tr>
          <th>Fecha</th>
          <th>Hora</th>
          <th>Código</th>
          <th>Producto</th>
          <th>Cantidad</th>
          <th>Quién recibió</th>
          <th>Quién entregó</th>
          <th>Observaciones</th>
        </tr>
      </thead>
      <tbody>' : '').'';

foreach ($salidas as $s) {
    $fecha = $s['fecha_salida'] ?: date('Y-m-d', strtotime($s['created_at']));
    $hora = $s['hora_salida'] ?: date('H:i', strtotime($s['created_at']));
    $content .= '
        <tr>
          <td><span style="font-size:12px">'.$fecha.'</span></td>
          <td><span style="font-size:12px">'.$hora.'</span></td>
          <td><strong class="mono">'.e($s['codigo'] ?? '—').'</strong></td>
          <td>'.e($s['nombre'] ?? '—').'</td>
          <td><strong>'.(int)$s['cantidad_usada'].' '.e($s['unidad'] ?? '').'</strong></td>
          <td>'.e($s['quien_recibio'] ?? '—').'</td>
          <td>'.e($s['quien_entrego'] ?? '—').'</td>
          <td style="font-size:12px; color:#666">'.e($s['observaciones'] ?? '—').'</td>
        </tr>';
}

$content .= ($salidas ? '
      </tbody>
    </table>
  </div>' : '<p class="muted" style="margin:0">Sin registros de salida</p>').'
</div>
';

echo view('layout', [
  'title' => $title ?? 'Kardex General',
  'active' => $active ?? 'kardex',
  'content' => $content
]);
