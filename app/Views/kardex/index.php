<?php
/** @var array $entradas */
/** @var array $salidas */
/** @var array $filters */
/** @var string $tipo */

use App\Support\OrdenHelper;

$filters = $filters ?? ['q' => '', 'orden' => '', 'desde' => '', 'hasta' => ''];
$tipo = $tipo ?? 'ambos';

$ordenCell = function (?int $numero, string $prefix): string {
  if ($numero === null) {
    return '<span class="muted">—</span>';
  }
  return '<span class="badge"><span class="dot"></span>'.e($prefix . ' ' . OrdenHelper::formatNumero($numero)).'</span>';
};

$content = '
<div class="page-head">
  <div>
    <h2 class="page-title">Kardex General</h2>
    <p class="page-sub">Registro de entradas y salidas con órdenes y filtros.</p>
  </div>
  <div class="row">
    <a class="btn" href="'.e(url('inventario')).'">Inventario</a>
  </div>
</div>

<div class="card pad" style="margin-bottom:20px">
  <form method="GET" action="'.e(url('kardex')).'" class="toolbar" style="flex-wrap:wrap;gap:12px">
    <div class="field" style="margin:0;min-width:160px">
      <label>Tipo</label>
      <select name="tipo">
        <option value="ambos"'.($tipo === 'ambos' ? ' selected' : '').'>Entradas y salidas</option>
        <option value="entradas"'.($tipo === 'entradas' ? ' selected' : '').'>Solo entradas</option>
        <option value="salidas"'.($tipo === 'salidas' ? ' selected' : '').'>Solo salidas</option>
      </select>
    </div>
    <div class="field" style="margin:0;min-width:140px">
      <label>Orden #</label>
      <input name="orden" value="'.e($filters['orden']).'" placeholder="000" />
    </div>
    <div class="field" style="margin:0;flex:1;min-width:180px">
      <label>Producto / código</label>
      <input name="q" value="'.e($filters['q']).'" placeholder="Buscar…" />
    </div>
    <div class="field" style="margin:0">
      <label>Desde</label>
      <input type="date" name="desde" value="'.e($filters['desde']).'" />
    </div>
    <div class="field" style="margin:0">
      <label>Hasta</label>
      <input type="date" name="hasta" value="'.e($filters['hasta']).'" />
    </div>
    <div class="row" style="align-self:flex-end">
      <button type="submit" class="btn primary">Filtrar</button>
      <a class="btn" href="'.e(url('kardex')).'">Limpiar</a>
    </div>
  </form>
</div>

<div style="height:8px"></div>';

if ($tipo !== 'salidas') {
$content .= '
<div class="card pad" style="margin-bottom:24px">
  <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px">
    <div class="kardex-icon kardex-icon-in">↓</div>
    <h3 style="margin:0;font-size:18px;font-weight:700">Entradas ('.count($entradas).')</h3>
  </div>
  '.($entradas ? '<div class="table-wrap"><table><thead><tr>
          <th>Orden de entrada</th>
          <th>Fecha</th>
          <th>Código</th>
          <th>Producto</th>
          <th>Cantidad</th>
          <th>De quién llegó</th>
          <th>Quién recibió</th>
          <th>Observaciones</th>
        </tr></thead><tbody>' : '');

foreach ($entradas as $e) {
    $num = isset($e['orden_numero']) && $e['orden_numero'] !== null ? (int)$e['orden_numero'] : null;
    $fecha = $e['fecha_entrada'] ?: substr((string)($e['created_at'] ?? ''), 0, 10);
    $content .= '
        <tr>
          <td>'.$ordenCell($num, 'Orden de entrada').'</td>
          <td>'.e($fecha).'</td>
          <td><strong class="mono">'.e($e['codigo'] ?? '—').'</strong></td>
          <td>'.e($e['nombre'] ?? '—').'</td>
          <td><strong>'.(int)$e['cantidad'].' '.e($e['unidad'] ?? '').'</strong></td>
          <td>'.e($e['quien_entrego'] ?? '—').'</td>
          <td>'.e($e['quien_recibio'] ?? '—').'</td>
          <td class="muted">'.e($e['observaciones'] ?? '—').'</td>
        </tr>';
}

$content .= ($entradas ? '</tbody></table></div>' : '<p class="muted" style="margin:0">Sin registros de entrada</p>').'
</div>';
}

if ($tipo !== 'entradas') {
$content .= '
<div class="card pad">
  <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px">
    <div class="kardex-icon kardex-icon-out">↑</div>
    <h3 style="margin:0;font-size:18px;font-weight:700">Salidas ('.count($salidas).')</h3>
  </div>
  '.($salidas ? '<div class="table-wrap"><table><thead><tr>
          <th>Orden de salida</th>
          <th>Fecha</th>
          <th>Hora</th>
          <th>Código</th>
          <th>Producto</th>
          <th>Cantidad</th>
          <th>Quién recibió</th>
          <th>Quién entregó</th>
          <th>Observaciones</th>
        </tr></thead><tbody>' : '');

foreach ($salidas as $s) {
    $num = isset($s['orden_numero']) && $s['orden_numero'] !== null ? (int)$s['orden_numero'] : null;
    $fecha = $s['fecha_salida'] ?: date('Y-m-d', strtotime((string)$s['created_at']));
    $hora = $s['hora_salida'] ?: date('H:i', strtotime((string)$s['created_at']));
    $content .= '
        <tr>
          <td>'.$ordenCell($num, 'Orden de salida').'</td>
          <td>'.e($fecha).'</td>
          <td>'.e($hora).'</td>
          <td><strong class="mono">'.e($s['codigo'] ?? '—').'</strong></td>
          <td>'.e($s['nombre'] ?? '—').'</td>
          <td><strong>'.(int)$s['cantidad_usada'].' '.e($s['unidad'] ?? '').'</strong></td>
          <td>'.e($s['quien_recibio'] ?? '—').'</td>
          <td>'.e($s['quien_entrego'] ?? '—').'</td>
          <td class="muted">'.e($s['observaciones'] ?? '—').'</td>
        </tr>';
}

$content .= ($salidas ? '</tbody></table></div>' : '<p class="muted" style="margin:0">Sin registros de salida</p>').'
</div>';
}

echo view('layout', [
  'title' => $title ?? 'Kardex General',
  'active' => $active ?? 'kardex',
  'content' => $content
]);
