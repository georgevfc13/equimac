<?php
/** @var string $title */
/** @var string $message */

$content = '
<div class="card pad">
  <div class="page-head">
    <div>
      <h2 class="page-title">'.e($title ?? 'Error').'</h2>
      <p class="page-sub">Algo falló en tu entorno local.</p>
    </div>
    <div class="row">
      <a class="btn" href="/inventario">Volver</a>
    </div>
  </div>

  <div class="card pad" style="background: rgba(0,0,0,.18); border-color: rgba(239,68,68,.25)">
    <div class="muted" style="font-size:12px; letter-spacing:.12em; text-transform:uppercase">Detalle</div>
    <div style="margin-top:8px; white-space:pre-wrap">'.e($message ?? 'Error').'</div>
  </div>
</div>';

echo view('layout', [
  'title' => $title ?? 'Error',
  'active' => '',
  'content' => $content
]);

