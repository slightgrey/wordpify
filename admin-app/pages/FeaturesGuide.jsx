import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { AlertCircle, ShieldCheck, ShoppingCart, RefreshCw, LayoutGrid, Settings } from 'lucide-react';

function Section({ title, children }) {
  return (
    <div className="space-y-3">
      <h2 className="text-lg font-semibold text-foreground border-b pb-2">{title}</h2>
      {children}
    </div>
  );
}

function SubSection({ title }) {
  return <p className="text-sm font-semibold text-muted-foreground mt-3 mb-1">{title}</p>;
}

function BulletList({ items }) {
  return (
    <ul className="space-y-1 pl-4">
      {items.map((item, i) => (
        <li key={i} className="text-sm text-muted-foreground flex gap-2">
          <span className="mt-1.5 h-1.5 w-1.5 rounded-full bg-primary shrink-0" />
          <span>{item}</span>
        </li>
      ))}
    </ul>
  );
}

function StepList({ items }) {
  return (
    <ol className="space-y-1 pl-4">
      {items.map((item, i) => (
        <li key={i} className="text-sm text-muted-foreground flex gap-2.5">
          <span className="shrink-0 font-semibold text-primary w-4">{i + 1}.</span>
          <span dangerouslySetInnerHTML={{ __html: item }} />
        </li>
      ))}
    </ol>
  );
}

const SHORTCODES_TABLE = [
  { tag: '[wordpify_products]',  desc: 'Product grid (configurable columns, colors, fonts)' },
  { tag: '[wordpify_product]',   desc: 'Single product detail page' },
  { tag: '[wordpify_cart]',      desc: 'Full cart with items and totals' },
  { tag: '[wordpify_mini_cart]', desc: 'Cart icon with item count' },
  { tag: '[wordpify_checkout]',  desc: 'Checkout page (redirects guests away)' },
  { tag: '[wordpify_account]',   desc: 'Shopify account details' },
  { tag: '[wordpify_address]',   desc: 'Saved billing/shipping addresses' },
  { tag: '[wordpify_orders]',    desc: 'Order history table' },
];

export default function FeaturesGuide() {
  return (
    <div className="p-6 space-y-8 max-w-3xl">
      <div>
        <h1 className="text-2xl font-bold">Features &amp; Setup Guide</h1>
        <p className="text-sm text-muted-foreground mt-1">
          A complete overview of the Wordpify plugin and how to configure Shopify before going live.
        </p>
      </div>

      {/* Overview */}
      <Section title="Overview">
        <p className="text-sm text-muted-foreground">
          Wordpify connects your WordPress site to a Shopify store, syncing products and giving logged-in
          users a full shopping experience — cart, checkout, account, and orders — all within WordPress.
        </p>
        <div className="flex gap-4 flex-wrap">
          {[
            { label: 'Version', value: '1.2.2' },
            { label: 'WordPress', value: '6.7+' },
            { label: 'PHP', value: '8.2+' },
          ].map(({ label, value }) => (
            <div key={label} className="rounded-md border bg-muted/40 px-3 py-1.5 text-xs">
              <span className="text-muted-foreground">{label}: </span>
              <span className="font-medium">{value}</span>
            </div>
          ))}
        </div>
      </Section>

      {/* Core Features */}
      <Section title="Core Features">
        <div className="space-y-4">
          <Card>
            <CardHeader className="pb-2 pt-4 px-4">
              <CardTitle className="text-sm flex items-center gap-2">
                <RefreshCw className="h-4 w-4 text-primary" /> Product Sync
              </CardTitle>
            </CardHeader>
            <CardContent className="px-4 pb-4">
              <BulletList items={[
                'Automatically syncs Shopify products on a schedule (hourly, twice daily, or daily) or manually via the admin dashboard',
                'Supports large catalogs with batched pagination',
                'Stores product variants, images, collections, tags, and pricing locally',
              ]} />
            </CardContent>
          </Card>
          <Card>
            <CardHeader className="pb-2 pt-4 px-4">
              <CardTitle className="text-sm flex items-center gap-2">
                <ShoppingCart className="h-4 w-4 text-primary" /> Shopping Cart
              </CardTitle>
            </CardHeader>
            <CardContent className="px-4 pb-4">
              <BulletList items={[
                'Each logged-in user gets a Shopify-backed cart',
                'Add, update, and remove items',
                'Generates a direct Shopify checkout URL',
              ]} />
            </CardContent>
          </Card>
          <Card>
            <CardHeader className="pb-2 pt-4 px-4">
              <CardTitle className="text-sm flex items-center gap-2">
                <Settings className="h-4 w-4 text-primary" /> Customer Accounts
              </CardTitle>
            </CardHeader>
            <CardContent className="px-4 pb-4">
              <BulletList items={[
                'Automatically creates and links a Shopify customer account for each WordPress user',
                'Displays saved addresses and full order history from Shopify',
              ]} />
            </CardContent>
          </Card>
        </div>
      </Section>

      {/* Shortcodes */}
      <Section title="Shortcodes">
        <p className="text-xs text-muted-foreground italic">All shortcodes require the user to be logged in.</p>
        <div className="rounded-md border overflow-hidden">
          <table className="w-full text-sm">
            <thead>
              <tr className="bg-muted/60 border-b">
                <th className="text-left px-4 py-2 font-semibold text-xs text-muted-foreground">Shortcode</th>
                <th className="text-left px-4 py-2 font-semibold text-xs text-muted-foreground">What it displays</th>
              </tr>
            </thead>
            <tbody>
              {SHORTCODES_TABLE.map(({ tag, desc }, i) => (
                <tr key={tag} className={i % 2 === 0 ? 'bg-background' : 'bg-muted/20'}>
                  <td className="px-4 py-2 font-mono text-xs text-foreground whitespace-nowrap">{tag}</td>
                  <td className="px-4 py-2 text-xs text-muted-foreground">{desc}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </Section>

      {/* Elementor */}
      <Section title="Elementor Support">
        <div className="flex items-start gap-3 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
          <LayoutGrid className="h-4 w-4 mt-0.5 shrink-0 text-blue-500" />
          <span>
            All 8 shortcodes are also available as <strong>Elementor widgets</strong> under a dedicated
            &ldquo;Wordpify&rdquo; widget category — drag and drop, no shortcode knowledge needed.
          </span>
        </div>
      </Section>

      {/* Security */}
      <Section title="Security">
        <BulletList items={[
          'Shopify API tokens are encrypted at rest using libsodium',
          'All admin endpoints require WordPress admin capabilities',
          'All cart and account endpoints require a logged-in user and nonce verification',
        ]} />
      </Section>

      {/* Checkout Behaviour */}
      <Section title="Checkout Behaviour">
        <div className="flex items-start gap-3 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
          <AlertCircle className="h-4 w-4 mt-0.5 shrink-0 text-amber-500" />
          <span>
            <strong>Redirect to Shopify checkout is by design and cannot be changed.</strong> Shopify
            intentionally prevents its checkout from being embedded in any external website to protect
            customers from payment fraud. Redirecting to Shopify&apos;s checkout is the standard approach
            used by all headless Shopify stores worldwide.
          </span>
        </div>
      </Section>

      {/* Required Shopify Admin Setup */}
      <Section title="Required Shopify Admin Setup">
        <p className="text-sm text-muted-foreground">
          Before the store goes live, two things must be configured in the Shopify admin.
        </p>

        <Card>
          <CardHeader className="pb-2 pt-4 px-4">
            <CardTitle className="text-sm">1. Sales Channel Visibility (Products)</CardTitle>
          </CardHeader>
          <CardContent className="px-4 pb-4 space-y-3">
            <p className="text-sm text-muted-foreground">
              Each product must have the <strong className="text-foreground">Online Store channel unchecked</strong> and
              only the <strong className="text-foreground">headless / custom storefront sales channel checked</strong>.
            </p>

            <SubSection title="To update a single product:" />
            <StepList items={[
              'Go to <strong>Shopify Admin → Products</strong>',
              'Click on a product to open it',
              'On the right side under <strong>Sales channels and apps</strong>, click <strong>Manage</strong>',
              '<strong>Uncheck</strong> the <strong>Online Store</strong> channel',
              'Make sure your <strong>headless / custom storefront</strong> sales channel is <strong>checked</strong>',
              'Click <strong>Done</strong>, then <strong>Save</strong>',
            ]} />

            <SubSection title="To update all products at once:" />
            <StepList items={[
              'Go to <strong>Shopify Admin → Products</strong>',
              'Select all products using the checkbox at the top',
              'Click <strong>Bulk edit</strong>',
              'In the <strong>Sales channels</strong> column, <strong>uncheck Online Store</strong> and <strong>check your headless/custom storefront channel</strong> for all products',
            ]} />
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="pb-2 pt-4 px-4">
            <CardTitle className="text-sm flex items-center gap-2">
              <ShieldCheck className="h-4 w-4 text-primary" />
              2. Remove Password Protection (Online Store)
            </CardTitle>
          </CardHeader>
          <CardContent className="px-4 pb-4 space-y-3">
            <p className="text-sm text-muted-foreground">
              Shopify&apos;s checkout loads under your Shopify domain. If password protection is active,
              customers will hit a login wall when completing their purchase — even though they are
              shopping on your WordPress site.
            </p>
            <StepList items={[
              'Go to <strong>Shopify Admin → Online Store → Preferences</strong>',
              'Scroll down to the <strong>Password protection</strong> section',
              'Uncheck <strong>&ldquo;Restrict access to visitors with the password&rdquo;</strong>',
              'Click <strong>Save</strong>',
            ]} />
          </CardContent>
        </Card>
      </Section>
    </div>
  );
}
