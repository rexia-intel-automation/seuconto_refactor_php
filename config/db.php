<?php
/**
 * Configuração de Banco de Dados
 *
 * Este arquivo gerencia a conexão com o banco de dados MySQL
 * utilizando PDO para maior segurança e compatibilidade
 */

// Carrega variáveis de ambiente
require_once __DIR__ . '/env.php';

class Database {
    private static $instance = null;
    private $connection;

    /**
     * Construtor privado (Singleton pattern)
     */
    private function __construct() {
        $host = getenv('DB_HOST');
        $dbname = getenv('DB_NAME');
        $username = getenv('DB_USER');
        $password = getenv('DB_PASSWORD');
        $charset = getenv('DB_CHARSET') ?: 'utf8mb4';

        try {
            $dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$charset}"
            ];

            $this->connection = new PDO($dsn, $username, $password, $options);

        } catch (PDOException $e) {
            error_log("Erro de conexão com banco de dados: " . $e->getMessage());
            throw new Exception("Erro ao conectar com o banco de dados. Por favor, tente novamente mais tarde.");
        }
    }

    /**
     * Retorna a instância única do Database (Singleton)
     *
     * @return Database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Retorna a conexão PDO
     *
     * @return PDO
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * Previne clonagem da instância
     */
    private function __clone() {}

    /**
     * Previne desserialização da instância
     */
    public function __wakeup() {
        throw new Exception("Não é possível desserializar uma instância Singleton");
    }
}

/**
 * Função helper para obter a conexão do banco
 *
 * @return PDO
 */
function getDB() {
    return Database::getInstance()->getConnection();
}
