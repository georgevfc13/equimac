<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Estantes;
use App\Models\Inventario;
use App\Models\Entradas;
use App\Models\OrdenesEntrada;
use App\Models\Salidas;
use App\Support\Database;
use App\Support\ExcelExport;
use App\Support\OrdenHelper;
use App\Support\Response;

final class InventarioController
{
    private Inventario $inv;
    private Estantes $estantes;
    private Entradas $entradas;
    private Salidas $salidas;

    public function __construct()
    {
        $this->inv = new Inventario();
        $this->estantes = new Estantes();
        $this->entradas = new Entradas();
        $this->salidas = new Salidas();
    }

    public function index(): Response
    {
        $q = trim((string)($_GET['q'] ?? ''));
        $items = $this->inv->all($q);
        $stats = $this->inv->stats();
        $lowStockItems = $this->inv->lowStockItems();
        $outOfStockIds = array_map(fn($item) => $item['id'], $this->inv->outOfStockItems());

        return Response::html(view('inventario/index', [
            'title' => 'Inventario',
            'active' => 'inventario',
            'items' => $items,
            'stats' => $stats,
            'q' => $q,
            'lowStockItems' => $lowStockItems,
            'outOfStockIds' => $outOfStockIds,
        ]));
    }

    /** Exporta inventario a Excel con formato (encabezados azules, cuerpo gris). */
    public function exportExcel(): void
    {
        $items = $this->inv->all('');
        ExcelExport::inventario($items);
    }

    public function show(string $id): Response
    {
        $pid = (int)$id;
        $item = $this->inv->find($pid);
        if (!$item) {
            return Response::html(view('error/404', ['title' => 'Producto no encontrado']), 404);
        }

        $entradas = $this->entradas->byInventarioId($pid);
        $salidas = $this->salidas->byInventarioId($pid);

        return Response::html(view('inventario/show', [
            'title' => 'Detalle · ' . $item['codigo'],
            'active' => 'inventario',
            'item' => $item,
            'entradas' => $entradas,
            'salidas' => $salidas,
        ]));
    }

    public function create(): Response
    {
        return Response::html(view('inventario/form', [
            'title' => 'Nuevo producto',
            'active' => 'inventario',
            'mode' => 'create',
            'item' => null,
            'estantes' => $this->estantes->all(),
            'errors' => [],
        ]));
    }

    public function tipoEntrada(): Response
    {
        return Response::html(view('inventario/entrada-tipo', [
            'title' => 'Registrar Entrada',
            'active' => 'inventario',
        ]));
    }

    public function edit(string $id): Response
    {
        $pid = (int)$id;
        $item = $this->inv->find($pid);
        if (!$item) {
            return Response::html(view('error/404', ['title' => 'Producto no encontrado']), 404);
        }

        return Response::html(view('inventario/form', [
            'title' => 'Editar · ' . $item['codigo'],
            'active' => 'inventario',
            'mode' => 'edit',
            'item' => $item,
            'estantes' => $this->estantes->all(),
            'errors' => [],
        ]));
    }

    public function storeOrUpdate(): void
    {
        $id = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : null;

        $data = [
            'codigo' => trim((string)($_POST['codigo'] ?? '')),
            'nombre' => trim((string)($_POST['nombre'] ?? '')),
            'descripcion' => trim((string)($_POST['descripcion'] ?? '')),
            'unidad' => trim((string)($_POST['unidad'] ?? '')),
            'cantidad' => (int)($_POST['cantidad'] ?? 0),
            'stock_minimo' => (int)($_POST['stock_minimo'] ?? 5),
            'marca' => trim((string)($_POST['marca'] ?? '')),
            'equipo' => trim((string)($_POST['equipo'] ?? '')),
            'aplicacion' => trim((string)($_POST['aplicacion'] ?? '')),
            'estante' => (int)($_POST['estante'] ?? 0),
            'entrepaño' => (int)($_POST['entrepaño'] ?? 0),
            'posicion' => (int)($_POST['posicion'] ?? 0),
            'estado' => trim((string)($_POST['estado'] ?? '')),
            'tipo_maquinaria' => trim((string)($_POST['tipo_maquinaria'] ?? '')),
            'de_quien_llego' => trim((string)($_POST['de_quien_llego'] ?? '')),
            'precio_pagado' => $_POST['precio_pagado'] ?? null,
            'quien_recibio' => trim((string)($_POST['quien_recibio'] ?? '')),
        ];

        $errors = $this->validate($data, $id);
        if ($errors) {
            $view = view('inventario/form', [
                'title' => $id ? 'Editar' : 'Nuevo producto',
                'active' => 'inventario',
                'mode' => $id ? 'edit' : 'create',
                'item' => array_merge($data, ['id' => $id]),
                'estantes' => $this->estantes->all(),
                'errors' => $errors,
            ]);
            Response::html($view, 422)->send();
        }

        if ($id) {
            // Codigo immutable in edit (UI en readonly), pero lo respetamos.
            $this->inv->update($id, $data);
            redirect('/inventario/' . $id);
        } else {
            $newId = $this->inv->create($data);

            if ($data['cantidad'] > 0) {
                $ordenEntradaId = $this->createOrdenEntrada([
                    'quien_entrego' => $data['de_quien_llego'],
                    'quien_recibio' => $data['quien_recibio'],
                    'observaciones' => trim((string)($_POST['observaciones_entrada'] ?? '')) ?: 'Entrada inicial al crear producto',
                    'fecha_entrada' => trim((string)($_POST['fecha_entrada'] ?? '')) ?: null,
                    'hora_entrada' => trim((string)($_POST['hora_entrada'] ?? '')) ?: null,
                ]);
                $this->entradas->create([
                    'inventario_id' => $newId,
                    'codigo' => $data['codigo'],
                    'cantidad' => $data['cantidad'],
                    'quien_entrego' => $data['de_quien_llego'],
                    'quien_recibio' => $data['quien_recibio'],
                    'observaciones' => trim((string)($_POST['observaciones_entrada'] ?? '')) ?: 'Entrada inicial al crear producto',
                    'fecha_entrada' => trim((string)($_POST['fecha_entrada'] ?? '')) ?: null,
                    'hora_entrada' => trim((string)($_POST['hora_entrada'] ?? '')) ?: null,
                    'orden_entrada_id' => $ordenEntradaId,
                ]);
            }

            redirect('/inventario/' . $newId);
        }
    }

    public function destroy(string $id): void
    {
        $pid = (int)$id;
        $item = $this->inv->find($pid);
        if ($item) {
            $this->inv->delete($pid);
        }
        redirect('/inventario');
    }

    public function reabastecer(): Response
    {
        $selectedId = (int)($_GET['id'] ?? $_GET['producto'] ?? 0);
        return Response::html(view('inventario/reabastecer', [
            'title' => 'Reabastecer Producto',
            'active' => 'inventario',
            'items' => $this->inv->allForSelect(),
            'errors' => [],
            'selectedProductId' => $selectedId > 0 ? $selectedId : null,
            'old' => [],
        ]));
    }

    public function storeReabastecimiento(): void
    {
        $inventarioId = (int)($_POST['inventario_id'] ?? 0);
        $cantidad = (int)($_POST['cantidad'] ?? 0);
        $deQuienLlego = trim((string)($_POST['de_quien_llego'] ?? ''));
        $quienRecibio = trim((string)($_POST['quien_recibio'] ?? ''));
        $observaciones = trim((string)($_POST['observaciones'] ?? ''));
        $fechaEntrada = trim((string)($_POST['fecha_entrada'] ?? ''));
        $horaEntrada = trim((string)($_POST['hora_entrada'] ?? ''));

        $old = [
            'inventario_id' => $inventarioId,
            'cantidad' => $cantidad,
            'de_quien_llego' => $deQuienLlego,
            'quien_recibio' => $quienRecibio,
            'observaciones' => $observaciones,
            'fecha_entrada' => $fechaEntrada,
            'hora_entrada' => $horaEntrada,
        ];

        $errors = [];
        if ($inventarioId <= 0) {
            $errors['inventario_id'] = 'Selecciona un producto del inventario.';
        }
        if ($cantidad <= 0) {
            $errors['cantidad'] = 'La cantidad debe ser mayor a cero.';
        }
        if ($deQuienLlego === '') {
            $errors['de_quien_llego'] = 'Indica de quién llegó el producto.';
        }
        if ($quienRecibio === '') {
            $errors['quien_recibio'] = 'Indica quién recibió el producto.';
        }

        $renderForm = function (array $errors, ?int $selectedId) use ($old): void {
            Response::html(view('inventario/reabastecer', [
                'title' => 'Reabastecer Producto',
                'active' => 'inventario',
                'items' => $this->inv->allForSelect(),
                'errors' => $errors,
                'selectedProductId' => $selectedId,
                'old' => $old,
            ]), 422)->send();
        };

        if ($errors) {
            $renderForm($errors, $inventarioId > 0 ? $inventarioId : null);
            return;
        }

        $producto = $this->inv->find($inventarioId);
        if (!$producto) {
            $errors['inventario_id'] = 'Producto no encontrado.';
            $renderForm($errors, null);
            return;
        }

        if (!$this->inv->incrementCantidad($inventarioId, $cantidad)) {
            $errors['cantidad'] = 'No se pudo actualizar el stock.';
            $renderForm($errors, $inventarioId);
            return;
        }

        $ordenEntradaId = $this->createOrdenEntrada([
            'quien_entrego' => $deQuienLlego,
            'quien_recibio' => $quienRecibio,
            'observaciones' => $observaciones !== '' ? $observaciones : 'Reabastecimiento',
            'fecha_entrada' => $fechaEntrada ?: null,
            'hora_entrada' => $horaEntrada ?: null,
        ]);

        $this->entradas->create([
            'inventario_id' => $inventarioId,
            'codigo' => $producto['codigo'],
            'cantidad' => $cantidad,
            'quien_entrego' => $deQuienLlego,
            'quien_recibio' => $quienRecibio,
            'observaciones' => $observaciones !== '' ? $observaciones : 'Reabastecimiento',
            'fecha_entrada' => $fechaEntrada ?: null,
            'hora_entrada' => $horaEntrada ?: null,
            'orden_entrada_id' => $ordenEntradaId,
        ]);

        redirect('/inventario/' . $inventarioId . '?reabastecido=1');
    }

    public function entradaLote(): Response
    {
        return Response::html(view('inventario/entrada-lote', [
            'title' => 'Entrada múltiple',
            'active' => 'inventario',
            'items' => $this->inv->allForSelect(),
            'errors' => [],
            'old' => $this->defaultEntradaLoteOld(),
        ]));
    }

    public function storeEntradaLote(): void
    {
        $old = [
            'de_quien_llego' => trim((string)($_POST['de_quien_llego'] ?? '')),
            'quien_recibio' => trim((string)($_POST['quien_recibio'] ?? '')),
            'observaciones' => trim((string)($_POST['observaciones'] ?? '')),
            'fecha_entrada' => trim((string)($_POST['fecha_entrada'] ?? '')),
            'hora_entrada' => trim((string)($_POST['hora_entrada'] ?? '')),
        ];
        $lines = $this->parseEntradaLinesFromPost();
        $old['lines'] = $lines;

        $errors = [];
        if ($old['de_quien_llego'] === '') {
            $errors['de_quien_llego'] = 'Indica de quién llegó el material.';
        }
        if ($old['quien_recibio'] === '') {
            $errors['quien_recibio'] = 'Indica quién recibió el material.';
        }
        if (!$lines) {
            $errors['lines'] = 'Agrega al menos un producto.';
        }

        foreach ($lines as $i => $line) {
            $producto = $this->inv->find($line['inventario_id']);
            if (!$producto) {
                $errors["line_{$i}"] = 'Producto no válido en la línea ' . ($i + 1) . '.';
            } elseif ($line['cantidad'] <= 0) {
                $errors["line_{$i}"] = 'Cantidad inválida en la línea ' . ($i + 1) . '.';
            }
        }

        if ($errors) {
            Response::html(view('inventario/entrada-lote', [
                'title' => 'Entrada múltiple',
                'active' => 'inventario',
                'items' => $this->inv->allForSelect(),
                'errors' => $errors,
                'old' => $old,
            ]), 422)->send();
        }

        $pdo = Database::pdo();
        $pdo->beginTransaction();
        $ordenEntradaId = null;
        try {
            $ordenEntradaId = $this->createOrdenEntrada([
                'quien_entrego' => $old['de_quien_llego'],
                'quien_recibio' => $old['quien_recibio'],
                'observaciones' => $old['observaciones'] ?: 'Entrada múltiple',
                'fecha_entrada' => $old['fecha_entrada'] ?: null,
                'hora_entrada' => $old['hora_entrada'] ?: null,
            ]);

            foreach ($lines as $line) {
                $producto = $this->inv->find($line['inventario_id']);
                if (!$producto || !$this->inv->incrementCantidad($line['inventario_id'], $line['cantidad'])) {
                    throw new \RuntimeException('No se pudo actualizar stock.');
                }
                $this->entradas->create([
                    'inventario_id' => $line['inventario_id'],
                    'codigo' => $producto['codigo'],
                    'cantidad' => $line['cantidad'],
                    'quien_entrego' => $old['de_quien_llego'],
                    'quien_recibio' => $old['quien_recibio'],
                    'observaciones' => $old['observaciones'] ?: 'Entrada múltiple',
                    'fecha_entrada' => $old['fecha_entrada'] ?: null,
                    'hora_entrada' => $old['hora_entrada'] ?: null,
                    'orden_entrada_id' => $ordenEntradaId,
                ]);
            }
            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            $errors['general'] = $e->getMessage();
            Response::html(view('inventario/entrada-lote', [
                'title' => 'Entrada múltiple',
                'active' => 'inventario',
                'items' => $this->inv->allForSelect(),
                'errors' => $errors,
                'old' => $old,
            ]), 422)->send();
        }

        $orden = null;
        if ($ordenEntradaId && OrdenHelper::schemaReady()) {
            $row = (new OrdenesEntrada())->find($ordenEntradaId);
            if ($row) {
                $orden = OrdenHelper::labelEntrada((int)$row['numero']);
            }
        }
        $params = ['entrada_ok' => '1'];
        if ($orden) {
            $params['orden'] = $orden;
        }
        redirect('/inventario/entrada-lote?' . http_build_query($params));
    }

    /** @return array<string, mixed> */
    private function defaultEntradaLoteOld(): array
    {
        return [
            'de_quien_llego' => '',
            'quien_recibio' => '',
            'observaciones' => '',
            'fecha_entrada' => date('Y-m-d'),
            'hora_entrada' => date('H:i'),
            'lines' => [['inventario_id' => '', 'cantidad' => '1']],
        ];
    }

    /** @return list<array{inventario_id:int,cantidad:int}> */
    private function parseEntradaLinesFromPost(): array
    {
        $ids = $_POST['line_inventario_id'] ?? [];
        $cantidades = $_POST['line_cantidad'] ?? [];
        if (!is_array($ids)) {
            return [];
        }
        $lines = [];
        foreach ($ids as $i => $id) {
            $id = (int)$id;
            if ($id <= 0) {
                continue;
            }
            $lines[] = [
                'inventario_id' => $id,
                'cantidad' => (int)($cantidades[$i] ?? 0),
            ];
        }
        return $lines;
    }

    /** @param array{quien_entrego:string,quien_recibio:string,observaciones?:string,fecha_entrada?:?string,hora_entrada?:?string} $data */
    private function createOrdenEntrada(array $data): ?int
    {
        if (!OrdenHelper::schemaReady()) {
            return null;
        }
        $orden = (new OrdenesEntrada())->create($data);
        return $orden['id'];
    }

    private function validate(array $data, ?int $id): array
    {
        $errors = [];
        if ($data['codigo'] === '') $errors['codigo'] = 'El código es requerido.';
        if ($data['nombre'] === '') $errors['nombre'] = 'El nombre es requerido.';
        if ($data['descripcion'] === '') $errors['descripcion'] = 'La descripción es requerida.';
        if ($data['unidad'] === '') $errors['unidad'] = 'La unidad es requerida.';
        if ((int)$data['cantidad'] < 0) $errors['cantidad'] = 'La cantidad no puede ser negativa.';
        if ((int)$data['estante'] <= 0) $errors['estante'] = 'Selecciona un estante.';
        if ((int)$data['entrepaño'] <= 0) $errors['entrepaño'] = 'Selecciona una fila.';
        if ((int)$data['posicion'] <= 0) $errors['posicion'] = 'Selecciona una posición.';

        // Requerir información de entrada para nuevos productos
        if (!$id) {
            if ($data['de_quien_llego'] === '') $errors['de_quien_llego'] = 'Debe indicarse de quién llegó el producto.';
            if ($data['quien_recibio'] === '') $errors['quien_recibio'] = 'Debe indicarse quién recibió el producto.';
        }

        if ($data['codigo'] !== '' && $this->inv->codigoExists($data['codigo'], $id)) {
            $errors['codigo'] = 'Ese código ya existe.';
        }

        return $errors;
    }
}

