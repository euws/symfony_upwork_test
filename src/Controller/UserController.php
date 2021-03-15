<?php

namespace App\Controller;

use App\Event\AccountPasswordChanged;
use App\Service\UserService;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class UserController extends ApiController
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function edit(Request $request, UserInterface $user, JWTTokenManagerInterface $JWTManager): JsonResponse
    {
        $request = $this->transformJsonBody($request);
        $username = $request->get('username');
        $email = $request->get('email');

        if (empty($username) || empty($email)){
            return $this->respondValidationError("Invalid Username or Email");
        }

        $user = $this->userService->editUser($user, $email,  $username);

        return new JsonResponse(['token' => $JWTManager->create($user)]);
    }

    public function changePassword(Request $request, UserInterface $user, EventDispatcherInterface $eventDispatcher): JsonResponse
    {
        $request = $this->transformJsonBody($request);
        $password = $request->get('password');

        if (empty($password)){
            return $this->respondValidationError("Invalid Password");
        }

        $user = $this->userService->changePassword($user, $password);

        $eventDispatcher->dispatch(new AccountPasswordChanged($user));

        return $this->respondWithSuccess(sprintf('User %s password successfully changed', $user->getUsername()));
    }
}
