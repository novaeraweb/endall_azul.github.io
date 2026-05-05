<?php
/**
 * ENDALL INSPEÇÕES - Sistema de Vendas
 * Classe de Conexão com Banco de Dados
 * 
 * @package EndallVendas
 * @version 1.0.0
 */

// Prevenir acesso direto
if (!defined('SISTEMA_ENDALL') && !defined('ENDALL_APP')) {
    die('Acesso negado');
}

class Database {
    private static $instance = null;
    private $conn;
    
    /**
     * Construtor privado (Singleton)
     */
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ];
            
            $this->conn = new PDO($dsn, DB_USER, DB_PASS, $options);
            
        } catch (PDOException $e) {
            if (DEBUG_MODE) {
                die("Erro de conexão: " . $e->getMessage());
            } else {
                die("Erro ao conectar com o banco de dados. Por favor, tente novamente mais tarde.");
            }
        }
    }
    
    /**
     * Obter instância única da classe (Singleton)
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Obter conexão PDO
     */
    public function getConnection() {
        return $this->conn;
    }
    
    /**
     * Executar query SELECT
     * 
     * @param string $sql Query SQL
     * @param array $params Parâmetros para bind
     * @return array Resultado da query
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            $this->logError($e, $sql, $params);
            return false;
        }
    }
    
    /**
     * Executar query que retorna uma única linha
     * 
     * @param string $sql Query SQL
     * @param array $params Parâmetros para bind
     * @return array|false Resultado da query ou false
     */
    public function queryRow($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            $this->logError($e, $sql, $params);
            return false;
        }
    }
    
    /**
     * Executar query INSERT, UPDATE ou DELETE
     * 
     * @param string $sql Query SQL
     * @param array $params Parâmetros para bind
     * @return bool|int ID inserido (INSERT) ou true (UPDATE/DELETE)
     */
    public function execute($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute($params);
            
            // Se for INSERT, retornar último ID inserido
            if (stripos($sql, 'INSERT') === 0) {
                return $this->conn->lastInsertId();
            }
            
            return $result;
        } catch (PDOException $e) {
            $this->logError($e, $sql, $params);
            return false;
        }
    }
    
    /**
     * Contar registros
     * 
     * @param string $table Nome da tabela
     * @param string $where Condição WHERE (opcional)
     * @param array $params Parâmetros para bind
     * @return int Número de registros
     */
    public function count($table, $where = '', $params = []) {
        $sql = "SELECT COUNT(*) as total FROM {$table}";
        if (!empty($where)) {
            $sql .= " WHERE {$where}";
        }
        
        $result = $this->queryRow($sql, $params);
        return $result ? (int)$result['total'] : 0;
    }
    
    /**
     * Iniciar transação
     */
    public function beginTransaction() {
        return $this->conn->beginTransaction();
    }
    
    /**
     * Confirmar transação
     */
    public function commit() {
        return $this->conn->commit();
    }
    
    /**
     * Reverter transação
     */
    public function rollback() {
        return $this->conn->rollBack();
    }
    
    /**
     * Verificar se está em transação
     */
    public function inTransaction() {
        return $this->conn->inTransaction();
    }
    
    /**
     * Escapar string (para casos especiais)
     * Nota: Use prepared statements sempre que possível
     */
    public function escape($value) {
        return $this->conn->quote($value);
    }
    
    /**
     * Log de erros
     */
    private function logError($exception, $sql, $params) {
        if (DEBUG_MODE) {
            echo "<div style='background:#ff0000;color:#fff;padding:20px;margin:10px;border-radius:5px;'>";
            echo "<h3>Erro no Banco de Dados</h3>";
            echo "<p><strong>Mensagem:</strong> " . $exception->getMessage() . "</p>";
            echo "<p><strong>SQL:</strong> " . htmlspecialchars($sql) . "</p>";
            echo "<p><strong>Parâmetros:</strong> " . print_r($params, true) . "</p>";
            echo "<p><strong>Stack Trace:</strong></p><pre>" . $exception->getTraceAsString() . "</pre>";
            echo "</div>";
        } else {
            // Em produção, salvar em arquivo de log
            $logFile = BASE_PATH . '/logs/db_errors.log';
            $logMessage = date('Y-m-d H:i:s') . " - " . $exception->getMessage() . "\n";
            $logMessage .= "SQL: " . $sql . "\n";
            $logMessage .= "Params: " . print_r($params, true) . "\n\n";
            
            @file_put_contents($logFile, $logMessage, FILE_APPEND);
        }
    }
    
    /**
     * Prevenir clonagem
     */
    private function __clone() {}
    
    /**
     * Prevenir unserialize
     */
    public function __wakeup() {
        throw new Exception("Não é possível unserialize singleton");
    }
}

/**
 * Função auxiliar para obter conexão
 */
function db() {
    return Database::getInstance();
}

/**
 * Função auxiliar para obter conexão PDO
 */
function getDB() {
    return Database::getInstance()->getConnection();
}

?>
