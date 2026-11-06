<?php

class ClienteController
{
    public function index(): void
    {
        $clientes = Cliente::todos();
        $mensaje = sacar_mensaje();
        require __DIR__ . '/../views/clientes/index.php';
    }

    public function crear(): void
    {
        $cliente = new Cliente();
        $errores = [];
        $titulo = 'Nuevo cliente';
        $accion = 'guardar';
        require __DIR__ . '/../views/clientes/form.php';
    }

    public function guardar(): void
    {
        $this->soloPost();
        verificar_csrf();

        $cliente = $this->clienteDesdeFormulario();
        $errores = $this->validar($cliente);
        $fotoNueva = null;

        if (empty($errores)) {
            $fotoNueva = $this->subirFoto($errores);
        }

        if (empty($errores)) {
            $cliente->setFoto($fotoNueva);

            try {
                $cliente->crear();
                poner_mensaje('Cliente creado correctamente.');
                $this->redirigir();
            } catch (PDOException $e) {
                $this->borrarFoto($fotoNueva);
                $cliente->setFoto(null);

                if ($e->getCode() === '23000') {
                    $errores[] = 'Ya existe un cliente con ese email.';
                } else {
                    throw $e;
                }
            }
        }

        $titulo = 'Nuevo cliente';
        $accion = 'guardar';
        require __DIR__ . '/../views/clientes/form.php';
    }

    public function editar(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $cliente = Cliente::buscarPorId($id);

        if ($cliente === null) {
            $this->noEncontrado();
        }

        $errores = [];
        $titulo = 'Editar cliente';
        $accion = 'actualizar';
        require __DIR__ . '/../views/clientes/form.php';
    }

    public function actualizar(): void
    {
        $this->soloPost();
        verificar_csrf();

        $id = (int) ($_POST['id'] ?? 0);
        $existente = Cliente::buscarPorId($id);

        if ($existente === null) {
            $this->noEncontrado();
        }

        $cliente = $this->clienteDesdeFormulario($id, $existente->getFoto());
        $errores = $this->validar($cliente);
        $fotoNueva = null;

        if (empty($errores)) {
            $fotoNueva = $this->subirFoto($errores);
        }

        if (empty($errores)) {
            if ($fotoNueva !== null) {
                $cliente->setFoto($fotoNueva);
            }

            try {
                $cliente->actualizar();

                if ($fotoNueva !== null) {
                    $this->borrarFoto($existente->getFoto());
                }

                poner_mensaje('Cliente actualizado correctamente.');
                $this->redirigir();
            } catch (PDOException $e) {
                $this->borrarFoto($fotoNueva);
                $cliente->setFoto($existente->getFoto());

                if ($e->getCode() === '23000') {
                    $errores[] = 'Ya existe otro cliente con ese email.';
                } else {
                    throw $e;
                }
            }
        }

        $titulo = 'Editar cliente';
        $accion = 'actualizar';
        require __DIR__ . '/../views/clientes/form.php';
    }

    public function eliminar(): void
    {
        $this->soloPost();
        verificar_csrf();

        $id = (int) ($_POST['id'] ?? 0);
        $cliente = Cliente::buscarPorId($id);

        if ($cliente !== null) {
            Cliente::eliminar($id);
            $this->borrarFoto($cliente->getFoto());
            poner_mensaje('Cliente eliminado correctamente.');
        }

        $this->redirigir();
    }

    // ---------- AUXILIARES ----------

    private function clienteDesdeFormulario(?int $id = null, ?string $foto = null): Cliente
    {
        $telefono = trim((string) ($_POST['telefono'] ?? ''));

        return new Cliente(
            $id,
            trim((string) ($_POST['nombre'] ?? '')),
            trim((string) ($_POST['apellido'] ?? '')),
            trim((string) ($_POST['email'] ?? '')),
            $telefono === '' ? null : $telefono,
            trim((string) ($_POST['direccion'] ?? '')),
            $foto
        );
    }

    private function validar(Cliente $cliente): array
    {
        $errores = [];

        if ($cliente->getNombre() === '') {
            $errores[] = 'El nombre es obligatorio.';
        } elseif (mb_strlen($cliente->getNombre()) > 100) {
            $errores[] = 'El nombre no puede pasar de 100 caracteres.';
        }

        if ($cliente->getApellido() === '') {
            $errores[] = 'El apellido es obligatorio.';
        } elseif (mb_strlen($cliente->getApellido()) > 100) {
            $errores[] = 'El apellido no puede pasar de 100 caracteres.';
        }

        if (!filter_var($cliente->getEmail(), FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El email no es válido.';
        } elseif (mb_strlen($cliente->getEmail()) > 150) {
            $errores[] = 'El email no puede pasar de 150 caracteres.';
        }

        $telefono = $cliente->getTelefono();
        if ($telefono !== null && !preg_match('/^[0-9+ ]{7,20}$/', $telefono)) {
            $errores[] = 'El teléfono solo puede tener números, espacios o +, entre 7 y 20 caracteres.';
        }

        if (mb_strlen($cliente->getDireccion()) > 255) {
            $errores[] = 'La dirección no puede pasar de 255 caracteres.';
        }

        return $errores;
    }

    private function subirFoto(array &$errores): ?string
    {
        if (!isset($_FILES['foto']) || $_FILES['foto']['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $archivo = $_FILES['foto'];

        if ($archivo['error'] === UPLOAD_ERR_INI_SIZE || $archivo['error'] === UPLOAD_ERR_FORM_SIZE) {
            $errores[] = 'La foto es demasiado pesada (máximo 2 MB).';
            return null;
        }

        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            $errores[] = 'No se pudo subir la foto. Intenta con otra imagen.';
            return null;
        }

        if ($archivo['size'] > 2 * 1024 * 1024) {
            $errores[] = 'La foto no puede pesar más de 2 MB.';
            return null;
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($archivo['tmp_name']);

        $permitidos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!isset($permitidos[$mime])) {
            $errores[] = 'La foto debe ser JPG, PNG o WEBP.';
            return null;
        }

        $carpeta = __DIR__ . '/../../public/uploads';
        if (!is_dir($carpeta)) {
            mkdir($carpeta, 0755, true);
        }

        $nombre = bin2hex(random_bytes(8)) . '.' . $permitidos[$mime];

        if (!move_uploaded_file($archivo['tmp_name'], $carpeta . '/' . $nombre)) {
            $errores[] = 'No se pudo guardar la foto en el servidor.';
            return null;
        }

        return $nombre;
    }

    private function borrarFoto(?string $foto): void
    {
        if ($foto === null || $foto === '') {
            return;
        }

        $ruta = __DIR__ . '/../../public/uploads/' . basename($foto);
        if (is_file($ruta)) {
            unlink($ruta);
        }
    }

    private function soloPost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Método no permitido.';
            exit;
        }
    }

    private function noEncontrado(): void
    {
        http_response_code(404);
        echo 'Cliente no encontrado.';
        exit;
    }

    private function redirigir(): void
    {
        header('Location: index.php');
        exit;
    }
}