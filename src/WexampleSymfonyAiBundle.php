<?php

namespace Wexample\SymfonyAi;

use Wexample\SymfonyHelpers\Class\AbstractBundle;

/**
 * What an application offers to an agent: the tools it runs for it on the
 * server (AgentBackToolInterface), the pages it opens to it (AgentRoute), and
 * what it lets it use (the `agent_tools` configuration). Nothing of the
 * conversations themselves, which an engine or `symfony-ai-conversation`
 * keeps.
 */
class WexampleSymfonyAiBundle extends AbstractBundle
{
}
