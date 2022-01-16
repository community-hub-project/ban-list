<?php
declare(strict_types=1);

namespace Tests;

use CommunityHub\Components\BanList\EndPoint;

class AddTests extends TestCase
{
    /**
     * @test
     */
    public function it_should_add_an_endpoint(): void
    {
        $this->createSqliteFile('127.0.0.1');

        $endPoint = EndPoint::make('127.0.0.2');
        $store = $this->makeStore();

        $this->assertSame($store, $store->add($endPoint));
        $this->assertSqliteFileContains('127.0.0.1', '127.0.0.2');
    }

    /**
     * @test
     */
    public function it_should_not_add_an_endpoint_that_already_exists(): void
    {
        $this->createSqliteFile('127.0.0.1');

        $endPoint = EndPoint::make('127.0.0.1');
        $store = $this->makeStore();

        $this->assertSame($store, $store->add($endPoint));
        $this->assertSqliteFileContains('127.0.0.1');
    }

    /**
     * @test
     */
    public function it_should_create_the_table_if_it_does_not_exist(): void
    {
        $endPoint = EndPoint::make('127.0.0.1');
        $this->makeStore()->add($endPoint);

        $this->assertSqliteFileContains('127.0.0.1');
    }
}
