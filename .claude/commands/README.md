# Custom Commands

Custom slash commands for the FTP-V3 project.

## What are Commands?

Commands are shortcuts that can be invoked with `/command-name` in the Claude Code interface. They're useful for frequently executed operations that you want to trigger with minimal typing.

## Structure

Each command is a markdown file with frontmatter:

```markdown
---
name: example
description: Short description of what this command does
---

Instructions for Claude on what to do when this command is invoked.

You can include:
- Step-by-step instructions
- Tool calls to make
- Files to read
- Checks to perform
```

## Example Commands

### `/drupal-sniff`
Run PHP CodeSniffer on a specific module or file:

```markdown
---
name: drupal-sniff
description: Run PHPCS on a specific path
---

1. Ask the user which path to check (or use the current file if obvious)
2. Run `composer code-sniff` with the appropriate path
3. If errors are found, ask if they want auto-fix with `composer code-sniff-fix`
4. Report the results
```

### `/build-theme`
Build the Morgan theme:

```markdown
---
name: build-theme
description: Build the Morgan custom theme
---

1. Navigate to web/themes/custom/morgan/
2. Run `npm ci` to ensure clean install
3. Run `npm run build` to compile assets
4. Report any build errors or success
```

## Creating Commands

1. Create a new `.md` file in this directory
2. Add frontmatter with `name` and `description`
3. Write clear instructions for Claude
4. Test by typing `/your-command-name` in Claude Code

## Tips

- Keep commands focused on a single task
- Use clear, imperative instructions
- Reference specific tools (Bash, Read, Edit, etc.)
- Handle common error cases
- Ask for user input when needed

## Reserved Names

Avoid these built-in Claude Code commands:
- `/help`, `/clear`, `/undo`, `/fast`, `/debug`
- `/commit`, `/review-pr`
