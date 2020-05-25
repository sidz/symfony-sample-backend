<?php

declare(strict_types=1);

namespace App\Tests\Unit\Exception;

use App\Exception\InvalidRequestData;
use PHPUnit\Framework\TestCase;

class InvalidRequestDataTest extends TestCase
{
    public function test_exception_message(): void
    {
        $exception = InvalidRequestData::with([]);

        self::assertSame('There was a validation error', $exception->getMessage());
        self::assertSame([], $exception->violations());
    }
}
