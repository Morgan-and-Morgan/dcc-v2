---
name: drupal-sniff
description: Run PHPCS on a specific module or file
---

# Drupal Code Sniffer Command

Run PHP CodeSniffer on a specific path in the project.

## Process

1. Ask the user which path to check (or use current context if obvious)
2. Run `composer code-sniff` (checks all custom modules + themes), or run
   `./vendor/bin/phpcs --standard=Drupal,DrupalPractice <path>` to scope to a
   single path
3. If errors are found, show them clearly with file:line references
4. Offer to auto-fix fixable violations with `./vendor/bin/phpcbf --standard=Drupal <path>`
5. Report the final results

## Options

- If user specifies a module name only (e.g., "dcc_common"), expand to full path: `web/modules/custom/dcc_common/`
- The custom theme lives at `web/themes/custom/dcc/`

## Success Criteria

- All PHPCS checks pass, or
- User is satisfied with the reported violations
