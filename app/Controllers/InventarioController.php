<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Estantes;
use App\Models\Inventario;
use App\Support\Response;

final class InventarioController
{
    private Inventario $inv;
    private Estantes $estantes;

    public function __construct()
    {
        $this->inv = new Inventario();
        $this->estantes = new Estantes();
    }

    public function index(): Response
    {
        $q = trim((string)($_GET['q'] ?? ''));
        $items = $this->inv->all($q);
        $stats = $this->inv->stats();

        return Response::html(view('inventario/index', [
            'title' => 'Inventario',
            'active' => 'inventario',
            'items' => $items,
            'stats' => $stats,
            'q' => $q,
        ]));
    }

    public function show(string $id): Response
    {
        $pid = (int)$id;
        $item = $this->inv->find($pid);
        if (!$item) {
            return Response::html(view('error/404', ['title' => 'Producto no encontrado']), 404);
        }

        return Response::html(view('inventario/show', [
            'title' => 'Detalle · ' . $item['codigo'],
            'active' => 'inventario',
            'item' => $item,
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
            'descripcion' => trim((string)($_POST['descripcion'] ?? '')),
            'unidad' => trim((string)($_POST['unidad'] ?? '')),
            'cantidad' => (int)($_POST['cantidad'] ?? 0),
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

    private function validate(array $data, ?int $id): array
    {
        $errors = [];
        if ($data['codigo'] === '') $errors['codigo'] = 'El código es requerido.';
        if ($data['descripcion'] === '') $errors['descripcion'] = 'La descripción es requerida.';
        if ($data['unidad'] === '') $errors['unidad'] = 'La unidad es requerida.';
        if ((int)$data['cantidad'] < 0) $errors['cantidad'] = 'La cantidad no puede ser negativa.';
        if ((int)$data['estante'] <= 0) $errors['estante'] = 'Selecciona un estante.';
        if ((int)$data['entrepaño'] <= 0) $errors['entrepaño'] = 'Selecciona una fila.';
        if ((int)$data['posicion'] <= 0) $errors['posicion'] = 'Selecciona una posición.';

        if ($data['codigo'] !== '' && $this->inv->codigoExists($data['codigo'], $id)) {
            $errors['codigo'] = 'Ese código ya existe.';
        }

        return $errors;
    }
}

