<?php
// 1. INICIA A SESSÃO para acessar o nível de usuário
session_start();

// Define os níveis de usuário permitidos para esta ação
$niveis_permitidos = ['gestor', 'administrador']; // Restringindo a colaboradores

// 2. VERIFICAÇÃO DE PERMISSÃO: Bloqueia se o usuário não tiver o nível adequado
if (!isset($_SESSION['user_nivel']) || !in_array($_SESSION['user_nivel'], $niveis_permitidos)) {
    http_response_code(403); // Forbidden
    echo json_encode([
        'status' => 'error', 
        'message' => 'Acesso Negado. Seu nível de usuário não tem permissão para registrar verificação de ambiente.'
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
        $usuario_id  = $_SESSION["user_id"] ?? null; 
        
        $ambiente_id = $data["ambientes_id_ambientes"] ?? null;
        
        // Validação
        // A validação agora se foca no ID do ambiente e na segurança do ID do usuário (vindo da sessão)
        if (!$usuario_id || !$ambiente_id) {
            http_response_code(400); // Bad Request
            echo json_encode([
                'status' => 'error',
                'message' => 'Campos obrigatórios faltando (ambientes_id_ambientes) ou sessão de usuário inválida.'
            ]);
            exit();
        }

        // CORREÇÃO: Removida a vírgula extra após 'ambientes_id_ambientes'
        $stmt = $pdo->prepare("
            INSERT INTO verificacao_ambiente (
                data_hora, verificacao_del, usuarios_id_usuario, ambientes_id_ambientes
            ) VALUES (NOW(), 'ativo', ?, ?)
        ");

        $stmt->execute([
            $usuario_id, // ID do usuário da SESSÃO (Seguro)
            $ambiente_id
        ]);

        echo json_encode([
            'status' => 'success',
            'message' => 'Registro de verificação inserido com sucesso!',
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