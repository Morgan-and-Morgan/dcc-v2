import { defineConfig, devices } from '@playwright/test';

/**
 * Base URL is set via environment variable in CI.
 * Defaults to production for local runs.
 *
 * CI usage:
 *   PLAYWRIGHT_BASE_URL=https://dev-dcc-v2.pantheonsite.io npx playwright test
 *
 * Local usage (runs against prod by default):
 *   cd tests/playwright && npx playwright test
 *
 * Production is www.disabilitycarecenter.org. The Pantheon environments are
 * dev-dcc-v2.pantheonsite.io and <branch>-dcc-v2.pantheonsite.io.
 */
export default defineConfig({
  testDir: './tests',
  outputDir: './test-results',
  // Tests within the same file run sequentially in one worker, sharing the
  // beforeAll page load. Different spec files still run in parallel across
  // workers. (fullyParallel: true would split tests across workers, breaking
  // beforeAll sharing and causing one page load per test rather than per file.)
  fullyParallel: false,

  // Fail the build on CI if test.only() is accidentally committed.
  forbidOnly: !!process.env.CI,

  // Retry failed tests twice in CI to reduce flake noise.
  retries: process.env.CI ? 2 : 0,

  // Use 4 workers in CI; auto-detect locally.
  workers: process.env.CI ? 4 : undefined,

  // Cap auto-retrying assertions at 3s (default is 5s) so failures surface
  // faster. Per-assertion overrides still win where a longer wait is needed.
  expect: { timeout: 3000 },

  reporter: [
    // Human-readable HTML report (open manually with `npm run report`).
    ['html', { open: 'never', outputFolder: 'playwright-report' }],
    // JUnit XML — parsed by parse-playwright-report.php for the Slack summary.
    ['junit', { outputFile: 'reports/results.xml' }],
    // Live output in terminal.
    ['list'],
  ],

  use: {
    // Resolved from env in CI; defaults to production for local spot-checks.
    baseURL:
      process.env.PLAYWRIGHT_BASE_URL || 'https://www.disabilitycarecenter.org',

    // Capture a trace on the first retry of a failed test.
    trace: 'on-first-retry',

    // Screenshot and video only on failure — keeps artifact size down.
    screenshot: 'only-on-failure',
    video: 'on-first-retry',
  },

  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
  ],
});
