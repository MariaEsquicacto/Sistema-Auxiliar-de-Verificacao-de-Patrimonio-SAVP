<?php
// 1. INICIA A SESSÃO: Isso deve ser a primeira coisa a acontecer.
session_start();

require_once "../config.php";

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['email']) && isset($data['senha'])) {
        $email = trim($data['email']);
        $senha = $data['senha']; // Senha em texto puro, vinda do front

        if (empty($email) || empty($senha)) {
            http_response_code(400); // Bad Request
            echo json_encode(['status' => 'error', 'message' => 'Email e senha são obrigatórios.']);
            exit();
        }

        // 1. Criptografa a senha recebida para comparação no banco (usando SHA-512)
        $senha_criptografada = hash('sha512', $senha);
        
        $pdo = conn();

        // 2. Consulta: Verifica se existe usuário com ESSE EMAIL E ESSA SENHA
        $smt = $pdo->prepare('SELECT id_usuario, usuario_email, usuario_nivel FROM usuarios WHERE usuario_email = ? AND senha = ? AND usuario_del = "ativo"');
        $smt->execute([$email, $senha_criptografada]);
        $user = $smt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Se chegou aqui, as credenciais estão corretas e o usuário está ativo.
            
            // Gera token único com SHA512
            $random_string = random_bytes(32);
            $token = hash('sha512', $random_string);

            // LÓGICA DE LIMPEZA: Exclui TODOS os tokens antigos para este usuário.
            $smt_delete = $pdo->prepare("DELETE FROM token WHERE usuarios_id_usuario = ?");
            $smt_delete->execute([$user['id_usuario']]);

            // Salva o novo token na tabela
            $smt = $pdo->prepare("INSERT INTO token (usuarios_id_usuario, token) VALUES (?, ?)");
            $smt->execute([$user['id_usuario'], $token]);

            if ($smt->rowCount() > 0) {
                
                // === CORREÇÃO: GARANTE QUE O NÍVEL ESTÁ LIMPO E PADRONIZADO ===
                $user_nivel_limpo = strtolower(trim($user['usuario_nivel']));
                
                // ARMAZENA AS INFORMAÇÕES DO USUÁRIO NA SESSÃO
                $_SESSION['login_token'] = $token;
                $_SESSION['user_id'] = $user['id_usuario'];
                $_SESSION['user_email'] = $user['usuario_email'];
                $_SESSION['user_nivel'] = $user_nivel_limpo; 
                $_SESSION['session_created_at'] = time(); 
                
                // Resposta de sucesso 
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Login realizado com sucesso e sessão iniciada.',
                    'token'  => $token,
                    'user'    => [
                        'id'    => $user['id_usuario'],
                        'email' => $user['usuario_email'],
                        'nivel' => $user_nivel_limpo // Retorna o valor LIMPO para o front
                    ],
                    'session_data' => [ // Dados de sessão para DEBUG
                        'login_token' => $_SESSION['login_token'],
                        'user_nivel' => $_SESSION['user_nivel'],
                    ]
                ]);
                exit();
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Erro ao salvar novo token no banco de dados.']);
                exit();
            }
        } else {
            // Email ou senha estão incorretos, ou o usuário está inativo.
            http_response_code(401); // Unauthorized
            echo json_encode(['status' => 'error', 'message' => 'Credenciais inválidas. Verifique seu e-mail e senha.']);
            exit();
        }
    } else {
        http_response_code(400); // Bad Request
        echo json_encode(['status' => 'error', 'message' => 'Campos de email ou senha ausentes.']);
        exit();
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Método inválido. Use POST.']);
    exit();
}