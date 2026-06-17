# .claude/ Directory

This directory contains Claude Code project configuration and customizations for the DCC-V2 project.

## Structure

```
.claude/
├── CLAUDE.md              # Project instructions (checked into git)
├── CLAUDE.local.md        # Local environment instructions (gitignored)
├── settings.local.json    # Local Claude Code settings (gitignored)
├── commands/              # Custom slash commands
├── rules/                 # Project-specific rules and guidelines
├── skills/                # Custom skills/macros
└── agents/                # Custom agent definitions
```

## Files

### CLAUDE.md
Project-wide instructions that Claude Code will follow. This file should be checked into version control and shared with the team. Contains:
- Project overview and structure
- Coding standards and linting rules
- Build commands and CI/CD information
- Technology stack details

### CLAUDE.local.md
Local environment-specific instructions (gitignored). Contains:
- Local development URLs
- DDEV commands
- Personal preferences
- Machine-specific configurations

### settings.local.json
Claude Code settings overrides for this project (gitignored). Managed by Claude Code itself.

## Directories

### commands/
Custom slash commands that can be invoked with `/command-name`. See `commands/README.md` for details.

### rules/
Project-specific rules that Claude should follow. These are more granular than CLAUDE.md and can be organized by topic. See `rules/README.md` for details.

### skills/
Reusable skills/macros for common workflows. Skills are invoked with the Skill tool. See `skills/README.md` for details.

### agents/
Custom agent definitions for specialized tasks. Agents can be spawned to handle complex, multi-step operations. See `agents/README.md` for details.

## Usage

Claude Code automatically:
1. Loads `CLAUDE.md` and `CLAUDE.local.md` at conversation start
2. Applies settings from `settings.local.json`
3. Makes commands, rules, skills, and agents available during the session

## Best Practices

- **CLAUDE.md**: Keep it focused on standards, structure, and team-wide guidelines
- **CLAUDE.local.md**: Use for environment-specific details that shouldn't be shared
- **commands/**: Create for frequently used command sequences
- **rules/**: Break down complex guidelines into focused, single-topic rules
- **skills/**: Build for workflows you want to invoke by name
- **agents/**: Define for autonomous, multi-step operations

## Git

Recommended `.gitignore` entries:
```
.claude/settings.local.json
.claude/CLAUDE.local.md
```

Keep in version control:
```
.claude/CLAUDE.md
.claude/commands/
.claude/rules/
.claude/skills/
.claude/agents/
.claude/README.md
```
