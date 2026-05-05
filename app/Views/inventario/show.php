<?php
/** @var array $item */

$content = '
<div class="page-head">
  <div>
    <h2 class="page-title">Detalle</h2>
    <p class="page-sub">'.e($item['codigo']).' · '.e($item['descripcion']).'</p>
  </div>
  <div class="row">
    <a class="btn" href="'.e(url('inventario')).'">Volver</a>
    <a class="btn primary" href="'.e(url('inventario/'.(int)$item['id'].'/editar')).'">Editar</a>
    <form method="POST" action="'.e(url('inventario/'.(int)$item['id'].'/eliminar')).'" style="display:inline" onsubmit="return confirm(\'¿Eliminar este producto?\')">
      <button class="btn danger" type="submit">Eliminar</button>
    </form>
  </div>
</div>

<div class="card pad">
  <div class="form-grid">
    <div class="field">
      <label>Código</label>
      <input readonly value="'.e($item['codigo']).'" />
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
      <label>Cantidad</label>
      <input readonly value="'.(int)$item['cantidad'].' '.e($item['unidad']).'" />
    </div>
  </div>

  <div style="height:14px"></div>
  <div class="muted" style="font-size:12px">
    Creado: '.e((string)($item['fecha_creacion'] ?? '—')).' · Actualizado: '.e((string)($item['fecha_actualizacion'] ?? '—')).'
  </div>
</div>';

echo view('layout', [
  'title' => $title ?? 'Detalle',
  'active' => $active ?? 'inventario',
  'content' => $content
]);

