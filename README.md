# Professor Web — WordPress plugin

Custom Elementor widgets, a site settings screen and a small design system, built by
[Professor Web](https://professorweb.co.uk/) for **The Jolly Joiner** (bespoke joinery, Stoke on Trent).

## Features

- **11 Elementor widgets** in a "Professor Web" panel category: Header (with mega menu and full-screen
  mobile menu), Footer, Home Hero (rotating background videos), Page Hero, Content Block (framed photo,
  photo collage or text columns), Section Heading, Card Grid, Stats Strip, Scrolling Ribbon,
  Call to Action and Contact Details.
- **Settings screen** (Dashboard → Professor Web) for business details, header/footer text, brand colours,
  wood texture strength and animations — one change updates the whole site.
- Headings support `{curly brackets}` for gold highlighted words.
- Bundled **Bebas Neue** and **Lato** fonts (no Google Fonts requests) and switches off Elementor's
  Google Fonts loading.
- Scroll animations, parallax, counters and video reel in plain JavaScript (no jQuery, no libraries),
  with `prefers-reduced-motion` respected.
- Click-to-call buttons only on touch devices; plain phone number on desktop.
- Shortcodes: `[pw_phone]`, `[pw_email]`, `[pw_address]`, `[pw_year]`.

## Requirements

- WordPress 6.4+, PHP 7.4+
- Elementor, plus Elementor Pro or Pro Elements (Theme Builder header/footer and the Form widget)
- Tested with the Hello Elementor theme

## Installation

1. Copy the folder to `wp-content/plugins/` (or upload the zip in Plugins → Add New).
2. Activate **Professor Web**.
3. Fill in Dashboard → Professor Web, then add the widgets in Elementor.

## Structure

```
professor-web.php            bootstrap
includes/                    settings, front end, Elementor integration, helpers, icons
includes/widgets/            one file per widget
assets/css|js|fonts|img      styles, scripts, bundled fonts, fallback logo
```

## Licence

GPL-2.0-or-later
