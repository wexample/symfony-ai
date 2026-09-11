<?php

namespace Wexample\SymfonyAi\Api\Controller\Entity;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Wexample\SymfonyAi\Api\Dto\Entity\Message\CreateMessageDto;
use Wexample\SymfonyAi\Api\Normalizer\Entity\Message\DefaultMessageNormalizer;
use Wexample\SymfonyAi\Entity\Message;
use Wexample\SymfonyAi\Repository\MessageRepository;
use Wexample\SymfonyAi\Repository\SessionRepository;
use Wexample\SymfonyApi\Api\Attribute\QueryOption\LengthQueryOption;
use Wexample\SymfonyApi\Api\Attribute\QueryOption\PageQueryOption;
use Wexample\SymfonyApi\Api\Attribute\QueryOption\StringQueryOption;
use Wexample\SymfonyApi\Api\Attribute\ValidateRequestContent;
use Wexample\SymfonyApi\Api\Class\ApiResponse;
use Wexample\SymfonyApi\Api\Controller\AbstractApiController;
use Wexample\SymfonyHelpers\Controller\AbstractController;

/**
 * What the conversation is read through.
 *
 * A thread is always asked for by its session: a message says nothing on its
 * own, and nothing here ever answers with the messages of the whole board.
 */
#[Route(path: 'api/message/', name: 'api_message_')]
class MessageController extends AbstractApiController
{
    final public const string QUERY_OPTION_SESSION = 'session';

    final public const string ROUTE_CREATE = 'create';

    final public const string ROUTE_LIST = 'list';

    /**
     * Writes a turn spoken by the operator, and says so.
     *
     * Nothing here answers it: what runs the conversation is the engine, and
     * reaching it is not this endpoint's business. The application that has one
     * listens to the event, and the line is written whether or not anybody does.
     */
    #[Route(path: 'create', name: self::ROUTE_CREATE, methods: [Request::METHOD_POST], options: AbstractController::ROUTE_OPTIONS_ONLY_EXPOSE)]
    #[ValidateRequestContent(dto: CreateMessageDto::class, attributeName: 'createMessageDto')]
    public function create(
        CreateMessageDto $createMessageDto,
        MessageRepository $messageRepository,
        SessionRepository $sessionRepository,
        DefaultMessageNormalizer $normalizer,
    ): ApiResponse {
        $session = $sessionRepository->find($createMessageDto->session);

        if (! $session) {
            return self::apiResponseError('Unknown session.');
        }

        return self::apiResponseSuccess(
            data: $normalizer->normalize(
                $messageRepository->saveNewMessageAndPushEvent(
                    $session,
                    Message::TYPE_USER,
                    $createMessageDto->body
                )
            )
        );
    }

    #[Route(path: 'list', name: self::ROUTE_LIST, methods: AbstractController::ROUTE_OPTIONS_METHOD_ONLY_GET, options: AbstractController::ROUTE_OPTIONS_ONLY_EXPOSE)]
    #[PageQueryOption]
    #[LengthQueryOption]
    #[StringQueryOption(key: self::QUERY_OPTION_SESSION, default: '')]
    public function list(
        Request $request,
        MessageRepository $messageRepository,
        SessionRepository $sessionRepository,
        DefaultMessageNormalizer $normalizer,
    ): ApiResponse {
        $session = $sessionRepository->find(
            self::getQueryOptionValue($request, self::QUERY_OPTION_SESSION, '')
        );

        if (! $session) {
            return self::apiResponseError('Unknown session.');
        }

        $builder = $messageRepository->queryBySessionOldestFirst($session);

        $pagination = self::getQueryOptionPagination(
            request: $request,
            total: $messageRepository->countAll($builder)
        );

        return self::apiResponsePaginated(
            pagination: $pagination,
            items: $normalizer->normalizeCollection(
                $messageRepository->findPaginated(
                    page: $pagination->page,
                    length: $pagination->length,
                    builder: $builder
                )
            )
        );
    }
}
