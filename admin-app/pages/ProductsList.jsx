import { useState } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { ChevronLeft, ChevronRight, Copy, Check, ArrowUpDown } from 'lucide-react';
import { Switch } from '@/components/ui/switch';
import { useProducts, useSetProductEnabled } from '@/hooks/useProducts';
import ProductReorder from '@/pages/ProductReorder';

function CopyShortcode({ id }) {
  const [copied, setCopied] = useState(false);
  const tag = `[wordpify_product id="${id}"]`;

  const handleCopy = () => {
    navigator.clipboard.writeText(tag).then(() => {
      setCopied(true);
      setTimeout(() => setCopied(false), 2000);
    });
  };

  return (
    <button
      onClick={handleCopy}
      title="Copy shortcode"
      className="flex items-center gap-1.5 rounded border px-2 py-1 text-xs font-mono text-muted-foreground hover:bg-muted hover:text-foreground transition-colors"
    >
      {copied ? <Check className="h-3 w-3 text-green-600 shrink-0" /> : <Copy className="h-3 w-3 shrink-0" />}
      {copied ? 'Copied!' : tag}
    </button>
  );
}

export default function ProductsList() {
  const [page, setPage] = useState(1);
  const [reordering, setReordering] = useState(false);
  const { data, isLoading } = useProducts(page, 20);
  const setEnabled = useSetProductEnabled();
  // Optimistic switch state while the request runs.
  const [pendingEnabled, setPendingEnabled] = useState({});

  const handleToggle = (id, enabled) => {
    setPendingEnabled((prev) => ({ ...prev, [id]: enabled }));
    setEnabled.mutate(
      { id, enabled },
      { onSettled: () => setPendingEnabled(({ [id]: _, ...rest }) => rest) }
    );
  };

  const products = data?.products ?? [];
  const total = data?.total ?? 0;
  const totalPages = data?.total_pages ?? 1;

  if (reordering) {
    return <ProductReorder onDone={() => setReordering(false)} />;
  }

  return (
    <div className="p-6 space-y-4">
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-bold">Synced Products</h1>
        <div className="flex items-center gap-3">
          <Badge variant="secondary">{total} total</Badge>
          <Button variant="outline" size="sm" onClick={() => setReordering(true)}>
            <ArrowUpDown className="h-4 w-4" />
            Reorder Products
          </Button>
        </div>
      </div>

      <Card>
        <CardContent className="p-0">
          {isLoading ? (
            <div className="p-6 text-muted-foreground">Loading products…</div>
          ) : products.length === 0 ? (
            <div className="p-6 text-muted-foreground">No products synced yet. Run a sync from the Dashboard.</div>
          ) : (
            <table className="w-full text-sm">
              <thead>
                <tr className="border-b bg-muted/50">
                  <th className="text-left px-4 py-3 font-medium" title="Disabled products are hidden from the storefront">Enabled</th>
                  <th className="text-left px-4 py-3 font-medium">Product</th>
                  <th className="text-left px-4 py-3 font-medium">Handle</th>
                  <th className="text-left px-4 py-3 font-medium">Variants</th>
                  <th className="text-left px-4 py-3 font-medium">Last Synced</th>
                  <th className="text-left px-4 py-3 font-medium">Shortcode</th>
                </tr>
              </thead>
              <tbody>
                {products.map((p) => {
                  const enabled = pendingEnabled[p.id] ?? p.enabled;
                  return (
                  <tr key={p.id} className="border-b last:border-0 hover:bg-muted/30 transition-colors">
                    <td className="px-4 py-3">
                      <Switch
                        checked={enabled}
                        onCheckedChange={(checked) => handleToggle(p.id, checked)}
                        aria-label={`${enabled ? 'Disable' : 'Enable'} ${p.title}`}
                      />
                    </td>
                    <td className={`px-4 py-3 ${enabled ? '' : 'opacity-60'}`}>
                      <div className="flex items-center gap-3">
                        {p.image_url && (
                          <img src={p.image_url} alt={p.title} className="h-10 w-10 rounded object-cover border" />
                        )}
                        <div>
                          <span className="font-medium">{p.title}</span>
                          {p.is_bundle && <Badge variant="outline" className="ml-2">Bundle</Badge>}
                          {!enabled && <Badge variant="secondary" className="ml-2">Disabled</Badge>}
                          <p className="text-xs text-muted-foreground">ID: {p.id}</p>
                        </div>
                      </div>
                    </td>
                    <td className="px-4 py-3 text-muted-foreground font-mono text-xs">{p.handle}</td>
                    <td className="px-4 py-3">
                      <Badge variant="secondary">{p.variant_count}</Badge>
                    </td>
                    <td className="px-4 py-3 text-muted-foreground text-xs">
                      {p.synced_at ? new Date(p.synced_at).toLocaleString() : '—'}
                    </td>
                    <td className="px-4 py-3">
                      <CopyShortcode id={p.id} />
                    </td>
                  </tr>
                  );
                })}
              </tbody>
            </table>
          )}
        </CardContent>
      </Card>

      {totalPages > 1 && (
        <div className="flex items-center justify-between">
          <p className="text-sm text-muted-foreground">Page {page} of {totalPages}</p>
          <div className="flex gap-2">
            <Button variant="outline" size="sm" onClick={() => setPage(p => p - 1)} disabled={page === 1}>
              <ChevronLeft className="h-4 w-4" />
            </Button>
            <Button variant="outline" size="sm" onClick={() => setPage(p => p + 1)} disabled={page >= totalPages}>
              <ChevronRight className="h-4 w-4" />
            </Button>
          </div>
        </div>
      )}
    </div>
  );
}
