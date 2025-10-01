<?php
require_once "../config.php"; // Ajuste se o caminho do config.php for diferente
header("Content-Type: application/json");
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

try {
    $pdo = conn();
    $stmt = $pdo->query("SELECT id_ambientes, ambiente_nome, categoria, localizacao FROM ambientes WHERE ambiente_del = 'ativo'");
    $ambientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "data" => $ambientes
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
