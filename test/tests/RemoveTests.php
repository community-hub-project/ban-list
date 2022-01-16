<?php
declare(strict_types=1);

namespace Tests;

use CommunityHub\Components\BanList\EndPoint;

class RemoveTests extends TestCase
{
    /**
     * @test
     */
    public function it_should_remove_an_endpoint(): void
    {
        $this->createSqliteFile('127.0.0.1', '127.0.0.2');

        $endPoint = EndPoint::make('127.0.0.2');
        $store = $this->makeStore();

        $this->assertSame($store, $store->remove($endPoint));
        $this->assertSqliteFileContains('127.0.0.1');
    }

    /**
     * @test
     */
    public function it_should_not_remove_an_endpoint_that_does_not_exist(): void
    {
        $this->createSqliteFile('127.0.0.1');

        $endPoint = EndPoint::make('127.0.0.2');
        $store = $this->makeStore();

        $this->assertSame($store, $store->remove($endPoint));
        $this->assertSqliteFileContains('127.0.0.1');
    }

    /**
     * @test
     */
    public function it_should_create_the_table_if_it_does_not_exist(): void
    {
        $endPoint = EndPoint::make('127.0.0.1');
        $this->makeStore()->remove($endPoint);

        $this->assertSqliteFileContains();
    }
}
