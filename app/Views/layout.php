<?php
/** @var string $title */
/** @var string $content */
/** @var string $active */

use App\Models\Inventario;

$__notif = ['lowStockItems' => [], 'outOfStockIds' => []];
try {
  $inv = new Inventario();
  $__notif['lowStockItems'] = $inv->lowStockItems();
  $__notif['outOfStockIds'] = array_map(fn($it) => $it['id'], $inv->outOfStockItems());
} catch (\Throwable $e) {
  // Fallback silencioso: sin notificaciones si falla DB.
  $__notif = ['lowStockItems' => [], 'outOfStockIds' => []];
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= e($title ?? 'EQUIMAC') ?></title>
  <link rel="stylesheet" href="<?= e(url('assets/css/app.css')) ?>" />
</head>
<body>
  <div class="app-shell">
    <header class="topbar">
      <div class="container">
        <div class="topbar-inner">
          <a class="brand" href="<?= e(url('inventario')) ?>">
            <img src="<?= e(url('assets/icons/logo.png')) ?>" alt="EQUIMAC Logo" width="40" height="40" />
            <span>
              <h1>EQUIMAC</h1>
              <small>Manejo de inventario de la empresa Equimac</small>
            </span>
          </a>
          <nav class="nav" aria-label="Principal">
            <a class="chip <?= ($active ?? '') === 'inventario' ? 'is-active' : '' ?>" href="<?= e(url('inventario')) ?>">Inventario</a>
            <a class="chip <?= ($active ?? '') === 'estantes' ? 'is-active' : '' ?>" href="<?= e(url('estantes')) ?>">Estantes</a>
            <a class="chip <?= ($active ?? '') === 'salida' ? 'is-active' : '' ?>" href="<?= e(url('salida')) ?>">Salida</a>
            <a class="chip <?= ($active ?? '') === 'kardex' ? 'is-active' : '' ?>" href="<?= e(url('kardex')) ?>" title="Registro de todas las entradas y salidas">📋 Kardex</a>
            <a class="chip" href="<?= e(url('inventario/entrada')) ?>">+ Entrada</a>

            <div class="notif-wrap" id="js-notif-wrap" style="position:relative">
              <button type="button" class="notif-bell" id="js-notif-bell" aria-haspopup="dialog" aria-expanded="false" aria-controls="js-notif-panel" title="Notificaciones">
                <span class="notif-bell-icon" aria-hidden="true">🔔</span>
                <span class="notif-bell-badge" id="js-notif-badge" style="display:none" aria-hidden="true">0</span>
                <span class="sr-only" id="js-notif-sr">Notificaciones</span>
              </button>
              <div class="notif-panel" id="js-notif-panel" role="dialog" aria-label="Notificaciones de stock bajo" style="display:none">
                <div class="notif-panel-head">
                  <div style="font-weight:800;color:var(--brand)">Stock bajo</div>
                  <button type="button" class="icon-btn" id="js-notif-close" aria-label="Cerrar" style="width:34px;height:34px;border-radius:12px">✕</button>
                </div>
                <div class="notif-panel-body">
                  <div class="notif-empty" id="js-notif-empty">No hay notificaciones.</div>
                  <div class="notif-list" id="js-notif-list"></div>
                </div>
              </div>
            </div>
          </nav>
        </div>
      </div>
    </header>

    <main>
      <div class="container">
        <?= $content ?? '' ?>
      </div>
    </main>

    <footer>
      <div class="container">
        <div class="row" style="justify-content:space-between">
          <div>Pagina local</div>
          <div class="muted">Para uso interno de Equimac</div>
        </div>
      </div>
    </footer>
  </div>

  <script>
    window.__EQUIMAC_BASE__ = <?= json_encode(base_url(), JSON_UNESCAPED_SLASHES) ?>;
  </script>
  <script id="js-low-stock-data" type="application/json"><?= json_encode($__notif, JSON_UNESCAPED_SLASHES) ?></script>
  <script src="<?= e(url('assets/js/app.js?v=' . time())) ?>"></script>
</body>
</html>

