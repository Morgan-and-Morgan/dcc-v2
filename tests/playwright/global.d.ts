/**
 * Ambient type declarations for the third-party globals the suite probes via
 * `page.evaluate`. These are injected at runtime by the site's tag manager and
 * are not part of the standard DOM `Window` type.
 *
 * Google Tag Manager (container GTM-KH52X49) is delivered by Drupal's
 * google_tag module, which serves the container bootstrap from
 * /sites/default/files/google_tag/google_tag/disability_care_center/google_tag.script.js
 * and creates `window.dataLayer`. Note that the rendered HTML contains only
 * GTM's <noscript> iframe inline; the dataLayer is created by that external
 * script at runtime, so it must be asserted in the browser rather than by
 * matching markup.
 *
 * `window.gtag` is declared because a consent integration may define it once
 * the GTM container loads; assertions that depend on it should tolerate its
 * absence rather than require it.
 */
export {};

declare global {
  interface Window {
    /** Google Tag Manager data layer. */
    dataLayer?: unknown[];
    /** Google Analytics / gtag.js function, defined by the GTM container. */
    gtag?: (...args: unknown[]) => void;
  }
}
