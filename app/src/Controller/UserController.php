<?php

namespace App\Controller;

use App\Contracts\AuthenticationInterface;
use App\Service\PurchasedService;
use App\Service\UserService;
use App\Validators\AddProductValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController implements AuthenticationInterface
{
    private int $userId;

    public function __construct(
        private UserService $userService,
        private PurchasedService $purchasedService,
        private RequestStack $requestStack
    )
    {
        $session = $this->requestStack->getSession();
        $this->userId = $session->has('userId') ? $session->get('userId') : 0;
    }

    #[Route('/user', name: 'user')]
    public function userDetail(): Response
    {
        try {
            return $this->json($this->userService->getUserDetail($this->userId));
        } catch (\Exception $e) {
            return (new JsonResponse(["error" => $e->getMessage()], 403));
        }
    }

    #[Route('/user/products', name: 'user_products', methods: 'GET')]
    public function userProducts(): Response
    {
        try {
            return $this->json($this->purchasedService->getUserProducts($this->userId));
        } catch (\Exception $e) {
            return (new JsonResponse(["error" => $e->getMessage()], 403)); //TODO different error codes for different exceptions
        }
    }

    #[Route('/user/products', name: 'add_user_product', methods: 'POST')]
    public function addUserProduct(Request $request, AddProductValidator $validator): Response
    {
        try {
            $validator->validate($request->toArray());

            $this->purchasedService->addUserProduct($this->userId, $request->toArray()['sku']);

            return (new JsonResponse([], 200));

        } catch (\Exception $e) {
            return (new JsonResponse(["error" => $e->getMessage()], 403));
        }
    }

    #[Route('/user/products/{sku}', name: 'delete_user_product', methods: 'DELETE')]
    public function deleteUserProduct(Request $request): Response
    {
        $sku = $request->get('sku');
        if ($sku == '') {
            return (new JsonResponse(["error" => "Sku is required"], 403));
        }

        try {
            $this->purchasedService->deleteUserProduct($this->userId, $sku);
            return (new JsonResponse([], 200));

        } catch (\Exception $e) {
            return (new JsonResponse(["error" => $e->getMessage()], 403));
        }
    }
}
