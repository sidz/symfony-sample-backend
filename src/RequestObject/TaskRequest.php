<?php

declare(strict_types=1);

namespace App\RequestObject;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

final class TaskRequest implements RequestObject
{
    /**
     * @Assert\NotBlank
     * @Assert\Length(min="1", max="255")
     *
     * @Serializer\Type("string")
     */
    private ?string $title = null;

    /**
     * @Assert\NotBlank
     * @Assert\Length(min="1", max="255")
     *
     * @Serializer\Type("string")
     */
    private ?string $description = null;

    /**
     * @Assert\DateTime
     * @Assert\NotBlank
     *
     * @Serializer\Type("string")
     */
    private ?string $targetDate = null;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getTargetDate(): ?string
    {
        return $this->targetDate;
    }

    public function setTargetDate(?string $targetDate): void
    {
        $this->targetDate = $targetDate;
    }
}
