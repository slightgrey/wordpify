import { useState, useEffect } from 'react';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { useShopSettings, useSaveShopSettings } from '@/hooks/useShop';

export default function ShopSettings() {
  const { data, isLoading } = useShopSettings();
  const save = useSaveShopSettings();

  const [form, setForm] = useState({
    gst_cart_enabled: true,
    gst_product_enabled: false,
    gst_text: '+ GST',
    checkout_notice_enabled: true,
    checkout_notice: 'Shipping calculated at checkout',
    product_page_id: 0,
  });

  useEffect(() => {
    if (data) setForm(data);
  }, [data]);

  const handleToggle = (field, checked) => {
    setForm((prev) => ({ ...prev, [field]: checked }));
    save.mutate({ [field]: checked });
  };

  const handleSaveText = () => {
    save.mutate({ gst_text: form.gst_text });
  };

  const handleSaveNotice = () => {
    save.mutate({ checkout_notice: form.checkout_notice });
  };

  const handleProductPageChange = (value) => {
    const id = Number(value);
    setForm((prev) => ({ ...prev, product_page_id: id }));
    save.mutate({ product_page_id: id });
  };

  const selectedPage = (data?.available_pages ?? []).find((p) => p.id === form.product_page_id);

  if (isLoading) return <div className="p-6 text-muted-foreground">Loading…</div>;

  return (
    <div className="p-6 space-y-6 max-w-2xl">
      <h1 className="text-2xl font-bold">Shop</h1>

      <Card>
        <CardHeader>
          <CardTitle>Product Page</CardTitle>
          <CardDescription>
            Choose the page that displays a single product. Add <code>[wordpify_product]</code> (or the Elementor
            Single Product widget) to that page <strong>without a handle</strong>. Product images, titles and
            “View Options” buttons in <code>[wordpify_products]</code> will then link to it.
            Tip: choose your shop page and put both shortcodes on it to open products in a popup over the grid.
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-3">
          <Select value={String(form.product_page_id ?? 0)} onValueChange={handleProductPageChange}>
            <SelectTrigger className="w-80">
              <SelectValue />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="0">— None (products not linked) —</SelectItem>
              {(data?.available_pages ?? []).map((p) => (
                <SelectItem key={p.id} value={String(p.id)}>
                  {p.title}{p.has_single_view ? ' ✓' : ''}
                </SelectItem>
              ))}
            </SelectContent>
          </Select>
          <p className="text-xs text-muted-foreground">✓ = page contains a single-product shortcode or widget.</p>
          {selectedPage && !selectedPage.has_single_view && (
            <p className="rounded border border-amber-300 bg-amber-50 px-3 py-2 text-sm text-amber-800">
              “{selectedPage.title}” doesn’t contain <code>[wordpify_product]</code> or the Single Product widget,
              so product links will open it without showing the product. Pick a separate page that does — not your shop/grid page.
            </p>
          )}
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Tax Label</CardTitle>
          <CardDescription>
            Display a tax label alongside prices across your store. The label text is editable — set it to anything (e.g. <em>+ GST</em>, <em>incl. VAT</em>).
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-5">
          <div className="flex items-center gap-3">
            <Switch
              id="gst-cart"
              checked={form.gst_cart_enabled}
              onCheckedChange={(checked) => handleToggle('gst_cart_enabled', checked)}
            />
            <Label htmlFor="gst-cart">Show tax label on cart total</Label>
          </div>

          <div className="flex items-center gap-3">
            <Switch
              id="gst-product"
              checked={form.gst_product_enabled}
              onCheckedChange={(checked) => handleToggle('gst_product_enabled', checked)}
            />
            <Label htmlFor="gst-product">Show tax label on each product price</Label>
          </div>

          <div className="space-y-1.5">
            <Label htmlFor="gst-text">Label text</Label>
            <div className="flex gap-2">
              <Input
                id="gst-text"
                className="w-48"
                value={form.gst_text}
                onChange={(e) => setForm((prev) => ({ ...prev, gst_text: e.target.value }))}
                onKeyDown={(e) => e.key === 'Enter' && handleSaveText()}
                placeholder="+ GST"
              />
              <Button onClick={handleSaveText} disabled={save.isPending}>
                {save.isPending ? 'Saving…' : 'Save'}
              </Button>
            </div>
          </div>
        </CardContent>
      </Card>
      <Card>
        <CardHeader>
          <CardTitle>Checkout Notice</CardTitle>
          <CardDescription>
            A short message shown below the cart total, visible to customers before they proceed to checkout.
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-5">
          <div className="flex items-center gap-3">
            <Switch
              id="checkout-notice-enabled"
              checked={form.checkout_notice_enabled}
              onCheckedChange={(checked) => handleToggle('checkout_notice_enabled', checked)}
            />
            <Label htmlFor="checkout-notice-enabled">Show checkout notice in cart</Label>
          </div>

          <div className="space-y-1.5">
            <Label htmlFor="checkout-notice">Notice text</Label>
            <div className="flex gap-2">
              <Input
                id="checkout-notice"
                className="w-80"
                value={form.checkout_notice}
                onChange={(e) => setForm((prev) => ({ ...prev, checkout_notice: e.target.value }))}
                onKeyDown={(e) => e.key === 'Enter' && handleSaveNotice()}
                placeholder="Shipping calculated at checkout"
              />
              <Button onClick={handleSaveNotice} disabled={save.isPending}>
                {save.isPending ? 'Saving…' : 'Save'}
              </Button>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
