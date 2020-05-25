<?php

declare(strict_types=1);

namespace App\Entity;

use App\RequestObject\TaskRequest;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository")
 * @ORM\Table(name="tasks")
 *
 * @Serializer\ExclusionPolicy("all")
 */
final class Task
{
    /**
     * @ORM\Id
     * @ORM\Column(name="uuid", type="string", length=36)
     * @ORM\GeneratedValue(strategy="NONE")
     *
     * @Serializer\Expose
     */
    private string $uuid;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="user_uuid", referencedColumnName="uuid")
     */
    private User $user;

    /**
     * @ORM\Column(name="tile", type="string", length=255)
     *
     * @Serializer\Expose
     */
    private string $title;

    /**
     * @ORM\Column(name="description", type="string", length=255)
     *
     * @Serializer\Expose
     */
    private string $description;

    /**
     * @ORM\Column(name="target_date", type="datetime_immutable")
     *
     * @Serializer\Expose
     * @Serializer\Type("DateTimeImmutable<'Y-m-d H:i:s'>")
     */
    private DateTimeImmutable $targetDate;

    private function __construct()
    {
    }

    public static function with(TaskRequest $taskRequest, User $user): self
    {
        $self = new self();

        $self->uuid = Uuid::uuid4()->toString();
        $self->title = $taskRequest->getTitle();
        $self->description = $taskRequest->getDescription();
        $self->user = $user;
        $self->targetDate = new DateTimeImmutable($taskRequest->getTargetDate());

        return $self;
    }

    public function updateWith(TaskRequest $taskRequest): void
    {
        $this->title = $taskRequest->getTitle();
        $this->description = $taskRequest->getDescription();
        $this->targetDate = new DateTimeImmutable($taskRequest->getTargetDate());
    }

    public function uuid(): string
    {
        return $this->uuid;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function user(): User
    {
        return $this->user;
    }

    public function targetDate(): DateTimeImmutable
    {
        return $this->targetDate;
    }
}
