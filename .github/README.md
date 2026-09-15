# GitHub Actions CI/CD Setup Guide

Step-by-step instructions for setting up the GitHub Actions pipeline on this Drupal site, which uses the Pantheon build-tools workflow and the `mm-drupal-core-d9-shared-infra` private Composer dependency.

## Prerequisites

- Repo uses the Pantheon build-tools `.ci/` scripts
- `composer.json` references `mm-drupal-core-d9-shared-infra` via SSH URL
- Site is hosted on Pantheon with the Terminus build-tools plugin

## Workflows

| File | Purpose |
|---|---|
| `build-deploy-test.yml` | Static tests (hard gate), PHP build, deploy to Pantheon (dev on `master`, multidev on PR branches), then Playwright across 4 shards and a Slack summary (both `master` only) |
| `playwright-notify.yml` | Reusable; merges the per-shard blob reports and posts the summary to Slack via `scripts/parse-playwright-report.php` |
| `claude-code-review.yml` | Automated Claude review on PRs; `@claude` mention handler on comments/issues |

> End-to-end coverage is Playwright (`tests/playwright/`). The Behat suite it
> replaced has been removed, along with its `.ci/test/behat/` scripts and its
> `behat/mink-extension` and `dmore/behat-chrome-extension` dev dependencies.
>
> There is no scheduled/cron workflow in this repo yet. Adding one needs no
> change to `playwright-notify.yml` — it is already parameterised.

## 1. Site-Specific Values

- **Pantheon site machine name** — `dcc-v2`, which matches this repo's name, so
  `TERMINUS_SITE` is set from `github.event.repository.name` and needs no
  repository variable. Terminus is case-sensitive on site names.
- **Pantheon site UUID** — `85f7f5ec-40dd-48c5-9480-daae73fbb6a8`. No longer
  hardcoded in the workflow: the `deploy_to_pantheon` job resolves it at run
  time with `terminus site:info "$TERMINUS_SITE" --field=id` and fails with a
  diagnosable error if it cannot. A literal UUID silently rots if the site is
  ever rebuilt, so prefer leaving the lookup in place. The value is recorded
  here only for reference — find it in the Pantheon dashboard under Site
  Settings, or in the site's git clone URL.

## 2. Create SSH Key for Private Composer Repos

This allows Composer to clone `mm-drupal-core-d9-shared-infra` (or any other private GitHub dependency) during `composer install`.

```bash
ssh-keygen -t ed25519 -C "github-actions-dcc-v2-composer" -f composer_deploy_key -N ""
```

### Add the public key as a Deploy Key

1. Go to the **private dependency repo** (`morgan-and-morgan/mm-drupal-core-d9-shared-infra`)
2. Settings > Deploy keys > Add deploy key
3. Title: `dcc-v2 GitHub Actions`
4. Paste contents of `composer_deploy_key.pub`
5. Leave "Allow write access" **unchecked** (read-only is sufficient)

### Add the private key as a repo secret

1. Go to the **site repo** (`Morgan-and-Morgan/dcc-v2`)
2. Settings > Secrets and variables > Actions > New repository secret
3. Name: `SHARED_INFRA_SSH_KEY`
4. Value: paste the **entire** contents of `composer_deploy_key` (including `-----BEGIN` and `-----END` lines)

### Clean up

```bash
rm composer_deploy_key composer_deploy_key.pub
```

## 3. Create SSH Key for Pantheon Git Access

This allows the deploy job to `git push` built code to Pantheon's codeserver.

```bash
ssh-keygen -t rsa -b 4096 -C "github-actions-dcc-v2-pantheon" -f pantheon_key -N ""
```

> Pantheon recommends RSA keys for SSH access.

### Add the public key to Pantheon

1. Log in to the Pantheon Dashboard
2. Account > SSH Keys
3. Paste contents of `pantheon_key.pub`

> Note: SSH keys in Pantheon are per-user, not per-site. If you already have a CI SSH key registered, you can reuse it across repos.

### Add the private key as a repo secret

1. Go to the **site repo**
2. Settings > Secrets and variables > Actions > New repository secret
3. Name: `PANTHEON_SSH_KEY`
4. Value: paste the **entire** contents of `pantheon_key` (including `-----BEGIN` and `-----END` lines)

### Clean up

```bash
rm pantheon_key pantheon_key.pub
```

## 4. Remaining Secrets & Variables

These should already exist from CircleCI. Add them as GitHub Actions secrets on the site repo:

| Secret Name | Used By | Where to Get It |
|---|---|---|
| `TERMINUS_TOKEN` | deploy_to_pantheon | Pantheon Dashboard > Account > Machine Tokens |
| `ANTHROPIC_API_KEY` | claude-code-review | Anthropic Console |

> `GITHUB_TOKEN` is provided automatically by GitHub Actions — no setup needed.
> The Claude review workflow authenticates with this built-in token, so no
> GitHub App is required.

## 5. Repo Permissions

Ensure the repo's Actions permissions allow the workflow to run:

1. Go to the site repo > Settings > Actions > General
2. Under "Workflow permissions", select **Read and write permissions**
3. This is needed for the deploy job to post commit comments via the GitHub API

## Summary of All Secrets

| Secret | Used By | Purpose |
|---|---|---|
| `SHARED_INFRA_SSH_KEY` | build_php | SSH deploy key to clone private Composer dependencies from GitHub |
| `PANTHEON_SSH_KEY` | deploy_to_pantheon | SSH key to push built code to Pantheon's git codeserver |
| `TERMINUS_TOKEN` | deploy_to_pantheon | Machine token for Terminus API authentication |
| `ANTHROPIC_API_KEY` | claude-code-review | Anthropic API key for the Claude review action |

## Gotchas / Things We Learned

### GitHub Actions artifacts don't preserve file permissions
The `upload-artifact` / `download-artifact` actions strip execute bits. This pipeline avoids the issue by passing build output between jobs via `actions/cache` instead of artifacts. If you switch to artifacts, re-add a `chmod +x vendor/bin/* vendor/drush/drush/drush` step after download.

### Container jobs need safe.directory config
Running as `root` inside a container while the workspace is owned by the GitHub runner user triggers git's `safe.directory` check. The deploy job includes:
```yaml
- name: Fix git safe.directory for container
  run: git config --global --add safe.directory "$GITHUB_WORKSPACE"
```

### Deploy job needs full git history
The checkout in `deploy_to_pantheon` uses `fetch-depth: 0` because Terminus build tools need the full commit history to push to Pantheon.

### CIRCLE_* environment variables
The workflow sets `CIRCLE_*` env vars alongside `CI_*` vars for backwards compatibility with the Pantheon build-tools `.ci/` scripts, which may reference either convention.

## Pipeline Overview

```
push to master / PR
  build_php ──────────────── deploy_to_pantheon
  (composer, dcc theme)      (dev on master, multidev on PR branches)

PRs also trigger claude-code-review (auto PR review + @claude handler)
```
