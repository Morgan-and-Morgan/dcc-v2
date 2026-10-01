# Page Objects

One TypeScript class per page or major component.

Naming:
- File:  `{slug}.ts`        e.g. `homepage.ts`
- Class: `{ClassName}Page`  e.g. `HomepagePage`

Build landmark locators from `../helpers/landmarks` rather than raw selectors,
so a theme change is a one-line fix.

Verify selectors against the rendered page, not the Twig templates. The theme
emits no explicit ARIA `role` attributes and nests two `<nav>` elements in the
navigation region, so several locators here are deliberately class-scoped
rather than role-scoped — see the notes in `../helpers/landmarks.ts`.
