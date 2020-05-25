<?php

declare(strict_types=1);

namespace App\Resolver;

use App\Exception\InvalidRequestData;
use App\RequestObject\RequestObject;
use function count;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class RequestObjectResolver implements ArgumentValueResolverInterface
{
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;

    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return is_subclass_of($argument->getType(), RequestObject::class);
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $data = $request->request->all();

        if ($request->isMethod(Request::METHOD_GET)) {
            $data = $request->query->all();
        }

        $dto = $this->serializer->deserialize(
            $this->serializer->serialize($data, 'json'),
            $argument->getType(),
            'json'
        );

        $this->validateDTO($dto);

        yield $dto;
    }

    private function validateDTO(RequestObject $dto): void
    {
        $errors = $this->validator->validate($dto);

        if (0 !== count($errors)) {
            throw InvalidRequestData::with($errors);
        }
    }
}
