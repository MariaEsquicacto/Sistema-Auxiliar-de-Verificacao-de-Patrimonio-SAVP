<?php
// 1. INICIA A SESSÃO para acessar o nível de usuário
session_start();

// Define os níveis de usuário permitidos para esta ação
// A criação de usuários deve ser restrita ao 'administrador'
$niveis_permitidos = ['administrador'];

// 2. VERIFICAÇÃO DE PERMISSÃO: Bloqueia se o usuário não tiver o nível adequado
if (!isset($_SESSION['user_nivel']) || !in_array($_SESSION['user_nivel'], $niveis_permitidos)) {
    http_response_code(403); // Forbidden
    echo json_encode([
        'status' => 'error', 
        'message' => 'Acesso Negado. Apenas administradores podem cadastrar novos usuários.'
    ]);
    exit();
}

require_once "../config.php";
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = conn(); // função conn() vinda do config.php

        // Recebe JSON 
        $data = json_decode(file_get_contents("php://input"), true);

        $nome   = $data["usuario_nome"]   ?? null;
        $nivel  = $data["usuario_nivel"]  ?? null;
        $email  = $data["usuario_email"]  ?? null;
        $senha  = $data["senha"]          ?? null;
        $status = $data["usuario_del"]    ?? "ativo"; // padrão ativo

        if ($nome && $nivel && $email && $senha) { // A senha agora é obrigatória para o cadastro
            
            // Criptografa a senha com SHA-512
            $senha_criptografada = hash('sha512', $senha);
            
            // Validação de segurança adicional: Garante que o nível sendo criado é válido
            $niveis_validos = ['administrador', 'gestor', 'colaborador'];
            if (!in_array($nivel, $niveis_validos)) {
                http_response_code(400); 
                echo json_encode(['status' => 'error', 'message' => 'Nível de usuário inválido fornecido.']);
                exit();
            }

            $stmt = $pdo->prepare("
                INSERT INTO usuarios (usuario_nome, usuario_nivel, usuario_email, senha, usuario_del) 
                VALUES (:nome, :nivel, :email, :senha, :status)
            ");

            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':nivel', $nivel);
            $stmt->bindParam(':email', $email);
            // Binda a senha criptografada
            $stmt->bindParam(':senha', $senha_criptografada);
            $stmt->bindParam(':status', $status);

            $stmt->execute();

            http_response_code(201); // Created
            echo json_encode([
                'status' => 'success',
                'message' => 'Usuário criado com sucesso!',
                'data' => [
                    'usuario_nome'  => $nome,
                    'usuario_nivel' => $nivel,
                    'usuario_email' => $email,
                    'usuario_del'   => $status
                ]
            ]);
            exit();
        } else {
            http_response_code(400); // Bad Request
            echo json_encode([
                'status' => 'error',
                'message' => 'Campos obrigatórios faltando (usuario_nome, usuario_nivel, usuario_email, senha).'
            ]);
            exit();
        }
    } catch (Exception $e) {
        http_response_code(500); // Internal Server Error
        // Tentativa de erro de email duplicado (se for uma chave UNIQUE)
        if ($e->getCode() == '23000') {
             echo json_encode([
                'status' => 'error',
                'message' => 'Erro: Este email já está cadastrado.'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Erro ao inserir usuário: ' . $e->getMessage()
            ]);
        }
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