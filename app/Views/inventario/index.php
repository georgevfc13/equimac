<?php
/** @var array $items */
/** @var array $stats */
/** @var string $q */

$statsHtml = '
<div class="stats">
  <div class="stat"><div class="k">Total productos</div><div class="v">'.(int)($stats['total_productos'] ?? 0).'</div></div>
  <div class="stat"><div class="k">Cantidad total</div><div class="v">'.(int)($stats['cantidad_total'] ?? 0).'</div></div>
  <div class="stat"><div class="k">Estantes usados</div><div class="v">'.(int)($stats['total_estantes'] ?? 0).'</div></div>
  <div class="stat"><div class="k">Marcas</div><div class="v">'.(int)($stats['total_marcas'] ?? 0).'</div></div>
</div>';

$content = '
<div class="page-head">
  <div>
    <h2 class="page-title">Inventario</h2>
    <p class="page-sub">Búsqueda instantánea, acciones rápidas y UI local con animaciones.</p>
  </div>
  <div class="row">
    <a class="btn primary" href="'.e(url('inventario/nuevo')).'">+ Crear producto</a>
  </div>
</div>
'.$statsHtml.'

<div class="card pad">
  <div class="toolbar">
    <div class="search" title="Busca por código, descripción, marca o equipo">
      <span aria-hidden="true">⌕</span>
      <input id="js-search" autocomplete="off" placeholder="Buscar (instantáneo)…" value="'.e($q ?? '').'" />
      <span class="muted" style="font-size:12px">Resultados: <strong id="js-counter">'.count($items).'</strong></span>
    </div>
    <div class="row">
      <a class="btn" href="'.e(url('estantes')).'">Ver estantes</a>
    </div>
  </div>

  <div style="height:12px"></div>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Código</th>
          <th>Descripción</th>
          <th>Marca</th>
          <th>Ubicación</th>
          <th>Cantidad</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody id="js-table-body">';

foreach ($items as $p) {
    $content .= '
        <tr>
          <td class="mono"><strong>'.e($p['codigo']).'</strong></td>
          <td>
            <div>'.e($p['descripcion']).'</div>
            '.(!empty($p['equipo']) ? '<div class="muted" style="margin-top:6px;font-size:12px">📌 '.e($p['equipo']).'</div>' : '').'
          </td>
          <td>'.(!empty($p['marca']) ? '<span class="badge"><span class="dot"></span>'.e($p['marca']).'</span>' : '<span class="muted">—</span>').'</td>
          <td><span class="badge"><span class="dot warn"></span>Est. '.(int)$p['estante'].' · F'.(int)$p['entrepaño'].' · P'.(int)$p['posicion'].'</span></td>
          <td><span class="badge"><span class="dot good"></span>'.(int)$p['cantidad'].' '.e($p['unidad']).'</span></td>
          <td>
            <div class="row" style="gap:10px">
              <a class="btn" href="'.e(url('inventario/'.(int)$p['id'])).'">Ver</a>
              <a class="btn" href="'.e(url('inventario/'.(int)$p['id'].'/editar')).'">Editar</a>
              <button class="btn danger" data-quick-delete="'.(int)$p['id'].'" data-quick-name="'.e($p['descripcion']).'">Eliminar</button>
            </div>
          </td>
        </tr>';
}

$content .= '
      </tbody>
    </table>
  </div>

  <div id="js-empty" class="empty" style="display: '.(count($items) ? 'none' : 'block').';">
    <h3>Sin resultados</h3>
    <p>Prueba otra búsqueda o crea el primer producto.</p>
    <a class="btn primary" href="'.e(url('inventario/nuevo')).'">+ Crear producto</a>
  </div>
</div>';

echo view('layout', [
  'title' => $title ?? 'Inventario',
  'active' => $active ?? 'inventario',
  'content' => $content
]);

