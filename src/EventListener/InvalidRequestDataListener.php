<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Exception\InvalidRequestData;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

final class InvalidRequestDataListener
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!($exception instanceof InvalidRequestData)) {
            return;
        }

        $response = [
            'type' => 'validation_error',
            'message' => $exception->getMessage(),
            'errors' => $exception->violations(),
        ];

        $event->setResponse(
            JsonResponse::create(
                $this->serializer->serialize($response, 'json'),
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            )
        );
    }
}
