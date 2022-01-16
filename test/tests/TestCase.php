<?php
declare(strict_types=1);

namespace Tests;

use CommunityHub\Components\BanList\Store;

use PDO;

use function array_map;
use function in_array;
use function sprintf;
use function unlink;
use function touch;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    private const FILE_PATH = __DIR__ . '/../test.sqlite';

    protected function createSqliteFile(string ...$endpoints): void
    {
        $pdo = $this->createPdo();

        $pdo->exec('CREATE TABLE IF NOT EXISTS endpoints (endpoint TEXT NOT NULL)');
        foreach ($endpoints as $endpoint) {
            $pdo->exec('INSERT INTO endpoints (endpoint) VALUES ("' . $endpoint . '")');
        }
    }

    protected function assertSqliteFileContains(string ...$endPoints): void
    {
        $pdo = $this->createPdo();

        $statement = $pdo->prepare('SELECT endpoint FROM endpoints');
        $statement->execute();

        $results = array_map(function (array $row): string {
            return $row[0];
        }, $statement->fetchAll(PDO::FETCH_NUM));

        foreach ($results as $endPoint) {
            $endPointExists = in_array($endPoint, $endPoints);

            $this->assertTrue($endPointExists, sprintf(
                'EndPoint not found in file: %s.',
                $endPoint
            ));
        }

        $expectedCount = count($endPoints);
        $actualCount = count($results);

        $this->assertSame($expectedCount, $actualCount, sprintf(
            'Unexpected number of endpoints, expected %d, got %d.',
            $expectedCount,
            $actualCount
        ));
    }

    protected function makeStore(): Store
    {
        return new Store(self::FILE_PATH);
    }

    protected function setUp(): void
    {
        parent::setUp();

        @unlink(self::FILE_PATH);
    }

    protected function tearDown(): void
    {
        @unlink(self::FILE_PATH);
    }

    private function createPdo(): PDO
    {
        touch(self::FILE_PATH);

        $pdo = new PDO('sqlite:' . self::FILE_PATH);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }
}
