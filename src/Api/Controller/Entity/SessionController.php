<?php

namespace Wexample\SymfonyAi\Api\Controller\Entity;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Wexample\SymfonyAi\Api\Normalizer\Entity\Session\DefaultSessionNormalizer;
use Wexample\SymfonyAi\Repository\AgentRepository;
use Wexample\SymfonyAi\Repository\SessionRepository;
use Wexample\SymfonyApi\Api\Attribute\QueryOption\LengthQueryOption;
use Wexample\SymfonyApi\Api\Attribute\QueryOption\PageQueryOption;
use Wexample\SymfonyApi\Api\Attribute\QueryOption\StringQueryOption;
use Wexample\SymfonyApi\Api\Class\ApiResponse;
use Wexample\SymfonyApi\Api\Controller\AbstractApiController;
use Wexample\SymfonyHelpers\Controller\AbstractController;

/**
 * What the conversations are listed through.
 *
 * A listing is always asked for by the agent that held the conversations: the
 * board has no page showing everybody's threads at once, and this answers none.
 */
#[Route(path: 'api/session/', name: 'api_session_')]
class SessionController extends AbstractApiController
{
    final public const string QUERY_OPTION_AGENT = 'agent';

    final public const string ROUTE_LIST = 'list';

    #[Route(path: 'list', name: self::ROUTE_LIST, methods: AbstractController::ROUTE_OPTIONS_METHOD_ONLY_GET, options: AbstractController::ROUTE_OPTIONS_ONLY_EXPOSE)]
    #[PageQueryOption]
    #[LengthQueryOption]
    #[StringQueryOption(key: self::QUERY_OPTION_AGENT, default: '')]
    public function list(
        Request $request,
        SessionRepository $sessionRepository,
        AgentRepository $agentRepository,
        DefaultSessionNormalizer $normalizer,
    ): ApiResponse {
        $agent = $agentRepository->find(
            self::getQueryOptionValue($request, self::QUERY_OPTION_AGENT, '')
        );

        if (! $agent) {
            return self::apiResponseError('Unknown agent.');
        }

        $builder = $sessionRepository->queryByAgent($agent);

        $pagination = self::getQueryOptionPagination(
            request: $request,
            total: $sessionRepository->countAll($builder)
        );

        return self::apiResponsePaginated(
            pagination: $pagination,
            items: $normalizer->normalizeCollection(
                $sessionRepository->findPaginated(
                    page: $pagination->page,
                    length: $pagination->length,
                    builder: $builder
                )
            )
        );
    }
}
