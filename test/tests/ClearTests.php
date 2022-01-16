<?php
declare(strict_types=1);

namespace Tests;

class ClearTests extends TestCase
{
    /**
     * @test
     */
    public function it_should_clear_the_store(): void
    {
        $this->createSqliteFile('127.0.0.1', '127.0.0.2', '127.0.0.3');

        $store = $this->makeStore();

        $this->assertSame($store, $store->clear());
        $this->assertSqliteFileContains();
    }

    /**
     * @test
     */
    public function it_should_create_the_table_if_it_does_not_exist(): void
    {
        $this->makeStore()->clear();

        $this->assertSqliteFileContains();
    }
}
