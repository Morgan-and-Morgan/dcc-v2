import { type Page } from '@playwright/test';

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
 * The checks read the HTML as served by Drupal, not the live DOM: a tag
 * manager can inject the same SDK at runtime, which would hide the server-side
 * attachment going missing. The domain script ID is State, not config, so a
 * fresh database or a lost State value removes the SDK without any error.
 */

/** OneTrust domain script ID for disabilitycarecenter.org (all environments). */
export const ONETRUST_DOMAIN_SCRIPT_ID = '656b37c8-3ec3-4879-a474-c6dcf8ce79f0';

/** URL fragment that identifies the OneTrust SDK loader script. */
export const ONETRUST_SDK_STUB = 'cdn.cookielaw.org/scripttemplates/otSDKStub.js';

/** What the server-rendered HTML says about the OneTrust head scripts. */
export interface OneTrustMarkup {
  /** data-domain-script of every otSDKStub.js script in the document. */
  sdkDomainScripts: (string | null)[];
  /** Index among the document's scripts of the consent default, or -1. */
  consentDefault: number;
  /** Index among the document's scripts of the first SDK loader, or -1. */
  sdkStub: number;
  /** Index among the document's scripts of OptanonWrapper, or -1. */
  optanonWrapper: number;
}

/**
 * Inspects the OneTrust scripts in server-rendered HTML.
 *
 * The HTML is parsed with DOMParser, which never executes scripts, so nothing
 * added at runtime can satisfy the checks.
 *
 * @param page
 *   Any open page; used only to run DOMParser.
 * @param html
 *   The document HTML as returned by the server.
 */
export async function oneTrustMarkup(
  page: Page,
  html: string,
): Promise<OneTrustMarkup> {
  return page.evaluate(
    ({ source, stub }) => {
      const doc = new DOMParser().parseFromString(source, 'text/html');
      const scripts = Array.from(doc.querySelectorAll('script'));
      const isStub = (s: HTMLScriptElement): boolean =>
        (s.getAttribute('src') || '').includes(stub);
      return {
        sdkDomainScripts: scripts
          .filter(isStub)
          .map((s) => s.getAttribute('data-domain-script')),
        consentDefault: scripts.findIndex((s) =>
          /gtag\(\s*['"]consent['"]\s*,\s*['"]default['"]/.test(s.text),
        ),
        sdkStub: scripts.findIndex(isStub),
        optanonWrapper: scripts.findIndex((s) =>
          /function\s+OptanonWrapper\s*\(/.test(s.text),
        ),
      };
    },
    { source: html, stub: ONETRUST_SDK_STUB },
  );
}
