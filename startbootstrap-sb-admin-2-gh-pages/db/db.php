<?php
declare(strict_types=1);

/**
 * Datenbankverbindung als Singleton.
 *
 * Diese Klasse stellt genau eine PDO-Instanz bereit,
 * damit in der Anwendung überall dieselbe Verbindung genutzt wird.
 */
final class Database
{
    /** @var PDO|null */
    private static ?PDO $instance = null;

    private function __construct()
    {
        // Konstruktor ist privat, damit niemand von außen neue Instanzen erzeugt.
    }

    /**
     * Liefert die aktive PDO-Verbindung zurück.
     *
     * @throws RuntimeException wenn die Verbindung nicht aufgebaut werden kann.
     */
    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            // In einer Lernumgebung sind feste Defaults hilfreich.
            // Gleichzeitig kannst du die Werte jederzeit über Umgebungsvariablen überschreiben.
            $host = getenv('DB_HOST') ?: 'localhost';
            $database = getenv('DB_NAME') ?: 'haushaltsplaner';
            $user = getenv('DB_USER') ?: 'root';
            $password = getenv('DB_PASSWORD') ?: 'root';
            $charset = 'utf8mb4';

            $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $host, $database, $charset);

            try {
                self::$instance = new PDO($dsn, $user, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $exception) {
                // Die ursprüngliche Exception wird als "previous" weitergereicht.
                throw new RuntimeException(
                    'Datenbankverbindung fehlgeschlagen: ' . $exception->getMessage(),
                    0,
                    $exception
                );
            }
        }

        return self::$instance;
    }
}