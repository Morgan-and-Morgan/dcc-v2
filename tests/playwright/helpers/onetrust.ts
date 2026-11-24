import { type Locator, type Page } from '@playwright/test';

/**
 * OneTrust (cookie consent) helpers.
 *
 * The OneTrust SDK is attached server-side by the Shared Infra mm_privacy
 * module (mm_privacy_page_attachments()). It emits three head scripts, in this
 * order:
 *
 *   1. an inline Google Consent Mode default (`gtag('consent', 'default', …)`),
 *   2. the SDK loader, cdn.cookielaw.org/scripttemplates/otSDKStub.js, carrying
 *      the OneTrust domain script ID in `data-domain-script`,
 *   3. an inline `function OptanonWrapper()` callback.
 *
 * The domain script ID is State, not config, so a fresh database or a lost
 * State value removes the banner without any error. Asserting the attribute
 * catches that as well as the module being uninstalled.
 */

/** OneTrust domain script ID for disabilitycarecenter.org (all environments). */
export const ONETRUST_DOMAIN_SCRIPT_ID = '656b37c8-3ec3-4879-a474-c6dcf8ce79f0';

/** URL fragment that identifies the OneTrust SDK loader script. */
export const ONETRUST_SDK_STUB = 'cdn.cookielaw.org/scripttemplates/otSDKStub.js';

/**
 * The OneTrust SDK loader <script> element(s) in the page.
 *
 * @param page
 *   The page to search.
 */
export function oneTrustSdkScript(page: Page): Locator {
  return page.locator(`script[src*="${ONETRUST_SDK_STUB}"]`);
}

/**
 * Positions of the three OneTrust head scripts, in document order.
 *
 * Each value is the index of the matching <script> among all scripts in the
 * document, or -1 when it is missing.
 *
 * @param page
 *   The already-navigated page.
 */
export async function oneTrustScriptOrder(
  page: Page,
): Promise<{ consentDefault: number; sdkStub: number; optanonWrapper: number }> {
  return page.evaluate((stub) => {
    const scripts = Array.from(document.querySelectorAll('script'));
    const find = (match: (s: HTMLScriptElement) => boolean): number =>
      scripts.findIndex(match);
    return {
      consentDefault: find((s) =>
        /gtag\(\s*['"]consent['"]\s*,\s*['"]default['"]/.test(s.text),
      ),
      sdkStub: find((s) => (s.getAttribute('src') || '').includes(stub)),
      optanonWrapper: find((s) => /function\s+OptanonWrapper\s*\(/.test(s.text)),
    };
  }, ONETRUST_SDK_STUB);
}
