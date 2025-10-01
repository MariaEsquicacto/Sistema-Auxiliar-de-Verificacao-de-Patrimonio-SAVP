<?php
require_once "../config.php";
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $pdo = conn();
        
        // pega o id ou email pela query string
        $id = $_GET['id'] ?? null;
        $email = $_GET['email'] ?? null;

        if ($id) {
            $stmt = $pdo->prepare("SELECT usuario_nome, usuario_email, usuario_nivel FROM usuarios WHERE usuario_id = :id");
            $stmt->bindParam(':id', $id);
        } elseif ($email) {
            $stmt = $pdo->prepare("SELECT usuario_nome, usuario_email, usuario_nivel FROM usuarios WHERE usuario_email = :email");
            $stmt->bindParam(':email', $email);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ID ou Email não informado']);
            exit();
        }

        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            echo json_encode(['status' => 'success', 'data' => $usuario]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Usuário não encontrado']);
        }

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método inválido. Use GET.']);
}
?>
