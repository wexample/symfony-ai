# symfony-ai: finish the scaffolding before any feature work

## Status of the package

Freshly created, one `Initial commit`. Nothing but the wex/doc skeleton is
tracked: `src/` and `tests/` do not appear in `git ls-files` at all.

Its purpose has **not** been discussed — no design decision is being handed
over here. Defining what this package holds is the first thing to settle, and
it belongs to whoever picks this up.

## Blocking gaps

Diffed against `symfony-wex`, a comparable sibling bundle:

1. `composer.json` is skeletal (`{name, version}` only). Missing `type`,
   `license`, `authors`, `require` (php constraint + `wexample/symfony-helpers`)
   and above all `autoload` — **no PSR-4 entry, so nothing under `src/` is
   loadable by Composer**. Reference:

   ```json
   "autoload": { "psr-4": { "Wexample\\SymfonyAi\\": "src/" } }
   ```

2. No Symfony bundle infrastructure: no `src/WexampleSymfonyAiBundle.php`, no
   `src/DependencyInjection/` (`Configuration.php` + `WexampleSymfonyAiExtension.php`),
   no `src/Resources/config/services.yaml`. Every sibling bundle has these three.

3. `src/Entity/` and `src/Entity/Traits/Manipulator/` exist but are empty and
   untracked (no `.gitkeep`). `src/Entity/` is owned by `root:root` instead of
   the normal user — created from inside a Docker container. `chown` it before
   writing files there.

4. `tests/` is empty, no `.gitkeep`.

Non-blocking: `.wex/ai/agents/main/agent.yml` is absent (siblings have one), and
the README/knowledge pages still say "The repository does not provide any
concrete code that could be documented for now" — both resolve on their own once
there is code.
