<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">

    <h1 class="h3 mb-3">Clientes</h1>

    <?php if ($mensaje): ?>
        <div class="alert alert-success"><?= e($mensaje) ?></div>
    <?php endif; ?>

    <a href="index.php?accion=crear" class="btn btn-primary mb-3">+ Nuevo cliente</a>

    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>ID</th>
                <th>Foto</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($clientes)): ?>
                <tr><td colspan="7" class="text-center">Aún no hay clientes registrados.</td></tr>
            <?php endif; ?>

            <?php foreach ($clientes as $cliente): ?>
                <tr>
                    <td><?= $cliente->getId() ?></td>
                    <td>
                        <?php if ($cliente->getFoto()): ?>
                            <img src="uploads/<?= e($cliente->getFoto()) ?>"
                                 alt="Foto de <?= e($cliente->getNombre()) ?>"
                                 width="48" height="48" class="rounded-circle" style="object-fit: cover;">
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td><?= e($cliente->getNombre() . ' ' . $cliente->getApellido()) ?></td>
                    <td><?= e($cliente->getEmail()) ?></td>
                    <td><?= e($cliente->getTelefono()) ?></td>
                    <td><?= e($cliente->getDireccion()) ?></td>
                    <td>
                        <a class="btn btn-sm btn-outline-secondary"
                           href="index.php?accion=editar&id=<?= $cliente->getId() ?>">Editar</a>

                        <form action="index.php?accion=eliminar" method="post" class="d-inline"
                              onsubmit="return confirm('¿Seguro que quieres eliminar este cliente?');">
                            <?= csrf_campo() ?>
                            <input type="hidden" name="id" value="<?= $cliente->getId() ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>