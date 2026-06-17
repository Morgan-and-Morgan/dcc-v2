# Project Rules

Focused, topic-specific rules that Claude should follow for the FTP-V3 project.

## What are Rules?

Rules are granular guidelines that supplement CLAUDE.md. While CLAUDE.md provides broad standards, rules focus on specific topics, patterns, or edge cases. Think of them as "mini-policies" that can be referenced or loaded as needed.

## Structure

Each rule is a markdown file with frontmatter:

```markdown
---
title: Rule Title
category: category-name
applies_to: [files, patterns, or contexts]
---

Clear statement of the rule.

## Rationale
Why this rule exists.

## Examples

### Good ✓
```php
// Example of following the rule
```

### Bad ✗
```php
// Example of violating the rule
```

## Exceptions
When this rule doesn't apply.
```

## Categories

Organize rules by category:

- `php/` - PHP-specific rules
- `javascript/` - JavaScript patterns
- `drupal/` - Drupal conventions
- `security/` - Security practices
- `performance/` - Performance guidelines
- `testing/` - Testing standards
- `git/` - Git workflow rules

## Example Rules

### No Direct Database Queries in Controllers

```markdown
---
title: No Direct Database Queries in Controllers
category: drupal
applies_to: [web/modules/custom/*/src/Controller/*.php]
---

Controllers should use services for database access, not direct queries.

## Rationale
- Maintains separation of concerns
- Makes code testable
- Follows Drupal best practices
- Enables query caching and optimization

## Good ✓
```php
public function __construct(
  private readonly MyCustomService $myService,
) {}

public function myMethod() {
  $data = $this->myService->getData();
}
```

## Bad ✗
```php
public function myMethod() {
  $query = \Drupal::database()->select('my_table', 't');
  $data = $query->execute()->fetchAll();
}
```
```

### Cache Tags on Entity Renders

```markdown
---
title: Always Add Cache Tags for Entity Renders
category: drupal/performance
applies_to: [*.module, src/Controller/*.php]
---

When rendering entities, always add cache tags so content updates properly invalidate the cache.

## Rationale
Prevents stale content from being served after entity updates.

## Good ✓
```php
$build = [
  '#theme' => 'my_template',
  '#cache' => [
    'tags' => $node->getCacheTags(),
  ],
];
```

## Bad ✗
```php
$build = [
  '#theme' => 'my_template',
  // Missing cache tags!
];
```
```

## Using Rules

Rules are automatically available to Claude when working in the project. You can also explicitly reference them:

- "Check if this follows the cache tags rule"
- "Apply the database query rule here"
- "Review this against security rules"

## Creating Rules

1. Create a new `.md` file in the appropriate category subdirectory
2. Use clear frontmatter
3. State the rule explicitly
4. Explain the rationale
5. Provide good/bad examples
6. Note any exceptions

## Tips

- One rule per file
- Keep rules focused and specific
- Update rules when patterns change
- Remove obsolete rules
- Link related rules together
