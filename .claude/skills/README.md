# Custom Skills

Reusable skills and macros for common FTP-V3 workflows.

## What are Skills?

Skills are reusable workflows that can be invoked by name using the Skill tool. Unlike commands (which are user-facing slash commands), skills are invoked by Claude during task execution. They're perfect for complex, multi-step operations that you perform frequently.

## Structure

Each skill is a markdown file with frontmatter:

```markdown
---
name: skill-name
description: Brief description of what this skill does
tags: [tag1, tag2]
---

# Skill Name

Detailed instructions for Claude on how to execute this skill.

## Steps

1. First step with specific tool calls
2. Second step with conditions
3. Final step with validation

## Inputs

- input1: Description
- input2: Description (optional)

## Outputs

Description of what this skill produces or accomplishes.

## Examples

Example invocations and expected results.
```

## Example Skills

### Module Validator

```markdown
---
name: validate-module
description: Validate a Drupal custom module's structure and code quality
tags: [drupal, validation, phpcs]
---

# Validate Module

Comprehensive validation of a Drupal custom module.

## Steps

1. **Check Structure**
   - Verify .info.yml exists and is valid
   - Check for required files (*.module, composer.json if needed)
   - Validate PSR-4 namespace structure

2. **Run PHPCS**
   - Execute `composer code-sniff` on module directory
   - Report violations with file:line references
   - Suggest auto-fix if appropriate

3. **Check Dependencies**
   - Parse .info.yml for dependencies
   - Verify required modules are available
   - Flag deprecated dependencies

4. **Review Hooks**
   - List all hooks implemented
   - Check for proper documentation
   - Verify hook naming follows module_hookname pattern

## Inputs

- module_name: Machine name of the module (required)
- auto_fix: Whether to auto-fix PHPCS issues (optional, default: false)

## Outputs

- Validation report with pass/fail status
- List of issues found
- Suggested fixes
```

### Shared Infra Update

```markdown
---
name: update-shared-infra
description: Update shared infrastructure profile to latest version
tags: [composer, dependencies, shared-infra]
---

# Update Shared Infra

Update the mm_drupal_core_d9_shared_infra profile to the latest version.

## Steps

1. **Check Current Version**
   - Read composer.json
   - Note current version of morgan-and-morgan/mm_drupal_core_d9_shared_infra

2. **Update via Composer**
   - Run `composer update morgan-and-morgan/mm_drupal_core_d9_shared_infra`
   - Capture version change

3. **Check for Breaking Changes**
   - Read CHANGELOG or release notes if available
   - Flag any major version changes

4. **Update Configuration**
   - Run `ddev drush config:import` if needed
   - Check for config sync issues

5. **Run Code Sniff**
   - Execute `composer code-sniff-si` to check shared infra
   - Report any new violations

6. **Report Results**
   - Summarize version change
   - List any action items
   - Note if push to Pantheon is needed

## Inputs

None required (operates on current project)

## Outputs

- Version change summary
- Breaking changes (if any)
- Action items for developer
```

### Create Custom Module

```markdown
---
name: create-module
description: Scaffold a new custom Drupal module with proper structure
tags: [drupal, scaffold, module]
---

# Create Custom Module

Create a new custom Drupal module with proper Drupal 9/10 structure.

## Steps

1. **Get Module Details**
   - Ask for machine name (lowercase, underscores)
   - Ask for human-readable name
   - Ask for description
   - Ask for package (default: "Custom")
   - Ask for dependencies (optional)

2. **Create Directory Structure**
   ```
   web/modules/custom/{machine_name}/
   ├── {machine_name}.info.yml
   ├── {machine_name}.module
   └── src/
   ```

3. **Generate .info.yml**
   - Use proper YAML structure
   - Set core_version_requirement: ^9 || ^10
   - Add type: module
   - Include dependencies if specified

4. **Create .module File**
   - PHP opening tag
   - @file docblock
   - No closing tag
   - Empty by default

5. **Add to Git**
   - Stage new files
   - Show git status
   - Don't commit (let user review)

## Inputs

- machine_name: Module machine name (required)
- name: Human-readable name (required)
- description: Module description (required)
- package: Package name (optional, default: "Custom")
- dependencies: Array of module dependencies (optional)

## Outputs

- New module directory structure
- Ready-to-use module skeleton
- Git staged changes
```

## Invoking Skills

Skills are invoked by Claude using the Skill tool:

```
Skill(skill: "validate-module", args: "ftp_common")
Skill(skill: "update-shared-infra")
Skill(skill: "create-module", args: "my_new_module")
```

You can also tell Claude explicitly: "Use the validate-module skill on ftp_forms"

## Creating Skills

1. Create a new `.md` file in this directory
2. Use clear frontmatter with name, description, and tags
3. Break down the workflow into discrete steps
4. Specify inputs and outputs
5. Provide examples
6. Test by asking Claude to invoke the skill

## Tips

- Make skills composable (skills can call other skills)
- Handle edge cases explicitly
- Include validation steps
- Provide clear success/failure criteria
- Document required tools or commands
- Consider idempotency (can it run safely multiple times?)

## Skill vs Command

- **Command** (`/name`): User-invoked shortcut, triggered manually
- **Skill** (Skill tool): Claude-invoked workflow, triggered during task execution

Use commands for manual operations, skills for automated workflows.
