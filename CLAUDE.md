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
  `dd_button_popup_render` (`render_block_core/button` filter) which replaces
  the rendered `<a>` element with a `<button type="button">` carrying
  `data-bs-toggle="modal"` / `data-bs-target="#modal-{id}"` for `core/button`
  blocks that have `ddPopupId` set, then appends the modal HTML inline via
  `do_shortcode('[popup id="..."]')`; a static `$rendered_popups` array ensures
  the modal HTML is emitted only once per popup even when multiple buttons
  target the same popup. The shortcode call is guarded with
  `function_exists('__popup')` so the filter is safe in admin/REST contexts
  where `modules.php` (and therefore `__popup`) is not loaded.
- `assets/js/main.js` — main frontend JS, runs on `jQuery(document).ready`.
  Initialises all frontend behaviors: mini-cart, header menu, accordions,
  Swiper carousels, phone inputs, AJAX, hero, post navigation, URL param
  passthrough, and `initResponsiveTableCards` (converts `.responsive--table-2`
  comparison tables to column-card layout on mobile).
- `assets/js/extend-button-popup.js` — Gutenberg block editor extension
  (enqueued on `enqueue_block_editor_assets`) that adds an "Open Popup"
  InspectorControls panel to `core/button` blocks. Stores the selection as a
  `ddPopupId` attribute; fetches published `popups` posts via the WP REST API.
  Works in tandem with `dd_button_popup_render` in `functions.php`.
  **Gotcha:** `core/button` renders an `<a>` element; `dd_button_popup_render`
  uses `preg_replace_callback` to swap it for a `<button type="button">` (strips
  `href`, adds Bootstrap modal attrs). The popup modal HTML is rendered inline
  (not in the footer) via the `[popup]` shortcode.
- Carbon Fields has been replaced by a bespoke shim (`includes/meta-shim/` +
  `includes/meta-reader.php`). The shim implements the same `Container::make()`
  / `Field::make()` chainable API as CF3 and reads/writes data in CF3's
  pipe-delimited meta-key format — so existing DB rows are untouched. All files
  live in the `CoptrzTheme\MetaShim` namespace.
  - `includes/meta-shim/Key_Formatter.php` — pure codec for the CF3 key format
    (`_root|field:chain|group:indexes|value_index|property`, a port of CF's
    `Key_Toolset`): `build_key()` / `parse_key()`, plus cache-backed data access
    (`load_root_map()` reads post/term meta via WP's object cache and theme
    options via a single targeted query; `persist_root()` / `delete_root()` do
    the delete-then-insert writes).
  - `includes/meta-shim/Field.php` — pure descriptor with the CF3 chainable
    setters (`set_options`, `set_conditional_logic`, `set_types`, `add_fields`
    for named complex groups, etc.) plus `storage_kind()` introspection
    (scalar / multi / association / complex / none). Unmodelled setters no-op via
    `__call`.
  - `includes/meta-shim/Container.php` — `make()/where()/or_where()/add_tab()/
    add_fields()`; builds the global field-tree INDEX
    (`object_type → field_name → Field`) that the reader and renderer query.
  - `includes/meta-shim/Container_Admin.php` — `boot()` wires the WP admin
    lifecycle: post meta boxes + `save_post`, theme-options pages + save, term
    fields + save, nav-menu-item fields + save, and the association AJAX search.
    Display conditions (`where`) are evaluated for `post_type`/`post_template`/
    `term_taxonomy`.
  - `includes/meta-shim/View.php` — server-side HTML renderer for every field
    type + nested repeaters; emits a flat markup contract enhanced by the JS.
  - `includes/meta-shim/Writer.php` — `build_flat()` (exact inverse of the
    reader) shared by the admin save and the programmatic `coptrz_set_post_meta`
    / `coptrz_set_term_meta` / `coptrz_set_theme_option` API; `serialize()`
    normalises a Carbon-format value tree (reader output) back into posted shape.
  - `includes/meta-reader.php` — `Reader::read()` tree-aware reconstruction +
    the drop-in functions `coptrz_get_post_meta` / `coptrz_get_the_post_meta` /
    `coptrz_get_term_meta` / `coptrz_get_theme_option` /
    `coptrz_get_nav_menu_item_meta`. NOTE: both `meta-reader.php` and `Writer.php`
    use **braced** namespace syntax because they declare a named namespace *and*
    a global (`namespace {}`) block in one file — do not convert to unbracketed.
  - `includes/meta-shim/self-test.php` — transitional parity checker (admin-only,
    `?coptrz_meta_selftest=<post_id>`), compares `carbon_get_post_meta` vs the
    shim while CF is still active. Remove after sign-off.
  The shim is required at the top of `functions.php`; `tissue_paper_register_custom_fields()`
  (hooked on `after_setup_theme`, priority 20) loads `post-meta.php` (which now
  `use`s `CoptrzTheme\MetaShim\Container` / `Field`) then calls
  `Container_Admin::boot()`. `post-meta.php` is skipped only when both
  `is_admin()` and the blocks-editor template are active simultaneously. The
  theme's meta wrappers (`get__post_meta`, `get___term_meta`,
  `get__post_meta_by_id`, `get__theme_option`) now delegate to the `coptrz_get_*`
  readers. **All `carbon_*` call sites in the theme have been replaced with
  `coptrz_*` shim equivalents — the code migration is complete.** Carbon Fields
  can be deactivated once the self-test (`?coptrz_meta_selftest=<id>`) confirms
  parity. Note: `get__term_meta` (two underscores) still delegates to raw
  `get_term_meta()` for simple scalar term fields; use `get___term_meta` (three
  underscores) for complex/nested term fields.
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
  compatibility. `_coptrz_link_aria_label($visible_text, $context_title)` in
  `elements.php` generates accessible aria-label strings for linked elements
  (returns empty string when context is already conveyed by the visible text).
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
- `single-producttaxonomypages.php` — single template for the `producttaxonomypages`
  post type. Supports a `?copy_from=<post_id>` URL param that reads meta fields
  from another post and writes them to the current post via `coptrz_set_post_meta`;
  individual field groups (`copy_after`, `training`, `software`, `drones`,
  `accessories`) are each gated by their own URL param.
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
- `register_cf7_pdf_url_attribute` (`shortcode_atts_wpcf7` filter) whitelists
  `pdf_url` on CF7 shortcodes. If the value is numeric it is treated as a
  `documents` post ID and resolved to a URL via
  `get__post_meta_by_id($id, 'document')` → `wp_get_attachment_url()`;
  otherwise it is passed through as a literal URL.

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
  templates (e.g. `header.php` pushes hardcoded popup post IDs; `modules.php`
  pushes meta-shim button popup IDs). Gutenberg `core/button` blocks with
  `ddPopupId` do NOT use `$popups_id` — their modal HTML is rendered inline by
  `dd_button_popup_render`.
