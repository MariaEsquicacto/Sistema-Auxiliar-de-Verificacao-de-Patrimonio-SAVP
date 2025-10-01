<?php
// user_logout.php

// ATENÇÃO: MANTENHA A EXIBIÇÃO DE ERROS LIGADA ENQUANTO TESTA!
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../config.php"; // Caminho da função conn()

header('Content-Type: application/json');
date_default_timezone_set('America/Sao_Paulo');

session_start();
$pdo = conn(); // *** CONEXÃO OBTIDA NA VARIÁVEL $pdo ***

// --- TRECHO DE DEBUG CORRIGIDO ---
if (!($pdo instanceof PDO)) { // ** MUDANÇA: Verifica $pdo **
    http_response_code(500);
    echo json_encode([
        "status" => "erro",
        "mensagem" => "Erro crítico: A variável \$pdo (conexão PDO) não está definida ou é inválida."
    ]);
    exit;
}
// ---------------------------------

$input = json_decode(file_get_contents("php://input"), true);
$token = $input['token']
       ?? ($_POST['token'] ?? null)
       ?? ($_SESSION['token'] ?? null);

// 2) Se não veio token, retorna erro em JSON
if (!$token) {
    http_response_code(400); // Bad Request
    echo json_encode([
        "status"  => "erro",
        "mensagem"=> "Token não fornecido"
    ]);
    exit;
}

try {
    // 3) DELETA o token na tabela `token` para invalidar a sessão
    $sql  = "DELETE FROM token 
             WHERE token = :token";
    // ** MUDANÇA: Usa $pdo para preparar a query **
    $stmt = $pdo->prepare($sql); 
    $stmt->bindParam(":token", $token, PDO::PARAM_STR);
    $stmt->execute();

    // 4) Verifica se o token existia e foi deletado
    $deletedRows = $stmt->rowCount();

    if ($deletedRows > 0) {
        // Token encontrado e deletado com sucesso
        
        // 5) Destroi a sessão PHP (segurança)
        session_unset();
        session_destroy();

        // 6) Retorna confirmação JSON
        http_response_code(200); // OK
        echo json_encode([
            "status"  => "success",
            "mensagem"=> "Logout realizado com sucesso. Token excluído."
        ]);
        exit;
    }

    // Se chegou aqui, o token não foi encontrado ou já havia sido excluído
    http_response_code(401); // Unauthorized
    echo json_encode([
        "status"  => "erro",
        "mensagem"=> "Sessão inválida ou já encerrada (Token não encontrado)."
    ]);
    exit;

} catch (\PDOException $e) {
    // Captura erros específicos do banco de dados (PDO)
    http_response_code(500);
    echo json_encode([
        "status" => "erro",
        "mensagem" => "Erro ao executar o logout no BD: " . $e->getMessage()
    ]);
    exit;
} catch (\Exception $e) {
    // Captura outros erros gerais
    http_response_code(500);
    echo json_encode([
        "status" => "erro",
        "mensagem" => "Erro interno do servidor: " . $e->getMessage()
    ]);
    exit;
}

?>