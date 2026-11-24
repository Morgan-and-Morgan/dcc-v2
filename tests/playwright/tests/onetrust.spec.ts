import { test, expect } from '../fixtures/base';
import { type Page } from '@playwright/test';
import {
  ONETRUST_DOMAIN_SCRIPT_ID,
  type OneTrustMarkup,
  oneTrustMarkup,
} from '../helpers/onetrust';

test.describe('OneTrust cookie consent', () => {
  let page: Page;
  let markup: OneTrustMarkup;

  test.beforeAll(async ({ browser }) => {
    page = await browser.newPage();
    const response = await page.goto('/', { waitUntil: 'domcontentloaded' });
    if (!response) {
      throw new Error('No response for the home page.');
    }
    markup = await oneTrustMarkup(page, await response.text());
  });

  test.afterAll(async () => {
    await page?.close();
  });

  test('attaches the OneTrust SDK script once on the home page', async () => {
    expect(markup.sdkDomainScripts).toHaveLength(1);
  });

  test('loads the SDK with the site domain script ID', async () => {
    // The ID is stored in State rather than config, so it can go missing
    // independently of the module being enabled.
    expect(markup.sdkDomainScripts[0]).toBe(ONETRUST_DOMAIN_SCRIPT_ID);
  });

  test('emits consent default, SDK and OptanonWrapper in order', async () => {
    // Consent Mode must be defaulted before the SDK loads, and the callback
    // must follow it, or tags can fire before the visitor's choice applies.
    expect(markup.consentDefault).toBeGreaterThanOrEqual(0);
    expect(markup.sdkStub).toBeGreaterThan(markup.consentDefault);
    expect(markup.optanonWrapper).toBeGreaterThan(markup.sdkStub);
  });
});
