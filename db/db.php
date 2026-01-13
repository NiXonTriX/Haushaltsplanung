<?php
/**
 * DB Singleton Klasse (PDO)
 * Unterstützt MySQL und SQLite
 *
 * Beispiel:
 * require_once __DIR__ . '/db.php';
 * $db = DB::getInstance([
 *   'driver' => 'mysql',
 *   'host' => '127.0.0.1',
 *   'dbname' => 'haushalt',
 *   'user' => 'root',
 *   'pass' => '',
 *   'charset' => 'utf8mb4',
 * ])->getConnection();
 *
 * Alternativ können vor dem Aufruf die Konstanten DB_DRIVER / DB_HOST / DB_NAME / DB_USER / DB_PASS / DB_CHARSET definiert werden.
 */

class DB
{
    private static $instance = null;
    private $pdo;

    /**
     * Privater Konstruktor verhindert direkte Erzeugung.
     * @param array|null $config optional beim ersten Aufruf übergeben
     * @throws RuntimeException bei Verbindungsfehler
     */
    private function __construct(array $config = null)
    {
        if ($config === null) {
            $config = [
                'driver'  => defined('DB_DRIVER')  ? constant('DB_DRIVER')  : 'mysql',
                'host'    => defined('DB_HOST')    ? constant('DB_HOST')    : '127.0.0.1',
                'dbname'  => defined('DB_NAME')    ? constant('DB_NAME')    : '',
                'user'    => defined('DB_USER')    ? constant('DB_USER')    : 'root',
                'pass'    => defined('DB_PASS')    ? constant('DB_PASS')    : '',
                'charset' => defined('DB_CHARSET') ? constant('DB_CHARSET') : 'utf8mb4',
            ];
        } else {
            $config += ['driver' => 'mysql', 'host' => '127.0.0.1', 'dbname' => '', 'user' => 'root', 'pass' => '', 'charset' => 'utf8mb4'];
        }

        try {
            if ($config['driver'] === 'sqlite') {
                $dsn = 'sqlite:' . $config['dbname'];
                $this->pdo = new PDO($dsn);
            } else {
                $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $config['host'], $config['dbname'], $config['charset']);
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];
                $this->pdo = new PDO($dsn, $config['user'], $config['pass'], $options);
            }
        } catch (PDOException $e) {
            throw new RuntimeException('Database connection failed: ' . $e->getMessage());
        }
    }

    /**
     * Liefert die Singleton-Instanz (bei erstem Aufruf optional mit Config)
     * @param array|null $config
     * @return DB
     */
    public static function getInstance(array $config = null)
    {
        if (self::$instance === null) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    /**
     * Liefert das PDO-Objekt
     * @return PDO
     */
    public function getConnection()
    {
        return $this->pdo;
    }

    /**
     * Kurzhelper für vorbereitete Abfragen
     * @param string $sql
     * @param array $params
     * @return PDOStatement
     */
    public function query($sql, array $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Letzte Insert ID
     */
    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

    private function __clone() {}
    private function __wakeup() {}
}
