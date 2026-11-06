<?php

class Cliente
{
    private ?int $id;
    private string $nombre;
    private string $apellido;
    private string $email;
    private ?string $telefono;
    private string $direccion;
    private ?string $foto;

    public function __construct(
        ?int $id = null,
        string $nombre = '',
        string $apellido = '',
        string $email = '',
        ?string $telefono = null,
        string $direccion = '',
        ?string $foto = null
    ) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->email = $email;
        $this->telefono = $telefono;
        $this->direccion = $direccion;
        $this->foto = $foto;
    }

    public function getId(): ?int { return $this->id; }
    public function getNombre(): string { return $this->nombre; }
    public function getApellido(): string { return $this->apellido; }
    public function getEmail(): string { return $this->email; }
    public function getTelefono(): ?string { return $this->telefono; }
    public function getDireccion(): string { return $this->direccion; }
    public function getFoto(): ?string { return $this->foto; }

    public function setFoto(?string $foto): void
    {
        $this->foto = $foto;
    }

    private static function conexion(): PDO
    {
        $baseDatos = new Database();
        return $baseDatos->conectar();
    }

    private static function desdeFila(array $fila): Cliente
    {
        return new Cliente(
            (int) $fila['id'],
            $fila['nombre'],
            $fila['apellido'] ?? '',
            $fila['email'],
            $fila['telefono'],
            $fila['direccion'] ?? '',
            $fila['foto']
        );
    }

    public static function todos(): array
    {
        $consulta = self::conexion()->query('SELECT * FROM clientes ORDER BY id ASC');

        $clientes = [];
        foreach ($consulta->fetchAll() as $fila) {
            $clientes[] = self::desdeFila($fila);
        }

        return $clientes;
    }

    public static function buscarPorId(int $id): ?Cliente
    {
        $consulta = self::conexion()->prepare('SELECT * FROM clientes WHERE id = :id');
        $consulta->execute([':id' => $id]);

        $fila = $consulta->fetch();

        return $fila ? self::desdeFila($fila) : null;
    }

    public function crear(): bool
    {
        $conexion = self::conexion();

        $sql = 'INSERT INTO clientes (nombre, apellido, email, telefono, direccion, foto)
                VALUES (:nombre, :apellido, :email, :telefono, :direccion, :foto)';

        $consulta = $conexion->prepare($sql);

        $resultado = $consulta->execute([
            ':nombre' => $this->nombre,
            ':apellido' => $this->apellido,
            ':email' => $this->email,
            ':telefono' => $this->telefono,
            ':direccion' => $this->direccion,
            ':foto' => $this->foto,
        ]);

        $this->id = (int) $conexion->lastInsertId();

        return $resultado;
    }

    public function actualizar(): bool
    {
        $sql = 'UPDATE clientes
                SET nombre = :nombre, apellido = :apellido, email = :email,
                    telefono = :telefono, direccion = :direccion, foto = :foto
                WHERE id = :id';

        $consulta = self::conexion()->prepare($sql);

        return $consulta->execute([
            ':nombre' => $this->nombre,
            ':apellido' => $this->apellido,
            ':email' => $this->email,
            ':telefono' => $this->telefono,
            ':direccion' => $this->direccion,
            ':foto' => $this->foto,
            ':id' => $this->id,
        ]);
    }

    public static function eliminar(int $id): bool
    {
        $consulta = self::conexion()->prepare('DELETE FROM clientes WHERE id = :id');

        return $consulta->execute([':id' => $id]);
    }
}