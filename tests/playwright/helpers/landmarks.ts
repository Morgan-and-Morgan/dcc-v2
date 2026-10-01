import { type Page, type Locator } from '@playwright/test';

/**
 * Shared locators for site-wide layout landmarks.
 *
 * Page Objects should build landmark locators from here rather than writing
 * raw element selectors, so a theme change is a one-line fix in this file
 * instead of an edit to every POM.
 *
 * The dcc theme renders bare <header>, <main> and <footer> elements with no
 * explicit `role` attributes. These target the roles anyway, because each of
 * those elements carries the role implicitly: <main> is always `main`, and
 * <header>/<footer> map to `banner`/`contentinfo` as long as they are not
 * nested inside an <article>, <section>, <nav> or <aside>. Verified against the
 * rendered page: both are direct descendants of <body>, so the roles resolve.
 */

/**
 * The site header.
 *
 * Contains the logo and the header telephone link. Note that the primary
 * navigation is NOT inside this element — see primaryNav().
 */
export function siteHeader(page: Page): Locator {
  return page.getByRole('banner');
}

/**
 * The site's primary navigation.
 *
 * Deliberately NOT scoped inside the banner: the theme renders the navigation
 * region as a sibling that follows </header>, not a child of it, so scoping to
 * the banner resolves to zero elements.
 *
 * Targets the class rather than the `navigation` role because there are two
 * nested <nav> elements here — an outer bare <nav> wrapping the navigation
 * region, and this inner one emitted by the WE Mega Menu block
 * (#block-headernavigation-2). A bare getByRole('navigation') matches both and
 * trips strict mode.
 */
export function primaryNav(page: Page): Locator {
  return page.locator('nav.header-navigation');
}

/**
 * The page's main landmark.
 */
export function siteMain(page: Page): Locator {
  return page.getByRole('main');
}

/**
 * The page's contentinfo (footer) landmark.
 *
 * Also holds the cookie-consent preference trigger.
 */
export function siteFooter(page: Page): Locator {
  return page.getByRole('contentinfo');
}
