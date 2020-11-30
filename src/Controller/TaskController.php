<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Task;
use App\RequestObject\TaskRequest;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tasks")
 *
 * @SWG\Tag(name="Tasks")
 * @Security(name="Bearer")
 */
final class TaskController extends AbstractController
{
    private SerializerInterface $serializer;
    private EntityManagerInterface $em;

    public function __construct(SerializerInterface $serializer, EntityManagerInterface $em)
    {
        $this->serializer = $serializer;
        $this->em = $em;
    }

    /**
     * @Route("/", name="task_list", methods={Request::METHOD_GET})
     *
     * @SWG\Response(
     *     response=JsonResponse::HTTP_OK,
     *     description="Returns a list of tasks for a day",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Task::class))
     *     )
     * )
     */
    public function list(): JsonResponse
    {
        $taskListForToday = $this->em
            ->getRepository(Task::class)
            ->findByUserForToday($this->getUser(), new DateTimeImmutable('today'));

        return JsonResponse::fromJsonString(
            $this->serializer->serialize($taskListForToday, 'json'),
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @Route("/", name="create_task", methods={Request::METHOD_POST})
     *
     * @SWG\Parameter(
     *     name="title",
     *     in="formData",
     *     type="string",
     *     description="Task title",
     *     required=true
     * )
     * @SWG\Parameter(
     *     name="description",
     *     in="formData",
     *     type="string",
     *     description="Task description",
     *     required=true
     * )
     * @SWG\Parameter(
     *     name="target_date",
     *     in="formData",
     *     type="string",
     *     description="Schedule to day and time. Format Y-m-d H:i:s",
     *     required=true
     * )
     *
     * @SWG\Response(
     *     response=JsonResponse::HTTP_CREATED,
     *     description="Returns a list of tasks for a day",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Task::class))
     *     )
     * )
     */
    public function create(TaskRequest $taskRequest): JsonResponse
    {
        $task = Task::with($taskRequest, $this->getUser());

        $this->em->persist($task);
        $this->em->flush();

        return JsonResponse::fromJsonString(
            $this->serializer->serialize($task, 'json'),
            JsonResponse::HTTP_CREATED
        );
    }

    /**
     * @Route("/{uuid}", name="update_task", methods={Request::METHOD_PUT})
     *
     * @SWG\Parameter(
     *     name="title",
     *     in="formData",
     *     type="string",
     *     description="Task title",
     *     required=true
     * )
     * @SWG\Parameter(
     *     name="description",
     *     in="formData",
     *     type="string",
     *     description="Task description",
     *     required=true
     * )
     * @SWG\Parameter(
     *     name="target_date",
     *     in="formData",
     *     type="string",
     *     description="Schedule to day and time. Format Y-m-d H:i:s",
     *     required=true
     * )
     *
     * @SWG\Response(
     *     response=JsonResponse::HTTP_OK,
     *     description="Task updated",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Task::class))
     *     )
     * )
     * @SWG\Response(
     *     response=JsonResponse::HTTP_NOT_FOUND,
     *     description="Task by UUID is not found"
     * )
     */
    public function update(string $uuid, TaskRequest $taskRequest): JsonResponse
    {
        $task = $this->em
            ->getRepository(Task::class)
            ->findByUserAndUUid($this->getUser(), $uuid);

        if (null === $task) {
            return new JsonResponse('', JsonResponse::HTTP_NO_CONTENT, [], true);
        }

        $task->updateWith($taskRequest);

        $this->em->flush();

        return JsonResponse::fromJsonString(
            $this->serializer->serialize($task, 'json'),
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @Route("/{uuid}", name="delete_task", methods={Request::METHOD_DELETE})
     *
     * @SWG\Response(
     *     response=JsonResponse::HTTP_NO_CONTENT,
     *     description="Task is deleted"
     * )
     */
    public function delete(string $uuid): JsonResponse
    {
        $task = $this->em
            ->getRepository(Task::class)
            ->findByUserAndUUid($this->getUser(), $uuid);

        if (null !== $task) {
            $this->em->remove($task);
            $this->em->flush();
        }

        return new JsonResponse('', JsonResponse::HTTP_NO_CONTENT, [], true);
    }
}
