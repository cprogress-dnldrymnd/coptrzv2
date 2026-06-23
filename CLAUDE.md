# Coptrz Theme (coptrzv2)

A bespoke WordPress theme ("Coptrz by cProgress") built for a WooCommerce-powered
drone/equipment retail and content site (industries, capabilities, guides,
case studies, rentals, landing pages, etc).

- Theme name: `Coptrz by cProgress`, text domain `coptrz-theme`
- Version constant: `coptz_version` in `functions.php`
- No JS package manager / bundler — vendor JS/CSS (Bootstrap 5.3.3, Swiper,
  intl-tel-input) is pulled in via `composer.json` or CDN links in
  `enqueue_scripts()` (`functions.php`). Carbon Fields is no longer a runtime
  dependency.

## Build / styles

- Styles are authored in SCSS (`style.scss`, `admin/css/admin.scss`) and
  compiled to `style.css` / `admin/css/admin.css` (+ `.map` files checked in,
  likely via a Sass Live Compile VS Code extension — see `.vscode/settings.json`,
  `px-to-rem` is configured at 16px).
- `style.scss` import order: vendor (Bootstrap, Swiper) → mixins/variables/fonts
  → base (typography, helpers, base, animation) → plugins (woocommerce, wpforms,
  cf7, booqable) → sections (header, footer, hero, archive, landing).
- SCSS partials live under `assets/scss/`. There is no separate build step
  documented — edit the `.scss` and re-export `.css`/`.map`.
- `landing.css` is a separate standalone stylesheet for landing pages.

## PHP architecture

- `functions.php` is the entry point: defines constants (`theme_dir`,
  `assets_dir`, `image_dir`, `vendor_dir`), theme setup, enqueue logic, and
  meta-shim helper wrappers (`get__post_meta`, `get__term_meta`,
  `get___term_meta`, `get__post_meta_by_id`, `get__theme_option`). Always use
  these wrappers rather than calling the meta-shim directly. Also registers
  `dd_button_popup_render` (`render_block_core/button` filter) which injects
  Bootstrap modal attributes into core/button blocks that have `ddPopupId` set
  and pushes the ID into `$popups_id` for footer rendering.
- `assets/js/extend-button-popup.js` — Gutenberg block editor extension
  (enqueued on `enqueue_block_editor_assets`) that adds an "Open Popup"
  InspectorControls panel to `core/button` blocks. Stores the selection as a
  `ddPopupId` attribute; fetches published `popups` posts via the WP REST API.
  Works in tandem with `dd_button_popup_render` in `functions.php`.
- Carbon Fields has been replaced by a bespoke shim (`includes/meta-shim/` +
  `includes/meta-reader.php`). The shim implements the same `Container::make()`
  / `Field::make()` chainable API as CF3 and reads/writes data in CF3's
  pipe-delimited meta-key format — so existing DB rows are untouched.
  - `includes/meta-shim/Key_Formatter.php` — encodes/decodes the CF3 key
    format (`_root|hierarchy|hierarchy_index|value_index|property`) and
    provides static `read_post/read_term/read_option` and
    `write_simple_*/write_complex_*` helpers that do the actual DB work.
  - `includes/meta-shim/Field.php` — pure descriptor with chainable setters
    matching CF3's API (`set_options`, `add_options`, `set_attribute`,
    `set_alpha_enabled`, etc.).
  - `includes/meta-shim/Container.php` — registers meta boxes / options pages,
    renders admin UI, and saves via `Key_Formatter`.
  - `includes/meta-reader.php` — standalone read functions wrapping
    `Key_Formatter::read_*`; drop-in replacements for `carbon_get_*` calls.
  The shim is booted via `CoptrzTheme\MetaShim\Container::boot()` on
  `after_setup_theme` (priority 5), then `post-meta.php` is loaded on `init`
  (priority 0). `post-meta.php` uses `CoptrzTheme\MetaShim\Container` and
  `CoptrzTheme\MetaShim\Field` in place of the old Carbon Fields namespaces.
  `post-meta.php` is skipped only when both `is_admin()` and the blocks-editor
  template are active simultaneously.
- `includes/_required_files.php` loads the rest of `includes/` in order:
  `schema.php`, `post-types.php`, then (skipped on the block-editor template /
  admin) `elements.php`, `modules.php`, `ajax.php`, `svg.php`, then
  `shortcodes.php`, `hooks.php`, `theme-widgets.php`, `menus.php`,
  `woocommerce.php`, `customizer.php`, `marquee.php`.
- `vendor/` is the Composer vendor dir (Bootstrap + legacy `htmlburger/carbon-fields`
  source still present but not a runtime dependency — the bespoke shim replaces it).
- `assets/js/admin-meta-boxes.js` + `assets/css/admin-meta-boxes.css` — admin
  UI for the meta shim (tab nav, repeater add/remove, conditional logic, media
  uploader, association AJAX search). Enqueued separately for the admin.

### Key `includes/` files

- `post-types.php` — registers all custom post types/taxonomies via small
  `newPostType`/`newTaxonomy` wrapper classes: `testimonials`, `faq`, `team`,
  `casestudies`, `guides`, `industries`, `capabilities`, `events` (+ location/
  category/type taxonomies), `layouts`, `popups`, `compareproducts`,
  `producttaxonomypages`, `globalpostboxes`, `rentals`, `landingpages`, `quiz`,
  `documents`.
- `post-meta.php` (~7200 lines) — meta box/field definitions for all the above
  post types, using the `CoptrzTheme\MetaShim\Container` / `Field` API (same
  chainable style as Carbon Fields 3). Largest file in the theme; search by
  post type name when adding/editing fields.
- `modules.php` — misc snippet-style hooks (save-post handlers, date
  formatting helpers like `_date_format`, etc). Each function is a standalone
  "module" with a doc comment.
- `hooks.php` — general action/filter hooks, including CF7 integrations (see
  Forms below).
- `elements.php`, `shortcodes.php`, `theme-widgets.php`, `menus.php`,
  `customizer.php`, `marquee.php`, `ajax.php`, `schema.php`, `checkout.php` —
  one concern per file, named accordingly. `__button()` in `elements.php`
  resolves popup post IDs via `apply_filters('wpml_object_id', ...)` for WPML
  compatibility.
- `woocommerce.php` (2350 lines) — WooCommerce template/hook overrides; pairs
  with the `woocommerce/` directory which overrides core WooCommerce templates
  (`archive-product.php`, `cart/`, `checkoutx/`, `loop/`, `single-product/`,
  `global/`, `content-single-product.php`).

### Templates & template parts

- `templates/` — full page templates selectable in the editor (e.g.
  `page-landing.php`, `page-modules.php`, `page-product-form.php`,
  `page-quiz.php`, `page-calculator.php`, `page-blocks-editor.php`,
  `page-gutenberg.php`, etc).
- `template-parts/header/` — header pieces (`header-left`, `header-menu`,
  `header-right`, `header-right-landing`), pulled into `header.php` via
  `get_template_part()`.
- `template-parts/sections/` — reusable content sections (hero, CTA, USP,
  testimonials, industries, products, guides, checklists, chips, etc.) used
  by the "modules"/page-builder style templates and driven by meta-shim data
  from `post-meta.php`.
- `template-parts/single/` — single-post templates for `capabilities`,
  `industries`, `events`, plus generic `single-post.php`.
- `template-parts/product-form/` — multi-section product configurator form
  (`section-1`..`section-4`, `section-video`).
- Multiple header/footer variants exist for different layouts: `header.php`,
  `header-clean.php`, `header-simple.php`, `header-landing.php`,
  `header-landing-v2.php`; `footer.php`, `footer-clean.php`,
  `footer-simple.php`, `footer-landing.php`.

### Forms — CF7 → Zapier

- `includes/hooks.php` adds `submission_date` and `form_name` fields to the
  payload sent by the "Contact Form 7 to Zapier" plugin via the
  `ctz_get_data_from_contact_form` filter (`dd_append_date_to_cf7_zapier_payload`),
  since that plugin builds its own payload and ignores `wpcf7_posted_data`.
- Also in `hooks.php`: a `wpcf7mailsent` JS listener for post-submit redirects.

## Conventions / gotchas

- Many files are authored as standalone "snippets" with
  `Plugin/Snippet Author: Digitally Disruptive - Donald Raymundo` doc blocks —
  keep that style when adding new hook-based functions to `modules.php`/
  `hooks.php`.
- `capabilites.txt` and `industries.txt` at the project root are legacy
  reference snippets that copied shared "global default" CF3 content (via
  `carbon_set_post_meta`) — not auto-loaded, kept for historical reference only.
- Several files have `.old`/`.backup`/`.phpx` suffixes (e.g.
  `sidebar-shop.php.old`, `single-quiz.php.backup`, `page-checkout.phpx`,
  `woocommerce/content-single-product.php.backup`) — these are inactive,
  kept for reference only.
- `$popups_id`, `$layouts_global`, `$product_taxonomy_page` are theme-wide
  globals initialized in `action_after_setup_theme()` and appended to in
  templates (e.g. `header.php` pushes popup post IDs based on context).
