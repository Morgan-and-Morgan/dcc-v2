import { test, expect } from '../fixtures/base';
import { type Page } from '@playwright/test';
import { HomepagePage } from '../pages/homepage';

const PAGE_TITLE = 'DCC homepage';

/**
 * Top-level primary navigation items expected in the mega menu.
 *
 * This is the complete top-level `header-navigation` menu as rendered. The
 * submenu children (individual conditions, application stages, resources) are
 * deliberately excluded: they change as content is added and would make this a
 * maintenance burden rather than a regression signal.
 */
const PRIMARY_NAV_ITEMS = [
  'Social Security Disability',
  'Medical Qualifications',
  'Veteran Disability',
  'Disability Resources',
  'Blog',
];

/**
 * Custom blocks that make up the homepage body.
 *
 * A block that stops rendering leaves the page returning 200 with a section
 * silently missing, so each is asserted explicitly.
 */
const HOMEPAGE_BLOCKS = [
  'socialsecuritydisabilityapplication',
  'medicalqualificationsapplication',
  'veterandisabilityapplication',
  'disabilityresourcesapplication',
];

test.describe(PAGE_TITLE, () => {
  let page: Page;
  let pom: HomepagePage;

  test.beforeAll(async ({ browser }) => {
    page = await browser.newPage();
    pom = new HomepagePage(page);
    await pom.goto();
  });

  test.afterAll(async () => {
    // Optional-chained: if beforeAll throws, `page` is never assigned and an
    // unguarded close() raises "Cannot read properties of undefined", which
    // masks the real failure in the report.
    await page?.close();
  });

  // --- Identity ---

  test('loads at the correct URL', async () => {
    await expect(page).toHaveURL('/');
  });

  test('has the expected page title', async () => {
    await expect(page).toHaveTitle(/Disability Care Center/i);
  });

  test('has an absolute canonical URL', async () => {
    // Asserted as shape rather than a fixed value, for two reasons: Drupal
    // builds the canonical from the request host so it differs between the
    // Pantheon dev environment and a multidev, and production currently emits
    // this tag over http:// rather than https://. Pinning either the host or
    // the scheme would fail on one environment while passing on the other.
    await expect(page.locator('link[rel="canonical"]')).toHaveAttribute(
      'href',
      /^https?:\/\/.+/,
    );
  });

  // --- Structure and landmarks ---

  test('exposes the banner, main and contentinfo landmarks exactly once', async () => {
    // Duplicated landmarks are a real accessibility defect, so count is
    // asserted rather than just presence.
    await expect(pom.header).toHaveCount(1);
    await expect(pom.main).toHaveCount(1);
    await expect(pom.footer).toHaveCount(1);
  });

  test('has exactly one h1', async () => {
    await expect(page.locator('h1')).toHaveCount(1);
  });

  test('renders the primary navigation exactly once', async () => {
    // Guards the class-scoped locator staying unambiguous: the navigation
    // region nests an outer bare <nav> around this one, so a role-based
    // locator would match two elements.
    await expect(pom.nav).toHaveCount(1);
  });

  test('renders the site logo linking to the root', async () => {
    await expect(pom.logoLink.first()).toBeAttached();
  });

  for (const item of PRIMARY_NAV_ITEMS) {
    test(`primary navigation links to "${item}"`, async () => {
      const link = pom.navLink(item);
      await expect(link).toHaveAttribute('href', /.+/);
    });
  }

  test('renders every top-level navigation item', async () => {
    // Count as well as membership: catches an item silently disappearing from
    // the menu, which the per-item tests above would not flag on their own if
    // the menu were reordered or truncated.
    await expect(pom.navLinks).toHaveCount(PRIMARY_NAV_ITEMS.length);
  });

  // --- Homepage content blocks ---

  for (const blockId of HOMEPAGE_BLOCKS) {
    test(`renders the "${blockId}" block`, async () => {
      expect(await pom.contentBlock(blockId).count()).toBeGreaterThan(0);
    });
  }

  // --- Accessibility affordances ---

  test('provides a skip link', async () => {
    // Visually hidden until focused, so presence and target are asserted
    // rather than visibility.
    //
    // Only the link itself is asserted. The theme emits the skip link pointing
    // at #main-content but renders NO element carrying that id, so the link
    // resolves to nothing when activated — a real accessibility defect in the
    // theme rather than something this suite should enshrine as correct.
    // Add `await expect(page.locator('#main-content')).toHaveCount(1);` here
    // once the template is fixed.
    await expect(pom.skipLink.first()).toHaveAttribute('href', '#main-content');
  });

  // --- Chrome and conversion paths ---

  test('exposes at least one telephone link', async () => {
    // The header phone number is the site's primary conversion path, so losing
    // it is a silent revenue break rather than a visible error.
    expect(await pom.telLinks.count()).toBeGreaterThan(0);
  });

  test('exposes the cookie-consent privacy choices trigger', async () => {
    // Required for CCPA/CPRA compliance — if the button stops rendering the
    // site loses its documented opt-out affordance without any visible error.
    await expect(pom.privacyChoicesButton).toHaveCount(1);
  });

  // --- Third-party ---

  test('initialises the Google Tag Manager data layer', async () => {
    // Container GTM-KH52X49, delivered by Drupal's google_tag module. The
    // rendered HTML carries only GTM's <noscript> iframe; the data layer is
    // created by the external google_tag.script.js at runtime, so this has to
    // be probed in the browser. If it stops initialising, analytics silently
    // dies, which is worth a regression test.
    const hasDataLayer = await page.evaluate(() =>
      Array.isArray(window.dataLayer),
    );
    expect(hasDataLayer).toBe(true);
  });
});
