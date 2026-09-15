# CLAUDE.md — DCC-V2 Project Reference

## Project Overview

- **Framework:** Drupal 9/10 (Composer-managed)
- **Site:** https://www.disabilitycarecenter.org
- **Hosting:** Pantheon — site machine name `dcc-v2` (matches the GitHub repo
  name, so `TERMINUS_SITE` follows from the repo name and needs no repository
  variable; note Terminus is case-sensitive on site names)
- **Environments:** dev → `https://dev-dcc-v2.pantheonsite.io`;
  feature branches → `https://<branch>-dcc-v2.pantheonsite.io`
- **CI/CD:** GitHub Actions (`.github/workflows/build-deploy-test.yml`)
- **Local Dev:** DDEV (`.ddev/`) — PHP 8.3, MariaDB 10.4, Node 18
- **PHP:** 8.3
- **Custom Theme:** `dcc` (`web/themes/custom/dcc`, npm/node-sass build)
- **Shared Infra:** `morgan-and-morgan/mm_drupal_core_d9_shared_infra`
- **E2E tests:** Playwright (`tests/playwright/`)

> Node stays pinned at 18: the theme's `node-sass` compiles against a specific
> Node ABI and does not build on current Node. The PHP 8.3 bump does not move it.

### Key Paths

| Path | Purpose |
|------|---------|
| `web/modules/custom/` | Custom Drupal modules (`dcc_common`, `dcc_general`, `dcc_migrations`) |
| `web/themes/custom/dcc/` | Primary custom theme |
| `web/profiles/contrib/mm_drupal_core_d9_shared_infra/` | Shared infrastructure profile |
| `config/` | Drupal configuration exports |
| `.ci/` | Pantheon build-tools CI scripts (build, deploy, test) |

---

## Linters & Code Quality Tools

### PHP — PHPCS (Drupal Coder)

- **Standards:** `Drupal` + `DrupalPractice` (via `drupal/coder`)
- **Extensions checked:** php, module, inc, install, test, profile, theme, css, info, txt, md
- **Run:** `composer code-sniff` (custom modules + themes)
- **Lint syntax:** `composer lint`

### Theme Build — `dcc`

- **Build:** `composer build-theme-dcc` (`npm install` → `npm rebuild node-sass` → `npm run build` in `web/themes/custom/dcc`)
- **Full production build:** `composer build-assets` (composer install --no-dev → build theme)

---

## PHP Coding Standards (Drupal)

### File Structure
```php
<?php

/**
 * @file
 * Brief description of the file.
 */
```
- No closing `?>` tag
- `@file` docblock immediately after opening tag (procedural files only — `.module`, `.install`, `.theme`, `.inc`, `.profile`). Class files use the class-level docblock instead.

### Naming Conventions
- **Functions (procedural):** `snake_case` prefixed with module name — `dcc_common_preprocess_page()`
- **Methods (OOP):** `camelCase` — `getFormId()`, `buildForm()`, `submitForm()`
- **Classes:** `PascalCase`
- **Variables:** `snake_case`
- **Constants:** `UPPER_SNAKE_CASE`
- **Properties:** `$camelCase` or `$snake_case` (follow surrounding code)

### Docblocks (PSR-5 / Drupal style)
```php
/**
 * Short description in imperative mood.
 *
 * @param \Drupal\Core\Entity\EntityInterface $entity
 *   Description of the parameter.
 * @param string|null $optional_param
 *   (optional) Description. Defaults to NULL.
 *
 * @return array
 *   Description of return value.
 */
```
- Hook implementations: `Implements hook_name().`
- Inherited methods: `{@inheritdoc}`

### Type Hints & Return Types
- Use PHP 8.3 type hints for all parameters and return types
- Trailing commas in multi-line parameter lists
- Nullable types with `?Type` or union `Type|null`

### Dependency Injection
- Constructor injection in classes (Forms, Controllers, Services)
- Implement `ContainerInjectionInterface` or extend base classes
- Static `create()` method for container-aware instantiation
- In procedural code, use `\Drupal::service('service_name')` when DI is not available

### Namespace & Use Statements
```php
namespace Drupal\module_name\Form;

use Drupal\Core\Cache\CacheTagsInvalidator;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
```
- Alphabetically ordered
- All dependencies explicitly imported
- Fully qualified in docblocks: `@param \Drupal\Core\Entity\EntityInterface`

### Error Handling
```php
try {
  // ...
}
catch (\Exception $e) {
  \Drupal::logger('module_name')->error('Message: @message', [
    '@message' => $e->getMessage(),
  ]);
}
```
- `catch` on its own line (Drupal standard)
- Use Drupal logger for error reporting
- Use `@placeholder` syntax in log messages

### Arrays
- Trailing commas in multi-line arrays
- One element per line for complex/nested arrays
- Short array syntax `[]` (not `array()`)

---

## JavaScript Coding Standards

### Pattern: Drupal Behaviors (IIFE)
```javascript
(function ($, Drupal, drupalSettings, once) {
  'use strict';

  Drupal.behaviors.behaviorName = {
    attach: function (context) {
      once('unique-id', '.selector', context).forEach(function (element) {
        // Behavior logic
      });
    },
  };
})(jQuery, Drupal, drupalSettings, once);
```

### Naming & Formatting
- **Variables & functions:** `camelCase`
- **Behavior names:** `camelCase` — `Drupal.behaviors.dccGlobal`
- 2-space indentation, single quotes, semicolons required, trailing commas
- `once()` for one-time DOM attachment (replaces jQuery.once)

---

## Twig Template Standards

### File Header
```twig
{#
/**
 * @file
 * Template for [component description].
 *
 * Available variables:
 * - variable_name: Description.
 */
#}
```

### Conventions
- **Variable names:** `snake_case` — `page.header`, `site_logo`
- **Indentation:** 2 spaces
- **String concatenation:** `~` operator
- **Conditional classes:** `{% set classes = 'base-class' ~ (condition ? ' modifier' : '') %}`
- **Comments:** `{# inline comment #}`

---

## SCSS/CSS Standards

### Naming
- **Classes:** kebab-case with BEM-like modifiers
- **CSS Custom Properties:** `var(--...)`
- **SCSS imports:** prefer modern `@use` syntax

### Structure
- 2-space indentation
- Source files compiled via the theme's npm build into `dist/`
- Organized by concern (`components/`, `layout/`, etc.)

---

## Build Commands

| Command | Purpose |
|---------|---------|
| `composer code-sniff` | Run PHPCS on custom modules + themes |
| `composer lint` | PHP syntax lint of custom modules + themes |
| `composer unit-test` | Run unit tests (currently a no-op placeholder) |
| `composer build-theme-dcc` | Build the `dcc` theme (`npm install` + `npm run build`) |
| `composer build-assets` | Full production build (composer install --no-dev + theme build) |
| `composer nuke-rebuild` | Wipe and rebuild site dependencies + theme |

---

## Local Development (DDEV)

This project uses [DDEV](https://ddev.readthedocs.io/) for local development
(config in `.ddev/`). The local site is served at `https://dcc-v2.ddev.site`.

| Command | Purpose |
|---------|---------|
| `ddev start` | Boot the local containers |
| `ddev composer install` | Install PHP dependencies inside the web container |
| `ddev pull pantheon` | Pull the database + files from Pantheon (needs `TERMINUS_MACHINE_TOKEN` in `~/.ddev/global_config.yaml`) |
| `ddev composer build-theme-dcc` | Build the `dcc` theme |
| `ddev drush <cmd>` | Run Drush against the local site (e.g. `ddev drush cr`) |
| `ddev launch` | Open the local site in a browser |

Pantheon DB/file sync is configured in `.ddev/providers/pantheon.yaml`.

---

## CI Pipeline (GitHub Actions)

Workflows live in `.github/workflows/`:

1. **build-deploy-test.yml** — on push to `master` and on PRs:
   - `build_php` — `composer build-assets` (via `.ci/build/php`)
   - `deploy_to_pantheon` — push built artifact to Pantheon (dev on master, multidev otherwise)
2. **claude-code-review.yml** — automated Claude review on PRs; `@claude` mention handler on comments/issues.

See `.github/README.md` for the secrets/setup guide.

---

## Rules for Claude

1. **Always follow Drupal coding standards** — code must pass `composer code-sniff`
2. **PHP files:** No closing tag; `@file` docblock only on procedural files; snake_case functions with module prefix; full docblocks on all functions/methods
3. **Use type hints** on all PHP parameters and return types
4. **JavaScript:** Use Drupal behaviors IIFE pattern, `once()` for DOM attachment, 2-space indent, single quotes, trailing commas
5. **Twig:** 2-space indent, snake_case variables, document available variables in file header
6. **SCSS:** kebab-case classes, BEM modifiers, CSS custom properties, `@use` imports, 2-space indent
7. **Never modify** files in `web/core/`, `vendor/`, `web/modules/contrib/`, or `web/themes/contrib/` — these are managed by Composer
8. **Custom code only** lives in `web/modules/custom/`, `web/themes/custom/`, and `config/`
9. **Hooks go in `.module` files**, classes in `src/` directory following PSR-4
10. **Use dependency injection** in classes; only use `\Drupal::` static calls in procedural `.module`/`.theme` files
11. **Catch blocks** go on their own line (Drupal standard, not same-line as closing brace)
12. **Log errors** with `\Drupal::logger()` using `@placeholder` syntax
13. **YAML indentation** is 2 spaces
