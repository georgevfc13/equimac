<?php
/** No variables needed */

$content = '
<div class="page-head">
  <div>
    <h2 class="page-title">Registrar Entrada</h2>
    <p class="page-sub">¿Qué deseas hacer?</p>
  </div>
  <div class="row">
    <a class="btn" href="'.e(url('inventario')).'">Volver</a>
  </div>
</div>

<div style="height:32px"></div>

<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:24px; max-width:900px">
  <!-- Opción 1: Nuevo Producto -->
  <div class="card pad entrada-option" style="cursor:pointer; transition:all 300ms ease; border:2px solid transparent" data-option="nuevo" onclick="document.querySelector(\'[data-option="nuevo"]\').parentElement.style.transform=\'scale(1.02)\'; setTimeout(() => window.location.href=\''.e(url('inventario/nuevo')).'\', 150)">
    <style>
      .entrada-option { 
        position: relative; 
        overflow: hidden;
      }
      .entrada-option::before {
        content: \'\';
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(59, 130, 246, 0) 100%);
        opacity: 0;
        transition: opacity 300ms ease;
        pointer-events: none;
      }
      .entrada-option:hover {
        border-color: rgba(59, 130, 246, 0.5) !important;
        box-shadow: 0 8px 24px rgba(59, 130, 246, 0.15);
        transform: translateY(-4px);
      }
      .entrada-option:hover::before {
        opacity: 1;
      }
      .entrada-option-icon {
        font-size: 48px;
        margin-bottom: 16px;
        display: block;
        animation: float 3s ease-in-out infinite;
      }
      @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-8px); }
      }
      .entrada-option-title {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 8px;
        color: #000;
      }
      .entrada-option-desc {
        font-size: 13px;
        color: var(--muted);
        line-height: 1.5;
        margin-bottom: 12px;
      }
      .entrada-option-badge {
        display: inline-block;
        background: rgba(59, 130, 246, 0.1);
        color: rgb(59, 130, 246);
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
      }
    </style>
    <span class="entrada-option-icon">📦</span>
    <div class="entrada-option-title">Crear Nuevo Producto</div>
    <div class="entrada-option-desc">Agrega un producto completamente nuevo al inventario con su información inicial</div>
    <span class="entrada-option-badge">Nuevo Ingreso</span>
  </div>

  <!-- Opción 2: Reabastecer -->
  <div class="card pad entrada-option" style="cursor:pointer; transition:all 300ms ease; border:2px solid transparent" data-option="reabastecer" onclick="document.querySelector(\'[data-option="reabastecer"]\').parentElement.style.transform=\'scale(1.02)\'; setTimeout(() => window.location.href=\''.e(url('inventario/reabastecer')).'\', 150)">
    <span class="entrada-option-icon">🔄</span>
    <div class="entrada-option-title">Reabastecer Existente</div>
    <div class="entrada-option-desc">Aumenta el stock de un producto que ya existe en tu inventario</div>
    <span class="entrada-option-badge">Incrementar Stock</span>
  </div>
</div>

<script>
document.querySelectorAll(\'.entrada-option\').forEach(option => {
  option.addEventListener(\'mouseenter\', function() {
    this.style.borderColor = \'rgba(59, 130, 246, 0.5)\';
    this.style.boxShadow = \'0 8px 24px rgba(59, 130, 246, 0.15)\';
    this.style.transform = \'translateY(-4px)\';
  });
  option.addEventListener(\'mouseleave\', function() {
    this.style.borderColor = \'transparent\';
    this.style.boxShadow = \'\';
    this.style.transform = \'translateY(0)\';
  });
});
</script>
';

echo view('layout', [
  'title' => $title ?? 'Registrar Entrada',
  'active' => $active ?? 'inventario',
  'content' => $content
]);
