<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/messages')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class MessageController extends AbstractController
{
    #[Route('/', name: 'app_message_index', methods: ['GET'])]
    public function index(MessageRepository $messageRepository): Response
    {
        $user = $this->getUser();
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            throw $this->createAccessDeniedException('Admins cannot use messaging.');
        }

        // Logic to group messages by conversation partner
        // This is complex in Doctrine.
        // We want distinct conversation partners and the last message.
        // Simpler approach: Fetch all messages sent OR received by user, then group in PHP.
        // Optimized approach: Query builder.

        $allMessages = $messageRepository->createQueryBuilder('m')
            ->where('m.sender = :user OR m.recipient = :user')
            ->setParameter('user', $user)
            ->orderBy('m.sentAt', 'DESC')
            ->getQuery()
            ->getResult();

        $conversations = [];
        foreach ($allMessages as $msg) {
            $partner = ($msg->getSender() === $user) ? $msg->getRecipient() : $msg->getSender();
            $partnerId = $partner->getId();

            if (!isset($conversations[$partnerId])) {
                $conversations[$partnerId] = [
                    'partner' => $partner,
                    'lastMessage' => $msg,
                    'unreadCount' => 0
                ];
            }

            if ($msg->getRecipient() === $user && !$msg->isRead()) {
                $conversations[$partnerId]['unreadCount']++;
            }
        }

        return $this->render('message/index.html.twig', [
            'conversations' => $conversations,
        ]);
    }

    #[Route('/{id}', name: 'app_message_chat', methods: ['GET', 'POST'])]
    public function chat(User $partner, Request $request, EntityManagerInterface $em, MessageRepository $messageRepository): Response
    {
        $user = $this->getUser();
        if (in_array('ROLE_ADMIN', $user->getRoles()) || in_array('ROLE_ADMIN', $partner->getRoles())) {
            throw $this->createAccessDeniedException('Messaging with admins is restricted.');
        }

        if ($user === $partner) {
            return $this->redirectToRoute('app_message_index');
        }

        if ($request->isMethod('POST')) {
            $content = $request->request->get('content');
            if (!empty($content)) {
                $message = new Message();
                $message->setSender($user);
                $message->setRecipient($partner);
                $message->setContent($content);
                $message->setSentAt(new \DateTimeImmutable());
                $message->setIsRead(false);

                $em->persist($message);
                $em->flush();

                return $this->redirectToRoute('app_message_chat', ['id' => $partner->getId()]);
            }
        }

        // Mark messages as read
        $unreadMessages = $messageRepository->findBy([
            'sender' => $partner,
            'recipient' => $user,
            'isRead' => false
        ]);

        foreach ($unreadMessages as $msg) {
            $msg->setIsRead(true);
        }
        $em->flush();

        // Fetch conversation history
        $messages = $messageRepository->createQueryBuilder('m')
            ->where('(m.sender = :user AND m.recipient = :partner) OR (m.sender = :partner AND m.recipient = :user)')
            ->setParameter('user', $user)
            ->setParameter('partner', $partner)
            ->orderBy('m.sentAt', 'ASC') // Oldest first for chat view
            ->getQuery()
            ->getResult();

        return $this->render('message/chat.html.twig', [
            'partner' => $partner,
            'messages' => $messages,
        ]);
    }
}
