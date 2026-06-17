---
name: update-profile
description: Update shared infrastructure profile to latest
---

# Update Shared Infrastructure Profile

Update the `morgan-and-morgan/mm_drupal_core_d9_shared_infra` profile.

## Process

1. Check current version in composer.json (this repo pins an exact version,
   e.g. `2.0.50`)
2. Bump the version constraint in composer.json to the target version
3. Run `composer update morgan-and-morgan/mm_drupal_core_d9_shared_infra`
4. Show version change (old → new)
5. Run `composer code-sniff` to check for new violations
6. Report results and any action items

## Notes

- Don't push to Pantheon unless user explicitly asks
- Flag any major version changes
- Note if a config import is needed (`drush config:import`) after deploy
