<?php
declare(strict_types=1);

namespace CommunityHub\Components\BanList;

use PDOException;
use PDOStatement;
use PDO;

use Throwable;

use function pathinfo;
use function is_file;
use function sprintf;
use function is_dir;
use function mkdir;
use function touch;

use const PATHINFO_DIRNAME;

/**
 * Ban List Store.
 *
 * Wrapper for SQLite database which contains a list of HTTP endpoints which
 * have been banned. The SQLite database must strictly be held in a file,
 * it cannot be held in memory.
 */
final class Store
{
    private PDO $pdo;

    /**
     * @throws Exception
     *     If the sqlite file path was ":memory" (in memory databases are
     *     strictly forbidden here). Or if the file/directory of the file path
     *     did not exist and could not be created. Or if the sqlite database
     *     connection could not be established.
     */
    public function __construct(string $filePath)
    {
        if (':memory:' === $filePath) {
            throw $this->createException('SQLite database cannot be in memory.');
        }

        if (!@is_file($filePath)) {
            $dirPath = pathinfo($filePath, PATHINFO_DIRNAME);

            if (!@is_dir($dirPath) && !@mkdir($dirPath, 0755, true)) {
                $message = sprintf('Could not create directory: %s.', $dirPath);

                throw $this->createException($message);
            }

            if (!@touch($filePath)) {
                $message = sprintf('Could not create file: %s.', $filePath);

                throw $this->createException($message);
            }
        }

        try {
            $pdo = new PDO('sqlite:' . $filePath);
        } catch (PDOException $e) {
            $message = sprintf('Could not connect to sqlite database: %s.', $filePath);

            throw $this->createException($message);
        }

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->pdo = $pdo;
    }

    /**
     * @throws Exception
     *     if the SQL statement to add the endpoint to
     *     the sqlite file could not be executed.
     */
    public function add(EndPoint $endPoint): self
    {
        if (!$this->check($endPoint)) {
            $this->execute(
                'INSERT INTO endpoints (endpoint) VALUES (?)',
                (string) $endPoint
            );
        }

        return $this;
    }

    /**
     * @throws Exception
     *     if the SQL statement to remove the endpoint from
     *     the sqlite file could not be executed.
     */
    public function remove(EndPoint $endPoint): self
    {
        if ($this->check($endPoint)) {
            $this->execute(
                'DELETE FROM endpoints WHERE endpoint=?',
                (string) $endPoint
            );
        }

        return $this;
    }

    /**
     * @throws Exception
     *     if the SQL statement to check that the endpoint exists in the sqlite
     *     file could not be executed.
     */
    public function check(EndPoint $endPoint): bool
    {
        $statement = $this->execute(
            'SELECT * FROM endpoints WHERE endpoint=?',
            (string) $endPoint
        );

        return (bool) count($statement->fetchAll());
    }

    /**
     * @throws Exception
     *     if the SQL statement to clear the endpoints table in the sqlite file
     *     could not be executed.
     */
    public function clear(): self
    {
        $this->execute('DELETE FROM endpoints');

        return $this;
    }

    /**
     * @throws Exception
     *     If the endpoints table could not be created
     *     or if the SQL statement could not be executed.
     */
    private function execute(string $sql, string ...$parameters): PDOStatement
    {
        try {
            return $this->executeSql($sql, ...$parameters);
        } catch (Exception $e) {
            $this->executeSql('CREATE TABLE IF NOT EXISTS endpoints (endpoint TEXT NOT NULL)');
        }

        return $this->executeSql($sql, ...$parameters);
    }

    /**
     * @throws Exception
     *     if the SQL statement could not be executed.
     */
    private function executeSql(string $sql, string ...$parameters): PDOStatement
    {
        try {
            $statement = $this->pdo->prepare($sql);
            $statement->execute($parameters);
        } catch (PDOException $e) {
            $message = sprintf('SQL statement could not be executed: %s.', $e->getMessage());

            throw $this->createException($message, $e);
        }

        return $statement;
    }

    private function createException(string $message, ?Throwable $previous = null): Exception
    {
        require_once __DIR__ . '/Exception.php';

        return new Exception($message, $previous);
    }
}
