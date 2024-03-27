<?php

namespace MagicLibrary\Tests\Security\Authentication\Application\UseCase\AddUser;

use MagicLibrary\Security\Authentication\Application\Repository\ReadUserRepositoryInterface;
use MagicLibrary\Security\Authentication\Application\Repository\WriteUserRepositoryInterface;
use MagicLibrary\Security\Authentication\Application\UseCase\AddUser\AddUser;
use MagicLibrary\Security\Authentication\Domain\Exception\UserException;
use MagicLibrary\Security\Authentication\Domain\Model\User;
use MagicLibrary\Security\Authentication\Infrastructure\API\AddUser\AddUserRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

beforeEach(function () {
    $this->readUserRepository = $this->createMock(ReadUserRepositoryInterface::class);
    $this->writeUserRepository = $this->createMock(WriteUserRepositoryInterface::class);
    $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
});

it('Should return a Bad Request when the user already exists', function () {
    $useCase = new AddUser(
        $this->readUserRepository,
        $this->writeUserRepository,
        $this->passwordHasher
    );

    $request = new AddUserRequest();
    $request->email = 'test@test.fr';
    $request->password = 'mypassword';

    $user = (new User('test@test.fr', 'test', ['ROLE_USER']))->setId(1);

    $this->readUserRepository
        ->method('findByEmail')
        ->willReturn($user);

    $response = $useCase($request);

    expect($response->getContent())->toBe('"' . UserException::emailAlreadyUsed('test@test.fr')->getMessage() . '"');
    expect($response->getStatusCode())->toBe(Response::HTTP_BAD_REQUEST);
});

it('Should return a Internal Server Error with generic message when an uncontrolled error occurred', function () {
    $useCase = new AddUser(
        $this->readUserRepository,
        $this->writeUserRepository,
        $this->passwordHasher
    );

    $request = new AddUserRequest();
    $request->email = 'test@test.fr';
    $request->password = 'mypassword';

    $this->readUserRepository
        ->method('findByEmail')
        ->willReturn(null);

    $this->writeUserRepository
        ->method('add')
        ->willThrowException(new \Exception());

    $response = $useCase($request);

    expect($response->getContent())->toBe('"An error occurred while creating user"');
    expect($response->getStatusCode())->toBe(Response::HTTP_INTERNAL_SERVER_ERROR);
});

it('Should return a Created Response when no errors occurred', function () {
    $useCase = new AddUser(
        $this->readUserRepository,
        $this->writeUserRepository,
        $this->passwordHasher
    );

    $request = new AddUserRequest();
    $request->email = 'test@test.fr';
    $request->password = 'mypassword';

    $this->readUserRepository
        ->method('findByEmail')
        ->willReturn(null);

    $response = $useCase($request);

    expect($response->getContent())->toBe('{}');
    expect($response->getStatusCode())->toBe(Response::HTTP_CREATED);
});