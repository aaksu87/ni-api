<?php

namespace App\Service;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class UserService
{

    public function __construct(
        private UserRepository $userRepository, //TODO send repositoryInterfaces instead of repositories directly
        private RequestStack $requestStack
    )
    {
    }

    /**
     * @param string $email
     * @param string $password
     * @throws \Exception
     */
    public function loginUser(string $email, string $password): void
    {
        $user = $this->userRepository->findOneBy(['email' => $email, 'password' => $password]);
        if (!$user) {
            throw new \Exception('Invalid email or password');
        }

        $session = $this->requestStack->getSession();
        $session->set('isLogin', 1);
        $session->set('userId', $user->getId());
    }

    public function logoutUser(): void
    {
        $session = $this->requestStack->getSession();
        $session->clear();
    }

    /**
     * @param int $userId
     * @return array
     * @throws \Exception
     */
    public function getUserDetail(int $userId): array
    {
        $user = $this->userRepository->find($userId);
        if (!$user) {
            throw new \Exception('Could not find user');
        }

        return [
            'name' => $user->getName()
        ];
    }


}
