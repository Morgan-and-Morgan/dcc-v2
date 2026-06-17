---
title: Always Add Cache Tags for Entity Renders
category: drupal/performance
applies_to: ["*.module", "src/Controller/*.php", "src/**/*Block.php"]
---

# Always Add Cache Tags for Entity Renders

When rendering entities or entity-dependent content, always include cache tags so content updates properly invalidate the cache.

## Rationale

Without proper cache tags:
- Stale content is served after entity updates
- Users see outdated information
- Manual cache clears are required
- Cache invalidation doesn't cascade properly

## Good ✓

```php
$build = [
  '#theme' => 'my_template',
  '#data' => $node,
  '#cache' => [
    'tags' => $node->getCacheTags(),
  ],
];
```

```php
$build = [
  '#type' => 'container',
  '#cache' => [
    'tags' => array_merge(
      $node->getCacheTags(),
      $term->getCacheTags(),
    ),
  ],
];
```

## Bad ✗

```php
$build = [
  '#theme' => 'my_template',
  '#data' => $node,
  // Missing cache tags!
];
```

```php
return [
  '#markup' => $node->getTitle(),
  // No cache metadata at all!
];
```

## Also Include

- **Cache contexts**: Add contexts like 'user', 'url', 'languages' as needed
- **Max-age**: Set appropriate max-age (or Cache::PERMANENT)
- **Cache keys**: Use cache keys for fragments that vary

## References

- [Drupal Cacheability Metadata](https://www.drupal.org/docs/drupal-apis/cache-api/cache-api)
- [Cache Tags Documentation](https://www.drupal.org/docs/drupal-apis/cache-api/cache-tags)
