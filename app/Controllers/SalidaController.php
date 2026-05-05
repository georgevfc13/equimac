<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Inventario;
use App\Models\Salidas;
use App\Support\Database;
use App\Support\Response;

final class SalidaController
{
    private Inventario $inv;

    public function __construct()
    {
        $this->inv = new Inventario();
    }

    public function form(): Response
    {
        return Response::html(view('salida/form', [
            'title' => 'Salida de material',
            'active' => 'salida',
            'errors' => [],
            'old' => [],
        ]));
    }

    public function store(): void
    {
        $old = [
            'codigo' => trim((string)($_POST['codigo'] ?? '')),
            'quien_recibio' => trim((string)($_POST['quien_recibio'] ?? '')),
            'quien_entrego' => trim((string)($_POST['quien_entrego'] ?? '')),
            'cantidad_usada' => (string)($_POST['cantidad_usada'] ?? ''),
        ];

        $errors = $this->validate($old);
        if ($errors) {
            Response::html(view('salida/form', [
                'title' => 'Salida de material',
                'active' => 'salida',
                'errors' => $errors,
                'old' => $old,
            ]), 422)->send();
        }

        $codigo = $old['codigo'];
        $producto = $this->inv->findByCodigo($codigo);
        if (!$producto) {
            $errors['codigo'] = 'No existe un producto con ese código.';
            Response::html(view('salida/form', [
                'title' => 'Salida de material',
                'active' => 'salida',
                'errors' => $errors,
                'old' => $old,
            ]), 422)->send();
        }

        $cantidad = (int)$old['cantidad_usada'];
        if ($cantidad > (int)$producto['cantidad']) {
            $errors['cantidad_usada'] = 'Stock insuficiente. Disponible: ' . (int)$producto['cantidad'] . ' ' . $producto['unidad'] . '.';
            Response::html(view('salida/form', [
                'title' => 'Salida de material',
                'active' => 'salida',
                'errors' => $errors,
                'old' => $old,
            ]), 422)->send();
        }

        $pdo = Database::pdo();
        $pdo->beginTransaction();
        try {
            if (!$this->inv->decrementCantidad((int)$producto['id'], $cantidad)) {
                $pdo->rollBack();
                $errors['cantidad_usada'] = 'No se pudo descontar el inventario (stock insuficiente).';
                Response::html(view('salida/form', [
                    'title' => 'Salida de material',
                    'active' => 'salida',
                    'errors' => $errors,
                    'old' => $old,
                ]), 422)->send();
            }

            (new Salidas())->create(
                (int)$producto['id'],
                $codigo,
                $old['quien_recibio'],
                $old['quien_entrego'],
                $cantidad
            );
            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }

        redirect('/inventario/' . (int)$producto['id']);
    }

    private function validate(array $old): array
    {
        $errors = [];
        if ($old['codigo'] === '') {
            $errors['codigo'] = 'Indica el código del producto.';
        }
        if ($old['quien_recibio'] === '') {
            $errors['quien_recibio'] = 'Indica quién recibió el material.';
        }
        if ($old['quien_entrego'] === '') {
            $errors['quien_entrego'] = 'Indica quién entregó el material.';
        }
        $n = (int)$old['cantidad_usada'];
        if ($old['cantidad_usada'] === '' || !is_numeric($old['cantidad_usada'])) {
            $errors['cantidad_usada'] = 'Indica la cantidad usada (número entero).';
        } elseif ($n <= 0) {
            $errors['cantidad_usada'] = 'La cantidad debe ser mayor que cero.';
        }
        return $errors;
    }
}
