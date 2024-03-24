<?php

namespace MagicLibrary\Security\Authentication\Application\UseCase\Login;

use DateInterval;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use MagicLibrary\Security\Authentication\Domain\Exception\UserException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use MagicLibrary\Security\Authentication\Infrastructure\API\Login\LoginRequest;
use MagicLibrary\Security\Authentication\Application\Repository\ReadUserRepositoryInterface;
use MagicLibrary\Security\Authentication\Application\Repository\WriteSessionRepositoryInterface;
use MagicLibrary\Security\Authentication\Domain\Model\Session;

final class Login
{
    public function __construct(
        private readonly ReadUserRepositoryInterface $readUserRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly WriteSessionRepositoryInterface $writeSessionRepository
    ) {
    }

    public function __invoke(LoginRequest $request): JsonResponse
    {
        try {
            $user = $this->readUserRepository->findByEmail($request->email);
            if (
                $user === null
                || ! $this->passwordHasher->isPasswordValid($user, $request->password)
            ) {
                throw UserException::authenticationFailed();
            }

            $token = substr(md5(rand()), 0, rand(10,20));
            $expireAt = (new \DateTimeImmutable())->add(new DateInterval('PT2H'));

            $session = new Session(
                $user->getId(),
                $token,
                $expireAt
            );

            $this->writeSessionRepository->add($session);

            return new JsonResponse([
                'token' => $token,
                'expire_at' => $expireAt
            ]);
        } catch (UserException $ex) {
            return new JsonResponse($ex->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
