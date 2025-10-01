<?php
// 1. Inicia a sessão para acessar os dados do usuário logado
session_start();

// Define os níveis de usuário permitidos para esta ação (Importação)
// Baseado na sua tabela 'usuarios', vamos permitir 'gestor' e 'administrador'.
$niveis_permitidos = ['gestor', 'administrador'];

// 2. VERIFICAÇÃO DE PERMISSÃO
if (!isset($_SESSION['user_nivel']) || !in_array($_SESSION['user_nivel'], $niveis_permitidos)) {
    // Se o nível de usuário NÃO EXISTE ou NÃO está na lista de permitidos (ex: é 'colaborador')
    // Retorna uma mensagem de erro e interrompe a execução.
    
    // Você pode redirecionar para uma página de erro ou exibir uma mensagem:
    header("Location: /caminho/para/pagina_nao_autorizada.html"); // Mude para o seu caminho de redirecionamento
    // Ou simplesmente exibe um erro
    die("Acesso Negado. Seu nível de usuário ({$_SESSION['user_nivel']}) não tem permissão para importar arquivos.");
}

// O código só continua a partir daqui se o usuário for 'gestor' ou 'administrador'.
require_once "../config.php";

// Conecta ao banco de dados
$pdo = conn();

// Verifica se um arquivo foi enviado via formulário
if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
    $tmp_name = $_FILES['csv_file']['tmp_name'];

    // Abre o arquivo CSV para leitura
    if (($handle = fopen($tmp_name, 'r')) !== FALSE) {
        // Lê e descarta a primeira linha do cabeçalho
        fgetcsv($handle, 1000, ';'); 

        // Prepara a query de inserção para a tabela 'patrimonios'
        $sql_patrimonios = "INSERT INTO patrimonios (num_patrimonio, denominacao, ambientes_id_ambientes, patrimonio_del, status, patrimonio_img, created_at) 
                             VALUES (:num, :denominacao, :id_ambiente, 'ativo', 'pendente', '', NOW())";
        $stmt_patrimonios = $pdo->prepare($sql_patrimonios);
        
        // Prepara a query de busca na tabela 'ambientes'
        $sql_busca_ambiente = "SELECT id_ambientes FROM ambientes WHERE localizacao = :localizacao LIMIT 1";
        $stmt_busca_ambiente = $pdo->prepare($sql_busca_ambiente);

        // Inicia uma transação para garantir que todas as inserções sejam bem-sucedidas
        $pdo->beginTransaction();

        try {
            // Loop para ler cada linha do CSV
            while (($data = fgetcsv($handle, 1000, ';')) !== FALSE) {
                // Mapeamento das colunas do CSV para variáveis
                $num_patrimonio      = ($data[0] ?? null);
                $denominacao         = ($data[1] ?? null);
                $localizacao_ambiente = ($data[2] ?? null);

                // Apenas um exemplo de validação básica para evitar a inserção de linhas vazias
                if (empty($num_patrimonio) || empty($denominacao) || empty($localizacao_ambiente)) {
                    continue; // Pula para a próxima linha se os dados essenciais estiverem faltando
                }

                // 1. Busca o id_ambiente correspondente à localização
                $stmt_busca_ambiente->execute([':localizacao' => $localizacao_ambiente]);
                $id_ambiente = $stmt_busca_ambiente->fetchColumn();

                // 2. Se o id_ambiente for encontrado, insere o patrimônio
                if ($id_ambiente) {
                    $stmt_patrimonios->execute([
                        ':num' => $num_patrimonio,
                        ':denominacao' => $denominacao,
                        ':id_ambiente' => $id_ambiente
                    ]);
                } else {
                    continue;
                }
            }

            // Confirma a transação se todas as inserções foram bem-sucedidas
            $pdo->commit();
            fclose($handle);
            // Redireciona para a página home_admin
            header("Location: /../enzo-zanardi/patrimonio/front-end/view/home_admin.html");
            exit();
        } catch (Exception $e) {
            // Reverte a transação em caso de erro
            $pdo->rollBack();
            echo "❌ Erro na importação: " . $e->getMessage();
        }
    } else {
        // Você pode adicionar um tratamento de erro aqui se o arquivo não puder ser aberto
    }
}
// O bloco 'else' de tratamento de erro de arquivo não enviado pode ficar aqui
// se não for um redirecionamento.