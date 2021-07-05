<?php

namespace App\Controller;

use App\Service\UserService;
use App\Validators\LoginValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    public function __construct(
        private UserService $userService
    ){

    }

    #[Route('/auth', name: 'auth',methods: 'POST')]
    public function auth(Request $request, LoginValidator $validator): Response
    {
        try {
            $validator->validate($request->toArray());

            $this->userService->loginUser($request->toArray()['email'], $request->toArray()['password']);

            return (new JsonResponse([],200));

        }catch (\Exception $e) {
            return (new JsonResponse(["error" => $e->getMessage()],403)); //TODO different error codes for different exceptions
        }
    }

    #[Route('/logout', name: 'logout')]
    public function logout(): Response
    {
        $this->userService->logoutUser();
        return (new JsonResponse([],200));
    }
}
