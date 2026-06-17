---
title: No Direct Database Queries in Controllers
category: drupal/architecture
applies_to: ["src/Controller/*.php", "src/Form/*.php"]
---

# No Direct Database Queries in Controllers

Controllers and forms should use services or the Entity API for database access, not direct queries.

## Rationale

- **Separation of concerns**: Controllers handle HTTP, not data access
- **Testability**: Services can be mocked, database connections cannot
- **Maintainability**: Business logic belongs in services, not controllers
- **Performance**: Services can implement caching strategies
- **Best practices**: Follows Drupal architecture standards

## Good ✓

```php
namespace Drupal\my_module\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\my_module\Service\MyDataService;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MyController extends ControllerBase {

  public function __construct(
    private readonly MyDataService $dataService,
  ) {}

  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('my_module.data_service'),
    );
  }

  public function myMethod() {
    $data = $this->dataService->getData();
    return ['#markup' => $data];
  }
}
```

## Bad ✗

```php
namespace Drupal\my_module\Controller;

use Drupal\Core\Controller\ControllerBase;

class MyController extends ControllerBase {

  public function myMethod() {
    // Direct database query in controller!
    $query = \Drupal::database()->select('my_table', 't');
    $query->fields('t', ['field1', 'field2']);
    $data = $query->execute()->fetchAll();

    return ['#markup' => print_r($data, TRUE)];
  }
}
```

## Exceptions

- Simple queries in `.module` hooks are acceptable (procedural context)
- Update hooks (`.install` files) can use direct queries
- Drush commands may use direct queries for maintenance tasks

## Alternative: Entity Queries

For entity-based queries, use Entity Query API:

```php
$storage = $this->entityTypeManager()->getStorage('node');
$query = $storage->getQuery()
  ->condition('type', 'article')
  ->condition('status', 1)
  ->accessCheck(TRUE);
$nids = $query->execute();
$nodes = $storage->loadMultiple($nids);
```

## References

- [Drupal Services Documentation](https://www.drupal.org/docs/drupal-apis/services-and-dependency-injection)
- [Entity Query API](https://www.drupal.org/docs/drupal-apis/entity-api/querying-entities)
