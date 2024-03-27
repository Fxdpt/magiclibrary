<?php

namespace MagicLibrary\Tests\Security\Authentication\Application\UseCase\Login;

use Symfony\Component\HttpFoundation\Response;
use MagicLibrary\Security\Authentication\Domain\Model\User;
use MagicLibrary\Security\Authentication\Domain\Exception\UserException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use MagicLibrary\Security\Authentication\Application\UseCase\Login\Login;
use MagicLibrary\Security\Authentication\Infrastructure\API\Login\LoginRequest;
use MagicLibrary\Security\Authentication\Application\Repository\ReadUserRepositoryInterface;
use MagicLibrary\Security\Authentication\Application\Repository\WriteSessionRepositoryInterface;

beforeEach(function () {
    $this->readUserRepository = $this->createMock(ReadUserRepositoryInterface::class);
    $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
    $this->writeSessionRepository = $this->createMock(WriteSessionRepositoryInterface::class);
});

it('Should return a Bad Request Response when the user is not found', function () {
    $useCase = new Login(
        $this->readUserRepository,
        $this->passwordHasher,
        $this->writeSessionRepository
    );

    $request = new LoginRequest();
    $request->email = 'test@test.fr';
    $request->password = 'mypassword';

    $this->readUserRepository
        ->method('findByEmail')
        ->willReturn(null);

    $response = $useCase($request);

    expect($response->getContent())->toBe('"' . UserException::authenticationFailed()->getMessage() . '"');
    expect($response->getStatusCode())->toBe(Response::HTTP_BAD_REQUEST);
});

it('Should return a Bad Request Response when the password is not valid', function () {
    $useCase = new Login(
        $this->readUserRepository,
        $this->passwordHasher,
        $this->writeSessionRepository
    );

    $request = new LoginRequest();
    $request->email = 'test@test.fr';
    $request->password = 'mypassword';

    $user = (new User('test@test.fr', 'test', ['ROLE_USER']))->setId(1);

    $this->readUserRepository
        ->method('findByEmail')
        ->willReturn($user);

    $this->passwordHasher
        ->method('isPasswordValid')
        ->willReturn(false);

    $response = $useCase($request);

    expect($response->getContent())->toBe('"' . UserException::authenticationFailed()->getMessage() . '"');
    expect($response->getStatusCode())->toBe(Response::HTTP_BAD_REQUEST);
});

it('Should return a Bad Request Response when the user id is invalid', function () {
    $useCase = new Login(
        $this->readUserRepository,
        $this->passwordHasher,
        $this->writeSessionRepository
    );

    $request = new LoginRequest();
    $request->email = 'test@test.fr';
    $request->password = 'mypassword';

    $user = (new User('test@test.fr', 'test', ['ROLE_USER']));

    $this->readUserRepository
        ->method('findByEmail')
        ->willReturn($user);

    $this->passwordHasher
        ->method('isPasswordValid')
        ->willReturn(true);

    $response = $useCase($request);

    expect($response->getContent())->toBe('"' . UserException::invalidId()->getMessage() . '"');
    expect($response->getStatusCode())->toBe(Response::HTTP_BAD_REQUEST);
});

it ('should return a OK Response when no error occurred', function () {
    $useCase = new Login(
        $this->readUserRepository,
        $this->passwordHasher,
        $this->writeSessionRepository
    );

    $request = new LoginRequest();
    $request->email = 'test@test.fr';
    $request->password = 'mypassword';

    $user = (new User('test@test.fr', 'test', ['ROLE_USER']))->setId(1);

    $this->readUserRepository
        ->method('findByEmail')
        ->willReturn($user);

    $this->passwordHasher
        ->method('isPasswordValid')
        ->willReturn(true);

    $response = $useCase($request);

    $body = json_decode($response->getContent(), true);
    expect($body)
        ->toBeArray()
        ->and($body)->toHaveKey('token')
        ->and($body)->toHaveKey('expire_at');
    expect($response->getStatusCode())->toBe(Response::HTTP_OK);
});