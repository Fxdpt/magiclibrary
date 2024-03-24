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
    /**
     * @param ReadUserRepositoryInterface $readUserRepository
     * @param UserPasswordHasherInterface $passwordHasher
     * @param WriteSessionRepositoryInterface $writeSessionRepository
     */
    public function __construct(
        private readonly ReadUserRepositoryInterface $readUserRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly WriteSessionRepositoryInterface $writeSessionRepository
    ) {
    }

    /**
     * @param LoginRequest $request
     * @return JsonResponse
     */
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

            $token = substr(md5((string) rand()), 0, rand(10, 20));
            $expireAt = (new \DateTimeImmutable())->add(new DateInterval('PT2H'));

            if ($user->getId() === null) {
                throw UserException::invalidId();
            }

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
        } catch (\Throwable $ex) {
            return $ex instanceof UserException
                ? new JsonResponse($ex->getMessage(), Response::HTTP_BAD_REQUEST)
                : new JsonResponse('An error occurred while authenticating', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
