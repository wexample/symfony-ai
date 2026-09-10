<?php

namespace Wexample\SymfonyAi\Service\FormProcessor;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Wexample\SymfonyAi\Entity\Session;
use Wexample\SymfonyForms\Service\FormProcessor\AbstractFormProcessor;

/**
 * Saves an edited session, and knows only one place to save it: the database.
 *
 * That is enough where the row is the record. It is not where the session was
 * read from somewhere else — a file, an API — since the next read would put
 * the old name back; such an application replaces this service by a subclass
 * of it, overriding `save()` alone.
 */
class SessionFormProcessor extends AbstractFormProcessor
{
    public function __construct(
        FormFactoryInterface $formFactory,
        RequestStack $requestStack,
        UrlGeneratorInterface $urlGenerator,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct($formFactory, $requestStack, $urlGenerator);
    }

    public function onValid(FormInterface $form): void
    {
        /** @var Session $session */
        $session = $form->getData();

        $this->save($session);

        $this->setSuccessAction(['type' => self::ACTION_DEFAULT]);
        $this->setNotification('@form::success.message');
    }

    /**
     * Where an edited session goes. The one thing an application changes here.
     */
    protected function save(Session $session): void
    {
        $this->entityManager->persist($session);
        $this->entityManager->flush();
    }
}
