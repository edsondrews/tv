<?php
// Caminho do arquivo de banco de dados SQLite
$dbPath = __DIR__ . '/../sistema.db';

try {
    // Conectar ao banco de dados SQLite
    $pdo = new PDO("sqlite:$dbPath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}
?>