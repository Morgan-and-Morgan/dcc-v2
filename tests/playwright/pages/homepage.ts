import { type Page, type Locator } from '@playwright/test';
import {
  primaryNav,
  siteFooter,
  siteHeader,
  siteMain,
} from '../helpers/landmarks';

/**
 * Page Object for the Disability Care Center homepage.
 *
 * Selectors here were read off the rendered production page rather than
 * inferred from the theme templates, so they reflect what the WE Mega Menu and
 * block layout actually emit.
 */
export class HomepagePage {
  readonly page: Page;

  /** Layout landmarks. */
  readonly header: Locator;
  readonly nav: Locator;
  readonly main: Locator;
  readonly footer: Locator;

  /**
   * Top-level links in the primary navigation.
   *
   * Scoped to the direct `li` children of the top-level mega-menu `ul` so the
   * submenu entries — which are also `li.we-mega-menu-li`, nested deeper, and
   * far more numerous — cannot satisfy the match.
   */
  readonly navLinks: Locator;

  /** The site logo link in the header, pointing at the site root. */
  readonly logoLink: Locator;

  /**
   * Keyboard skip link.
   *
   * Rendered visually-hidden until focused, so assert its presence and target
   * rather than its visibility.
   */
  readonly skipLink: Locator;

  /** Telephone links anywhere in the page chrome. */
  readonly telLinks: Locator;

  /** Cookie-consent "privacy choices" preference-centre trigger, in the footer. */
  readonly privacyChoicesButton: Locator;

  constructor(page: Page) {
    this.page = page;

    this.header = siteHeader(page);
    this.nav = primaryNav(page);
    this.main = siteMain(page);
    this.footer = siteFooter(page);

    this.navLinks = this.nav.locator(
      '> div > ul.we-mega-menu-ul > li.we-mega-menu-li > a',
    );
    this.logoLink = page.locator('#logo a[href="/"]');
    this.skipLink = page.locator('a[href="#main-content"]');
    this.telLinks = page.locator('a[href^="tel:"]');
    this.privacyChoicesButton = page.locator('#ot-sdk-btn');
  }

  /**
   * Load the homepage.
   */
  async goto(): Promise<void> {
    await this.page.goto('/', { waitUntil: 'domcontentloaded' });
  }

  /**
   * A top-level primary navigation link by its visible text.
   *
   * @param name
   *   The link text, e.g. 'Veteran Disability'.
   */
  navLink(name: string): Locator {
    return this.navLinks.filter({ hasText: name }).first();
  }

  /**
   * A homepage content section by its block id.
   *
   * The homepage body is assembled from custom blocks rather than Views, so a
   * missing section leaves the page rendering successfully with content
   * silently absent.
   *
   * @param blockId
   *   The block id without the `block-` prefix, e.g. 'veterandisabilityapplication'.
   */
  contentBlock(blockId: string): Locator {
    return this.page.locator(`#block-${blockId}`);
  }
}
