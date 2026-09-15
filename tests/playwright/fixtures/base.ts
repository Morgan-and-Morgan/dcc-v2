import { test as base } from '@playwright/test';

/**
 * Extended test fixture.
 *
 * Add shared setup here as the suite grows — authenticated sessions, custom
 * matchers, shared page state, etc.
 *
 * Usage in specs:
 *   import { test, expect } from '../fixtures/base';
 *
 * Importing from here rather than '@playwright/test' directly means extensions
 * are picked up automatically without editing every spec file.
 *
 * This site runs no A/B testing tag whose redirect campaigns would move the
 * page mid-run, so there is nothing to block and the fixture is a plain
 * pass-through. Override the worker `browser` fixture here if one is ever
 * introduced.
 */
export const test = base;

export { expect } from '@playwright/test';
