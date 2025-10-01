<?php
// 1. INICIA A SESSÃO para acessar o nível de usuário
session_start();

// Define os níveis de usuário permitidos para esta ação (Ajuste conforme a sua regra de negócio)
// Aqui, apenas 'gestor' e 'administrador' podem registrar movimentação.
$niveis_permitidos = ['gestor', 'administrador'];

// 2. VERIFICAÇÃO DE PERMISSÃO: Bloqueia se o usuário não tiver o nível adequado
if (!isset($_SESSION['user_nivel']) || !in_array($_SESSION['user_nivel'], $niveis_permitidos)) {
    http_response_code(403); // Forbidden
    echo json_encode([
        'status' => 'error', 
        'message' => 'Acesso Negado. Seu nível de usuário não tem permissão para registrar movimentação.'
    ]);
    exit();
}

require_once "../config.php";
header("Content-Type: application/json");
date_default_timezone_set('America/Sao_Paulo'); // Define fuso horário de SP/Brasília

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = conn();

        // Recebe JSON enviado no body
        $data = json_decode(file_get_contents("php://input"), true);

        // O ID do usuário deve ser pego da SESSÃO por segurança!
        $usuario_id     = $_SESSION["user_id"] ?? null; 
        
        $patrimonio_id  = $data["patrimonios_num_patrimonio"] ?? null;
        $origem         = $data["origem"] ?? null;
        $destino        = $data["destino"] ?? null;

        // Validação
        if (!$usuario_id || !$patrimonio_id || !$origem || !$destino) {
            http_response_code(400); // Bad Request
            echo json_encode([
                'status' => 'error',
                'message' => 'Campos obrigatórios faltando (patrimonio, origem, destino) ou sessão de usuário inválida.'
            ]);
            exit();
        }

        // Insert (movimentacao_del sempre 'ativo')
        // OBSERVAÇÃO: A coluna "patrimonios_num_patrimonio" na movimentação é o NUM_PATRIMONIO do item.
        $stmt = $pdo->prepare("
            INSERT INTO movimentacao_item (
                data_hora, movimentacao_del, patrimonios_num_patrimonio, origem, destino, usuarios_id_usuario
            ) VALUES (NOW(), 'ativo', ?, ?, ?, ?)
        ");

        $stmt->execute([
            $patrimonio_id,
            $origem,
            $destino,
            $usuario_id // ID do usuário da SESSÃO
        ]);

        echo json_encode([
            'status' => 'success',
            'message' => 'Movimentação registrada com sucesso!',
            'id_inserido' => $pdo->lastInsertId()
        ]);
        exit();
    } catch (Exception $e) {
        http_response_code(500); // Internal Server Error
        echo json_encode([
            'status' => 'error',
            'message' => 'Erro ao inserir: ' . $e->getMessage()
        ]);
        exit();
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        'status' => 'error',
        'message' => 'Método inválido. Use POST.'
    ]);
    exit();
}