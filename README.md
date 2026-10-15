# Wordpify

Wordpify connects a WordPress site to a Shopify store through the **Shopify Storefront API**. It syncs your products into WordPress and gives logged-in users the full shopping flow without leaving your site: product grid, product pages, cart, account, addresses and order history. Checkout is handed off to Shopify.

- **Requires:** WordPress 6.7+, PHP 8.2+ (with `sodium`)
- **Works with:** any theme. Every shortcode also has an Elementor widget.
- **Version:** 1.3.2

## Features

### Product sync
- Syncs products on a schedule (hourly, twice daily or daily) or on demand from the admin dashboard.
- Stores variants, prices, stock, images, tags and collections locally, so pages load without calling Shopify.
- Products that are deleted or unpublished in Shopify are moved to the trash on the next sync.
- **Shopify Bundles:** bundle products sync with their contents. Product pages list "What's included" with each item's image, quantity and a link to that product.

### Storefront
- **Product grid** with custom drag-and-drop ordering, collection filter, and per-shortcode styling (columns, image height, colours, font sizes).
- **Product pages:** pick one WordPress page as the *Product Page*, and grid items link to it as `/your-page/?item=<handle>`. The page's heading and tab title show the product name.
- **Popup mode:** put the grid and the single-product shortcode on the same page, and products open in a popup over the grid. The URL stays shareable, and the Back button closes the popup.
- Two-column product layout with an image gallery, variant selector and quantity field.
- **Enable/disable** any product from the admin. A disabled product keeps syncing but is hidden everywhere on the site.
- **Placeholder image** (e.g. your logo) for products without one.

### Cart and accounts
- Each logged-in WordPress user gets a Shopify-backed cart, with add, update and remove plus a live mini-cart count.
- Each WordPress user is linked to a Shopify customer, with editable account details, an address book and order history.
- Checkout opens Shopify's hosted checkout. Shopify doesn't allow its checkout to be embedded in other sites.

### Security
- The Shopify token is encrypted at rest with libsodium.
- Admin endpoints require `manage_options`. Cart and account endpoints require a logged-in user and a REST nonce.
- All storefront output is shown to **logged-in users only**.

## Installation

### From a release zip
1. Download `wordpify-vX.Y.Z.zip` from the [Releases](../../releases) page.
2. In WordPress, go to **Plugins → Add New → Upload Plugin**, upload the zip and activate it.

### From source
```bash
cd wp-content/plugins
git clone https://github.com/slightgrey/wordpify.git
cd wordpify
composer install      # generates vendor/autoload.php
npm install
npm run build         # builds the admin app into dist/
```
Then activate **Wordpify** under **Plugins**.

## Shopify setup

1. **Create a Storefront API token.** In Shopify admin, go to **Settings → Apps and sales channels → Develop apps**, create an app, and enable the **Storefront API**. Turn on at least these scopes:
   - `unauthenticated_read_product_listings`
   - `unauthenticated_read_product_inventory`
   - `unauthenticated_write_checkouts` and `unauthenticated_read_checkouts`
   - `unauthenticated_write_customers` and `unauthenticated_read_customers`

   Use the **Storefront API access token**. An Admin API token (`shpat_…`) won't work.
2. **Publish products to the app's sales channel.** The Storefront API only returns products that are available on that channel. Bundles created by the Shopify Bundles app go to the Online Store only by default, so add them to your app's channel too. To do this, open the product and go to **Sales channels → Manage**.
3. **Turn off password protection** under **Online Store → Preferences**. Otherwise customers hit a password page at checkout.

## Configuration

In WordPress admin, open **Wordpify**:

| Tab | What it does |
|---|---|
| **Dashboard** | Connection and last-sync status, **Sync Now** |
| **Connection** | Store domain (`your-store.myshopify.com`) and Storefront token, with a connection test |
| **Sync Settings** | Automatic sync schedule |
| **Shop** | Product Page, placeholder image, tax label (e.g. "+ GST"), checkout notice |
| **Products** | Synced products, the **Enabled** switch per product, drag-and-drop **Reorder Products** |
| **Shortcodes** | Shortcode reference with every attribute |
| **Features & Setup** | In-admin feature overview and Shopify setup guide |

## Shortcodes

| Shortcode | Displays |
|---|---|
| `[wordpify_products]` | Product grid |
| `[wordpify_product]` | Single product (fixed, from the URL, or as a popup) |
| `[wordpify_cart]` | Full cart with totals and checkout button |
| `[wordpify_mini_cart]` | Cart icon with item count |
| `[wordpify_checkout]` | Hand-off to Shopify checkout |
| `[wordpify_account]` | Customer account details form |
| `[wordpify_address]` | Address book |
| `[wordpify_orders]` | Order history |

### `[wordpify_products]`
| Attribute | Default | Notes |
|---|---|---|
| `columns` | `3` | 2–4 |
| `limit` | `12` | Maximum products shown |
| `orderby` | `menu_order` | `menu_order` (your drag-and-drop order), `title` or `date` |
| `collection` | | Collection slug |
| `image_height` | `220` | In px |
| `heading_color`, `heading_font_size`, `desc_color`, `desc_font_size` | | Card text styling |
| `btn_bg_color`, `btn_text_color`, `btn_border_radius` | | Button styling |

### `[wordpify_product]`
| Attribute | Default | Notes |
|---|---|---|
| `id` | | WordPress post ID of a synced product |
| `handle` | | Shopify product handle |
| `show_title` | `auto` | `auto`, `yes` or `no`. Auto hides the title where the page heading already shows the product name. |
| `display` | `auto` | `auto`, `inline` or `popup`. Auto uses a popup when this shortcode is on the same page as the grid. |

Without `id` or `handle`, the product is read from `?item=<handle>`. Use that form on your **Product Page**.

### Product page or popup
- **Separate page:** create a page containing `[wordpify_product]` and select it as the *Product Page* in **Wordpify → Shop**.
- **Popup:** put `[wordpify_products]` and `[wordpify_product]` on the same page (e.g. *Shop*) and select that page as the *Product Page*.

## Development

```bash
npm run dev        # rebuild the admin app on change
npm run build      # production build into dist/
npm run bundle     # build and package wordpify-vX.Y.Z.zip
npm run version:patch   # or version:minor / version:major
```

| Path | Contents |
|---|---|
| `wordpify.php` | Plugin bootstrap |
| `src/` | PHP (PSR-4, `Wordpify\`): `Sync/`, `Shopify/` (GraphQL client and queries), `REST/`, `Frontend/` (shortcodes, product links, images), `Elementor/`, `Admin/`, `Cart/`, `Auth/` |
| `templates/` | Front-end templates (product, cart, checkout, account) |
| `assets/` | Public CSS, JS and images |
| `admin-app/` | React admin (Vite, Tailwind, Radix UI), built into `dist/admin/` |

## License

[GPL-3.0](LICENSE)

---

Built by [Web of D](https://vincelayte.me/).
