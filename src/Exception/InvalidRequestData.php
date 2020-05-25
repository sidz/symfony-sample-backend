<?php

declare(strict_types=1);

namespace App\Exception;

use RuntimeException;

final class InvalidRequestData extends RuntimeException
{
    private iterable $violations;

    public static function with(iterable $violations): self
    {
        $self = new self('There was a validation error');

        $self->violations = $violations;

        return $self;
    }

    public function violations(): iterable
    {
        return $this->violations;
    }
}
