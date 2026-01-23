<?php
// Carrega o autoload do Composer, necessário para usar bibliotecas externas como Dotenv
require __DIR__ . '/../vendor/autoload.php'; // sobe de config → entra em vendor

// Importa a classe Dotenv do pacote vlucas/phpdotenv
use Dotenv\Dotenv;

// Aponta para a raiz do projeto (uma pasta acima da pasta atual) e carrega as variáveis do arquivo .env
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Define constantes a partir das variáveis de ambiente do .env
define("HOST", $_ENV['DB_HOST']);
define("PASSWORD", $_ENV['DB_PASS']);
define("USER", $_ENV['DB_USER']);
define("PORT", $_ENV['DB_PORT']);
define("DATABASE", $_ENV['DB_NAME']);

// Função para criar uma conexão PDO com o banco de dados
function conexaoBanco($db)
{
    // Verifica se a extensão PDO MySQL está habilitada no PHP
    // Se não estiver, lança uma exceção e interrompe a execução
    if(!extension_loaded('pdo_mysql')){
        throw new RuntimeException("Extensão PDO MYSQL não foi habilitada no PHP");
    }

    // Verifica se o nome do banco foi informado
    // Se estiver vazio, lança uma exceção
    if(empty($db)){
        throw new RuntimeException("Nome do banco não informado");
    }

    try {
        // Cria uma nova conexão PDO
        $pdo = new PDO(
            "mysql:host=" . HOST . ";dbname=" . $db . ";port=" . PORT . ";charset=utf8",
            USER,
            PASSWORD,
            [

                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
                // Faz com que qualquer erro do PDO lance uma exceção (melhor para tratar erros de forma sênior)

                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, 
                // Ao buscar dados, retorna cada linha como array associativo (ex: $linha['nome'])

                PDO::ATTR_EMULATE_PREPARES => false, 
                // Garante que o MySQL execute prepared statements reais, evitando SQL Injection

                PDO::ATTR_PERSISTENT => true 
                // Cria conexões persistentes para reaproveitar a mesma conexão entre requisições, melhorando performance
            ]
        );

        // Retorna o objeto PDO para ser usado em consultas
        return $pdo;

    } catch (PDOException $e) {
        // Caso ocorra algum erro ao conectar, exibe mensagem e interrompe a execução
        die("Erro ao conectar com o banco $db: " . $e->getMessage());
    }
}

// Chama a função para criar a conexão com o banco principal e armazena no objeto $pdo
$pdo = conexaoBanco(DATABASE);
