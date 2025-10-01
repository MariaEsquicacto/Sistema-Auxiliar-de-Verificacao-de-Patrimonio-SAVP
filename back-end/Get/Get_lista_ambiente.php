<?php
require_once "../config.php";
header("Content-Type: application/json");

try {
    $pdo = conn();

    // Pega o id do ambiente da URL
    $id = $_GET['id'] ?? null;
    if (!$id || !is_numeric($id)) {
        throw new Exception("ID do ambiente inválido");
    }

    // Consulta itens relacionados a esse ambiente
    $stmt = $pdo->prepare("SELECT * FROM itens_ambiente WHERE id_ambiente = ?");
    $stmt->execute([$id]);
    $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'data' => $itens
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
