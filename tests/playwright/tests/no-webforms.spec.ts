import { test, expect } from '../fixtures/base';

/**
 * WEM-7484: the DCC site must ship zero webforms. These landing pages used to
 * render a webform (SSD: start_your_application, SSI: step_1_disability_benefit_evalua).
 */
const PAGES = [
  { name: 'SSD landing', path: '/social-security-disability/' },
  { name: 'SSI qualify landing', path: '/ssi/qualify-for-disability/' },
];

for (const { name, path } of PAGES) {
  test.describe(`No webforms: ${name}`, () => {
    test('renders without any webform markup', async ({ page }) => {
      const response = await page.goto(path);
      expect(response?.status()).toBe(200);
      await expect(page.locator('.webform-submission-form')).toHaveCount(0);
      await expect(page.locator('form[id^="webform-submission-"]')).toHaveCount(0);
      expect(await page.content()).not.toContain('webform-submission-form');
    });
  });
}
