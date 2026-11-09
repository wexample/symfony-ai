# What an application offers to an agent

An agent talking to the user from a chat of the page acts through two kinds of tools: the page's own, run in the browser (symfony-loader, *What an agent may do in a page*), and the server's, run here as the user. The agent reads one list, `list_tools`, and the application decides what is in it.

## Tools of the server

A service implementing `AgentBackToolInterface` is a tool: a name, unique across the page's tools and the server's; an english description and a JSON schema, as the page's contracts; annotations (`readOnlyHint`, `destructiveHint`); `execute(array $arguments)`, whose return is what the agent reads, an exception its error. The bundle tags it, nothing else to declare.

- `whoami`: who the user is — account, roles, language.
- `list_pages`: the pages offered to agents that the user may open, each with what it is for. A page is offered with `#[AgentRoute]` on its controller, on the method or on the class for all its pages, and says what it is for with `#[RouteDescription('…')]` (symfony-helpers). An unmarked page is never listed, however open; a marked one without a description is left out, and the log says so. Then `RouteAccessService` (the design system's) keeps the pages the firewall lets this user through.

`ai:agent-tools` lists the tools of the server; `--run=list_pages --arguments='{"query":"model"}' --user=…` runs one as an account.

## The interception

The page never hears of the server's tools. An agent's call reaches the application before the page — a callback, a worker saving it —, and the application asks `AgentBackToolsService`:

- `handles($call)`: a call to a tool of the server, directly or through `call_tool` — or any call, the page's tools being off. The application marks it (`markAnsweredHere()`, `runBy: 'back'`) before publishing it, so the chat shows it and does not run it; then `answer($call, $owner)` runs it as the conversation's owner, the user's token in place while it runs, and the application sends the answer to the engine as it sends the page's.
- `completeToolList($call, $results)`: the page answers `list_tools` with its own tools; on its way to the engine, the application completes that answer with the server's, unless the agent asked for `scope: 'front'`.

Two applications do it today, each where its messages pass: `symfony-ai-conversation` (`AgentBackToolListener`, on the tool messages any engine writes, and its message endpoint for `list_tools`) and the Syrtis manager (`AgentBackToolRelay`, in the worker publishing saved messages).

## One turn, one sending

An agent may call several tools at once, and their answers arrive one by one. Each sent alone would open a turn of its own, and the conversation would go on as several agents, each seeing one answer. `AgentTurnCollector::collect($turn, $callIds, $callId, $answer, $with, $send)` keeps an answer until every call of its turn has one, then hands them all, in the order of the calls, to `$send` — once, whatever answers a call twice. The application names the turn and lists its calls; what waits, and the lock taken to add to it, live under the project's `var/`, which the server and its workers share.

## What the application lets the agents use

```yaml
wexample_symfony_ai:
    agent_tools:
        front: true           # the page's tools
        back: true            # the server's tools, and the interception
        disabled: [navigate]  # tools off by name, whichever side runs them
```

- `disabled` takes a tool out of `list_tools` and refuses its calls, on the page — the layout's render data carries the list, `agentTools`, through `LayoutAgentToolsProvider` — and on the server alike.
- `back: false` leaves every call to the page: nothing is intercepted, `list_tools` is not completed.
- `front: false` leaves every call to the server: it answers `list_tools` with its own tools and any other call itself, the page only showing them.
