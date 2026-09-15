# Specs

One spec file per page, paired with its Page Object in `../pages/`.

Run all tests (defaults to production):
  cd tests/playwright && npx playwright test

Run against the Pantheon dev environment, as CI does:
  PLAYWRIGHT_BASE_URL=https://dev-dcc-v2.pantheonsite.io npx playwright test

Run a single spec:
  npx playwright test tests/homepage.spec.ts

Run in headed mode (watch the browser):
  npx playwright test --headed

Open the HTML report after a run:
  npx playwright show-report

## Sharding

CI runs this suite as 4 parallel shards (`--shard=N/4`). Playwright shards by
**spec file**, not by test, so with a single spec file today shard 1 runs all
21 tests and shards 2–4 run none. Empty shards still exit 0 and still upload a
blob report, so the merge and the Slack summary are unaffected — the
parallelism simply does not pay off until there is more than one spec file.
Add specs rather than lowering the shard count.

## Known theme defects this suite documents but does not enshrine

- The skip link points at `#main-content`, but no element carries that id, so
  activating it goes nowhere. `homepage.spec.ts` asserts the link and notes the
  assertion to add once the template is fixed.
- Production emits its canonical URL over `http://` rather than `https://`, so
  canonical assertions check shape rather than an exact value.
