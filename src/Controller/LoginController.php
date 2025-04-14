<?php

namespace App\Controller;

use App\Controller\Payload\LoginPayload;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkNotification;

final class LoginController extends AbstractController
{
    #[Route(path: '/login', name: 'login', methods: ['POST'])]
    public function __invoke(
        #[MapRequestPayload()] LoginPayload $request,
        NotifierInterface $notifier,
        LoginLinkHandlerInterface $loginLinkHandler,
        UserRepository $userRepository
    ) {
        $user = $userRepository->findByEmail($request->email);
        if ($user === null) {
            return new JsonResponse(data: 'User not found', status: Response::HTTP_NOT_FOUND);
        }

        $loginLinkDetails = $loginLinkHandler->createLoginLink($user[0]);

        $notification = new LoginLinkNotification($loginLinkDetails, 'Welcome to MY WEBSITE');
        $recipient = new Recipient($user[0]->getEmail());

        $notifier->send($notification, $recipient);
        return new JsonResponse("An email with login link has been sent", Response::HTTP_OK);
    }
}