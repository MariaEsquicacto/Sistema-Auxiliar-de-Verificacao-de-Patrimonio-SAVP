<?php
// 1. INICIA A SESSÃO para acessar o nível de usuário
session_start();

// Define os níveis de usuário permitidos para esta ação
$niveis_permitidos = ['gestor', 'administrador'];

// 2. VERIFICAÇÃO DE PERMISSÃO: Bloqueia se o usuário não tiver o nível adequado
if (!isset($_SESSION['user_nivel']) || !in_array($_SESSION['user_nivel'], $niveis_permitidos)) {
    http_response_code(403); // Forbidden
    echo json_encode([
        'status' => 'error', 
        'message' => 'Acesso Negado. Você não tem permissão para realizar upload de arquivos.'
    ]);
    exit();
}

require_once "../config.php";
header("Content-Type: application/json");
date_default_timezone_set('America/Sao_Paulo');

// --- Valida o método da requisição ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Método inválido. Use POST.']);
    exit();
}

try {
    // --- Validações iniciais do arquivo ---
    if (!isset($_FILES['arquivo']) || $_FILES['arquivo']['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400); // Bad Request
        $uploadErrors = [
            UPLOAD_ERR_INI_SIZE   => 'O arquivo excede o limite definido em upload_max_filesize no php.ini.',
            UPLOAD_ERR_FORM_SIZE  => 'O arquivo excede o limite definido no formulário HTML.',
            UPLOAD_ERR_PARTIAL    => 'O upload do arquivo foi feito parcialmente.',
            UPLOAD_ERR_NO_FILE    => 'Nenhum arquivo foi enviado.',
            UPLOAD_ERR_NO_TMP_DIR => 'Pasta temporária ausente.',
            UPLOAD_ERR_CANT_WRITE => 'Falha em escrever o arquivo em disco.',
            UPLOAD_ERR_EXTENSION  => 'Uma extensão do PHP interrompeu o upload do arquivo.',
        ];
        $errorCode = $_FILES['arquivo']['error'] ?? UPLOAD_ERR_NO_FILE;
        $message = $uploadErrors[$errorCode] ?? 'Erro desconhecido no upload.';
        echo json_encode(['status' => 'error', 'message' => $message]);
        exit();
    }

    // --- Obtém o ID do usuário da SESSÃO, em vez de confiar no POST ---
    // Isso é uma medida de segurança importante para evitar que um usuário envie um ID falso.
    $usuario_id = $_SESSION['user_id'] ?? null;
    
    if (empty($usuario_id)) {
         // O ID da sessão deve existir se o check de permissão passou.
         // Se não existir, é um erro interno ou sessão expirada/inválida.
        http_response_code(401); 
        echo json_encode(['status' => 'error', 'message' => 'Sessão de usuário inválida ou expirada.']);
        exit();
    }
    
    $arquivo = $_FILES['arquivo'];

    // --- Valida a extensão do arquivo ---
    $nomeArquivo = $arquivo['name'];
    $extensao = strtolower(pathinfo($nomeArquivo, PATHINFO_EXTENSION));
    $extensoesPermitidas = ['csv', 'xls', 'xlsx'];

    if (!in_array($extensao, $extensoesPermitidas)) {
        http_response_code(415); // Unsupported Media Type
        echo json_encode(['status' => 'error', 'message' => 'Tipo de arquivo não permitido. Apenas CSV, XLS e XLSX são aceitos.']);
        exit();
    }

    // --- Salva o arquivo no servidor ---
    $diretorioUploads = __DIR__ . '/uploads/';
    if (!is_dir($diretorioUploads)) {
        mkdir($diretorioUploads, 0777, true);
    }
    // Cria um nome único para evitar sobrescrever arquivos
    $nomeArquivoUnico = uniqid('', true) . '.' . $extensao;
    $caminhoCompleto = $diretorioUploads . $nomeArquivoUnico;

    if (!move_uploaded_file($arquivo['tmp_name'], $caminhoCompleto)) {
        throw new Exception("Não foi possível mover o arquivo para o diretório de uploads.");
    }

    // --- Insere o registro no banco de dados ---
    $pdo = conn();
    $stmt = $pdo->prepare("
        INSERT INTO arquivo_importacao 
            (data_importacao, resultado, arquivo, arquivo_del, usuarios_id_usuario) 
        VALUES 
            (NOW(), :resultado, :caminho_arquivo, :arquivo_del, :id_usuario)
    ");

    $resultado = 'sucesso'; 
    $arquivo_del = 'ativo'; 

    $stmt->bindParam(':resultado', $resultado);
    $stmt->bindParam(':caminho_arquivo', $caminhoCompleto);
    $stmt->bindParam(':arquivo_del', $arquivo_del);
    $stmt->bindParam(':id_usuario', $usuario_id); // Usa o ID seguro da SESSÃO
    
    $stmt->execute();

    // --- Resposta de sucesso ---
    http_response_code(201); // Created
    echo json_encode([
        'status' => 'success',
        'message' => 'Arquivo enviado e registro criado com sucesso!',
        'id_registro' => $pdo->lastInsertId(),
        'caminho_arquivo' => $caminhoCompleto
    ]);

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode([
        'status' => 'error',
        'message' => 'Erro no servidor: ' . $e->getMessage()
    ]);
}
// depois fazer os documentos preencherem os campos respctivos no banco de dados