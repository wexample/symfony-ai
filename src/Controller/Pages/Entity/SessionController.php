<?php

namespace Wexample\SymfonyAi\Controller\Pages\Entity;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Wexample\SymfonyAi\Entity\Session;
use Wexample\SymfonyAi\Entity\Traits\Manipulator\SessionEntityManipulatorTrait;
use Wexample\SymfonyAi\Service\FormProcessor\SessionFormProcessor;
use Wexample\SymfonyAi\Traits\SymfonyAiBundleClassTrait;
use Wexample\SymfonyForms\Attribute\EntityFormProcessor;
use Wexample\SymfonyLoader\Controller\AbstractEntityPagesController;

#[Route(path: 'session/', name: 'entity_session_')]
class SessionController extends AbstractEntityPagesController
{
    use SessionEntityManipulatorTrait;
    use SymfonyAiBundleClassTrait;

    final public const string ROUTE_EDIT = self::DEFAULT_ROUTE_NAME_EDIT;

    #[EntityFormProcessor(SessionFormProcessor::class, Session::class)]
    #[Route(name: self::ROUTE_EDIT, path: '{id}/'.self::ROUTE_EDIT, options: self::ROUTE_OPTIONS_ONLY_EXPOSE)]
    public function edit(
        FormInterface $sessionForm
    ): Response {
        return $this->renderPage(self::ROUTE_EDIT, [
            'form_edit' => $sessionForm->createView(),
        ]);
    }
}
