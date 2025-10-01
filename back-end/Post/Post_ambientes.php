<?php
// Exibe erros (somente em desenvolvimento)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");

try {
    // Inclui o config.php que deve criar a função conn() retornando $pdo
    require_once "../config.php";

    $pdo = conn(); // Função conn() do config.php
    if (!$pdo) {
        throw new Exception("Falha na conexão com o banco.");
    }

    // Verifica método POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode([
            'status' => 'error',
            'message' => 'Método inválido. Use POST.'
        ]);
        exit();
    }

    // Recebe JSON
    $data = json_decode(file_get_contents("php://input"), true);
    if (!$data) {
        throw new Exception("JSON inválido ou vazio");
    }

    // Extrai e limpa dados
    $nome      = trim($data["ambiente_nome"] ?? '');
    $categoria = trim($data["categoria"] ?? '');
    $local     = trim($data["localizacao"] ?? '');
    $status    = trim($data["ambiente_del"] ?? 'ativo');

    // Valida campos obrigatórios
    if ($nome === '' || $categoria === '' || $local === '') {
        throw new Exception("Campos obrigatórios faltando (nome, categoria ou localizacao).");
    }

    // Valida categoria (coincidir com ENUM do banco)
    $categorias_validas = ['eletroeletronica', 'oficina', 'quimica', 't.i', 'panificacao', 'metalmecanica'];
    if (!in_array($categoria, $categorias_validas)) {
        throw new Exception("Categoria inválida");
    }

    // Valida status (coincidir com ENUM do banco)
    $status_validos = ['ativo', 'inativo'];
    if (!in_array($status, $status_validos)) {
        throw new Exception("Status inválido");
    }

    // Valida localizacao como número
    if (!is_numeric($local)) {
        throw new Exception("Código de localização deve ser numérico");
    }

    // Prepara e executa insert
    $stmt = $pdo->prepare("
        INSERT INTO ambientes (ambiente_nome, categoria, localizacao, ambiente_del)
        VALUES (:nome, :categoria, :local, :status)
    ");
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':categoria', $categoria);
    $stmt->bindParam(':local', $local);
    $stmt->bindParam(':status', $status);
    $stmt->execute();

    // Retorna sucesso
    echo json_encode([
        'status' => 'success',
        'message' => 'Ambiente cadastrado com sucesso!',
        'data' => [
            'ambiente_nome' => $nome,
            'categoria' => $categoria,
            'localizacao' => $local,
            'ambiente_del' => $status
        ]
    ]);

} catch (Exception $e) {
    // Retorna erro em JSON
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Erro ao inserir ambiente: ' . $e->getMessage()
    ]);
}
