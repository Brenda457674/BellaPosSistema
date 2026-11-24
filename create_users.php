<?php
$host = '127.0.0.1';
$db   = 'bd_tiendapos';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    // Hash de contraseña: 12345678
    $passwordHash = '$2y$12$6f.gK.ypbPnshLiV1lB40OsSjoLzhYirpXqGo3djhu94Idb6yOHbS';
    
    // Insertar usuario Vendedor
    $sqlVendedor = "INSERT INTO usuarios (rol_id, nombres, apellidos, email, email_verified_at, password, telefono, foto_perfil, estado, ultimo_acceso, remember_token, created_at, updated_at) 
        VALUES (2, 'Usuario', 'Vendedor', 'vendedor@bellaflor.test', NULL, :password, NULL, NULL, 'activo', NULL, NULL, NOW(), NOW())";
    $stmtVendedor = $pdo->prepare($sqlVendedor);
    $stmtVendedor->bindValue(':password', $passwordHash);
    $stmtVendedor->execute();
    echo "Vendedor creado OK. ID: " . $pdo->lastInsertId() . PHP_EOL;
    
    // Insertar usuario Almacenero
    $sqlAlmacenero = "INSERT INTO usuarios (rol_id, nombres, apellidos, email, email_verified_at, password, telefono, foto_perfil, estado, ultimo_acceso, remember_token, created_at, updated_at) 
        VALUES (3, 'Usuario', 'Almacenero', 'almacenero@bellaflor.test', NULL, :password, NULL, NULL, 'activo', NULL, NULL, NOW(), NOW())";
    $stmtAlmacenero = $pdo->prepare($sqlAlmacenero);
    $stmtAlmacenero->bindValue(':password', $passwordHash);
    $stmtAlmacenero->execute();
    echo "Almacenero creado OK. ID: " . $pdo->lastInsertId() . PHP_EOL;
    
    echo "Ambas cuentas creadas exitosamente.";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>