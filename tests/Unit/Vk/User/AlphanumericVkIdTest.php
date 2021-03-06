<?php

declare(strict_types=1);

namespace Tests\Unit\Vk\User;

use PHPUnit\Framework\TestCase;
use Vkbd\Vk\User\Id\AlphanumericVkId;

final class AlphanumericVkIdTest extends TestCase
{
    /**
     * @dataProvider invalidIdsProvider
     *
     * @param string $invalidId
     */
    public function test_it_can_not_be_created_with_not_alphanumeric_string(string $invalidId): void
    {
        $this->expectExceptionMessage('VK id must be an alphanumeric string');

        new AlphanumericVkId($invalidId);
    }

    /**
     * @dataProvider validIdsProvider
     *
     * @param string $validId
     */
    public function test_it_accepts_positive_integers(string $validId): void
    {
        $id = new AlphanumericVkId($validId);

        self::assertSame($validId, $id->value());
    }

    /**
     * @return iterable<string[]>
     */
    public function invalidIdsProvider(): iterable
    {
        return [
            [''],
            [' '],
            ['gfasd hrhrg'],
            ['~a4'],
            ['----08'],
        ];
    }

    /**
     * @return iterable<string[]>
     */
    public function validIdsProvider(): iterable
    {
        return [
            ['fageawd4213'],
            ['1'],
            ['a'],
            ['nbrdgrk'],
        ];
    }
}
