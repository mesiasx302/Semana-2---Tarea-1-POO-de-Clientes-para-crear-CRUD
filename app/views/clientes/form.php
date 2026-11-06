/*<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">

    <h1 class="h3 mb-3"><?= e($titulo) ?></h1>

    <?php if (!empty($errores)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errores as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="index.php?accion=<?= e($accion) ?>" method="post" enctype="multipart/form-data"
          class="card card-body">

        <?= csrf_campo() ?>

        <?php if ($cliente->getId() !== null): ?>
            <input type="hidden" name="id" value="<?= $cliente->getId() ?>">
        <?php endif; ?>

        <div class="mb-3">
            <label class="form-label" for="nombre">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" maxlength="100"
                   value="<?= e($cliente->getNombre()) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label" for="apellido">Apellido</label>
            <input type="text" class="form-control" id="apellido" name="apellido" maxlength="100"
                   value="<?= e($cliente->getApellido()) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label" for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" maxlength="150"
                   value="<?= e($cliente->getEmail()) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label" for="telefono">Teléfono (opcional)</label>
            <input type="text" class="form-control" id="telefono" name="telefono" maxlength="20"
                   value="<?= e($cliente->getTelefono()) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label" for="direccion">Dirección (opcional)</label>
            <input type="text" class="form-control" id="direccion" name="direccion" maxlength="255"
                   value="<?= e($cliente->getDireccion()) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label" for="foto">Foto (opcional, JPG, PNG o WEBP, máx. 2 MB)</label>

            <?php if ($cliente->getFoto()): ?>
                <div class="mb-2">
                    <img src="uploads/<?= e($cliente->getFoto()) ?>" alt="Foto actual"
                         width="96" height="96" class="rounded" style="object-fit: cover;">
                    <div class="form-text">Foto actual. Si eliges otra, la reemplazará.</div>
                </div>
            <?php endif; ?>

            <input type="file" class="form-control" id="foto" name="foto"
                   accept="image/jpeg,image/png,image/webp">
        </div>

        <div>
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>

</body>
</html>