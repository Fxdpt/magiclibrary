<?php

namespace MagicLibrary\Security\Authentication\Application\UseCase\AddUser;

use Symfony\Component\HttpFoundation\JsonResponse;
use MagicLibrary\Security\Authentication\Domain\Model\User;
use MagicLibrary\Security\Authentication\Domain\Exception\UserException;
use MagicLibrary\Security\Authentication\Infrastructure\API\AddUser\AddUserRequest;
use MagicLibrary\Security\Authentication\Application\Repository\ReadUserRepositoryInterface;
use MagicLibrary\Security\Authentication\Application\Repository\WriteUserRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AddUser
{
    public function __construct(
        private readonly ReadUserRepositoryInterface $readUserRepository,
        private readonly WriteUserRepositoryInterface $writeUserRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function __invoke(AddUserRequest $request): JsonResponse
    {
        try {
            $this->validateUniqueUser($request->email);
            $user = new User(
                $request->email,
                $request->username,
                []
            );
            $user->setPassword($this->passwordHasher->hashPassword($user, $request->password));

            $this->writeUserRepository->add($user);

            return new JsonResponse(null, Response::HTTP_CREATED);
        } catch (\Throwable $ex) {
            return $ex instanceof UserException
                ? new JsonResponse($ex->getMessage(), Response::HTTP_BAD_REQUEST)
                : new JsonResponse('An  error occurred while creating user', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function validateUniqueUser(string $email): void
    {
        if ($this->readUserRepository->findByEmail($email) !== null) {
            throw UserException::emailAlreadyUsed($email);
        }
    }
}
