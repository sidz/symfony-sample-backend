<?php

declare(strict_types=1);

namespace App\Validator\Constraint;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 */
class UniqueDTO extends Constraint
{
    public string $message = 'This value is already used.';

    public ?EntityManagerInterface $em = null;
    public ?string $entityClass = null;
    public string $repositoryMethod = 'findBy';
    public array $fields = [];
    public ?string $errorPath = null;
    public bool $ignoreNull = true;

    /**
     * @var string[]
     */
    protected static $errorNames = [
        UniqueEntity::NOT_UNIQUE_ERROR => 'NOT_UNIQUE_ERROR',
    ];

    public function getRequiredOptions(): array
    {
        return ['fields'];
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    public function getDefaultOption(): string
    {
        return 'fields';
    }
}
