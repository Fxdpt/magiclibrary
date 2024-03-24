<?php

namespace MagicLibrary\Security\Authentication\Infrastructure\API\Login;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use MagicLibrary\Common\Exception\ValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use MagicLibrary\Security\Authentication\Application\UseCase\Login\Login;

final class LoginController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator
    ) {
    }

    public function __invoke(Login $useCase, Request $request): Response
    {
        try {
            $request = $this->createLoginRequest($request);

            return $useCase($request);
        } catch (\Throwable $ex) {
            return new JsonResponse('An error occurred while autheticating', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function createLoginRequest(Request $request): LoginRequest
    {
        $loginRequest = $this->serializer->deserialize($request->getContent(), LoginRequest::class, 'json');
        $errors = $this->validator->validate($loginRequest);
        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[$error->getPropertyPath()] = $error->getMessage();
        }
        if (! empty($errorMessages)) {
            throw new ValidationException(json_encode($errorMessages, JSON_THROW_ON_ERROR));
        }

        return $loginRequest;
    }
}
