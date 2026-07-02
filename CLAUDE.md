# Coptrz Theme (coptrzv2)

A bespoke WordPress theme ("Coptrz by cProgress") built for a WooCommerce-powered
drone/equipment retail and content site (industries, capabilities, guides,
case studies, rentals, landing pages, etc).

- Theme name: `Coptrz by cProgress`, text domain `coptrz-theme`
- Version constant: `coptz_version` in `functions.php`
- No JS package manager / bundler — vendor JS/CSS (Bootstrap 5.3.3, Swiper,
  intl-tel-input) is pulled in via `composer.json` or CDN links in
  `enqueue_scripts()` (`functions.php`). Carbon Fields (`htmlburger/carbon-fields`
  ^3.6) is a live runtime dependency on this branch, loaded via the Composer
  autoloader (see `vendor/htmlburger/carbon-fields`).

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
- `style.css`/`style.css.map` are checked in compiled output, not hand-edited.
- **Gotcha:** `wp_enqueue_style('style', ...)` and most other asset enqueues use
  `coptz_version` as the cache-busting query param. Editing `style.scss`/`_base.scss`
  and recompiling `style.css` is not enough for changes to show up for returning
  visitors — bump `coptz_version` in `functions.php` too, or the browser/CDN may
  keep serving the previously cached `style.css`.

## PHP architecture

- `functions.php` is the entry point: defines constants (`theme_dir`,
  `assets_dir`, `image_dir`, `vendor_dir`), theme setup, enqueue logic, and
  Carbon Fields meta wrappers (`get__post_meta`, `get__term_meta`,
  `get___term_meta`, `get__post_meta_by_id`, `get__theme_option` — thin
  wrappers around `carbon_get_the_post_meta` / `carbon_get_term_meta` /
  `carbon_get_post_meta` / `carbon_get_theme_option`, except `get__term_meta`
  which calls plain `get_term_meta()` for simple scalar term fields). Always
  use these wrappers rather than calling `carbon_get_*` directly. Also registers
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
- `assets/js/dd-tabs-block.js` — registers two custom Gutenberg blocks:
  `dd/tab-panel` (child) and `dd/tabs` (parent). Enqueued via
  `digitally_disruptive_enqueue_swiper_editor_assets()` in `functions.php`.
  Key attributes on `dd/tabs`: `layoutStyle` (`horizontal` default | `stacked`),
  `mobileAccordion` (bool), `accordionBreakpoint` (`767` | `991` px),
  `stackedAccentColor` (CSS var `--dd-stacked-accent`), `stackedNavPosition`
  (`right` default | `left` — which side the vertical nav sits on in the
  stacked layout; only `left` is emitted as `data-nav-position` on
  `.dd-tabs-wrapper`, so existing right-aligned stacked blocks serialize
  unchanged; purely CSS-driven, no frontend JS changes needed — in
  `_base.scss`, `[data-nav-position="left"]` sets `flex-direction: row-reverse`
  on the wrapper and mirrors `.dd-vtab-button` text-align, the `:before` accent
  bar side, and the active-state padding side to match), `navPlacement`
  (`top` default | `bottom` — horizontal layout only; whether the tab nav row
  sits above or below the panels; only `bottom` is emitted as
  `data-nav-placement` on `.dd-tabs-wrapper`, so existing horizontal blocks
  serialize unchanged; `dd-tabs-frontend.js` appends `.dd-tabs-nav-desktop` to
  the end of the wrapper instead of inserting it first when this is set).
  Key attributes on
  `dd/tab-panel`: `tabTitle` (string), `tabDescription` (string, optional —
  stacked layout only; emitted as `data-tab-description` on the panel element,
  shown beneath the title in the right-hand nav when that tab is active; omitted
  from saved markup when empty so existing panels remain valid). Saved markup
  emits `data-mobile-accordion`, `data-accordion-breakpoint`, and `data-layout`
  on `.dd-tabs-wrapper`; stacked blocks pass these attributes through unchanged
  (the "Enable Accordion Conversion" toggle works for stacked layouts too).
  Existing horizontal blocks (no `layoutStyle` attribute) serialize identically
  so they remain valid — opt-in only. `dd/tabs` also carries a `deprecated`
  entry (v1) whose `save()` reproduces the earlier stacked markup (accordion
  forced off: `data-mobile-accordion="false"`, `data-accordion-breakpoint="none"`)
  so pre-accordion stacked blocks still validate in Gutenberg and get silently
  migrated to the current format on next save, instead of being flagged invalid.
- `assets/js/dd-tabs-frontend.js` — DOM-ready script that initialises all
  `.dd-tabs-wrapper` elements. Builds `.dd-tabs-nav-desktop` (horizontal nav)
  and `.dd-accordion-button` elements dynamically. Reads `data-layout`: for
  `stacked`, delegates to `buildVerticalTabs()` which produces a two-column
  layout — tab panel content in a `.dd-tabs-content-area` div on the left,
  a `.dd-tabs-nav-vertical` button list on the right; each `.dd-vtab-button`
  shows the title (`.dd-vtab-title`) and, when set, a `.dd-vtab-desc` span
  sourced from `data-tab-description` (hidden by default, shown only on the
  active tab); active state is indicated by a left border spanning the full
  button (title + description) using `var(--dd-stacked-accent, #6c47ff)`.
  On mobile (when accordion conversion is enabled), the right-hand nav is hidden
  and `.dd-accordion-button` headers injected above each panel inside
  `.dd-tabs-content-area` take over; clicking an open accordion header collapses
  it. When a `tabDescription` is set, a `.dd-tab-panel-desc` div is prepended to
  the panel so the description is visible in the accordion (mobile) view where the
  right-hand nav is not shown.
  SCSS for the stacked variant lives in `assets/scss/base/_base.scss` scoped
  to `[data-layout="stacked"]`.
- `assets/js/dd-cf7-pdf-block.js` — registers the **static** `dd/cf7-pdf-form`
  block (native editor equivalent of hand-typing
  `[contact-form-7 id="…" pdf_url="…"]` in a Shortcode block). Editor UI lets
  the user pick a CF7 form from a dropdown (populated from the `/dd/v1/cf7-forms`
  REST route, values are the CF7 hash) and a PDF either from the media library
  (`MediaUpload`) or from the `documents` post type (`/dd/v1/documents` REST
  route, which returns each document's resolved PDF `url`). Both REST routes are
  registered in `hooks.php` (`dd_register_cf7_pdf_block_rest_routes`), gated to
  `edit_posts` capability since CF7/`documents` aren't exposed via public REST.
  **`save()` emits the literal `[contact-form-7 … pdf_url="…"]` shortcode**
  (via `wp.element.RawHTML`, exactly like a native Shortcode block) so the stored
  post content — and therefore the Dynamic Text Extension `pdf_url` field that
  reads it — is byte-identical to a hand-typed shortcode. The chosen PDF is always
  stored as a **literal URL** in the `pdfUrl` attribute (a `documents` selection is
  resolved to its file URL at pick-time via the `/dd/v1/documents` `url`); the
  `pdfSource`/`pdfDocumentId` attributes are editor UI state only and don't affect
  the saved shortcode. There is **no server-side render_callback**; the existing
  `register_cf7_pdf_url_attribute` / `dd_attach_cf7_pdf_url_to_email` logic (see
  Forms below) applies to the emitted shortcode just as it does to a hand-typed one.
  **Gotcha:** this block was briefly a dynamic (`render_callback`, `save: null`)
  block; it was changed to static because a dynamic block stores only a block
  comment, so the DTX `pdf_url` field found no shortcode text to read and rendered
  empty. Any block instance saved under the old dynamic version will be flagged
  invalid in Gutenberg and must be re-inserted.
- Custom fields are registered on the `carbon_fields_register_fields` hook
  (`tissue_paper_register_custom_fields()` in `functions.php`), which requires
  `includes/post-meta.php` — a Carbon Fields 3 `Container::make()` /
  `Field::make()` definition file (`use Carbon_Fields\{Block,Container,
  Complex_Container,Field}`) for all custom post types, terms, nav-menu items,
  and theme options. `post-meta.php` is skipped only when both `is_admin()`
  and the blocks-editor template are active simultaneously (see
  `dd_is_blocks_editor_template_active()`).
- `includes/_required_files.php` loads the rest of `includes/` in order:
  `schema.php`, `post-types.php`, then (skipped on the block-editor template /
  admin) `elements.php`, `modules.php`, `ajax.php`, `svg.php`, then
  `shortcodes.php`, `hooks.php`, `theme-widgets.php`, `menus.php`,
  `woocommerce.php`, `customizer.php`, `marquee.php`.
- `vendor/` is the Composer vendor dir: `htmlburger/carbon-fields` (^3.6) and
  Bootstrap.

### Key `includes/` files

- `post-types.php` — registers all custom post types/taxonomies via small
  `newPostType`/`newTaxonomy` wrapper classes: `testimonials`, `faq`, `team`,
  `casestudies`, `guides`, `industries`, `capabilities`, `events` (+ location/
  category/type taxonomies), `layouts`, `popups`, `compareproducts`,
  `producttaxonomypages`, `globalpostboxes`, `rentals`, `landingpages`, `quiz`,
  `documents`.
- `post-meta.php` (~7200 lines) — meta box/field definitions for all the above
  post types, using the Carbon Fields 3 `Container::make()` / `Field::make()`
  API. Largest file in the theme; search by post type name when adding/editing
  fields.
- `modules.php` — misc snippet-style hooks (save-post handlers, date
  formatting helpers like `_date_format`, etc). Each function is a standalone
  "module" with a doc comment. `___sections($id = 'sections', $post_id = '')`
  renders the "sections" page-builder output (drives most page-builder-style
  templates via the `sections`/`section_items` complex repeater fields in
  `post-meta.php`).
- `hooks.php` — general action/filter hooks, including CF7 integrations (see
  Forms below).
- `elements.php`, `shortcodes.php`, `theme-widgets.php`, `menus.php`,
  `customizer.php`, `marquee.php`, `ajax.php`, `schema.php`, `checkout.php` —
  one concern per file, named accordingly. `__button()` in `elements.php`
  resolves popup post IDs via `apply_filters('wpml_object_id', ...)` for WPML
  compatibility. `_coptrz_link_aria_label($visible_text, $context_title)` in
  `elements.php` generates accessible aria-label strings for linked elements
  (returns empty string when context is already conveyed by the visible text).
  `pdf_url` shortcode in `shortcodes.php` resolves the post ID explicitly: it falls
  back from `get_the_ID()` to `get_queried_object_id()` because CF7 can render forms
  outside the main loop (e.g. via Dynamic Text Extension `[dynamic_hidden pdf_url "pdf_url"]`),
  at which point `get_the_ID()` returns 0 and `get__post_meta()` returns nothing.
- `woocommerce.php` (~2360 lines) — WooCommerce template/hook overrides; pairs
  with the `woocommerce/` directory which overrides core WooCommerce templates
  (`archive-product.php`, `cart/`, `checkoutx/`, `loop/`, `single-product/`,
  `global/`, `content-single-product.php`).

### Templates & template parts

- `templates/` — full page templates selectable in the editor: `page-landing.php`
  (+ `page-old-landing.php`), `page-modules.php`, `page-product-form.php`,
  `page-quiz.php`, `page-calculator.php`, `page-enterprise.php`,
  `page-training.php`, `page-blocks-editor.php`, `page-simple-header-footer.php`,
  `page-gutenberg.php` (renders standard header + hero + `the_content()` — for
  plain Gutenberg-edited pages), `page-html.php` (standalone full-page template
  for `page`/`guides` — emits a raw `<html>` document without the standard
  header/footer, used for fully self-contained HTML pages).
- `template-parts/header/` — header pieces (`header-left`, `header-menu`,
  `header-right`, `header-right-landing`), pulled into `header.php` via
  `get_template_part()`.
- `template-parts/sections/` — reusable content sections (hero, CTA, USP,
  testimonials, industries, products, guides, checklists, chips, etc.) used
  by the "modules"/page-builder style templates and driven by Carbon Fields
  data from `post-meta.php`.
- `template-parts/single/` — single-post templates for `capabilities`,
  `industries`, `events`, plus generic `single-post.php`.
- `template-parts/product-form/` — multi-section product configurator form
  (`section-1`..`section-4`, `section-video`).
- `single-producttaxonomypages.php` — single template for the `producttaxonomypages`
  post type. Supports a `?copy_from=<post_id>` URL param that reads meta fields
  from another post and writes them to the current post via `carbon_set_post_meta`;
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
- `dd_attach_cf7_pdf_url_to_email` (`wpcf7_mail_components` filter, priority 20)
  attaches the form's PDF to the outgoing CF7 email. **Opt-in required**: the
  active mail template's "File attachments" box must reference `pdf_url` with the
  `absolute_path` flag — e.g. `[pdf_url absolute_path="true"]` — otherwise the
  filter returns early. Once opted in, the submitted `pdf_url` value (a literal
  PDF URL or a numeric `documents` post ID — same convention as
  `register_cf7_pdf_url_attribute`) is resolved to an absolute file path and
  appended to `$components['attachments']` (deduped). URL-to-path resolution is
  handled by `dd_resolve_pdf_url_to_path()`: numeric values resolve via the
  `documents` post's `_document` attachment (`get__post_meta_by_id` →
  `get_attached_file`); URLs resolve via `attachment_url_to_postid()` with a
  fallback that maps uploads-dir URLs to their local path (scheme-insensitive
  comparison so http/https/protocol-relative all match). Arbitrary server paths
  outside `wp_get_upload_dir()` are rejected to prevent path-traversal abuse.
- The `dd/cf7-pdf-form` Gutenberg block (`assets/js/dd-cf7-pdf-block.js`,
  documented above) is the editor-friendly way to wire up a CF7 form + PDF —
  it emits the same shortcode shape by hand and reuses this section's
  resolution/attachment logic unchanged.

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
  pushes popup IDs from Carbon Fields button fields). Gutenberg `core/button`
  blocks with `ddPopupId` do NOT use `$popups_id` — their modal HTML is
  rendered inline by `dd_button_popup_render`.
