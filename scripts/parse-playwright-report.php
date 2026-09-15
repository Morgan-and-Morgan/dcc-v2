<?php

/**
 * @file
 * Parse Playwright JUnit XML reports into a concise human-readable summary.
 *
 * Usage:
 *   php scripts/parse-playwright-report.php [reports-dir] [output-dir]
 *
 * Defaults:
 *   reports-dir  tests/playwright/reports/
 *   output-dir   reports-dir
 *
 * Reads all *.xml files produced by Playwright's JUnit reporter (configured in
 * tests/playwright/playwright.config.ts as reports/results.xml) and writes two
 * files into output-dir:
 *   summary.txt   Human-readable pass/fail report
 *   summary.json  Machine-readable version of the same data
 *
 * With --slack it also writes slack-message.txt, a pre-formatted Slack message.
 *
 * Playwright's JUnit output nests <testsuite> elements (one per spec file)
 * inside a root <testsuites>. Each <testcase> has no "status" attribute — a
 * test is considered failed when it has a <failure>/<error> child and skipped
 * when it has a <skipped> child.
 */

declare(strict_types=1);

// ---------------------------------------------------------------------------
// Config.
// ---------------------------------------------------------------------------
// Separate positional arguments from flags (e.g. --slack).
$positional = array_values(array_filter(
  array_slice($argv, 1),
  fn($arg) => !str_starts_with($arg, '--'),
));
$flags = array_values(array_filter(
  array_slice($argv, 1),
  fn($arg) => str_starts_with($arg, '--'),
));

$reports_dir = $positional[0] ?? dirname(__DIR__) . '/tests/playwright/reports';
$output_dir  = $positional[1] ?? $reports_dir;
$want_slack  = in_array('--slack', $flags, TRUE);

if (!is_dir($reports_dir)) {
  fwrite(STDERR, "Reports directory not found: $reports_dir\n");
  fwrite(STDERR, "Run Playwright with the junit reporter first (outputs results.xml).\n");
  exit(1);
}

$xml_files = glob($reports_dir . '/*.xml');
if (!$xml_files) {
  fwrite(STDERR, "No XML report files found in: $reports_dir\n");
  fwrite(STDERR, "Run Playwright with the junit reporter first (outputs results.xml).\n");
  exit(1);
}

// ---------------------------------------------------------------------------
// Parse all XML files.
// ---------------------------------------------------------------------------
// $tests[] = [
// 'suite' => string, Spec file (from <testsuite name>).
// 'name' => string, Test title (from <testcase name>).
// 'status' => string, 'passed' | 'failed' | 'error' | 'skipped'.
// 'time' => float.
// 'failures' => string[], failure/error messages.
// ]
$tests        = [];
$total_time   = 0.0;
$files_parsed = 0;

foreach ($xml_files as $xml_file) {
  $xml = @simplexml_load_file($xml_file);
  if ($xml === FALSE) {
    fwrite(STDERR, "Warning: could not parse $xml_file — skipping.\n");
    continue;
  }
  $files_parsed++;

  // A JUnit file may contain one or more <testsuite> elements.
  // Playwright wraps them in <testsuites>; some tools output a single
  // <testsuite> at root.
  $suites = [];
  if ($xml->getName() === 'testsuites') {
    foreach ($xml->testsuite as $suite) {
      $suites[] = $suite;
    }
  }
  elseif ($xml->getName() === 'testsuite') {
    $suites[] = $xml;
  }

  foreach ($suites as $suite) {
    $suite_name = (string) ($suite['name'] ?? 'Unknown suite');
    $suite_time = (float) ($suite['time'] ?? 0);
    $total_time += $suite_time;

    foreach ($suite->testcase as $testcase) {
      $name   = (string) ($testcase['name'] ?? 'Unknown test');
      $time   = (float) ($testcase['time'] ?? 0);
      $status = 'passed';

      $failure_messages = [];

      foreach ($testcase->failure as $failure) {
        $failure_messages[] = trim((string) ($failure['message'] ?? $failure));
      }
      foreach ($testcase->error as $error) {
        $failure_messages[] = trim((string) ($error['message'] ?? $error));
      }

      // Playwright testcases carry no "status" attribute: a <skipped> child
      // marks a skip, a <failure>/<error> child marks a failure.
      if (!empty($failure_messages)) {
        $status = 'failed';
      }
      elseif (isset($testcase->skipped)) {
        $status = 'skipped';
      }

      $tests[] = [
        'suite'    => $suite_name,
        'name'     => $name,
        'status'   => $status,
        'time'     => $time,
        'failures' => $failure_messages,
      ];
    }
  }
}

// ---------------------------------------------------------------------------
// Tally results.
// ---------------------------------------------------------------------------
$passed  = array_filter($tests, fn($s) => $s['status'] === 'passed');
$failed  = array_filter($tests, fn($s) => in_array($s['status'], ['failed', 'error'], TRUE));
$skipped = array_filter($tests, fn($s) => $s['status'] === 'skipped');

$total      = count($tests);
$pass_count = count($passed);
$fail_count = count($failed);
$skip_count = count($skipped);
$pass_pct   = $total > 0 ? round($pass_count / $total * 100, 1) : 0;
$fail_pct   = $total > 0 ? round($fail_count / $total * 100, 1) : 0;
// Prefer real wall-clock elapsed time (earliest shard start to latest shard
// finish, passed in by CI via PLAYWRIGHT_WALL_SECONDS) over the JUnit times,
// which sum serially and ignore the parallel shards. Fall back to the summed
// suite time when the wall-clock figure isn't available (e.g. local runs).
$wall_seconds = getenv('PLAYWRIGHT_WALL_SECONDS');
$display_seconds = (is_string($wall_seconds) && is_numeric($wall_seconds) && (int) $wall_seconds > 0)
  ? (int) $wall_seconds
  : $total_time;
$duration_str = sprintf('%dm %ds', floor($display_seconds / 60), (int) $display_seconds % 60);

// ---------------------------------------------------------------------------
// Build text summary.
// ---------------------------------------------------------------------------
// Line width
$width = 80;
$bar = str_repeat('=', $width);
$thin = str_repeat('-', $width);

$lines = [];

$lines[] = $bar;
$lines[] = ' PLAYWRIGHT TEST REPORT';
$lines[] = ' Generated: ' . date('Y-m-d H:i:s');
$lines[] = ' Files:     ' . $files_parsed . ' XML report' . ($files_parsed !== 1 ? 's' : '') . ' parsed';
$lines[] = ' Duration:  ' . $duration_str;
$lines[] = $bar;
$lines[] = '';
$lines[] = ' SUMMARY';
$lines[] = ' ' . $thin;
$lines[] = sprintf(
  ' Tests : %d total  |  %d passed (%s%%)  |  %d failed (%s%%)%s',
  $total,
  $pass_count,
  $pass_pct,
  $fail_count,
  $fail_pct,
  $skip_count > 0 ? "  |  $skip_count skipped" : '',
);
$lines[] = '';

// ---------------------------------------------------------------------------
// Failures section.
// ---------------------------------------------------------------------------
if ($fail_count > 0) {
  $lines[] = $bar;
  $lines[] = " FAILURES ($fail_count)";
  $lines[] = $bar;
  $lines[] = '';

  foreach ($failed as $s) {
    $lines[] = ' ✗  ' . $s['name'];
    if ($s['suite']) {
      $lines[] = '    Suite: ' . $s['suite'];
    }
    foreach ($s['failures'] as $msg) {
      // Keep the message concise — first line only.
      $first_line = strtok($msg, "\n");
      $lines[] = '    Error: ' . $first_line;
    }
    $lines[] = '';
  }
}
else {
  $lines[] = $bar;
  $lines[] = ' FAILURES (0)';
  $lines[] = $bar;
  $lines[] = '';
  $lines[] = ' All tests passed.';
  $lines[] = '';
}

// ---------------------------------------------------------------------------
// Passed section.
// ---------------------------------------------------------------------------
$lines[] = $bar;
$lines[] = " PASSED ($pass_count)";
$lines[] = $bar;
$lines[] = '';

foreach ($passed as $s) {
  $time_str = $s['time'] > 0 ? sprintf(' (%.1fs)', $s['time']) : '';
  $lines[] = ' ✓  ' . $s['name'] . $time_str;
}

if ($skip_count > 0) {
  $lines[] = '';
  $lines[] = $bar;
  $lines[] = " SKIPPED ($skip_count)";
  $lines[] = $bar;
  $lines[] = '';
  foreach ($skipped as $s) {
    $lines[] = ' ⊘  ' . $s['name'];
  }
}

$lines[] = '';
$lines[] = $bar;

$text_report = implode("\n", $lines) . "\n";

// ---------------------------------------------------------------------------
// Build JSON summary.
// ---------------------------------------------------------------------------
$json_report = json_encode([
  'generated'    => date('c'),
  'duration_s'   => round($total_time, 2),
  'files_parsed' => $files_parsed,
  'summary' => [
    'total'   => $total,
    'passed'  => $pass_count,
    'failed'  => $fail_count,
    'skipped' => $skip_count,
  ],
  'failures' => array_values(array_map(fn($s) => [
    'test'   => $s['name'],
    'suite'  => $s['suite'],
    'errors' => $s['failures'],
  ], $failed)),
  'passed' => array_values(array_map(fn($s) => [
    'test'   => $s['name'],
    'suite'  => $s['suite'],
    'time_s' => round($s['time'], 2),
  ], $passed)),
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

// ---------------------------------------------------------------------------
// Write output files.
// ---------------------------------------------------------------------------
if (!is_dir($output_dir)) {
  mkdir($output_dir, 0755, TRUE);
}

$txt_path  = $output_dir . '/summary.txt';
$json_path = $output_dir . '/summary.json';

file_put_contents($txt_path, $text_report);
file_put_contents($json_path, $json_report . "\n");

// Also print the text summary to stdout.
echo $text_report;
echo "Written: $txt_path\n";
echo "Written: $json_path\n";

// ---------------------------------------------------------------------------
// Optional: Write Slack-formatted message (--slack flag).
//
// Reads env vars for run context (set in CI before invocation):
// PLAYWRIGHT_RUN_NUMBER — GitHub Actions run number.
// PLAYWRIGHT_RUN_URL — Link to the Actions run.
// PLAYWRIGHT_SITE_LABEL — Display label, e.g. "disabilitycarecenter.org".
// PLAYWRIGHT_REPORT_URL — Link to the hosted HTML report (visualizer).
// ---------------------------------------------------------------------------
if ($want_slack) {
  $run_number = getenv('PLAYWRIGHT_RUN_NUMBER') ?: '';
  $run_url    = getenv('PLAYWRIGHT_RUN_URL') ?: '';
  $site_label = getenv('PLAYWRIGHT_SITE_LABEL') ?: '';
  $report_url = getenv('PLAYWRIGHT_REPORT_URL') ?: '';

  $icon   = $fail_count > 0 ? ':x:' : ':white_check_mark:';
  $title  = $run_number ? "Run #$run_number" : 'Playwright Tests';
  $header = "$icon  *$title*";
  if ($site_label) {
    $header .= " | $site_label";
  }

  $stats = ["*$total* tests", "*$pass_count* passed"];
  if ($fail_count > 0) {
    $stats[] = "*$fail_count* failed";
  }
  if ($skip_count > 0) {
    $stats[] = "*$skip_count* skipped";
  }
  $stats[] = $duration_str;

  // Keep the Slack message to a summary line plus links — the full breakdown of
  // failures lives in the linked HTML report, so we don't duplicate it here.
  $msg = [];
  $msg[] = $header;
  $msg[] = implode('  •  ', $stats);
  if ($fail_count === 0) {
    $msg[] = 'All tests passed. :tada:';
  }

  if ($run_url || $report_url) {
    $msg[] = '';
  }
  if ($run_url) {
    $msg[] = $run_number ? "<$run_url|View run #$run_number>" : "View run: $run_url";
  }
  if ($report_url) {
    $msg[] = "<$report_url|Open HTML report ↗>";
  }

  $slack_message = implode("\n", $msg) . "\n";
  $slack_path    = $output_dir . '/slack-message.txt';
  file_put_contents($slack_path, $slack_message);
  echo "Written: $slack_path\n";
}

exit($fail_count > 0 ? 1 : 0);
