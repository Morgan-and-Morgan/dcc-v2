<?php

/**
 * @file
 * File for handling test and live deployments.
 */

echo "Environment: ";
echo $_ENV['PANTHEON_ENVIRONMENT'];
echo "\n";

if ($_ENV['PANTHEON_ENVIRONMENT'] == 'test') {
  // First run db updates.
  echo "Running DB updates.\n";
  passthru('drush updb -y');
  echo "DB updates complete.\n";
  echo "\n";

  // Test can be weird, so rebuild the cache here before cim.
  echo "Rebuilding cache.\n";
  passthru('drush cr');
  echo "Rebuilding cache complete.\n";
  echo "\n";

  // Import all config changes.
  echo "Importing configuration from yml files...\n";
  passthru('drush config-import -y');
  echo "Import of configuration complete.\n";
  echo "\n";

  // Rebuild the cache.
  echo "Rebuilding cache.\n";
  passthru('drush cr');
  echo "Rebuilding cache complete.\n";
  echo "\n";
}

if ($_ENV['PANTHEON_ENVIRONMENT'] == 'live') {
  // First run db updates.
  echo "Running DB updates.\n";
  passthru('drush updb -y');
  echo "DB updates complete.\n";
  echo "\n";

  // Import all config changes.
  echo "Importing configuration from yml files...\n";
  passthru('drush config-import -y');
  echo "Import of configuration complete.\n";
  echo "\n";

  // Rebuild the cache.
  echo "Rebuilding cache.\n";
  passthru('drush cr');
  echo "Rebuilding cache complete.\n";
  echo "\n";
}
