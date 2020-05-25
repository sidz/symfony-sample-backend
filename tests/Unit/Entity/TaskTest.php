<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Task;
use App\Entity\User;
use App\RequestObject\TaskRequest;
use DateTimeImmutable;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\TestCase;

final class TaskTest extends TestCase
{
    private User $user;
    private TaskRequest $taskRequest;
    private SerializerInterface $serializer;

    protected function setUp(): void
    {
        $this->serializer = SerializerBuilder::create()->build();

        $this->taskRequest = $this->serializer->deserialize(
            '{"title": "testTitle", "description": "testDescription", "target_date": "2020-05-26 12:00:00"}',
            TaskRequest::class,
            'json'
        );

        $this->user = $this->createMock(User::class);
    }

    public function test_task_creates_successfully(): void
    {
        $task = Task::with($this->taskRequest, $this->user);

        self::assertSame('testTitle', $task->title());
        self::assertSame('testDescription', $task->description());
        self::assertEquals(new DateTimeImmutable('2020-05-26 12:00:00'), $task->targetDate());
        self::assertIsString($task->uuid());
        self::assertEquals($this->user, $task->user());
    }

    public function test_task_updates_all_properties(): void
    {
        $task = Task::with($this->taskRequest, $this->user);
        $originalUuid = $task->uuid();
        $originalUser = $task->user();

        $title = 'newTestTitle';
        $description = 'newTestDescription';
        $targetDate = '2020-06-01 12:00:00';

        $taskRequestArray = [
            'title' => $title,
            'description' => $description,
            'target_date' => $targetDate,
        ];

        $taskRequest = $this->serializer->deserialize(
            json_encode($taskRequestArray),
            TaskRequest::class,
            'json'
        );

        $task->updateWith($taskRequest);

        self::assertSame($title, $task->title());
        self::assertSame($description, $task->description());
        self::assertEquals(new DateTimeImmutable($targetDate), $task->targetDate());
        self::assertSame($originalUuid, $task->uuid());
        self::assertSame($originalUser, $task->user());
    }
}
