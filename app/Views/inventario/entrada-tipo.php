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

<div style="display:flex; justify-content:center; align-items:center; min-height:400px; padding:40px 20px">
  <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:32px; max-width:960px; width:100%">
    
    <!-- Opción 1: Nuevo Producto -->
    <a class="entrada-card" href="'.e(url('inventario/nuevo')).'">
      <div class="entrada-card-inner">
        <span class="entrada-icon">📦</span>
        <h3 class="entrada-title">Crear Nuevo Producto</h3>
        <p class="entrada-desc">Agrega un producto completamente nuevo al inventario con su información inicial</p>
        <span class="entrada-badge">Nuevo Ingreso</span>
      </div>
    </a>

    <!-- Opción 2: Reabastecer -->
    <a class="entrada-card" href="'.e(url('inventario/reabastecer')).'">
      <div class="entrada-card-inner">
        <span class="entrada-icon">🔄</span>
        <h3 class="entrada-title">Reabastecer Existente</h3>
        <p class="entrada-desc">Aumenta el stock de un producto que ya existe en tu inventario</p>
        <span class="entrada-badge">Incrementar Stock</span>
      </div>
    </a>

    <!-- Opción 3: Entrada múltiple -->
    <a class="entrada-card" href="'.e(url('inventario/entrada-lote')).'">
      <div class="entrada-card-inner">
        <span class="entrada-icon">📋</span>
        <h3 class="entrada-title">Entrada Múltiple</h3>
        <p class="entrada-desc">Registra varios productos en una sola orden de entrada</p>
        <span class="entrada-badge">Orden de entrada</span>
      </div>
    </a>

  </div>
</div>

<style>
  .entrada-card {
    display: block;
    text-decoration: none;
    color: inherit;
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    background: white;
    border: 2px solid #e5e7eb;
    padding: 32px 24px;
    cursor: pointer;
    transition: all 300ms cubic-bezier(0.34, 1.56, 0.64, 1);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
  }

  .entrada-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.05) 0%, rgba(59, 130, 246, 0) 100%);
    opacity: 0;
    transition: opacity 300ms ease;
    pointer-events: none;
  }

  .entrada-card:hover {
    border-color: rgb(59, 130, 246);
    box-shadow: 0 16px 32px rgba(59, 130, 246, 0.2);
    transform: translateY(-8px) scale(1.02);
  }

  .entrada-card:hover::before {
    opacity: 1;
  }

  .entrada-card:active {
    transform: translateY(-8px) scale(0.98);
  }

  .entrada-card-inner {
    position: relative;
    z-index: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
  }

  .entrada-icon {
    font-size: 56px;
    display: block;
    margin-bottom: 16px;
    animation: float-entrada 3s ease-in-out infinite;
  }

  .entrada-card:hover .entrada-icon {
    animation: bounce-entrada 0.6s ease-in-out;
  }

  .entrada-title {
    font-size: 18px;
    font-weight: 700;
    margin: 0 0 12px 0;
    color: #1f2937;
  }

  .entrada-desc {
    font-size: 13px;
    color: #6b7280;
    line-height: 1.6;
    margin: 0 0 16px 0;
  }

  .entrada-badge {
    display: inline-block;
    background: rgba(59, 130, 246, 0.1);
    color: rgb(59, 130, 246);
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  @keyframes float-entrada {
    0%, 100% {
      transform: translateY(0px);
    }
    50% {
      transform: translateY(-10px);
    }
  }

  @keyframes bounce-entrada {
    0%, 100% {
      transform: scale(1);
    }
    50% {
      transform: scale(1.15);
    }
  }
</style>
';

echo view('layout', [
  'title' => $title ?? 'Registrar Entrada',
  'active' => $active ?? 'inventario',
  'content' => $content
]);
