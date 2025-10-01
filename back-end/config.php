<?php

function conn() {
    // CORREÇÃO: Use apenas o IP do servidor do banco de dados
    $host = 'localhost';
    $dbname = 'enzo-zanardi';
    $user = 'enzo-zanardi';
    $pass = 'enzo-zanardi';
    $charset = 'utf8mb4';
    $porta = '8024';

    // A porta 3024 estava incorreta e foi substituída por 8024,
    // que é a porta do host que você informou.
    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset;port=$porta";

    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        die(json_encode([
            'status' => 'error',
            'message' => 'Erro na conexão com o banco de dados: ' . $e->getMessage()
        ]));
    }
}