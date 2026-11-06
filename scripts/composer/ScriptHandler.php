<?php

/**
 * @file
 * Contains \DrupalProject\composer\ScriptHandler.
 */

namespace DrupalProject\composer;

use Composer\Script\Event;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class ScriptHandler
{

  /**
   * Whether this run started without a lock and with a missing merge include.
   *
   * @var bool
   */
  protected static $mergeIncludePending = FALSE;

  /**
   * The composer.lock contents at the start of `composer install`.
   *
   * @var string|null
   */
  protected static $lockBeforeInstall = NULL;

  /**
   * Remembers composer.lock at the start of `composer install`.
   *
   * Runs on pre-install-cmd. See restoreLockAfterInstall().
   */
  public static function rememberLock(Event $event) {
    $lock = static::getLockPath();
    static::$lockBeforeInstall = file_exists($lock) ? file_get_contents($lock) : NULL;
  }

  /**
   * Undoes any composer.lock rewrite made during `composer install`.
   *
   * Runs on post-install-cmd, after the merge plugin's own post-install
   * callback. When the merge plugin is installed during the run (cold vendor,
   * e.g. CI's Build PHP job), it runs "composer update to apply merge
   * settings". Under --no-dev its fresh Composer instance does not merge the
   * include's require-dev, so that update drops every packages-dev entry from
   * the lock and the next dev install fails with "Required (in require-dev)
   * package ... is not present in the lock file". An install must never
   * change the committed lock, so put the original back. vendor/ is
   * unaffected: that pass had nothing to install or remove.
   */
  public static function restoreLockAfterInstall(Event $event) {
    $before = static::$lockBeforeInstall;
    static::$lockBeforeInstall = NULL;
    if ($before === NULL) {
      return;
    }
    $lock = static::getLockPath();
    if (!file_exists($lock) || file_get_contents($lock) !== $before) {
      file_put_contents($lock, $before);
      $event->getIO()->write('<info>composer install rewrote composer.lock (merge plugin follow-up pass); restored the committed lock.</info>');
    }
  }

  /**
   * Records whether a lockless resolve is starting without its merge includes.
   *
   * Runs on pre-install-cmd / pre-update-cmd. After `composer nuke-profile`
   * or `nuke-site` there is no composer.lock and the shared-infra profile
   * (whose composer.json is merged by wikimedia/composer-merge-plugin) is not
   * on disk yet, so the first resolve misses everything that file declares,
   * notably its require-dev (phpunit, phpcs, coder, behat...). The merge
   * plugin's own follow-up pass does not fix this: it only runs when the merge
   * plugin itself was installed during the run (never true once
   * bootstrap-plugins has pre-installed it), and when it does run it is a
   * partial update that fails on already-locked transitive dependencies and
   * reverts the lock. See completeLocklessMerge().
   */
  public static function checkLocklessMerge(Event $event) {
    static::$mergeIncludePending = FALSE;
    if (file_exists(static::getLockPath())) {
      return;
    }
    $extra = $event->getComposer()->getPackage()->getExtra();
    $includes = $extra['merge-plugin']['include'] ?? [];
    foreach ((array) $includes as $pattern) {
      if (!glob(getcwd() . '/' . $pattern)) {
        static::$mergeIncludePending = TRUE;
        return;
      }
    }
  }

  /**
   * Re-resolves once the merge includes exist, so the lock is complete.
   *
   * Runs on post-install-cmd / post-update-cmd. When checkLocklessMerge()
   * saw a lockless start with a missing include, the lock that was just
   * written is incomplete. A full `composer update` now (the lock is brand
   * new, so there is nothing to preserve) resolves with the merged
   * shared-infra composer.json, giving the same lock as a lockless resolve
   * with the profile already present. The nested run finds a lock and does
   * not recurse.
   */
  public static function completeLocklessMerge(Event $event) {
    if (!static::$mergeIncludePending) {
      return;
    }
    static::$mergeIncludePending = FALSE;
    $io = $event->getIO();
    $io->write('<info>Lockless resolve ran without the merge-plugin includes; running a full composer update to merge them into composer.lock.</info>');
    $composer = getenv('COMPOSER_BINARY') ?: 'composer';
    $command = escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($composer) . ' update --no-interaction';
    if (!$event->isDevMode()) {
      $command .= ' --no-dev';
    }
    passthru($command, $exitCode);
    if ($exitCode !== 0) {
      throw new \RuntimeException('composer update to apply the merge-plugin includes failed with exit code ' . $exitCode . '.');
    }
  }

  /**
   * Gets the lock file path for the current project.
   */
  protected static function getLockPath() {
    $composerFile = getenv('COMPOSER') ?: 'composer.json';
    return getcwd() . '/' . preg_replace('/\.json$/', '', $composerFile) . '.lock';
  }


  protected static function getDrupalRoot($project_root)
  {
    return $project_root .  '/web';
  }

  public static function createRequiredFiles(Event $event)
  {
    $fs = new Filesystem();
    $root = static::getDrupalRoot(getcwd());

    $dirs = [
      'modules',
      'profiles',
      'themes',
    ];

    // Required for unit testing
    foreach ($dirs as $dir) {
      if (!$fs->exists($root . '/'. $dir)) {
        $fs->mkdir($root . '/'. $dir);
        $fs->touch($root . '/'. $dir . '/.gitkeep');
      }
    }

    // Create the files directory with chmod 0777
    if (!$fs->exists($root . '/sites/default/files')) {
      $oldmask = umask(0);
      $fs->mkdir($root . '/sites/default/files', 0777);
      umask($oldmask);
      $event->getIO()->write("Create a sites/default/files directory with chmod 0777");
    }
  }

  // This is called by the QuickSilver deploy hook to convert from
  // a 'lean' repository to a 'fat' repository. This should only be
  // called when using this repository as a custom upstream, and
  // updating it with `terminus composer <site>.<env> update`. This
  // is not used in the GitHub PR workflow.
  public static function prepareForPantheon()
  {
    // Get rid of any .git directories that Composer may have added.
    // n.b. Ideally, there are none of these, as removing them may
    // impair Composer's ability to update them later. However, leaving
    // them in place prevents us from pushing to Pantheon.
    $dirsToDelete = [];
    $finder = new Finder();
    foreach (
      $finder
        ->directories()
        ->in(getcwd())
        ->ignoreDotFiles(false)
        ->ignoreVCS(false)
        ->depth('> 0')
        ->name('.git')
      as $dir) {
      $dirsToDelete[] = $dir;
    }
    $fs = new Filesystem();
    $fs->remove($dirsToDelete);

    // Fix up .gitignore: remove everything above the "::: cut :::" line
    $gitignoreFile = getcwd() . '/.gitignore';
    $gitignoreContents = file_get_contents($gitignoreFile);
    $gitignoreContents = preg_replace('/.*::: cut :::*/s', '', $gitignoreContents);
    file_put_contents($gitignoreFile, $gitignoreContents);
  }
}
