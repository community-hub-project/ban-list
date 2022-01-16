<?php
declare(strict_types=1);

namespace Tests;

use CommunityHub\Components\BanList\EndPoint;

class CheckTests extends TestCase
{
    /**
     * @test
     */
    public function it_should_return_false_when_checking_an_endpoint_not_in_the_store(): void
    {
        $this->createSqliteFile('127.0.0.1');

        $endPoint = EndPoint::make('127.0.0.2');
        $result = $this->makeStore()->check($endPoint);

        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function it_should_return_true_when_checking_an_endpoint_in_the_store(): void
    {
        $this->createSqliteFile('127.0.0.1', '127.0.0.2');

        $endPoint = EndPoint::make('127.0.0.2');
        $result = $this->makeStore()->check($endPoint);

        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function it_should_create_the_table_if_it_does_not_exist(): void
    {
        $endPoint = EndPoint::make('127.0.0.1');
        $this->makeStore()->check($endPoint);

        $this->assertSqliteFileContains();
    }
}
