<?php
/** @var array $estantes */
/** @var array $map */
/** @var string $flash */
/** @var array $form_errors */
/** @var array $old_estante */

$flash = $flash ?? '';
$form_errors = $form_errors ?? [];
$old_estante = $old_estante ?? [];
$hasFormErrors = $form_errors !== [];

function estFieldErr(array $errors, string $key): string {
  if (!isset($errors[$key])) return '';
  return '<div class="help" style="color: rgba(239,68,68,.95)">'.e($errors[$key]).'</div>';
}

$flashHtml = '';
if ($flash === 'creado') {
  $flashHtml = '<div class="card pad" style="margin-bottom:14px;border-color: rgba(34,197,94,.45);background: rgba(34,197,94,.08)"><strong>Estante creado</strong> · Ya puedes asignar productos a ese número de estante.</div>';
} elseif ($flash === 'eliminado') {
  $flashHtml = '<div class="card pad" style="margin-bottom:14px;border-color: rgba(59,130,246,.45);background: rgba(59,130,246,.08)"><strong>Estante eliminado</strong></div>';
} elseif ($flash === 'ocupado') {
  $flashHtml = '<div class="card pad" style="margin-bottom:14px;border-color: rgba(239,68,68,.45);background: rgba(239,68,68,.1)"><strong>No se puede eliminar</strong> · Aún hay productos en ese estante. Muévelos o elimínalos primero.</div>';
} elseif ($flash === 'no_encontrado') {
  $flashHtml = '<div class="card pad" style="margin-bottom:14px;border-color: rgba(245,158,11,.45);background: rgba(245,158,11,.1)"><strong>Estante no encontrado</strong></div>';
}

$oeNum = (string)($old_estante['numero'] ?? '');
$oeDesc = (string)($old_estante['descripcion'] ?? '');
$oeUbi = (string)($old_estante['ubicacion'] ?? '');
$oeFilas = min(20, max(1, (int)($old_estante['filas'] ?? 5)));
$oeCols = min(20, max(1, (int)($old_estante['columnas'] ?? 5)));

$content = '
<div class="page-head">
  <div>
    <h2 class="page-title">Estantes</h2>
    <p class="page-sub">Ocupación local, altas/bajas y tamaño del estante (filas × posiciones) como en Word.</p>
  </div>
  <div class="row">
    <button type="button" class="btn primary" data-open-modal="modal-nuevo-estante">+ Agregar estante</button>
    <a class="btn" href="'.e(url('inventario')).'">Inventario</a>
  </div>
</div>
'.$flashHtml.'
';

if ($hasFormErrors) {
  $content .= '<div class="card pad" style="margin-bottom:14px;border-color: rgba(239,68,68,.45);background: rgba(239,68,68,.08)"><strong>Revisa el formulario</strong> · Corrige los campos marcados abajo.</div>';
}

$content .= '
<div id="modal-nuevo-estante" class="modal'.($hasFormErrors ? ' is-open' : '').'" aria-hidden="'.($hasFormErrors ? 'false' : 'true').'">
  <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="titulo-nuevo-estante">
    <div class="modal-head">
      <h3 id="titulo-nuevo-estante">Nuevo estante</h3>
      <button type="button" class="icon-btn" data-close-modal aria-label="Cerrar">✕</button>
    </div>
    <form method="POST" action="'.e(url('estantes/guardar')).'">
      <div class="modal-body" style="max-height: calc(100vh - 300px); overflow-y: auto;">
        <div class="form-grid">
          <div class="field">
            <label>Número de estante *</label>
            <input type="number" min="1" name="numero" value="'.e($oeNum).'" placeholder="Ej. 3" required />
            '.estFieldErr($form_errors, 'numero').'
          </div>
          <div class="field">
            <label>Ubicación</label>
            <input name="ubicacion" value="'.e($oeUbi).'" placeholder="Bodega / Taller…" />
          </div>
          <div class="field" style="grid-column:1/-1">
            <label>Descripción</label>
            <input name="descripcion" value="'.e($oeDesc).'" placeholder="Ej. Refacciones menores" />
          </div>
        </div>
        <div style="height:14px"></div>
        <div class="field">
          <label>Tamaño del estante (filas × posiciones)</label>
          <p class="help" style="margin:0 0 8px">Pasa el cursor por la cuadrícula y haz clic, como al insertar una tabla en Word.</p>
          <div class="table-size-picker" data-table-picker data-max="20" data-default-rows="'.(int)$oeFilas.'" data-default-cols="'.(int)$oeCols.'">
            <input type="hidden" name="filas" class="js-picker-filas" value="'.(int)$oeFilas.'" />
            <input type="hidden" name="columnas" class="js-picker-columnas" value="'.(int)$oeCols.'" />
            <div class="table-size-picker-grid js-picker-grid" data-grid></div>
            <div class="table-size-picker-label"><span class="js-picker-label" data-label></span></div>
          </div>
          '.estFieldErr($form_errors, 'filas').'
          '.estFieldErr($form_errors, 'columnas').'
        </div>
      </div>
      <div class="modal-foot">
        <button type="button" class="btn" data-close-modal>Cancelar</button>
        <button type="submit" class="btn primary">Guardar estante</button>
      </div>
    </form>
  </div>
</div>
';

foreach (($estantes ?? []) as $e) {
    $eid = (int)$e['id'];
    $num = (int)$e['numero'];
    $filas = max(1, (int)($e['filas'] ?? 5));
    $cols = max(1, (int)($e['columnas'] ?? 5));

    $content .= '
<div class="card pad" style="margin-bottom:14px">
  <div class="row" style="justify-content:space-between;align-items:flex-start">
    <div>
      <div class="muted" style="font-size:12px; letter-spacing:.12em; text-transform:uppercase">Estante</div>
      <div style="font-size:20px; font-weight:900; letter-spacing:-.02em">#'.$num.' · '.e($e['descripcion'] ?? '').'</div>
      <div class="muted" style="font-size:12px; margin-top:4px">'.e($e['ubicacion'] ?? '').'</div>
    </div>
    <div class="row" style="align-items:flex-start">
      <span class="badge"><span class="dot warn"></span>'.$filas.' filas</span>
      <span class="badge"><span class="dot"></span>'.$cols.' posiciones</span>
      <form method="POST" action="'.e(url('estantes/'.$eid.'/eliminar')).'" onsubmit="return confirm(\'¿Eliminar el estante #'.$num.'? Solo si no tiene productos.\');">
        <button type="submit" class="btn danger">Eliminar estante</button>
      </form>
    </div>
  </div>

  <div style="height:14px"></div>
  <div class="table-wrap">
    <table style="min-width:640px">
      <thead>
        <tr>
          <th>Fila</th>';
    for ($p = 1; $p <= $cols; $p++) {
        $content .= '<th>P'.$p.'</th>';
    }
    $content .= '</tr>
      </thead>
      <tbody>';

    for ($f = $filas; $f >= 1; $f--) {
        $content .= '<tr><td class="mono"><strong>F'.$f.'</strong></td>';
        for ($p = 1; $p <= $cols; $p++) {
            $prod = $map[$num][$f][$p] ?? null;
            if ($prod) {
                $content .= '<td>
                  <a class="btn" style="padding:8px 10px; border-radius:14px; width:100%; justify-content:center"
                     href="'.e(url('inventario/'.(int)$prod['id'])).'"
                     title="'.e($prod['descripcion']).'">
                    <span class="mono">'.e($prod['codigo']).'</span>
                  </a>
                </td>';
            } else {
                $content .= '<td><span class="badge" style="opacity:.55"><span class="dot good"></span>Libre</span></td>';
            }
        }
        $content .= '</tr>';
    }

    $content .= '
      </tbody>
    </table>
  </div>
</div>';
}

if (empty($estantes)) {
  $content .= '<div class="card pad"><div class="empty"><h3>No hay estantes</h3><p>Usa <strong>Agregar estante</strong> para crear el primero.</p></div></div>';
}

if ($hasFormErrors) {
  $content .= '<script>document.body.style.overflow="hidden";</script>';
}

echo view('layout', [
  'title' => $title ?? 'Estantes',
  'active' => $active ?? 'estantes',
  'content' => $content
]);
