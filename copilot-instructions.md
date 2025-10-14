# Composer package project plan — **WP CMF** (GitHub-ready)

Nice — here’s a complete, GitHub-compatible project plan you can drop into a repo. It includes milestones, issues/tasks (with suggested labels, priorities, and acceptance criteria), repo layout, example usage (array + JSON), testing & CI, and release guidance. I designed this to match your requirements: modular/custom post types, full field set + pluggable field API, array + JSON configuration, composer-f# Quick roadmap timeline (no blocking assumptions)

* Sprint 1 (1–2 weeks): M1 + M2 (skeleton, CPT, settings page).
* Sprint 2 (2–3 weeks): M3 (fields + extensibility).
* Sprint 3 (1–2 weeks): M4 (JSON + array config).
* Sprint 4 (1–2 weeks): M5 + M6 (security, tests, docs, examples).
* Sprint 5 (1 week): M7 CI & first release.

---

# Implementation Status & Example Organization

## ✅ Completed Milestones

### Milestone 1 - Project Init ✅
- ✅ Composer.json with PSR-4 autoloading
- ✅ Manager and Registrar skeleton classes
- ✅ Coding standards and tooling (PHPUnit, PHPCS, PHPStan)

### Milestone 2 Feature 1 - Custom Post Type Registration ✅
- ✅ CustomPostType class implemented (`src/CPT/CustomPostType.php`)
- ✅ Array configuration support
- ✅ Registrar integration updated
- ✅ Comprehensive test suite (13/13 tests passing)
- ✅ Organized examples in separate folders

## 📁 Example Organization

Examples are now organized in dedicated folders for better maintainability:

- **`examples/cpt-direct-usage/`** - Direct CustomPostType class usage with fluent interface
- **`examples/cpt-manager-usage/`** - Manager/Registrar integration with array config
- **`examples/cpt-advanced-config/`** - Advanced CPT with custom labels and rewrite rules
- **`examples/cpt-custom-capabilities/`** - Custom capability mapping for role-based access

Each example folder contains:
- `example.php` - Working code implementation
- `README.md` - Detailed documentation and explanation

## 🧪 Testing

Current test coverage: **13/13 tests passing**
- 8 CustomPostType tests (construction, configuration, fluent interface, etc.)
- 3 Manager tests (singleton, initialization, registrar access)
- 2 Registrar tests (construction, WordPress integration)

---

> You asked not to delay — I delivered the plan now. If you want, I can convert the above into a set of ready-to-create GitHub issues (JSON/CSV) or a `README.md` and `ISSUE_TEMPLATE.md` files — tell me which format and I'll produce them in the next message.ts, examples and docs.

---

# Repository overview (suggested name)


PSR: PSR-4 autoloading, PHP 8.1+ recommended.

---

# High-level milestones

1. **M1 — Project Init, core architecture & packaging**
2. **M2 — Custom Post Type & Settings Page Registration**
3. **M3 — Field Types (Core) + Field API (extensibility)**
4. **M4 — Array & JSON-driven registration**
5. **M5 — Validation, Sanitization, Escaping, i18n, Security**
6. **M6 — Tests, Examples & Documentation**
7. **M7 — CI/CD, Packaging, Release (first stable)**

Each milestone below lists issues/tasks you can create in GitHub under that milestone.

---

# Repository structure (recommended)

```
/
├─ src/
│  ├─ Core/
│  │  ├─ Manager.php              # central registry / bootstrap
│  │  ├─ Registrar.php            # registers CPTs, menus, settings
│  ├─ CPT/
│  │  ├─ CustomPostType.php
│  ├─ Settings/
│  │  ├─ SettingsPage.php
│  ├─ Field/
│  │  ├─ FieldInterface.php
│  │  ├─ AbstractField.php
│  │  ├─ fields/
│  │  │  ├─ TextField.php
│  │  │  ├─ SelectField.php
│  │  │  ├─ CheckboxField.php
│  │  │  └─ ... (all core inputs)
│  │  ├─ FieldFactory.php        # builds field from array/json
│  ├─ Json/
│  │  ├─ SchemaValidator.php
│  ├─ Helpers/
│  │  ├─ Sanitizers.php
│  │  ├─ Validators.php
├─ examples/
│  ├─ cpt-direct-usage/           # Direct CustomPostType usage
│  ├─ cpt-manager-usage/          # Manager/Registrar integration
│  ├─ cpt-advanced-config/        # Advanced CPT configuration
│  ├─ cpt-custom-capabilities/    # Custom capabilities example
│  ├─ plugin-array-example/       # Array-based configuration (M4)
│  ├─ plugin-json-example/        # JSON-based configuration (M4)
├─ tests/
├─ docs/
│  ├─ usage.md
│  ├─ field-api.md
├─ .github/
│  ├─ workflows/
│  │  ├─ ci.yml
│  ├─ ISSUE_TEMPLATE.md
├─ composer.json
├─ README.md
```

---

# Milestone 1 — Project Init, core architecture & packaging

**Goal:** Initialize package, define core bootstrap and packaging.

**Issues:**

1. `chore: init composer.json and PSR-4 autoloading`

   * Labels: `chore`, `setup`
   * Description: Create `composer.json` with package name, autoload, PHP requirement. Add basic README and LICENSE.
   * Acceptance: `composer install` works, `vendor/autoload.php` loads classes.

2. `feat: create Manager and Registrar skeleton`

   * Labels: `backend`, `architecture`
   * Description: `Manager` (singleton/factory) to coordinate registration. `Registrar` handles hook binding.
   * Acceptance: Unit tests verify Manager can be instantiated and registrar binds hooks (mocked).

3. `chore: add coding standards and tooling`

   * Labels: `chore`, `ci`
   * Install: PHPCS (WordPress rules), PHPStan baseline (level medium), phpunit.
   * Acceptance: Linting & static analysis run in CI.

---

# Milestone 2 — Custom Post Type & Settings Page Registration

**Goal:** Provide API to declare CPTs and settings pages programmatically.

**Issues:**

1. `feat: implement CustomPostType class`

   * Labels: `feature`, `cpt`
   * Description: Register labels, args, supports. Accepts array config.
   * Acceptance: Example plugin registers CPT; CPT appears in WP admin.

2. `feat: implement SettingsPage class`

   * Labels: `feature`, `settings`
   * Description: Create structure for top-level/sub-menu pages, callback rendering, capability checks.
   * Acceptance: Example plugin adds settings menu and page renders.

3. `test: CPT and Settings registration unit tests`

   * Labels: `test`
   * Acceptance: Tests for config validation & successful hook registration.

---

# Milestone 3 — Field Types (Core) + Field API (extensibility)

**Goal:** Core set of fields and pluggable API for custom field types.

**Issues:**

1. `feat: define FieldInterface & AbstractField`

   * Labels: `feature`, `field`
   * Description: Interface methods: render(), sanitize($input), validate($input), getName(), getSchema(); AbstractField implements common helpers.
   * Acceptance: New field classes implement interface.

2. `feat: implement core fields (Text, Textarea, Select, Radio, Checkbox, Number, Date, Email, URL, File, Image, WYSIWYG, Password)`

   * Labels: `feature`, `field`
   * Acceptance: Each field renders correct input, sanitizes and validates basic rules.

3. `feat: FieldFactory to create fields from config`

   * Labels: `feature`
   * Description: Build field instances using a registry or mapping. Provide `registerFieldType()` for third-party fields.
   * Acceptance: Third-party field can be registered via `FieldFactory::register('color', ColorField::class)`.

4. `doc: field API documentation page`

   * Labels: `docs`
   * Acceptance: docs/field-api.md with examples.

---

# Milestone 4 — Array & JSON-driven registration

**Goal:** Support array-based config and JSON file import for settings/CPT + fields.

**Issues:**

1. `feat: accept fields via PHP array`

   * Labels: `feature`
   * Description: Provide `Manager::registerFromArray($config)` where `$config` defines CPT/settings and a `fields` array.
   * Acceptance: Example plugin uses array config and fields appear on the page/CPT metabox.

2. `feat: implement JSON loader`

   * Labels: `feature`, `json`
   * Description: `Manager::registerFromJson($pathOrString)` that validates JSON against a schema and registers definitions. Support specifying JSON path on the command line (WP-CLI integration optional).
   * Acceptance: Example plugin uses `registerFromJson(__DIR__ . '/config.json')` and works.

3. `feat: define JSON Schema` (`schema.json`)

   * Labels: `feature`, `json`
   * Acceptance: Schema covers CPT/settings page fields definitions, field attributes, validation rules.

4. `test: JSON schema validation tests`

   * Labels: `test`
   * Acceptance: Passing & failing payloads tested.

**Example array format (usage snippet)**

```php
use Namith\WPAdminFields\Manager;

$config = [
  'settings_pages' => [
    [
      'id' => 'my-plugin-settings',
      'title' => 'My Plugin',
      'menu_title' => 'My Plugin',
      'capability' => 'manage_options',
      'slug' => 'my-plugin',
      'fields' => [
        [
          'name' => 'site_welcome',
          'type' => 'text',
          'label' => 'Welcome message',
          'default' => 'Hello world',
          'args' => ['placeholder' => 'Enter text'],
        ],
        [
          'name' => 'color',
          'type' => 'color',
          'label' => 'Theme color',
        ],
      ]
    ]
  ],
  'cpts' => [
    [
      'id' => 'book',
      'args' => ['label' => 'Books', 'supports' => ['title','editor']],
      'fields' => [ /* metabox fields */ ]
    ]
  ]
];

Manager::init()->registerFromArray($config);
```

**Example JSON snippet**

```json
{
  "settings_pages": [
    {
      "id": "my-plugin-settings",
      "title": "My Plugin",
      "menu_title": "My Plugin",
      "capability": "manage_options",
      "slug": "my-plugin",
      "fields": [
        {
          "name": "site_welcome",
          "type": "text",
          "label": "Welcome message",
          "default": "Hello world",
          "args": {"placeholder": "Enter text"}
        }
      ]
    }
  ]
}
```

---

# Milestone 5 — Validation, Sanitization, Escaping, i18n, Security

**Goal:** Secure, standards-compliant handling.

**Issues:**

1. `feat: sanitize & validate pipeline for fields`

   * Labels: `feature`, `security`
   * Description: Per-field `sanitize()` & `validate()` methods. Global hooks for custom validators.
   * Acceptance: Inputs saved via settings/CPT are sanitized & validated; tests exist.

2. `feat: nonces and capability checks`

   * Labels: `feature`, `security`
   * Acceptance: Forms include nonce fields; only users with proper capabilities can save.

3. `feat: escaping output on render`

   * Labels: `feature`, `security`
   * Acceptance: All field outputs escaped (esc_attr, esc_html, esc_url etc).

4. `feat: i18n support`

   * Labels: `feature`, `i18n`
   * Description: Text domain support; `__()` wrappers in labels and messages.
   * Acceptance: Docs show how plugin sets text domain.

---

# Milestone 6 — Tests, Examples & Documentation

**Goal:** Provide robust test suite, examples and docs.

**Issues:**

1. `test: add PHPUnit test suite (unit + integration)`

   * Labels: `test`
   * Acceptance: Tests for Manager, Registrar, FieldFactory, core fields pass locally and in CI.

2. `feat: create examples plugin (array-based)`

   * Labels: `example`, `docs`
   * Acceptance: Install via composer path in WP install; demonstrates registering settings page & CPT.

3. `feat: create examples plugin (json-based)`

   * Labels: `example`, `docs`
   * Acceptance: Demonstrates `registerFromJson`.

4. `doc: write README and usage docs`

   * Labels: `docs`
   * Acceptance: README includes quickstart, API, JSON schema, and contribution guide.

5. `doc: field reference & extension guide`

   * Labels: `docs`
   * Acceptance: A `docs/field-api.md` with examples to create and register custom fields.

6. `feat: generate docs site (GitHub Pages)`

   * Labels: `docs`, `ci`
   * Acceptance: docs deployed from `docs/` or `gh-pages`.

---

# Milestone 7 — CI/CD, Packaging, Release (first stable)

**Goal:** Automated tests, releases and semantic versioning.

**Issues:**

1. `ci: add GitHub Actions workflow`

   * Labels: `ci`
   * Tasks: Run PHPStan, PHPCS, phpunit on PR, on push to main run tests & build artifacts.

2. `ci: code coverage & badge`

   * Labels: `ci`
   * Acceptance: Upload coverage to Codecov or Coveralls; README badge added.

3. `chore: tag v0.1.0 and release process`

   * Labels: `chore`, `release`
   * Acceptance: Release notes template; use semver (0.x for alpha).

4. `chore: Packagist listing & composer.json metadata`

   * Labels: `chore`
   * Acceptance: Package is discoverable via Packagist (after initial release).

---

# Labels suggested (GitHub)

* `bug`
* `feature`
* `chore`
* `test`
* `docs`
* `ci`
* `security`
* `example`
* `help wanted`
* `good first issue`

---

# Example Issue templates (short)

**Feature:** *Implement FieldFactory registry*

* Description: Create `FieldFactory` with `registerType($name, $class)` and `create(array $config)` methods. Ensure PSR-4 autoload and tests.
* Acceptance criteria:

  * `FieldFactory::registerType('color', ColorField::class)` works.
  * `FieldFactory::create(['type'=>'color','name'=>'bg'])` returns `ColorField` instance.
  * Unit tests included.

**Bug:** *Select field not escaping options*

* Repro: Add select field with user-supplied options containing `"<script>"`.
* Expected: option labels are escaped with `esc_html`.
* Fix: update `SelectField::render()` to escape labels.

---

# API design notes (minimal & practical)

* **Manager**: central entry point `Manager::init($options = [])` returns a singleton.

  * `registerFromArray(array $config)`
  * `registerFromJson(string $pathOrJson)`
  * `registerFieldType(string $type, string $class)` (alias to FieldFactory)
* **Field config array keys**:

  * `name` (string, required)
  * `type` (string, required)
  * `label` (string)
  * `description` (string)
  * `default`
  * `args` (array of field-specific args: placeholder, options, attributes)
  * `validation` (rules: required, pattern, min, max, sanitize callbacks, custom)
  * `context` (for CPT metabox: side/normal/advanced)
* **FieldInterface methods**

  * `render(array $value): string`
  * `sanitize(mixed $input): mixed`
  * `validate(mixed $input): array` → returns `[valid => bool, errors => []]`
  * `getName(): string`
  * `getSchema(): array` (for JSON schema generation)
* **Extensibility**

  * Field registration via `FieldFactory::registerType()`.
  * Ability to pass callbacks (callable) in config for custom sanitizers/validators.
  * WP hooks: `wpaf/field/render/{type}`, `wpaf/field/sanitize/{type}`, `wpaf/field/validate/{type}` so plugin authors can hook into processing.

---

# JSON Schema (high-level)

* Provide `schema.json` in repo root describing structure:

  * root: `settings_pages` (array), `cpts` (array)
  * `fields` object schema: `name`, `type`, `label`, `default`, `args`, `validation`
* Implementation: use `justinrainbow/json-schema` or `opis/json-schema` for validation.

---

# Testing stack & approach

* **Unit tests:** PHPUnit, mock WordPress functions using `wp_mock` or `Brain Monkey` (for light integration), or use the WordPress PHPUnit test suite in integration mode.
* **Integration tests:** spin up WP test environment (GH Actions matrix: PHP versions 8.1–8.2).
* **Test coverage target:** 80%+ for core.

---

# CI / GitHub Actions (suggestions)

* Workflow `ci.yml`:

  * On PR: run phpstan, phpcs, phpunit.
  * On push to `main`: run tests, generate docs, build phar (if desired).
  * On release: create GitHub release with changelog generated from commits.

---

# Documentation & Examples

* README: Quickstart + composer require + basic usage snippet.
* docs/usage.md: Full examples (array + JSON + registering custom field types).
* docs/field-api.md: Step-by-step how to add a custom field type and register it.
* examples: two small example plugins demonstrating real usage (copyable into `wp-content/plugins` during dev).

---

# Security & best practices checklist (automated tasks)

* Use nonces for form submissions.
* Capability checks for settings and CPT management.
* All output escaped; inputs sanitized.
* Avoid eval() or dynamic includes; require validated JSON schema.
* Default text domain in package; allow plugin to set text domain when instantiating.

---

# Release & maintenance suggestions

* Follow semantic versioning. Start `v0.1.0` for alpha; `v1.0.0` when stable.
* Changelog via `keepachangelog` format.
* Tag releases on GitHub and notify Packagist webhook.
* Maintain `UPGRADE.md` for breaking changes.

---

# Quick roadmap timeline (no blocking assumptions)

* Sprint 1 (1–2 weeks): M1 + M2 (skeleton, CPT, settings page).
* Sprint 2 (2–3 weeks): M3 (fields + extensibility).
* Sprint 3 (1–2 weeks): M4 (JSON + array config).
* Sprint 4 (1–2 weeks): M5 + M6 (security, tests, docs, examples).
* Sprint 5 (1 week): M7 CI & first release.

> You asked not to delay — I delivered the plan now. If you want, I can convert the above into a set of ready-to-create GitHub issues (JSON/CSV) or a `README.md` and `ISSUE_TEMPLATE.md` files — tell me which format and I’ll produce them in the next message.
