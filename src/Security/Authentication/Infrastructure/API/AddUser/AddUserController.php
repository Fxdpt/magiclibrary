<?php

namespace MagicLibrary\Security\Authentication\Infrastructure\API\AddUser;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use MagicLibrary\Common\Exception\ValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use MagicLibrary\Security\Authentication\Application\UseCase\AddUser\AddUser;

final class AddUserController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator
    ) {
    }

    public function __invoke(AddUser $useCase, Request $request): Response
    {
        try {
            $request = $this->createAddUserRequest($request);

            return $useCase($request);
        } catch (ValidationException $ex) {
            return new JsonResponse(json_decode($ex->getMessage()), Response::HTTP_BAD_REQUEST);
        } catch (\Throwable) {
            return new JsonResponse('Unable to decode request', Response::HTTP_BAD_REQUEST);
        }
    }

    private function createAddUserRequest(Request $request): AddUserRequest
    {
        $addUserRequest = $this->serializer->deserialize($request->getContent(), AddUserRequest::class, 'json');
        $errors = $this->validator->validate($addUserRequest);
        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[$error->getPropertyPath()] = $error->getMessage();
        }
        if (! empty($errorMessages)) {
            throw new ValidationException(json_encode($errorMessages, JSON_THROW_ON_ERROR));
        }

        return $addUserRequest;
    }
}
