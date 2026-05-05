<?php
/** @var string $mode */
/** @var array|null $item */
/** @var array $estantes */
/** @var array $errors */

$isEdit = ($mode ?? 'create') === 'edit';
$id = $item['id'] ?? null;

function fieldError(array $errors, string $key): string {
  if (!isset($errors[$key])) return '';
  return '<div class="help" style="color: rgba(239,68,68,.95)">'.e($errors[$key]).'</div>';
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
        <select name="estante">
          <option value="0">Selecciona…</option>';
foreach (($estantes ?? []) as $e) {
    $sel = ((int)($item['estante'] ?? 0) === (int)$e['numero']) ? 'selected' : '';
    $content .= '<option value="'.(int)$e['numero'].'" '.$sel.'>Estante '.(int)$e['numero'].' · '.e($e['descripcion'] ?? '').'</option>';
}
$content .= '
        </select>
        '.fieldError($errors, 'estante').'
      </div>

      <div class="field">
        <label>Fila (entrepaño) *</label>
        <input type="number" min="1" name="entrepaño" value="'.e((string)($item['entrepaño'] ?? 1)).'" />
        '.fieldError($errors, 'entrepaño').'
      </div>

      <div class="field">
        <label>Posición *</label>
        <input type="number" min="1" name="posicion" value="'.e((string)($item['posicion'] ?? 1)).'" />
        '.fieldError($errors, 'posicion').'
      </div>

      <div class="field">
        <label>Precio pagado</label>
        <input type="number" step="0.01" min="0" name="precio_pagado" value="'.e((string)($item['precio_pagado'] ?? '')).'" />
      </div>
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

