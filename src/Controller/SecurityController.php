<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\RequestObject\RegisterUserRequest;
use Doctrine\ORM\EntityManagerInterface;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class SecurityController
{
    private EntityManagerInterface $em;
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/register", name="register", methods={Request::METHOD_POST})
     *
     * @SWG\Parameter(
     *     name="username",
     *     in="formData",
     *     type="string",
     *     description="Username",
     *     required=true
     * )
     *
     * @SWG\Parameter(
     *     name="password",
     *     in="formData",
     *     type="string",
     *     description="Password",
     *     required=true
     * )
     *
     * @SWG\Response(
     *     response=Response::HTTP_CREATED,
     *     description="Returns 201 when user successfully created."
     * )
     *
     * @SWG\Tag(name="Security")
     */
    public function register(RegisterUserRequest $userRequest): JsonResponse
    {
        $this->em->persist(
            User::with($userRequest, $this->passwordEncoder)
        );
        $this->em->flush();

        return JsonResponse::create(
            null,
            Response::HTTP_CREATED
        );
    }
}
