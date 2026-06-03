import { Code2, LayoutGrid } from 'lucide-react';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';

const SHORTCODES = [
  {
    tag: 'wordpify_products',
    description: 'Displays a grid of synced Shopify products.',
    attrs: [
      { name: 'columns',    default: '3',     note: 'Number of columns (2–4)' },
      { name: 'limit',      default: '12',    note: 'Max products to show' },
      { name: 'orderby',    default: 'title', note: 'Sort order: title or date' },
      { name: 'collection', default: '—',     note: 'Filter by collection slug' },
    ],
    examples: [
      '[wordpify_products]',
      '[wordpify_products columns="4" limit="8"]',
      '[wordpify_products collection="adhesives" orderby="date"]',
    ],
  },
  {
    tag: 'wordpify_product',
    description: 'Displays a single product with image gallery, description, variant selector, and add-to-cart.',
    attrs: [
      { name: 'id', default: '—', note: 'WordPress post ID of the product' },
    ],
    examples: [
      '[wordpify_product id="42"]',
    ],
  },
  {
    tag: 'wordpify_cart',
    description: 'Displays the full cart with line items, quantities, totals, and a Proceed to Checkout button.',
    attrs: [],
    examples: [
      '[wordpify_cart]',
    ],
  },
  {
    tag: 'wordpify_checkout',
    description: 'Displays the Shopify checkout embedded in an iframe. Redirects to /cart/ if the cart is empty.',
    attrs: [],
    examples: [
      '[wordpify_checkout]',
    ],
  },
  {
    tag: 'wordpify_mini_cart',
    description: 'Renders a cart icon with a live item count badge. Place in your header or navigation.',
    attrs: [
      { name: 'show_count', default: 'true', note: 'Show or hide the count badge: true or false' },
    ],
    examples: [
      '[wordpify_mini_cart]',
      '[wordpify_mini_cart show_count="false"]',
    ],
  },
  {
    tag: 'wordpify_account',
    description: 'Account details form — lets the customer update their name and email.',
    attrs: [],
    examples: [
      '[wordpify_account]',
    ],
  },
  {
    tag: 'wordpify_address',
    description: 'Address book — lists saved addresses with add / edit / delete.',
    attrs: [],
    examples: [
      '[wordpify_address]',
    ],
  },
  {
    tag: 'wordpify_orders',
    description: 'Order history table showing past Shopify orders for the logged-in customer.',
    attrs: [],
    examples: [
      '[wordpify_orders]',
    ],
  },
];

function CodeBlock({ children }) {
  return (
    <code className="block bg-muted rounded px-3 py-1.5 text-xs font-mono text-foreground">
      {children}
    </code>
  );
}

export default function ShortcodesPage() {
  return (
    <div className="p-6 space-y-6">
      <div>
        <h1 className="text-2xl font-bold">Shortcodes</h1>
        <p className="text-sm text-muted-foreground mt-1">
          Add these shortcodes to any WordPress page or post. All shortcodes require the visitor to be logged in.
        </p>
      </div>

      {/* Elementor notice */}
      <div className="flex items-start gap-3 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
        <LayoutGrid className="h-4 w-4 mt-0.5 shrink-0 text-blue-500" />
        <span>
          <strong>Using Elementor?</strong> Every shortcode below has a matching widget in the{' '}
          <strong>Wordpify</strong> widget category — drag and drop, no shortcode needed.
        </span>
      </div>

      <div className="space-y-4">
        {SHORTCODES.map((sc) => (
          <Card key={sc.tag}>
            <CardHeader className="pb-2">
              <div className="flex items-center gap-2">
                <Code2 className="h-4 w-4 text-primary shrink-0" />
                <CardTitle className="text-sm font-mono">[{sc.tag}]</CardTitle>
              </div>
              <CardDescription className="text-xs mt-1">{sc.description}</CardDescription>
            </CardHeader>
            <CardContent className="space-y-3">
              {sc.attrs.length > 0 && (
                <div>
                  <p className="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-1.5">Attributes</p>
                  <table className="w-full text-xs border-collapse">
                    <thead>
                      <tr className="border-b">
                        <th className="text-left py-1 pr-4 font-medium text-muted-foreground">Attribute</th>
                        <th className="text-left py-1 pr-4 font-medium text-muted-foreground">Default</th>
                        <th className="text-left py-1 font-medium text-muted-foreground">Description</th>
                      </tr>
                    </thead>
                    <tbody>
                      {sc.attrs.map((a) => (
                        <tr key={a.name} className="border-b last:border-0">
                          <td className="py-1 pr-4 font-mono text-foreground">{a.name}</td>
                          <td className="py-1 pr-4 text-muted-foreground">{a.default}</td>
                          <td className="py-1 text-muted-foreground">{a.note}</td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              )}
              <div>
                <p className="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-1.5">
                  {sc.examples.length > 1 ? 'Examples' : 'Usage'}
                </p>
                <div className="space-y-1">
                  {sc.examples.map((ex) => (
                    <CodeBlock key={ex}>{ex}</CodeBlock>
                  ))}
                </div>
              </div>
            </CardContent>
          </Card>
        ))}
      </div>
    </div>
  );
}
