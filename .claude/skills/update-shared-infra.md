---
name: update-shared-infra
description: Update shared infrastructure profile with validation and reporting
tags: [composer, dependencies, shared-infra, drupal]
---

# Update Shared Infrastructure

Comprehensive workflow for updating `morgan-and-morgan/mm_drupal_core_d9_shared_infra` profile to the latest version.

## Overview

This skill safely updates the shared infrastructure profile with proper validation, configuration sync, and reporting. It handles version checking, code quality validation, and provides clear actionable feedback.

## Steps

### 1. Pre-Update Assessment

**Check Current State:**
- Read `composer.json` to get current version constraint
- Run `composer show morgan-and-morgan/mm_drupal_core_d9_shared_infra` to see installed version
- Check `composer.lock` for exact version hash
- Run `git status` to ensure clean working directory

**Store Baseline:**
- Note current version (e.g., "2.3.11")
- Capture current git branch
- Check if there are uncommitted changes (warn if yes)

### 2. Perform Update

**Execute Composer Update:**

This repo pins an exact version (e.g. `2.0.50`), so bump the constraint in
composer.json to the target version first, then update:
```bash
composer update morgan-and-morgan/mm_drupal_core_d9_shared_infra
```

**Capture Results:**
- Parse output for version change (old → new)
- Note any other packages updated as dependencies
- Check if update actually occurred (version changed)
- Capture any warnings or deprecation notices

**Handle Update Failures:**
- If composer fails, capture error message
- Check for dependency conflicts
- Suggest resolution steps
- Exit skill if unresolvable

### 3. Check for Breaking Changes

**Read Release Notes:**
- Check for `CHANGELOG.md` in profile: `web/profiles/contrib/mm_drupal_core_d9_shared_infra/CHANGELOG.md`
- Read updates between old and new version
- Flag any "BREAKING CHANGE", "BC Break", or major version bump

**Analyze Update Type:**
- Patch update (2.3.11 → 2.3.12): Low risk
- Minor update (2.3.x → 2.4.0): Medium risk, check for new features
- Major update (2.x → 3.0): High risk, requires careful review

### 4. Configuration Management

**Check for Config Changes:**
```bash
drush config:status
```

**If Config Drift Detected:**
- Run `drush config:import --preview` to see what would change
- List changed config files
- Ask user if they want to import now or defer

**Import Config (if user approves):**
```bash
drush config:import -y
```
- Capture any errors or warnings
- Report imported config count
- Note: config import normally happens on the Pantheon environment during
  deploy (`.ci/deploy/pantheon/dev-multidev`), not locally

### 5. Code Quality Validation

**Run PHPCS:**
```bash
composer code-sniff
```

**Analyze Results:**
- Count violations by severity (Errors vs Warnings)
- If new violations introduced, list affected files
- Determine if violations are in:
  - Shared infra code (flag for upstream fix)
  - Custom code (needs local fix)
  - Both (separate reports)

**Handle Violations:**
- If violations in custom code only: offer to auto-fix with `./vendor/bin/phpcbf --standard=Drupal <path>`
- If violations in shared infra: note for upstream issue
- If critical errors: recommend not proceeding until fixed

### 6. Test Validation (Optional)

**Check for Tests:**
- Look for PHPUnit tests in custom modules
- Check if CI runs tests (read `.github/workflows/build-deploy-test.yml`)

**Run Tests (if available):**
```bash
./vendor/bin/phpunit web/modules/custom/
```
- Report pass/fail status
- If failures, determine if related to update

### 7. Generate Comprehensive Report

**Report Structure:**

```markdown
# Shared Infrastructure Update Report

## Update Summary
- **Previous Version:** 2.3.11
- **New Version:** 2.3.12
- **Update Type:** Patch (low risk)
- **Status:** ✓ Success | ⚠ Warning | ✗ Failed

## Changes
- [List of updated packages with version changes]

## Breaking Changes
- [None found] OR [List with severity and impact]

## Configuration
- **Config Status:** X items require import | No changes
- **Action Taken:** Imported | Deferred pending review

## Code Quality
- **PHPCS Status:** ✓ No violations | ⚠ X warnings | ✗ Y errors
- **Violations in:** Custom modules | Shared infra | Both
- **Action Taken:** Auto-fixed | Manual review needed

## Tests
- **Status:** ✓ All passed (X tests) | ⚠ Some failed | Not run
- **Failures:** [None] | [List failed tests]

## Next Steps
1. [Prioritized list of required actions]
2. [Optional improvements]
3. [Notes about pushing to Pantheon]

## Additional Notes
- [Any warnings, deprecations, or concerns]
- [Recommendations for follow-up]
```

### 8. Git Integration

**Stage Changes:**
```bash
git add composer.json composer.lock
```

**Show Diff:**
```bash
git diff --staged
```

**Commit Message Template:**
Offer to create commit with message:
```
Update shared infra to release [version]

- Update mm_drupal_core_d9_shared_infra from [old] to [new]
- [List any notable changes or fixes]
- [Note config changes if any]
```

**Important:** Do NOT commit or push unless user explicitly requests it.

## Inputs

- **target_version** (optional): Specific version to update to (e.g., "2.4.0")
  - If not provided, updates to latest available
- **import_config** (optional): Auto-import config without prompting
  - Default: false (ask user)
- **run_tests** (optional): Whether to run tests
  - Default: false (skip tests unless explicitly requested)
- **auto_fix_phpcs** (optional): Auto-fix PHPCS violations
  - Default: false (report only)

## Outputs

- Structured update report (markdown)
- Version change summary
- List of action items for user
- Git staged changes (not committed)

## Error Handling

**Dependency Conflicts:**
- Parse composer error for conflicting packages
- Suggest using `composer why-not` for details
- Recommend resolution strategy

**Config Import Failures:**
- Capture specific config that failed
- Check for UUID mismatches
- Suggest using `--partial` flag

**PHPCS Failures:**
- Separate shared infra issues from custom code
- Provide file:line references for custom code
- Don't block update on warnings (only errors in custom code)

**Test Failures:**
- Determine if failures are pre-existing or new
- Run `git stash` and re-run tests to check baseline
- Report comparison

## Success Criteria

Update is considered successful if:
- Composer update completed without errors
- No critical PHPCS violations in custom code
- Config imported successfully (if changes exist)
- Tests pass (if run)
- User has clear action items

Update requires attention if:
- PHPCS violations in custom code
- Config changes need review
- Tests failed
- Breaking changes detected

## Edge Cases

**No Update Available:**
- Report "Already at latest version"
- Check if constraint in composer.json is too restrictive
- Exit gracefully

**Downgrade Scenario:**
- Detect if requested version is older than current
- Warn user about potential issues
- Require explicit confirmation

**Multiple Dependency Updates:**
- Report all updated packages
- Note if they're expected dependencies
- Flag unexpected updates for review

## Example Invocation

User says: "Update the shared infrastructure profile"

Claude uses:
```
Skill(
  skill: "update-shared-infra",
  args: ""
)
```

User says: "Update shared infra to version 2.4.0 and import config"

Claude uses:
```
Skill(
  skill: "update-shared-infra",
  args: "target_version=2.4.0 import_config=true"
)
```

## Related Commands

- `/update-profile` - Quick command version (less comprehensive)
- `/drupal-sniff` - Run PHPCS separately

## Notes

- This skill does NOT push to Pantheon automatically
- Always ask before committing changes
- Prioritize safety over speed
- Provide rollback instructions if major issues found
- Consider running in a git worktree for risky updates
