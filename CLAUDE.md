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
  where `modules.php` (and therefore `__popup`) is not loaded. `__popup($id)`
  (in `modules.php`) renders a `popups` post's content through
  `do_shortcode(do_blocks(get_the_content(NULL, false, $id)))` — the
  `do_blocks()` pass is required so Gutenberg block markup in the popup body
  renders correctly (plain `do_shortcode()` alone left block comments/markup
  unprocessed).
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
  right-hand nav is not shown. When accordion conversion is **off**
  (`data-accordion-breakpoint="none"`), `buildVerticalTabs()` also appends a
  shared `.dd-tabs-mobile-desc` div after the nav; at ≤991px CSS turns
  `.dd-tabs-nav-vertical` into a horizontal scrollable strip of tab titles
  (underline on the active title instead of the side bar, per-button
  `.dd-vtab-desc` hidden) and `.dd-tabs-mobile-desc` shows the active tab's
  description below it — `activate()` keeps that div's text in sync on click.
  The wrapper is `display: block` (not flex) at this breakpoint so the strip
  can't be clipped by a flex sibling, and `navVertical` gets a `wheel` listener
  that redirects vertical wheel deltas into `scrollLeft` (no-op unless the strip
  is actually overflowing) since desktop mice have no other way to reach
  overflowed tabs there — touch swipes work natively.
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
  `/dd/v1/documents` resolves each document's PDF `url` and `speak_url` through the
  `dd_document_file_url()` / `dd_document_speak_url()` helpers (also in `hooks.php`),
  which read the **raw Carbon meta keys `_document` / `_speak_to_an_expert_url`
  directly** (reliable in any context) and only fall back to the Carbon API if that
  is empty — Carbon's `carbon_get_*` had been returning empty in the REST request,
  so reading the raw key first is what makes these resolve.
  `save()` returns `null`; the block is **rendered server-side by the
  `dd_render_cf7_pdf_block()` `render_block` filter in `functions.php`**, which
  rebuilds the shortcode from the block's *attributes* (`formId`/`formTitle`/
  `pdfUrl`/`speakUrl`, stored in the block-comment JSON) and runs `do_shortcode()`.
  Building from attributes (single source of truth) means an instance renders
  correctly regardless of its saved markup, so no manual re-save is needed. For a
  **Media** source the `pdfUrl`/`speakUrl` attributes hold literal URLs entered/
  picked in the editor (PDF from `MediaUpload`, speak URL typed directly). For a
  **Document** source the render filter **re-resolves the PDF and speak URLs fresh
  from the document** (`pdfDocumentId`) via the same helpers, so they're always
  current and correct even for a block configured before those values existed (the
  stored attributes are only a fallback, and the editor likewise shows the document's
  *live* `speak_url` from the fetched list). The emitted shortcode is
  byte-identical to a hand-typed one. **Form requirement (surfaced as notes in the
  block's editor UI — the canvas placeholder and the relevant controls' `help`
  text):** the selected CF7 form must contain the matching hidden field(s):
  `[hidden pdf_url default:shortcode_attr]` and/or
  `[hidden speak_to_an_expert_url default:shortcode_attr]`. That is CF7's native
  "populate from the shortcode attribute" default, and it only works because
  `register_cf7_pdf_url_attribute` whitelists those attrs on the CF7 shortcode (WP's
  `shortcode_atts` would otherwise strip unknown attrs); the `pdf_url` value then
  also drives `dd_attach_cf7_pdf_url_to_email` (see Forms below). If the form lacks
  a field, that value silently won't reach it. **History:** the block went through
  a dynamic (`render_callback`) then a static (`RawHTML` save) form before settling
  on `save: null` + `render_block`; a single `deprecated` entry reproducing the
  static `RawHTML` save lets those interim instances validate and migrate. (The
  original symptom that drove all this churn — an empty `pdf_url` field — turned out
  to be an unrelated wrong-form selection, not the block.)
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
- `section-converter.php` — retires the dynamic "sections" / `sections_after_main`
  page-builder by freezing each post's sections into static HTML. Non-product posts
  get Gutenberg "Custom HTML" blocks appended to `post_content`; `product` posts get
  a sortable `sections_html` / `sections_after_main_html` complex repeater (label +
  raw HTML rows). Original `_sections` meta is preserved (conversion is reversible).
  Converting a `page` also sets its `_wp_page_template` to `templates/page-gutenberg.php`;
  that template renders converted posts via `coptrz_render_converted_sections()` (the same
  wpautop-free path the Modules template uses) and falls back to `the_content()` for
  non-converted pages. CPTs are left on their bespoke single templates, which already
  route frozen content through `___sections()`.
  Provides: `coptrz_sections_is_converted($post_id)`, `coptrz_sections_should_route()`,
  `coptrz_render_converted_sections()`, `coptrz_convert_post_sections($post_id, $dry_run)`,
  `coptrz_convert_post_sections_to_blocks($post_id, $dry_run)`,
  `coptrz_register_html_sections_fields()`.
  Admin tools: a per-post "Convert Sections to HTML" meta box (side, with dry-run) and a
  convert-by-search runner at Tools > Convert Sections. The runner has no
  "convert everything" path — you search posts by name across every section post type
  (via the `wp_ajax_coptrz_search_sections_posts` endpoint, results show each post's
  type), pick an explicit selection, then dry-run or convert just those (50/run cap).
  The search only returns posts that still NEED converting (have a `_sections` /
  `_sections_after_main` row and are not already flagged converted).
  Two conversion modes (chosen per-post box / bulk `mode` select): **Custom HTML**
  (`coptrz_convert_post_sections()`, the default — one frozen Custom HTML block per
  section) and **native blocks** (`coptrz_convert_post_sections_to_blocks()`,
  non-product only). Native mode walks each section's elements through a mapping
  registry `coptrz_block_item_mappers()` (`_type` → callable returning a parsed-block
  array built by `coptrz_block()`, serialized via core `serialize_blocks()`); a section
  whose items ALL map is wrapped in a `core/group` (`coptrz_block_group()`, carries the
  section's utility classes), otherwise the whole section falls back to a Custom HTML
  snapshot. The registry is seeded with the lossless leaf mappers (`custom_html`→
  `core/html`, `shortcode`→`core/shortcode`) and is `apply_filters`-extensible; further
  element→block mappers (heading, description, image, buttons, columns, …) are added
  from the project's element→block guide. Both modes set the same converted flag (so
  rendering routes identically) plus `_coptrz_sections_mode` = `html|blocks`.
  `coptrz_render_converted_sections()` has a static re-entrancy guard (`$rendering`)
  to prevent infinite recursion when frozen content routes back into `___sections()`
  for the same post (e.g. a `[layouts]` embed that resolves to the same post).
  Rendering path: products concatenate `{$id}_html` repeater rows then pass
  through `do_shortcode()`; non-products call `do_shortcode(do_blocks($content))`
  — deliberately NOT `apply_filters('the_content')`, which would run `wpautop`
  (mangles frozen markup) and third-party `the_content` filters.
  ALL shortcodes are preserved literally during conversion (NOT expanded via
  `do_shortcode()`), so widgets like `[brands_logo_slider]` / `[case_study_slider_grid]`
  AND reusable `[layouts id='N']` embeds remain dynamic — resolved at render time by
  `do_shortcode()` in the render path above. (Keeping `[layouts]` as a shortcode means
  editing a reusable layout post still updates every converted page that embeds it.)
  During conversion, empty `[product_add_to_cart id='']` tags are stripped
  (they would render as literal text where `do_shortcode` is not applied).
  Caveat: non-shortcode dynamic content (class-driven widgets) is still a snapshot and
  won't auto-update.
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
- A **Layout** side box carries two per-post checkboxes, `hide_header` and
  `hide_footer`. `header.php` reads `hide_header` (via
  `get__post_meta_by_id($id, 'hide_header')`, resolved from `get_the_ID()` or
  `get_queried_object_id()` for `is_singular()` requests) and, when set, skips
  both the `<header>` element and the promo/announcement banner above it (the
  banner lives inside the same `if (!$hide_header)` block). `footer.php` reads
  `hide_footer` (via `get__post_meta('hide_footer')`) and, when set, skips the
  `<footer>` element (the `if (!$hide_footer)` gate already existed in
  `footer.php` — only the field registration was added). Both fields are
  registered through the **meta shim** in `functions.php`
  (`coptrz_register_global_layout_fields()`) rather than in `post-meta.php`,
  because `post-meta.php` is skipped in admin when the `page-blocks-editor.php`
  template is active (see `dd_is_blocks_editor_template_active()`), which would
  otherwise hide the controls on those pages. `coptrz_register_global_layout_fields()`
  is called unconditionally from `tissue_paper_register_custom_fields()` (before
  `Container_Admin::boot()`), on both the blocks-editor and normal branches, so the
  containers are indexed on every template and on the frontend. Applies to `page`,
  `post`, `product`, `guides`, `casestudies`, `industries`, `capabilities`,
  `events`, `rentals`, and `landingpages` (side context).
- The **Custom CSS** `post_meta` box (`custom_css` textarea, output in
  `<style id="wp-head">` by `action_wp_head()` in `hooks.php`) is registered the
  same way — in `coptrz_register_global_layout_fields()` (`functions.php`) via the
  meta shim rather than in `post-meta.php` — so it is available on **every post
  type** (no `where` clause; the shim treats an empty condition set as "all") and
  on every template, including `page-blocks-editor.php`. Defined only there; don't
  re-add it to `post-meta.php`.
- The **Hide Before Footer Layout** box (`hidden_layouts` set field — a list of
  published `layouts` posts flagged `before_footer`, whose ids `footer.php`
  excludes via `get__post_meta('hidden_layouts')`) is registered the same way —
  in `coptrz_register_global_layout_fields()` (`functions.php`) via the meta shim —
  so it shows on `page-blocks-editor.php` too. Same post types as before (`page`,
  `guides`, `casestudies`, `events`, `landingpages`; side context). Defined only
  there.

### Forms — CF7 → Zapier

- `includes/hooks.php` adds `submission_date` and `form_name` fields to the
  payload sent by the "Contact Form 7 to Zapier" plugin via the
  `ctz_get_data_from_contact_form` filter (`dd_append_date_to_cf7_zapier_payload`),
  since that plugin builds its own payload and ignores `wpcf7_posted_data`.
- Also in `hooks.php`: a `wpcf7mailsent` JS listener for post-submit redirects.
- `register_cf7_pdf_url_attribute` (`shortcode_atts_wpcf7` filter) whitelists
  extra attrs on CF7 shortcodes so a `[hidden NAME default:shortcode_attr]` field
  can read them. `pdf_url`: if the value is numeric it is treated as a `documents`
  post ID and resolved to a URL via `get__post_meta_by_id($id, 'document')` →
  `wp_get_attachment_url()`; otherwise it is passed through as a literal URL.
  `speak_to_an_expert_url`: always passed through as a literal custom URL.
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

### OpenAI Ads conversion tracking

- Both pieces live in `includes/hooks.php` (`dd_inject_openai_ads_base_pixel`
  on `wp_head`, `dd_inject_openai_ads_cf7_listener` on `wp_footer`), driven by
  Carbon Fields defined in `post-meta.php`:
  - Pixel ID / global enable: `__openai_ads_fields()`, an "OpenAI Ads" tab on
    `theme_options` (`openai_ads_enable` + `openai_ads_pixel_id` +
    `openai_ads_debug` — passed as `debug` in the `oaiq("init", ...)` call to
    log pixel SDK activity to the browser console; both `pixel_id` and `debug`
    fields are gated behind `openai_ads_enable` via conditional logic).
  - Per-page conversion opt-in: `__openai_ads_conversion_fields()`, an
    "OpenAI Ads Conversion" tab on the same `post_meta` "Hero" container used by
    `page`/`product`/`post`/`capabilities`/`casestudies`/`industries`/`events`/
    `guides`/`rentals`/`landingpages` (`openai_ads_conversion_enable`, an
    `association` field picking a single `wpcf7_contact_form` post, plus a
    `openai_ads_conversion_event` select — `lead_created` (default),
    `registration_completed`, `appointment_scheduled`, or `custom` — and
    `openai_ads_conversion_custom_event_name` (text, shown only when
    `custom` is selected)).
- `dd_inject_openai_ads_base_pixel` requires **both** `openai_ads_enable`
  (theme-wide) and `openai_ads_conversion_enable` (per-page) to be true — the
  base pixel (`window.oaiq` queue shim + `bzrcdn.openai.com/sdk/oaiq.min.js`)
  only loads on pages that have opted into conversion tracking, not site-wide,
  so pages with no conversion configured stay script-free.
- On the frontend, listens for the native `wpcf7mailsent` event (not
  onclick/onsubmit — CF7 submits via AJAX, and the event fires after a
  validated submission even from a form inside a modal/popup) and, if
  `event.detail.contactFormId` matches the configured form, calls
  `window.oaiq("measure", eventName, { type }, { event_id, custom_event_name? })`.
  `eventName` is the raw `openai_ads_conversion_event` value; `type` is derived
  server-side in `dd_inject_openai_ads_cf7_listener()` via an `$event_shapes` map
  (`lead_created`/`registration_completed`/`appointment_scheduled` →
  `customer_action`, `custom` → `custom`). `custom_event_name` is only added to
  the options object when the event is `custom` and a name was set — matches
  OpenAI Ads' measurement pixel event shapes (developers.openai.com/ads/measurement-pixel).

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
