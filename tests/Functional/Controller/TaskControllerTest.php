<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Tests\Functional\WebTestCase;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class TaskControllerTest extends WebTestCase
{
    private SerializerInterface $serializer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->serializer = self::$container->get(SerializerInterface::class);
    }

    public function test_create_task(): void
    {
        $client = $this->createAuthenticatedClient();

        $client->request(
            Request::METHOD_POST,
            '/api/tasks/',
            [
                'title' => 'test title',
                'description' => 'test description',
                'target_date' => '2020-05-26 12:00:00',
            ]
        );

        $data = $this->serializer->deserialize($client->getResponse()->getContent(), 'array', 'json');

        self::assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());

        self::assertSame('test title', $data['title']);
        self::assertSame('test description', $data['description']);
        self::assertSame('2020-05-26 12:00:00', $data['target_date']);
        self::assertArrayHasKey('uuid', $data);
    }

    public function test_update_task(): void
    {
        $client = $this->createAuthenticatedClient();

        $client->request(
            Request::METHOD_POST,
            '/api/tasks/',
            [
                'title' => 'test title',
                'description' => 'test description',
                'target_date' => '2020-05-26 12:00:00',
            ]
        );

        $createdData = $this->serializer->deserialize($client->getResponse()->getContent(), 'array', 'json');

        $client->request(
            Request::METHOD_PUT,
            '/api/tasks/' . $createdData['uuid'],
            [
                'title' => 'new test title',
                'description' => 'new test description',
                'target_date' => '2020-05-26 12:00:00',
            ]
        );

        $data = $this->serializer->deserialize($client->getResponse()->getContent(), 'array', 'json');

        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        self::assertSame('new test title', $data['title']);
        self::assertSame('new test description', $data['description']);
        self::assertSame('2020-05-26 12:00:00', $data['target_date']);
        self::assertArrayHasKey('uuid', $data);
    }

    public function test_delete_task(): void
    {
        $client = $this->createAuthenticatedClient();

        $client->request(
            Request::METHOD_POST,
            '/api/tasks/',
            [
                'title' => 'test title',
                'description' => 'test description',
                'target_date' => '2020-05-26 12:00:00',
            ]
        );

        $createdData = $this->serializer->deserialize($client->getResponse()->getContent(), 'array', 'json');

        $client->request(
            Request::METHOD_DELETE,
            '/api/tasks/' . $createdData['uuid']
        );

        self::assertSame(Response::HTTP_NO_CONTENT, $client->getResponse()->getStatusCode());
    }

    public function test_idempotence_delete_task(): void
    {
        $client = $this->createAuthenticatedClient();

        $client->request(
            Request::METHOD_POST,
            '/api/tasks/',
            [
                'title' => 'test title',
                'description' => 'test description',
                'target_date' => '2020-05-26 12:00:00',
            ]
        );

        $createdData = $this->serializer->deserialize($client->getResponse()->getContent(), 'array', 'json');

        $client->request(
            Request::METHOD_DELETE,
            '/api/tasks/' . $createdData['uuid']
        );

        self::assertSame(Response::HTTP_NO_CONTENT, $client->getResponse()->getStatusCode());

        $client->request(
            Request::METHOD_DELETE,
            '/api/tasks/' . $createdData['uuid']
        );

        self::assertSame(Response::HTTP_NO_CONTENT, $client->getResponse()->getStatusCode());
    }

    public function test_task_list(): void
    {
        $client = $this->createAuthenticatedClient();

        $currentDateTimeAsString = (new \DateTimeImmutable())->format('Y-m-d H:i:s');

        $client->request(
            Request::METHOD_POST,
            '/api/tasks/',
            [
                'title' => 'test title 0',
                'description' => 'test description 0',
                'target_date' => $currentDateTimeAsString,
            ]
        );

        $client->request(
            Request::METHOD_POST,
            '/api/tasks/',
            [
                'title' => 'test title 1',
                'description' => 'test description 1',
                'target_date' => $currentDateTimeAsString,
            ]
        );

        $client->request(Request::METHOD_GET, '/api/tasks/');

        $data = $this->serializer->deserialize($client->getResponse()->getContent(), 'array', 'json');

        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        self::assertCount(2, $data);

        foreach ($data as $i => $task) {
            self::assertSame("test title {$i}", $task['title']);
            self::assertSame("test description {$i}", $task['description']);
            self::assertSame($currentDateTimeAsString, $task['target_date']);
            self::assertArrayHasKey('uuid', $task);
        }
    }
}
