<?php
$title = $title ?? 'No encontrado';
$content = '
<div class="card pad">
  <div class="page-head">
    <div>
      <h2 class="page-title">404</h2>
      <p class="page-sub">La ruta no existe en esta app local.</p>
    </div>
    <div class="row">
      <a class="btn primary" href="/inventario">Ir a Inventario</a>
    </div>
  </div>
</div>';

echo view('layout', [
  'title' => $title,
  'active' => '',
  'content' => $content
]);

