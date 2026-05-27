<?php
/** @var string $mode */
/** @var array|null $item */
/** @var array $estantes */
/** @var array $errors */

$isEdit = ($mode ?? 'create') === 'edit';
$id = $item['id'] ?? null;

function fieldError(array $errors, string $key): string {
  if (!isset($errors[$key])) return '';
  return '<div class="help" style="color: #ef4444">'.e($errors[$key]).'</div>';
}

$content = '
<div class="page-head">
  <div>
    <h2 class="page-title">'.($isEdit ? 'Editar producto' : 'Nuevo producto').'</h2>
    <p class="page-sub">Entrada rápida, validación clara y UX local.</p>
  </div>
  <div class="row">
    <a class="btn" href="'.e(url('inventario')).'">Volver</a>
  </div>
</div>

<div class="card pad">
  <form method="POST" action="'.e(url('inventario/guardar')).'">
    '.($isEdit ? '<input type="hidden" name="id" value="'.(int)$id.'">' : '').'
    <div class="form-grid">
      <div class="field">
        <label>Código *</label>
        <input name="codigo" '.($isEdit ? 'readonly' : '').' value="'.e($item['codigo'] ?? '').'" placeholder="EQ-001" />
        '.fieldError($errors, 'codigo').'
      </div>
      <div class="field">
        <label>Unidad *</label>
        <input name="unidad" value="'.e($item['unidad'] ?? '').'" placeholder="Pieza / Unidad / Caja…" />
        '.fieldError($errors, 'unidad').'
      </div>

      <div class="field" style="grid-column:1/-1">
        <label>Descripción *</label>
        <textarea name="descripcion" placeholder="Describe el producto…">'.e($item['descripcion'] ?? '').'</textarea>
        '.fieldError($errors, 'descripcion').'
      </div>

      <div class="field">
        <label>Cantidad *</label>
        <input type="number" min="0" name="cantidad" value="'.e((string)($item['cantidad'] ?? 0)).'" />
        '.fieldError($errors, 'cantidad').'
      </div>
      <div class="field">
        <label>Stock mínimo</label>
        <input type="number" min="0" name="stock_minimo" value="'.e((string)($item['stock_minimo'] ?? 5)).'" placeholder="5" title="Cantidad mínima antes de mostrar alerta de stock bajo" />
        <div class="help" style="font-size:12px;color:var(--muted)">Cantidad mínima que genera alerta (por defecto: 5)</div>
      </div>
      <div class="field">
        <label>Estado</label>
        <input name="estado" value="'.e($item['estado'] ?? '').'" placeholder="Activo / Inactivo / Mantenimiento…" />
      </div>

      <div class="field">
        <label>Marca</label>
        <input name="marca" value="'.e($item['marca'] ?? '').'" placeholder="Siemens / ABB / …" />
      </div>
      <div class="field">
        <label>Equipo</label>
        <input name="equipo" value="'.e($item['equipo'] ?? '').'" placeholder="Bomba / Compresor / …" />
      </div>

      <div class="field">
        <label>Estante *</label>
        <select id="js-estante-select" name="estante">
          <option value="0">Selecciona…</option>';
foreach (($estantes ?? []) as $e) {
    $sel = ((int)($item['estante'] ?? 0) === (int)$e['numero']) ? 'selected' : '';
    $content .= '<option value="'.(int)$e['numero'].'" data-filas="'.(int)($e['filas'] ?? 5).'" data-columnas="'.(int)($e['columnas'] ?? 5).'" '.$sel.'>Estante '.(int)$e['numero'].' · '.e($e['descripcion'] ?? '').'</option>';
}
$content .= '
        </select>
        '.fieldError($errors, 'estante').'
      </div>

      <div class="field" style="grid-column:1/-1">
        <label>Ubicación en el estante *</label>
        <p class="help" style="margin-bottom:12px; font-size:13px">Haz clic en una posición disponible (azul) para seleccionarla. Las posiciones ocupadas (rojo) no se pueden seleccionar.</p>
        <div id="js-picker-container" style="display:none">
          <div class="table-size-picker" data-table-picker data-max="20">
            <div class="table-size-picker-grid js-picker-grid" data-grid></div>
            <div class="table-size-picker-label js-picker-label" data-label></div>
            <input type="hidden" name="entrepaño" class="js-picker-filas" value="1" />
            <input type="hidden" name="posicion" class="js-picker-columnas" value="1" />
          </div>
        </div>
        <div id="js-picker-placeholder" style="color:var(--muted); font-size:13px">
          Selecciona un estante primero…
        </div>
        '.fieldError($errors, 'entrepaño').'
        '.fieldError($errors, 'posicion').'
      </div>

      <div class="field">
        <label>Precio pagado</label>
        <input type="number" step="0.01" min="0" name="precio_pagado" value="'.e((string)($item['precio_pagado'] ?? '')).'" />
      </div>

      '.(!$isEdit ? '<div class="field" style="grid-column:1/-1;border-top:2px solid rgba(59, 130, 246, .15);padding-top:16px;margin-top:8px">
        <div class="muted" style="font-size:11px; letter-spacing:.12em; text-transform:uppercase">Información de Entrada</div>
      </div>' : '').'

      '.(!$isEdit ? '<div class="field">
        <label>De quién llegó *</label>
        <input name="de_quien_llego" value="'.e($item['de_quien_llego'] ?? '').'" placeholder="Proveedor / Persona que entregó…" />
        '.fieldError($errors, 'de_quien_llego').'
      </div>' : '').'

      '.(!$isEdit ? '<div class="field">
        <label>Quién recibió *</label>
        <input name="quien_recibio" value="'.e($item['quien_recibio'] ?? '').'" placeholder="Nombre de quién recibió…" />
        '.fieldError($errors, 'quien_recibio').'
      </div>' : '').'
    </div>

    <div class="modal-foot" style="padding-left:0;padding-right:0;border-top:none;background:transparent">
      <button class="btn good" type="submit">'.($isEdit ? 'Guardar cambios' : 'Crear producto').'</button>
      <a class="btn" href="'.e(url('inventario')).'">Cancelar</a>
    </div>
  </form>
</div>';

echo view('layout', [
  'title' => $title ?? ($isEdit ? 'Editar' : 'Nuevo'),
  'active' => $active ?? 'inventario',
  'content' => $content
]);

