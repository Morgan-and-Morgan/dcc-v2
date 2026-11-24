import { test, expect } from '../fixtures/base';
import { type Page } from '@playwright/test';
import {
  ONETRUST_DOMAIN_SCRIPT_ID,
  oneTrustScriptOrder,
  oneTrustSdkScript,
} from '../helpers/onetrust';

test.describe('OneTrust cookie consent', () => {
  let page: Page;

  test.beforeAll(async ({ browser }) => {
    page = await browser.newPage();
    await page.goto('/', { waitUntil: 'domcontentloaded' });
  });

  test.afterAll(async () => {
    await page?.close();
  });

  test('attaches the OneTrust SDK script once on the home page', async () => {
    await expect(oneTrustSdkScript(page)).toHaveCount(1);
  });

  test('loads the SDK with the site domain script ID', async () => {
    // The ID is stored in State rather than config, so it can go missing
    // independently of the module being enabled.
    await expect(oneTrustSdkScript(page)).toHaveAttribute(
      'data-domain-script',
      ONETRUST_DOMAIN_SCRIPT_ID,
    );
  });

  test('emits consent default, SDK and OptanonWrapper in order', async () => {
    // Consent Mode must be defaulted before the SDK loads, and the callback
    // must follow it, or tags can fire before the visitor's choice applies.
    const order = await oneTrustScriptOrder(page);
    expect(order.consentDefault).toBeGreaterThanOrEqual(0);
    expect(order.sdkStub).toBeGreaterThan(order.consentDefault);
    expect(order.optanonWrapper).toBeGreaterThan(order.sdkStub);
  });
});
