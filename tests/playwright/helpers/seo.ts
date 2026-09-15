import { expect, type Page } from '@playwright/test';

/**
 * Origin the suite is running against.
 *
 * Resolved from PLAYWRIGHT_BASE_URL, defaulting to production for local runs —
 * the same source playwright.config.ts uses for `baseURL`. The master pipeline
 * points this at the Pantheon dev environment.
 */
const BASE_URL =
  process.env.PLAYWRIGHT_BASE_URL || 'https://www.disabilitycarecenter.org';

/**
 * Absolute URL for a site-root-relative path against the base URL under test.
 *
 * Drupal renders host-derived SEO tags (canonical, og:url) from the current
 * request host, so the expected value differs per environment — dev serves
 * `https://dev-dcc-v2.pantheonsite.io/` as the canonical while production
 * serves the disabilitycarecenter.org host. Building it from the base URL keeps
 * these assertions correct on both rather than hardcoding the production host
 * and producing false negatives on dev.
 *
 * @param path
 *   Site-root-relative path, e.g. '/veterans'.
 * @return
 *   The absolute URL for the environment under test.
 */
export function siteUrl(path: string): string {
  return new URL(path, BASE_URL).href;
}

/**
 * Pattern matching an absolute URL on the origin under test.
 *
 * For assertions that only care that a value is an absolute on-site URL — most
 * commonly og:image, whose path changes whenever an editor swaps the image, so
 * the shape is asserted rather than a fixed path.
 */
export const SITE_ORIGIN_URL_RE = new RegExp(
  `^${BASE_URL.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')}/`,
);

/**
 * Assert the page's canonical link points at `path` on the base URL under test.
 *
 * Use with care on this site: production currently emits its canonical over
 * `http://`, not `https://`, so an exact comparison against a https base URL
 * fails there while passing on dev. Prefer asserting the canonical's shape
 * unless the scheme has been fixed.
 *
 * @param page
 *   The Playwright page.
 * @param path
 *   Site-root-relative canonical path.
 */
export async function expectCanonical(page: Page, path: string): Promise<void> {
  await expect(page.locator('link[rel="canonical"]')).toHaveAttribute(
    'href',
    siteUrl(path),
  );
}
