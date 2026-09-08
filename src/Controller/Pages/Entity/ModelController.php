<?php

namespace Wexample\SymfonyAi\Controller\Pages\Entity;

use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Wexample\SymfonyAi\Entity\Model;
use Wexample\SymfonyAi\Entity\Traits\Manipulator\ModelEntityManipulatorTrait;
use Wexample\SymfonyAi\Repository\ModelRepository;
use Wexample\SymfonyAi\Traits\SymfonyAiBundleClassTrait;
use Wexample\SymfonyLoader\Controller\AbstractEntityPagesController;

/**
 * The models an agent can be pointed at, as the catalogue holds them.
 *
 * Read only: the list comes from what the package ships, so a model is neither
 * created nor edited from here.
 */
#[Route(path: 'model/', name: 'entity_model_')]
class ModelController extends AbstractEntityPagesController
{
    use ModelEntityManipulatorTrait;
    use SymfonyAiBundleClassTrait;

    final public const string ROUTE_INDEX = self::DEFAULT_ROUTE_NAME_INDEX;
    final public const string ROUTE_SHOW = self::DEFAULT_ROUTE_NAME_SHOW;

    #[Route(name: self::ROUTE_INDEX, path: '')]
    public function index(
        ModelRepository $modelRepository
    ): Response {
        return $this->renderPage(self::ROUTE_INDEX, [
            'models' => $modelRepository->findBy(
                [],
                [
                    'provider' => ModelRepository::SORT_ASC,
                    'name' => ModelRepository::SORT_ASC,
                ]
            ),
        ]);
    }

    #[Route(name: self::ROUTE_SHOW, path: '{id}/', options: self::ROUTE_OPTIONS_ONLY_EXPOSE)]
    public function show(
        #[MapEntity] Model $model
    ): Response {
        return $this->renderPage(self::ROUTE_SHOW, [
            'model' => $model,
        ]);
    }
}
