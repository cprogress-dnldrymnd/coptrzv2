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
  comparison tables to column-card layout on mobile). `__mini_cart()` still
  wires up `#mini-cart-button`/`.mini-cart-holder`, but `header-right.php` no
  longer renders that markup for regular WooCommerce products (see Catalog
  mode below) — `#mini-cart-button` now only exists (if at all) via the
  `[booqable_cart_button]` shortcode output on rentals pages, so `__mini_cart()`
  is a no-op elsewhere. `__hero_video_column()`
  moves a `.hero--video-section-style-1` cover block's background media —
  `.wp-block-cover__video-background` video **or**
  `.wp-block-cover__image-background` image — into the first column of that
  hero's `core/columns` block at ≤991px (and back to its original position via
  a left-behind placeholder `<span>` above that breakpoint), driven by a
  `matchMedia('(max-width: 991px)')` listener so it re-runs on resize/rotate
  without a page reload.
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
- `assets/js/coptrz-layouts-block.js` — registers the `coptrz/layouts` block, a
  native editor equivalent of `[layouts id="…"]`. Same shape as `dd/cf7-pdf-form`
  above (`save: null`, rendered server-side by `coptrz_render_layouts_block()` in
  functions.php) but simpler: only one attribute (`layoutId`, plus `layoutTitle` for
  the editor label) and no custom REST route — the editor dropdown fetches core
  `/wp/v2/layouts` directly since that CPT is already `show_in_rest`. See
  section-converter.php notes below for how the converter's `layouts` item mapper
  emits this block.
- `assets/js/coptrz-global-widget-block.js` — registers the `coptrz/global-widget`
  block, a native editor equivalent of hand-typing one of the "Global Widgets"
  shortcodes (`[brands_logo_slider]`, `[case_study_slider_grid]`, `[testimonials]`,
  `[reviews]`, `[drone_servicing]`, `[three_year_servicing_plans]`,
  `[remote_support]`, `[latest_from_coptrz]`). Same shape as `dd/cf7-pdf-form` and
  `coptrz/layouts` (`save: null`, rendered server-side), but the widget list is
  `wp_localize_script()`-ed as `coptrzGlobalWidgets` rather than fetched over REST —
  it's static PHP data (`coptrz_global_widgets()` in functions.php), so localizing
  avoids a round-trip while keeping one source of truth shared with the
  section-converter's `global_widgets` mapper. Two attributes: `widget` (the
  registry slug) and `style` (Case Study Slider only — the sole widget in the
  family that takes a parameter; its registry entry's `shortcode` key
  deliberately doesn't match its slug: `case_study_slider` → `[case_study_slider_grid]`).
  These widgets take no content of their own — brand logos live in `pa_brands`
  term meta, featured case studies in a theme option — so the block is a pure
  placeholder naming the selected widget; content is edited at its usual location,
  not in the block.
- `assets/js/coptrz-post-grid-block.js` — registers the `coptrz/post-grid` block,
  a native editor equivalent of the legacy section builder's "Post Grid" item
  (`post_grid` inside `section_items`, includes/post-meta.php). Unlike the other
  `coptrz/*` blocks this one has real configurable content across three
  independent axes, matching the legacy field 1:1 rather than simplifying it:
  **Post Type** — one of exactly 4 hard-coded types (`industries`, `casestudies`,
  `testimonials`, `capabilities` — not a generic CPT picker, matching the legacy
  field), each with a `source` of `all` / `manually` (a `FormTokenField`-based
  multi-post search picker, component `IdTokenPicker`) / `category` (same
  component, term search — only offered for the two types with a taxonomy).
  **Post Box Styles** — the per-card wrapper's styling, stored as a flat
  `boxStyles` object attribute; a category (background/text color, padding,
  margin, alignment, column width, border, custom class) is only turned into a
  row server-side when the block author actually touched one of its fields,
  mirroring the legacy Carbon Fields row simply not existing until added —
  leaving everything blank in the block produces zero row-derived styling,
  same as the legacy default. **Post Elements** — an ordered, reorderable
  (`PostElementsRepeater` component, up/down buttons) list of up to 10 items:
  `post_title`/`featured_image`/`post_excerpt`/`permalink`/`icon` (each at most
  once) and `custom_field` (up to 5 — the legacy field's `custom_field_1..5` are
  five near-identical Carbon Fields groups working around "duplicate groups not
  allowed"; the block simplifies this to one repeatable `custom_field` type,
  capped at 5 in the "Add element" control since `____post_grid_module()` only
  recognises those 5 literal `_type` values).

  The full field taxonomy — which post types exist and what sources/taxonomy
  each supports, and every select's option list (option VALUES, i.e. the literal
  CSS utility classes, not just labels) — is `wp_localize_script()`-ed as
  `coptrzPostGrid` from `coptrz_post_grid_post_types()` /
  `coptrz_post_grid_field_options()` (functions.php), so editor and legacy admin
  field can't drift apart.

  There's no shortcode to delegate to (unlike `dd/cf7-pdf-form` or
  `coptrz/layouts`), so `coptrz_render_post_grid_block()` (functions.php) calls
  `____post_grid_module()` (includes/modules.php — the same function both legacy
  `case 'post_grid':` call sites and the `[testimonials]` shortcode use) directly,
  after converting attributes back into its expected row-array shape via
  `coptrz_post_grid_attrs_to_data()` + `coptrz_post_grid_box_styles_rows()` +
  `coptrz_post_grid_elements_rows()`. Guarded with `function_exists()` since
  modules.php is skipped in admin under the blocks-editor template. Each render
  gets a fresh `wp_unique_id()`-based id (fixing a latent bug: the legacy call
  sites share one id across every item in a section/column, which only becomes
  visible now that a sliding grid can be placed — and duplicated — anywhere).
  The section-converter's `post_grid` item mapper
  (includes/section-converter.php, `coptrz_post_grid_legacy_item_to_attrs()`) is
  the inverse transform, so existing legacy Post Grid sections convert straight
  into this block instead of snapshotting to Custom HTML.

  **Bug fixed while building this** (affects the legacy path too, not just the
  block): the `permalink` element's invisible "stretched card link" used to read
  its accessible text from a `$post_title` variable shared across the whole
  per-post loop and only set by the `post_title` element — if `permalink` was
  ordered before `post_title` (or `post_title` wasn't included at all), the link
  text was stale or empty. `____post_grid_module()`'s `permalink` case
  (modules.php) now calls `get_the_title($post->ID)` directly instead.
- `assets/js/extend-cover-responsive.js` — Gutenberg block editor extension
  (enqueued via `digitally_disruptive_enqueue_swiper_editor_assets()`, same
  hook as `dd-tabs-block.js`/`dd-cf7-pdf-block.js`) that adds a "Responsive
  Background" InspectorControls panel to the **core** `core/cover` block, with
  optional Mobile (`ddMobileImageId`/`ddMobileImageUrl`, ≤767px) and Tablet
  (`ddTabletImageId`/`ddTabletImageUrl`, 768–991px) image slots; the block's
  own image remains the desktop (≥992px) background. Each breakpoint also has
  a "Hide background image" toggle (`ddHideImageMobile`/`ddHideImageTablet`)
  that removes the image (and the cover's dim/overlay) entirely at that
  breakpoint instead of swapping it; hiding takes precedence over an image
  set for the same breakpoint. Rendering is handled server-side by
  `dd_cover_responsive_render()` (`render_block_core/cover` filter in
  `functions.php`), which no-ops when none of the four attributes are set.
  It handles two markup shapes core/cover can emit: (1) an `<img
  class="wp-block-cover__image-background">` — wrapped in a `<picture>` with
  `<source media>` elements prepended (mobile first) so the browser swaps the
  image natively, original `<img>` kept as the desktop fallback (skipped
  entirely if both breakpoints resolve to hidden/empty); (2) a fixed/repeated
  background rendered as a `<span>`/`<div>` with an inline `background-image`
  style and no `<img>`. Both shapes share one scoped-CSS code path: the
  wrapper is tagged with a unique `dd-cover-resp-N` class and `@media` rules
  are built — `background-image` overrides per breakpoint for shape (2), and
  `display:none!important` on
  `> picture, .wp-block-cover__image-background, .wp-block-cover__background`
  for any hidden breakpoint (either shape) — then pushed to the consolidated
  CSS collector (see `dd_custom_css_collector()` below) instead of an inline
  `<style>` tag.
- `assets/js/extend-responsive-layout.js` — Gutenberg block editor extension
  (enqueued via `digitally_disruptive_enqueue_swiper_editor_assets()`, same
  hook as the extensions above) adding two per-breakpoint responsive controls
  to core blocks, both using the theme's standard tablet ≤991px / mobile
  ≤767px breakpoint pair (matching Custom CSS and Cover Responsive above):
  (1) a "Stack on tablet" toggle (`ddStackOnTablet`, boolean) in a
  "Responsive Layout" panel on **core** `core/columns`, rendered server-side
  by `dd_columns_stack_tablet_render()` (`render_block_core/columns` filter
  in `functions.php`), which adds a `dd-stack-tablet` class picked up by a
  static SCSS rule in `assets/scss/base/_helpers.scss` scoped to
  `(min-width: 768px) and (max-width: 991px)` — the `768px` lower bound is
  deliberate so it doesn't also override core's own "Stack on mobile"
  toggle (`isStackedOnMobile`, core's built-in attribute, breaks at 781px)
  below that; (2) "Max columns (Tablet)" / "Max columns (Mobile)" number
  fields (`ddGridColumnsTablet` / `ddGridColumnsMobile`, strings) in a
  "Responsive Grid Columns" panel shown only when a **core** `core/group`
  block's `layout.type === 'grid'` (the Grid layout variation; plain
  Group/Row/Stack never show it), rendered server-side by
  `dd_group_grid_responsive_render()` (`render_block_core/group` filter),
  which builds scoped CSS the same way as `digitally_disruptive_render_custom_css()`
  (unique `dd-grid-N` class + `@media` rules forcing `grid-template-columns`
  with `!important`, since core prints its own `grid-template-columns` in a
  `<head>` stylesheet at equal specificity) rather than a fixed class, because
  the column count is an arbitrary per-instance value, and likewise pushes it
  to the consolidated CSS collector instead of an inline `<style>` tag.
  `dd_group_grid_responsive_render()` bails when `isSwiperSlider` is set, since
  `digitally_disruptive_render_universal_swiper()` strips the grid layout
  entirely to build a carousel — the two are mutually exclusive. Both editor
  panels also inject a `clientId`-scoped `<style>` tag (same live-preview trick
  as `extend-custom-css.js`) so the effect is visible in the editor canvas
  immediately, without waiting for the server-rendered class/consolidated CSS
  to exist.
  `dd_columns_stack_tablet_render()`/`dd_group_grid_responsive_render()` locate
  their target element with `next_tag(array('class_name' => 'wp-block-columns'
  /'wp-block-group'))` rather than a bare `next_tag()`, a holdover from when
  `digitally_disruptive_render_custom_css()` used to prepend an inline `<style>`
  tag to `$block_content` (which would have been the first tag otherwise) —
  see the consolidated-CSS mechanism below, which removed that prepend, but
  the qualified lookup is harmless and remains in place.
- `assets/js/extend-group-link.js` — Gutenberg block editor extension
  (enqueued via `digitally_disruptive_enqueue_swiper_editor_assets()`, same
  hook as the extensions above) adding a "Group Link" panel to **core**
  `core/group` with a URL field (`ddGroupLinkUrl`, string), an "Open in new
  tab" toggle (`ddGroupLinkNewTab`, boolean), and an optional accessible-label
  field (`ddGroupLinkLabel`, string). Rendered server-side by
  `dd_group_link_render()` (`render_block_core/group` filter in
  `functions.php`, registered alongside `dd_group_grid_responsive_render()`
  above), which no-ops when `ddGroupLinkUrl` is empty. When set, it adds
  `position-relative` to the group (via `WP_HTML_Tag_Processor`, matching the
  `next_tag(array('class_name' => 'wp-block-group'))` lookup used by the other
  group filters) and appends a Bootstrap `.stretched-link` `<a>` as the last
  child before the block's own closing tag — found via the *last* occurrence
  of that closing tag in the rendered string, since in well-formed nested HTML
  the outermost element's closing tag is always the final one; this also
  respects the Group block's "HTML element" advanced setting (div/section/
  aside/main/etc — the closing tag matched is whatever tag `next_tag()`
  actually found, not a hardcoded `</div>`). `.stretched-link` is Bootstrap's
  existing helper (already vendored, `assets/vendor/bootstrap/scss/helpers/
  _stretched-link.scss`), so no new CSS/SCSS was needed. `target`/`rel` are
  only added when "Open in new tab" is on; `aria-label` is only added when a
  label was set (there's no reliable generic way to derive fallback text for
  an arbitrary group's contents, unlike `_coptrz_link_aria_label()` elsewhere
  which needs a specific visible-text/context-title pair) — the editor UI's
  field `help` text recommends setting one when the group has no visible link
  text of its own.
- **Consolidated block CSS** (`functions.php`): `digitally_disruptive_render_custom_css()`
  (the `render_block` filter backing the per-block "Custom CSS" panel —
  `ddCustomCSS`/`ddCustomCSSTablet`/`ddCustomCSSMobile` attributes, whitelisted
  to `core/group`, `core/separator`, `core/image`, `core/heading`,
  `core/paragraph`, `core/button`, `core/columns`, `core/column`),
  `dd_cover_responsive_render()`, and `dd_group_grid_responsive_render()` no
  longer print their own inline `<style>` tag next to each block. All three
  push their compiled CSS string into a shared buffer via
  `dd_custom_css_collector($css)` (a static-array accumulator; calling it with
  no args reads the buffer back). `dd_consolidated_css_placeholder()` (`wp_head`,
  priority 999, so it lands after core's own block-support styles) echoes a
  `<!--DD_CONSOLIDATED_CSS-->` marker comment. `dd_start_css_buffer()`
  (`template_redirect`, skipped for admin/REST/AJAX/cron/feed requests) opens
  a full-page `ob_start('dd_flush_consolidated_css')` buffer so CSS collected
  later in the request (blocks render after `<head>` is already sent) can
  still be spliced in; `dd_flush_consolidated_css()` swaps the marker for a
  single `<style id="dd-consolidated-custom-css">` containing everything
  collected (falls back to appending before `</head>`, or to the very end of
  the HTML, if the marker is somehow missing). Net effect: one `<style>` tag
  per page instead of one per styled block instance.
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
  Forms below). `dd_send_security_headers()` (on `send_headers`, so it applies
  to every WP-served response, not just `<head>`) emits baseline security
  headers flagged by securityheaders.com / Mozilla Observatory scans:
  `X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, a
  `Content-Security-Policy` limited to `frame-ancestors 'self'`,
  `Referrer-Policy: strict-origin-when-cross-origin`, and (HTTPS only) a
  `Strict-Transport-Security` header (`max-age=31536000`, 1 year).
  `includeSubDomains`/`preload` are left off deliberately: enabling them makes
  every subdomain HTTPS-only and is near-irreversible once submitted to
  hstspreload.org — add them only once every subdomain is confirmed
  HTTPS-only. The CSP is intentionally frame-ancestors-only (governs framing,
  doesn't restrict `script-src`/`object-src`) so it can't break inline
  theme/Woo/CF7/analytics scripts; scanners still grade a frame-ancestors-only
  policy "unsafe" for lacking `script-src`, which is accepted here as
  defense-in-depth alongside `X-Frame-Options` (a real script-restricting CSP
  would need a nonce-based rollout). **Gotcha:** on a LiteSpeed
  full-page-cache HIT these PHP-emitted headers may be bypassed — mirror them
  in `.htaccess`/server config for guaranteed coverage.
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
- `section-converter.php` — one "Convert to Blocks" action that retires TWO
  legacy Carbon-Fields-shaped editing surfaces at once, whichever apply to a
  given post: the dynamic "sections" / `sections_after_main` page-builder, and
  the per-post "Hero" meta box (includes/post-meta.php, `__hero_fields()` etc —
  see includes/hero-converter.php, the hero data layer this file calls into).
  Sections and hero are independent per post — `post`/`guides` only ever have a
  hero to convert, `layouts`/`producttaxonomypages` only ever have sections,
  everything else may have either or both — all handled by the single
  `coptrz_convert_post_to_blocks($post_id, $dry_run)` entry point.
  Sections: all post types — including `product`, since
  `coptrz_enable_product_block_editor()` (woocommerce.php) gave products the
  block editor too — convert to native Gutenberg blocks appended to
  `post_content` (sections that can't map natively fall back to a Custom HTML
  block, per-section — see below). The older raw-HTML `sections_html` /
  `sections_after_main_html` repeater path (`coptrz_convert_post_sections()`,
  label + raw HTML rows, for the pre-block-editor product flow) is no longer
  reachable from either UI (per-post box or bulk runner) and is kept only as
  legacy/reference code, not partially gutted. Original `_sections` meta is
  preserved (conversion is reversible). Converting a `page`'s sections also
  sets its `_wp_page_template` to `templates/page-blocks-editor.php` (plain
  `the_content()`, no `___hero_modules()` call — see the hero paragraph below
  for why that's fine) — extended to route converted posts through
  `coptrz_render_converted_sections()` (the same wpautop-free path the Modules
  template uses) instead of a bare `the_content()`, mirroring
  `templates/page-gutenberg.php`, which still exists for any page converted
  before this switched targets. CPTs are left on their bespoke single
  templates, which already route frozen content through `___sections()`
  (and, unlike page-blocks-editor.php, also still render related
  products/guides/case-studies + `sections_after_main` — see
  `template-parts/single/single-capabilities.php` /
  `single-industries.php` — which is why CPTs deliberately stay off
  page-blocks-editor.php rather than being switched too).
  Hero: a `coptrz/hero` block is PREPENDED as the first block, built from
  `coptrz_hero_conversion_plan()` (includes/hero-converter.php) — which only
  returns a block to prepend when it's verified to render identically to the
  current meta-driven hero (`coptrz_hero_dry_run_check()`, rendering both paths
  through the pure `___hero_render()` and comparing HTML) — otherwise that
  part is skipped with a warning and the post keeps rendering its hero from
  meta, a permanent, correct fallback either way (`___hero_modules()`,
  modules.php, via `coptrz_hero_block_attrs()`, includes/hero-block.php).
  `COPTRZ_SECTIONS_CONVERTED_FLAG` (`coptrz_sections_is_converted()`) still
  gates `___sections()` render routing exactly as before — a hero-only
  conversion never sets it. `COPTRZ_HERO_CONVERTED_FLAG` is bookkeeping only;
  nothing at render time reads it.
  **Render mode** — the hero renders INLINE, at the block's actual position in
  `post_content`, for every post type EXCEPT `post`
  (`coptrz_hero_hoisted_post_types()`, includes/hero-block.php, filterable via
  `coptrz_hero_hoisted_post_types`) — inline is what makes reordering the
  block in the editor actually move the hero; it's safe because converted
  content on every other type renders as a direct child of `<main>` (no
  Bootstrap `.container`/`.col-*` in the way). `post` stays HOISTED because
  `the_content()` there sits inside a constrained `.col-lg-7.px-5`
  (`template-parts/single/single-post.php`) that a full-bleed `section.hero`
  would break out of — for that one type the block stays storage-only
  (`coptrz_render_hero_block()` returns `''`) and `___hero_modules()` keeps
  rendering at the template's existing hero call site, exactly as before this
  distinction existed. `coptrz_hero_renders_inline($post_id)` is the single
  source of truth both `coptrz_render_hero_block()` (the inline render) and
  `___hero_modules()` (the early-return that stops it double-rendering for
  inline posts) consult. Because an inline render has no template call site to
  supply a per-type fallback height/alignment (`single-rentals.php`,
  `single-events.php`, etc pass one; `___hero_render()` only applies it when
  the meta value is empty), `coptrz_hero_meta_to_attrs()` bakes the effective
  value into the block's attrs at conversion time via
  `coptrz_hero_template_defaults($post_type)` — and
  `coptrz_hero_dry_run_check()` applies the identical defaults to its
  meta-side comparison, or every `rentals`/`events`/`casestudies`/`guides`
  post would falsely report `not_identical`.
  **Hiding the Hero meta box** — `coptrz_register_hero_hidden_fields()`
  (includes/hero-converter.php, called from
  `tissue_paper_register_custom_fields()` in functions.php, same call site/
  ordering as `coptrz_register_html_sections_fields()`) blocklists every root
  field name across all three Hero tabs (`coptrz_hero_meta_field_names()` —
  derived from `__hero_fields()`/`__hero_button_fields()`/`__hero_form_fields()`
  rather than hand-maintained, since `Container_Admin::is_container_render_hidden()`
  only hides a container when EVERY non-display field on it is blocklisted); a
  `coptrz_meta_shim_field_visible` filter un-hides it per-post when
  `!coptrz_hero_is_converted($post_id)`. Only the post-side container is
  affected — the `term_meta` Hero container (`product_cat`/`pa_brands`) is
  untouched, since `is_container_render_hidden()` is only consulted by
  `Container_Admin::register_post_meta_boxes()`.
  Provides: `coptrz_sections_is_converted($post_id)`, `coptrz_sections_should_route()`,
  `coptrz_render_converted_sections()`, `coptrz_convert_post_sections($post_id, $dry_run)`,
  `coptrz_convert_post_to_blocks($post_id, $dry_run)`,
  `coptrz_revert_post_to_blocks($post_id, $dry_run)`,
  `coptrz_register_html_sections_fields()`, `coptrz_convertible_post_types()`
  (the union of `coptrz_section_post_types()` and `coptrz_hero_post_types()`,
  includes/hero-converter.php — drives the admin surface below),
  `coptrz_post_has_sections_data($post_id)` (whether a post has any legacy
  `sections`/`sections_after_main` row data — complex root fields are stored
  per-cell, e.g. `_sections|||0|value`, so this reads via
  `Key_Formatter::load_root_map()` rather than `metadata_exists()`, which
  always returns false for a bare `_sections` key),
  `coptrz_post_conversion_state($post_id)` (cheap per-post pending-parts
  estimate for the bulk search), `coptrz_conversion_remaining_counts()`,
  `coptrz_post_is_legacy_purged($post_id)`, `coptrz_sections_is_purged($post_id)`,
  `coptrz_hero_is_purged($post_id)`, `coptrz_post_purgeable_parts($post_id)`,
  `coptrz_purge_post_legacy_data($post_id, $dry_run, $force)`,
  `coptrz_content_is_corrupted($content)` (see Purge below).
  Admin tools: a per-post "Convert to Blocks" meta box (side, with dry-run) and
  a convert-by-search runner at Tools > Convert to Blocks — search-and-select
  is the ONLY way to convert or revert from this page (50/run cap); there is
  no batch "convert all remaining" action, by design, to avoid a wholesale
  sitewide conversion run. The per-post box only renders when there's
  actually something to convert: it's suppressed for a post with no sections
  data (`coptrz_post_has_sections_data()`) AND no meaningful hero content
  (`coptrz_hero_has_content()`, includes/hero-converter.php — real
  heading/description/background/buttons/form, not just the title-fallback
  every hero-type post otherwise has), unless the post is already converted
  (the box's Revert controls must stay reachable). This keeps the box off
  empty pages and plain blog posts. `coptrz_post_conversion_state()` (backing
  the search results' pending label) and the read-only "remaining by post
  type" progress table above the search both use these same two gates —
  `coptrz_post_has_sections_data()` for sections,
  `coptrz_hero_has_content_where()` (includes/hero-converter.php — the SQL
  mirror of `coptrz_hero_has_content()`, kept in sync by hand; storage
  formats: checkboxes are `'yes'`/`''`, `hero_background` is an attachment ID
  string, the `buttons` complex field's first row lives at
  `_buttons|||0|value`) for hero, wired into
  `coptrz_conversion_pending_where()`'s hero branch — so the count no longer
  overstates every hero-applicable post as pending (e.g. it previously showed
  600/602 Posts and 61/61 Guides "remaining" when almost none had real hero
  content to convert).
  The per-post box's `wp_ajax_coptrz_convert_sections` handler and the
  search-and-select runner's per-ID loop both call
  `coptrz_convert_post_to_blocks()` unconditionally — it decides internally
  which of sections/hero actually apply.
  `product` posts still need special handling internally (no `before`/`after`-main
  content split the way other post types have), which
  `coptrz_convert_post_to_blocks()` handles itself via
  `coptrz_product_content_split()` rather than by routing to a different function.
  Native-block conversion walks each section's elements through a mapping
  registry `coptrz_block_item_mappers()` (`_type` → callable returning a parsed-block
  array built by `coptrz_block()`/`coptrz_block_container()`) via
  `coptrz_section_to_blocks()`, serialized once with core `serialize_blocks()`.
  A section only goes native when EVERY item maps AND its styling is fully
  representable (see below) — otherwise **that section** (not the whole post) falls
  back to a Custom HTML snapshot: this is the only way Custom HTML output still
  happens for a non-product post, as a per-section fallback rather than a chosen
  mode (`coptrz_section_to_blocks()` returns `[markup, was_native, snapshot_reason]`;
  the reason is surfaced in the dry-run/convert report so it's clear per-section why
  it didn't go native). The registry is seeded with the lossless leaf mappers
  (`custom_html`→`core/html`, `shortcode`→`core/shortcode`, `layouts`→`coptrz/layouts`,
  `global_widgets`→`coptrz/global-widget` — both driven by the same registry the
  block editor uses, `coptrz_global_widgets()` in functions.php, so an unknown/
  unregistered widget slug — e.g. the `dji_*`/`parrot_*`/`elios_3` rows declared in
  post-meta.php's second `global_widgets` definition but never given a shortcode —
  is skipped the same way for both; see below) plus `heading`/`description`/`image`/
  `buttons`/`columns`/`product_compare`/`post_grid`→`coptrz/post-grid` (see the
  `coptrz-post-grid-block.js` entry above), and is `apply_filters`-extensible.
  Elements with no native block equivalent and no shortcode form (`tabs`,
  `accordion`) are intentionally absent from the registry → section snapshots.
  Both functions set the same converted flag (so rendering routes identically) plus
  `_coptrz_sections_mode` = `html|blocks` (reflecting which one actually ran, not a
  user choice).

  **Native section wrapper** — `coptrz_block_group()` nests two (occasionally three)
  `core/group` blocks to reproduce the wrapper `___sections()` emits at
  modules.php:774-790: `<section class="…">` → `<div class="wp-block-group container">`
  → an inner `.container-inner` group only when there's more than the seeded
  `position-relative container-inner` container class (matching
  `count($container_classes) > 1` at modules.php:788). Both groups use
  `layout:{"type":"default"}`, not `constrained` — the theme's SCSS targets Bootstrap's
  `.container` directly, so `constrained` would double up with theme.json content width.
  `coptrz_section_wrapper_data($section)` derives the outer/`container-inner` class
  lists AND CSS declaration lists from `section_styles` by mirroring the same
  `switch ($type)` modules.php uses (padding, margin, alignment, background/text
  color, container width, border radius/style/color/width — including the
  section+container border pair's shared radius→style→(color, then possibly
  per-side width) branching, factored into one `$border_pair` closure since
  modules.php:654-751 implements it identically twice) — this used to only carry
  the author's `section_class`, which is why early native conversions (e.g. a
  "Future-Proof Your Drone Operations" style section with only margin utilities
  and no custom class) lost all their styling.

  Anything modules.php would otherwise render as a raw inline `style=""` (custom
  hex colors, background image/gradient, min-height, custom border radius/color/
  width, custom container max-width) is collected into `section_css` /
  `container_css` (plain `"prop: value"` strings) instead of being dropped.
  `coptrz_block_group()` joins each into a single string and sets it on the
  matching group's **`ddCustomCSS`** attribute — the theme's existing per-block
  Custom CSS mechanism (`digitally_disruptive_render_custom_css()` in
  functions.php, already whitelisted for `core/group`; see the "Consolidated
  block CSS" note above) — rather than hand-serializing core/group's native
  `style` attribute, which would risk a save()-mismatch showing as "invalid
  block content" the next time an editor opens the page. Being a plain string
  attribute, `ddCustomCSS` carries none of that risk, and stays user-editable
  afterwards in the block's own Custom CSS panel. `container_css` only ever
  reaches the page when the `.container-inner` div itself is emitted (the same
  `count($container_classes) > 1` gate above) — matching how `___sections()`
  only ever applies `$container_styles` there too (modules.php:788-790), so e.g.
  a lone custom container width with nothing else on the container is silently
  dropped in both the legacy render and the converter, not "fixed" by the latter.

  **Column styling** — the `columns` item mapper originally only read a column's
  `column_width` and dropped every other `column_styles` row (background image/
  color, padding, margin, border, alignment, text color, custom class) silently —
  no `unmappable`, no dry-run warning, just a bare `wp-block-column` with the
  styling gone (this is what broke background images on converted product/section
  columns, e.g. the drones shop pages). `coptrz_column_wrapper_data($col,
  $shared_styles, $individual_column_settings, $mobile_styling, $same_image_height,
  $image_fit, $image_padding)` fixes this the same way `coptrz_section_wrapper_data()`
  fixed section styling — mirroring `____columns_modules()`'s per-column switch
  (modules.php:2065-2224 for `individual_column_settings`, or the shared
  `column_styles` complex applied to every column at modules.php:1841-1931 when that
  flag is off) case-for-case, including two legacy quirks preserved deliberately
  rather than "fixed": (1) `background_color_custom` is gated on
  `background_color === 'bg-custom'` in the individual branch but ungated in the
  shared branch (modules.php:2143 vs :1919); (2) `mobile_styling` only actually
  reaches a column in the shared branch — in the individual branch legacy resets
  its class accumulator per-column, discarding it before it can apply
  (modules.php:1836-1838 vs :2061). The legacy renderer nests each column's content
  in an inner `.column-holder` div carrying this styling, separate from the outer
  `.col` width div — `_base.scss` has descendant selectors (e.g. `.col-6
  .column-holder`) that stop matching if the two collapse onto one element, so the
  `columns` mapper reproduces `.column-holder` as an **inner `core/group`**, but
  only when a column actually has holder-level classes or CSS; an unstyled column
  stays a flat column `core/group` rather than gaining a pointless nesting level. The
  holder group's derived CSS (background image URL, custom colors, custom border
  radius/color/width) goes on its `ddCustomCSS` attribute, same mechanism as
  `coptrz_block_group()` above — `core/group` is already whitelisted for it. A
  `column_id` becomes the holder group's native `anchor` attribute (renders as
  `id="…"`, no hand-serialized `style`/custom attribute risk). Row-level settings
  with no block equivalent now correctly force a snapshot instead of quietly
  rendering wrong: `is_slider` (modules.php's swiper markup has nothing to map to)
  returns `null` from the mapper so the whole section falls back to Custom HTML,
  the same fallback used elsewhere in the registry.

  **Grid conversion for uniform rows** — the `columns` mapper emits a CSS Grid
  instead of `core/columns`/`core/column` ONLY when every column in the row
  shares the exact same Bootstrap width at every breakpoint (desktop/tablet/
  mobile). In that case the outer wrapper is a `core/group` with
  `layout: {type: 'grid', columnCount: N}`, where `N = 12 / span` — e.g. every
  column being `col-lg-4` (span 4) → `columnCount: 3`; `col-lg-6` (span 6)
  → `columnCount: 2`; `col-lg-3` (span 3) → `columnCount: 4`. Each column is a plain
  `core/group` with no Bootstrap class and no per-column CSS — the row's width
  is expressed once, on the parent, not per column. The row-level
  `align_items`/`justify_content`/`horizontal_spacing`/`vertical_spacing`
  classes still land on the parent's `className`, as they did on `core/columns`
  before. `coptrz_bootstrap_col_span($class, $col_count)` derives the 1-12 span
  from a Bootstrap class (`col-lg-6`, `col-6` → the number directly; `col`,
  `col-lg`, `col-auto`, or unset → an even share, `round(12 / $col_count)`, so N
  auto/equal-fill columns still map back to `columnCount: N`).
  Tablet (`column_width_tablet`, e.g. `col-md-*`) and mobile
  (`column_width_mobile`, bare `col-*`) widths — when the legacy row actually
  set them — become `ddGridColumnsTablet`/`ddGridColumnsMobile` on the SAME
  parent group, each computed the same way (`12 / span`). These two attributes
  are consumed by the theme's PRE-EXISTING `dd_group_grid_responsive_render()`
  (functions.php, `render_block_core/group` filter, gated on
  `layout.type === 'grid'`) — no new render-time plumbing was needed for the
  responsive half.

  **Mixed-width rows fall back to `core/columns`** — when the row's columns do
  NOT all share the same width at every breakpoint (e.g. `col-lg-8` +
  `col-lg-4`), a single `columnCount` can't represent them, so the mapper
  instead reproduces the original `core/columns` + `core/column[]` shape: each
  column keeps its own joined Bootstrap width class(es) — from
  `column_widths` (desktop/tablet/mobile, `coptrz_column_wrapper_data()`'s
  return shape, replacing the old flat `column_classes` list) — as its
  `className`, defaulting to `col` when none are set.

  Per-column `.column-holder` styling (background/border/etc, described below)
  is orthogonal to this width-uniformity check — a uniform-width grid row can
  still have per-column holder styling; only the width class itself moved from
  the column onto the parent's `columnCount`/`ddGridColumns*` in the grid
  branch. Section content unaffected either way — only the `columns` element's
  own wrapper/column blocks change shape.

  `unmappable` (which forces the whole section to snapshot as Custom HTML,
  dragging every item in it down regardless of how well those items map) is now
  reserved for the two `section_styles` cases that emit actual MARKUP
  `coptrz_block_group()` has nowhere to put — `background_video`/YouTube
  (`__background()`) and an `image`-type `background_overlay` (`__image()`).
  Everything else that used to trip `unmappable` (e.g. a single `border-custom`
  row with a custom top-width) now routes through `section_css`/`container_css`
  above instead, so a section is no longer punished for one inline declaration
  when the rest of it — including e.g. a `layouts` item — maps natively.

  `section-N` index classes and the `id="section-N"` anchor are deliberately NOT
  carried over (they're only meaningful for the legacy per-index CSS, not needed
  on a block).

  **Naming** — `coptrz_block_group()` takes the section's `title` and, when
  non-empty, sets it as the outer group's `attributes.metadata.name` — the same
  mechanism the editor's own block "Rename" context-menu action writes, so
  converted sections show their Section Title in List View instead of a wall of
  identical "Group" entries. `renaming` isn't disabled in `core/group`'s (or
  `core/html`'s) `supports`, so it defaults to available; no editor-side
  registration needed; `metadata` is core block-serialization plumbing, not
  something individual blocks declare in their JS attributes schema.
  `coptrz_section_to_blocks()` sets the same `metadata.name` on the Custom HTML
  snapshot fallback too (via `coptrz_block('core/html', …)` + `serialize_blocks()`,
  rather than hand-writing the `<!-- wp:html -->` comment, matching how the
  `custom_html` item mapper already builds `core/html` blocks) — snapshots are
  otherwise the hardest converted block to identify in List View.

  **`coptrz/layouts` block** (`assets/js/coptrz-layouts-block.js`) — native editor
  equivalent of `[layouts id="N"]`, same shape as `dd/cf7-pdf-form`: `save: null`,
  rendered server-side by the `coptrz_render_layouts_block()` `render_block` filter
  (functions.php) which rebuilds the shortcode from the `layoutId` attribute and runs
  `do_shortcode()` — inherits the `layouts` shortcode's own `function_exists('___sections')`
  guard (includes/shortcodes.php), so it's safe in admin/REST contexts. The editor
  dropdown fetches core `/wp/v2/layouts` directly (the `layouts` CPT is already
  `show_in_rest`, post-types.php:413-423) — no custom REST route needed, unlike the
  CF7-forms/Documents dropdowns in `dd/cf7-pdf-form`. The `layouts` item mapper emits
  this block instead of `core/shortcode` so converted `[layouts]` embeds are editable
  in the block inspector; the embed itself stays dynamic either way (only the shortcode
  reference is stored, not its expanded output), so editing the referenced Layout post
  still updates every page/converted-section that embeds it.
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

  **Revert** — `coptrz_revert_post_to_blocks($post_id, $dry_run)` undoes whichever
  of sections/hero were converted on a post (a post where only one part was ever
  converted still reverts cleanly — the other part's flag/backup simply isn't
  present). `product` posts with sections in `mode = 'html'` AND no hero
  conversion just clear the `sections_html`/`sections_after_main_html` repeater
  (post_content/template were never touched). Everything else restores
  `post_content` from `_coptrz_pre_convert_content` — a WRITE-ONCE backup: the
  first conversion of EITHER part on a post captures the true pre-conversion
  state, so a later run adding the other part doesn't overwrite it with content
  that already includes the first part — plus, for `page`,
  `_coptrz_pre_convert_template` (falling back to `templates/page-modules.php`
  when no template backup exists). `_coptrz_converted_blocks` (the sections
  suffix alone) and `_coptrz_hero_block` (the hero prefix alone,
  includes/hero-converter.php) are compared against the current `post_content`
  to warn if it was edited since conversion, but restoring is always a
  straight return to `_coptrz_pre_convert_content` — no arithmetic subtraction
  needed, since that backup already excludes both parts by construction. Posts
  converted before this backup existed have none: revert instead strips a
  leading hero block, then re-runs `coptrz_convert_post_to_blocks()` as a dry
  run to regenerate the exact section block markup and subtracts it as a
  suffix — tried both with and without a leading `\n\n`, since that depends on
  whether the ORIGINAL content was empty (the very thing being recovered). If
  it doesn't match, it aborts rather than guessing, so content that predated
  the converter is never silently mangled. Every path finishes by deleting
  both converted flags, `_coptrz_sections_mode`, and every backup key —
  `_sections`/`_sections_after_main` and the Hero post-meta are never touched
  by conversion, so both legacy builders resume the moment their flag is gone
  (`coptrz_sections_should_route()`, `coptrz_hero_block_attrs()` in
  includes/hero-block.php).

  **`wp_slash()` on every write** — `coptrz_convert_post_to_blocks()`,
  `coptrz_convert_post_sections()`, and `coptrz_revert_post_to_blocks()` all
  `wp_slash()` the value passed to `wp_update_post()`/`update_post_meta()`
  (`post_content`, `_coptrz_pre_convert_content`, `_coptrz_converted_blocks`,
  `_coptrz_hero_block`). Both of those core functions call `wp_unslash()` on
  their input internally (they're built to accept `$_POST`-shaped data), and
  `serialize_blocks()` output legitimately contains literal backslash-escapes
  — WP 7's `serialize_block_attributes()` (wp-includes/blocks.php) escapes `<`,
  `>`, `&`, `--`, `\`, and `\"` to `<`/`>`/`&`/`--`/
  `\`/`"` inside every block comment. Without `wp_slash()` first,
  `wp_unslash()` strips those backslashes and every escaped character is
  corrupted — e.g. a `<p>` inside a `coptrz/tabs-legacy` description survives
  as the literal text `u003cpu003e`, not a paragraph tag. The three meta writes
  and the `post_content` write in `coptrz_convert_post_to_blocks()` MUST stay
  slashed together: `coptrz_revert_post_to_blocks()`'s edited-since-conversion
  check reconstructs `$expected` from those same three meta values and compares
  it against the live `post_content` column, so slashing one without the
  others breaks that comparison for every future conversion. The meta shim's
  `Writer::set()` (includes/meta-shim/Writer.php) has the same requirement on
  its unknown-field fallback, matching `Key_Formatter::write_cell()`'s
  `add_post_meta(..., wp_slash($value))` a few lines below it in the sibling
  class — `update_option()` is the one branch in that fallback that must NOT be
  slashed, since (unlike post/term meta) it doesn't unslash on the way in.
  `coptrz_find_corrupted_conversions()` scans convertible post types'
  `post_content` for the six stripped-escape signatures (`u003c`, `u003e`,
  `u0026`, `u002du002d`, `u005c`, `u0022`) and lists matches on the Tools >
  Convert to Blocks page — the repair for a listed post is Revert then Convert
  (regenerates from the untouched `_sections`/Hero meta), not an in-place text
  patch: a stripped `\n` is ambiguous with a literal trailing "n", so exact
  recovery from the corrupted string alone isn't possible. Reverting discards
  any block-editor edits made to that post since it was converted.

  Reverting alone doesn't make the legacy builder *editable* again:
  `coptrz_register_html_sections_fields()` hides its meta boxes globally via
  `Container_Admin::hide_fields()`. `Container_Admin::is_container_render_hidden()`
  (meta-shim/Container_Admin.php) now takes the post id being edited (resolved in
  `register_post_meta_boxes()` from `$_GET['post']`/`$_POST['post_ID']`, same pattern
  `post_conditions_match()` already used) and, for a blocklisted field, consults a
  `coptrz_meta_shim_field_visible` filter (default `false`) before treating it as
  hidden. section-converter.php hooks that filter to un-hide `sections`/
  `sections_after_main` specifically for posts that are NOT currently converted, so a
  reverted post's builder box reappears without un-hiding it globally for every other
  (still converted) post.

  **Preview original / public legacy fallback** — two request-scoped ways to see the
  legacy render of an otherwise-converted post, neither of which touches the converted
  flag or can re-trigger a conversion. `coptrz_sections_legacy_override($post_id)`
  returns true, frontend only, either for an editor hitting `?coptrz_preview=original`
  on the post's permalink (checked via `current_user_can('edit_post', …)`), or for a
  logged-out visitor when the post has opted in via `_coptrz_serve_legacy_public`
  (checkbox in the "Convert to Blocks" box, shown only when sections were
  converted for that post; logged-in users always see the converted
  version regardless of that checkbox). It's saved by its own plain `save_post`
  handler rather than routed through the meta shim, since it's one checkbox living
  alongside the revert controls, not a Carbon-shaped field. A `wp_body_open` banner
  ("Previewing ORIGINAL…", linking back to the live URL) renders whenever the override
  is active, so neither mode can be mistaken for the real page.
  `coptrz_sections_render_converted($post_id)` (`is_converted && !legacy_override`) is
  what render sites actually call — `coptrz_sections_should_route()`, and
  `page-gutenberg.php`'s choice between `coptrz_render_converted_sections()` and
  `the_content()`. A `template_include` filter additionally forces
  `templates/page-modules.php` for a `page` while the override is active, since the
  page's real (post-conversion) template is `page-blocks-editor.php`, whose
  `the_content()`/`coptrz_render_converted_sections()` branch would otherwise
  render the converted `post_content` regardless of what `should_route()`
  says. Deliberately NOT consulted by `coptrz_sections_is_converted()`,
  which stays the pure write-path guard against double-conversion (the skip checks in
  both convert functions) — an overridable version there could let a preview/
  public-fallback request re-trigger a conversion and duplicate content.

  Admin tools, extended: the per-post box now also shows a "Preview original" link,
  the public-fallback checkbox, and Dry run revert / Revert buttons (converted posts
  only) — same inline-`<script>`/admin-ajax pattern as convert, hitting a new
  `wp_ajax_coptrz_revert_sections` handler. The bulk runner (Tools > Convert to Blocks)
  gained a Convert/Revert mode radio above the search box: it swaps which button pair
  is shown and is passed as `mode` to `wp_ajax_coptrz_search_sections_posts` (default
  `convert` = flag NOT EXISTS, as before; `revert` = flag EXISTS), so the same
  search-and-select UI finds already-converted posts to revert. Switching modes clears
  the current selection (converting vs reverting are disjoint candidate sets).

  **Post-list status label** — a `display_post_states` filter prints "Needs
  converting" (amber) and/or "Converted"/"Converted (purged)" (green/grey) next
  to the title on every convertible post type's list table (the same slot core
  uses for "— Private"/"— Posts Page", joined with ", " by core itself), scoped
  to `coptrz_convertible_post_types()`. The two are independent `$states[]`
  entries, not one winner-take-all label: sections and hero convert (and purge)
  independently, so a post CAN legitimately be both at once — e.g. sections
  converted-and-purged while hero was never converted and still has real
  content reads "Needs converting, Converted (purged)". `coptrz_post_is_legacy_purged()`
  answers "is everything that was ever purgeable now purged", which is not the
  same question as "is this post fully done" (see its docblock) — that's why
  it's fine for both labels to appear together. Reads only
  `coptrz_post_conversion_state()` / the converted flags / `coptrz_post_is_legacy_purged()`
  — all meta-cache-only (see below), so the label adds no queries per row.
  `coptrz_hero_has_content()` was rewritten to read its six signals
  (`hero_hidden`, `hero_heading`, `hero_description`, `hero_background`,
  `hero_background_youtube`, `buttons`, `hero_form_enable`) directly via
  `get__post_meta_by_id()` instead of routing through `coptrz_hero_meta_to_attrs()`,
  which additionally resolves `get_the_title()`/`wp_get_attachment_url()` for
  buttons/forms/backgrounds — real queries this function never needed, since it
  only checks non-emptiness, and which would otherwise run per row on every list
  screen. Behaviourally identical (same six signals, same height/alignment
  exclusion); `coptrz_hero_has_content_where()`'s docblock (includes/hero-converter.php)
  still describes the same shape and must stay in sync by hand.

  **Purge** — a per-post action (manage_options only, stricter than convert/revert's
  `edit_post`) that PERMANENTLY deletes the legacy sections/hero meta for a
  converted post's already-converted part(s), once the converted output has been
  confirmed correct. Deliberately has no batch/bulk form, for the same reason the
  "convert all remaining" batch action doesn't exist — see the read-only "remaining
  by post type" table above.
  `coptrz_post_purgeable_parts($post_id)` (section-converter.php) returns the subset
  of `['sections', 'hero']` that is BOTH converted AND still has legacy data present
  (`coptrz_post_has_sections_data()` / `coptrz_hero_has_content()`) — per-part,
  because purging the not-yet-converted part of a post would delete data still
  serving as its live editing surface.
  `coptrz_purge_post_legacy_data($post_id, $dry_run)` does the deletion, via
  `Key_Formatter::delete_root('post', $id, $field)` (includes/meta-shim/Key_Formatter.php
  — "delete every row, exact + descendants, for one root field", the only way to
  correctly clear a complex field's per-cell keys like `_sections|||0|value`) over
  `coptrz_section_source_fields()` for the sections part and
  `coptrz_hero_meta_field_names()` for the hero part, plus each part's own backup
  meta (`_coptrz_converted_blocks`, `_coptrz_pre_convert_template`,
  `_coptrz_serve_legacy_public` for sections; `_coptrz_hero_block` for hero).
  `_coptrz_pre_convert_content` (the write-once backup shared by both parts) is
  only dropped once a fresh `coptrz_post_purgeable_parts()` call confirms nothing
  purgeable remains — a post converted for both parts but purged one at a time
  must not lose the backup the other part might still need. Once purging leaves
  nothing purgeable, `_coptrz_legacy_purged` (`COPTRZ_LEGACY_PURGED_FLAG`,
  `coptrz_post_is_legacy_purged()`) is set. **This is NOT "this post is fully
  done"** — it's "every part that was ever purgeable is now purged" — and it can
  be true while a DIFFERENT part that was never converted in the first place is
  still genuinely pending (that part was never "purgeable", so it doesn't block
  the flag). E.g. sections purged, hero never touched: the flag is true, but the
  post still needs hero converting. Each part ALSO gets its own independent flag
  — `COPTRZ_SECTIONS_PURGED_FLAG` / `COPTRZ_HERO_PURGED_FLAG`
  (`coptrz_sections_is_purged()` / `coptrz_hero_is_purged()`) — set alongside the
  post-wide one, for callers that need to know about ONE specific part rather
  than "is everything purgeable done". `coptrz_sections_legacy_override()` checks
  both the post-wide flag AND `coptrz_sections_is_purged()` before allowing
  `?coptrz_preview=original` — the post-wide flag alone would miss the case
  above (sections purged, hero still pending keeps it false), and without the
  per-part check the preview would try to render `___sections()` legacy output
  from data that's already gone.
  The report every convert/revert/purge action returns now carries an explicit
  `'dry_run' => (bool) $dry_run` key. This was found necessary the hard way: a
  dry run's report is otherwise shaped IDENTICALLY to a real run's (`purged`/
  `restored`/etc describe what would/did happen either way), so reading one in
  the meta box's output pane gave no indication nothing had actually been
  written — exactly what caused a real support incident (a homepage believed
  force-purged, that in fact still had all its legacy data intact, because only
  "Dry run force purge" had been clicked). The box's JS now reads this key from
  the SERVER's response (not its own local intent) and, when true, prepends a
  visible "⚠ DRY RUN — nothing was written" banner with a distinct
  border/background on the output pane — not just a JSON field a user has to
  know to look for.
  Refuses (no writes, `skipped: true`) when nothing is purgeable (never converted,
  or already purged), or when `post_content` matches the `wp_slash()` corruption
  signature (extracted from `coptrz_find_corrupted_conversions()` into
  `coptrz_content_is_corrupted($content)`, used by both) — purging would destroy
  the only repair path, since repair IS Revert-then-Convert regenerating fresh
  content from this same legacy data. Bypassable via a `$force` param (still
  requires `coptrz_post_purgeable_parts()` non-empty — force never applies to
  "already purged"/"never converted") — this is the one thing `$force` changes;
  it never touches `post_content` any more than a normal purge does, so a
  force-purged corrupted post is left with its `u003c`-style garbage forever,
  PERMANENTLY unrepairable (the meta Revert-then-Convert would regenerate from
  is exactly what got deleted). `coptrz_find_corrupted_conversions()` marks
  such a post `unrepairable` (via `coptrz_post_is_legacy_purged()`), and the
  Tools-page notice below tags it inline so the repair instructions there stop
  being told to an admin for whom they can no longer work.
  Deliberately does NOT touch: `sections_html`/`sections_after_main_html` (the
  product HTML-mode repeater — still the live rendering surface when
  `_coptrz_sections_mode = 'html'`); `COPTRZ_SECTIONS_CONVERTED_FLAG`/
  `COPTRZ_HERO_CONVERTED_FLAG`/`_coptrz_sections_mode` (these ROUTE rendering —
  clearing them would send `___sections()`/`___hero_modules()` back to the now-empty
  legacy builders); `hide_on_list`/`cpd_maker`/`tquk_logo` (declared on OTHER
  containers in post-meta.php, so absent from `coptrz_hero_meta_field_names()` and
  unreachable by a name-driven purge even though the hero block's render path
  still reads them from meta).
  Purging a part is irreversible: `coptrz_revert_post_to_blocks()` now checks
  `coptrz_post_is_legacy_purged()` first and refuses (rather than falling through to
  its no-backup regeneration path, which — with the source meta gone — would
  silently strip the hero block and leave the unsourced converted section blocks in
  place instead of actually restoring anything). `coptrz_sections_legacy_override()`
  likewise refuses once EITHER the post-wide flag OR `coptrz_sections_is_purged()`
  is true, so `?coptrz_preview=original` stops working the moment sections
  specifically is purged, not only once the whole post is (the public-fallback
  checkbox is already neutralized either way, since purge deletes
  `_coptrz_serve_legacy_public` itself).
  Meta box: the terminal "This post/page was converted from the old editor." —
  no buttons — state requires BOTH `coptrz_post_is_legacy_purged()` AND nothing
  still pending (`coptrz_post_conversion_state()`); the flag alone isn't enough,
  per the "not the same as fully done" note above — showing the terminal message
  while a never-converted part still had real content would have hidden the only
  way to ever convert it. When something's still pending, the converted branch
  instead shows an extra amber "Still needs converting: hero." (or `sections`, or
  `sections + hero`) block with the same Dry run / "Convert remaining" buttons
  the unconverted state uses (`coptrz_convert_post_to_blocks()` already safely
  skips whichever part is done — same AJAX action, no new handler). While still
  converted but not fully purged, a manage_options user ALSO sees a Purge block
  (red warning text, Dry run purge / Purge behind a `confirm()`) below the
  existing Revert controls, shown only when `coptrz_post_purgeable_parts()` is
  non-empty — same inline-`<script>`/admin-ajax pattern as convert/revert,
  hitting a new `wp_ajax_coptrz_purge_sections` handler. The "Preview original"
  link and logged-out-fallback checkbox are gated on `$sections_converted &&
  !coptrz_sections_is_purged()`, not just `$sections_converted` — otherwise
  they'd keep showing (uselessly) after sections is purged but hero is still
  the reason the post as a whole isn't `$fully_purged`.
  When purgeable but `coptrz_content_is_corrupted($post->post_content)` is also true,
  the button is replaced with a one-line explanation pointing at the Tools page's
  corrupted-conversions repair flow instead — found necessary by testing against real
  site data, where most already-converted posts turned out to predate the
  `wp_slash()` fix and would otherwise show a Purge button that dry-run immediately
  refuses. Below that explanation sits a collapsed `<details>` disclosure, "Force
  purge anyway (not recommended)" — deliberately not a visible button, since this
  is strictly worse than a normal purge (it gives up the post's only remaining
  repair path on content that's already broken) and must never be the easy/default
  option. Opening it shows an explicit warning naming the consequence, then Dry run
  force purge / Force purge buttons; the real action requires TWO sequential
  `confirm()` dialogs (not one, unlike every other destructive action in this box)
  before it fires the same `wp_ajax_coptrz_purge_sections` handler with an added
  `force=1`, which `coptrz_purge_post_legacy_data()` reads as its third parameter.
- `[layouts id="..."]` shortcode in `shortcodes.php` renders a `layouts` post's
  `sections`/`section_items` fields via `___sections('sections', $id)`; it's
  guarded with `function_exists('___sections')` (not a `layouts()` function,
  which doesn't exist) since `___sections` lives in `modules.php`, which is
  skipped in admin/REST on the blocks-editor template.
- `woocommerce.php` (~2460 lines) — WooCommerce template/hook overrides; pairs
  with the `woocommerce/` directory which overrides core WooCommerce templates
  (`archive-product.php`, `cart/`, `checkoutx/`, `loop/`, `single-product/`,
  `global/`, `content-single-product.php`). See Catalog mode below — the store
  is currently browse-only.

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

### WooCommerce catalog mode

- The store is currently browse-only site-wide (`includes/woocommerce.php`,
  "Catalog Mode" block, near the end of the file). To fully revert, delete
  that block — it's self-contained and doesn't depend on the older
  category-scoped version it replaced.
- `add_filter('woocommerce_is_purchasable', '__return_false')` — nothing is
  purchasable. This alone removes the add-to-cart button from loops/single
  product pages and makes WooCommerce (incl. the Store API) reject add-to-cart
  calls.
- `woocommerce_single_product_summary` is re-hooked to `request_info` at
  priority 30 — core's add-to-cart template (which normally fires
  `woocommerce_after_add_to_cart_button`, where `request_info()` used to hang)
  bails out early for a non-purchasable product, so the "Request Info" CTA has
  to be re-attached directly or it disappears along with the cart button.
- `dd_catalog_mode_block_add_to_cart()` (`woocommerce_add_to_cart_validation`)
  rejects direct `?add-to-cart=123` URL hits with a readable notice, as a
  belt-and-braces layer on top of `woocommerce_is_purchasable`.
- `dd_catalog_mode_redirect_cart_checkout()` (`template_redirect`) sends any
  hit on the cart/checkout pages back to the shop URL with a notice —
  excludes `order-received`/`order-pay` endpoints so existing order links
  still work. Covers stale bookmarks/emails/internal links.
- `dd_catalog_mode_empty_existing_cart()` (`wp_loaded`, priority 20) empties
  any cart left over from before catalog mode, so mini-cart counts don't
  linger.
- The header mini-cart icon/dropdown was removed from
  `template-parts/header/header-right.php` (only the Booqable rentals cart
  button remains, for `rentals` posts / specific landing page IDs) — see the
  `main.js` note above.
- `custom_product_variation_training()`'s per-variation button in the
  comparison/training table was swapped from "Add to basket"/"Buy now" links
  to a single "Discover" link to the variation's own permalink
  (`$variation_product->get_permalink()` — `get_permalink()` on a variation
  post ID resolves incorrectly, it must be called on the variation product
  object).

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
