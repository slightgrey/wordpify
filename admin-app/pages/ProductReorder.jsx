import { useEffect, useState } from 'react';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { GripVertical, ArrowLeft } from 'lucide-react';
import { useProductOrder, useUpdateProductOrder } from '@/hooks/useProducts';

export default function ProductReorder({ onDone }) {
  const { data, isLoading } = useProductOrder();
  const { mutate, isPending } = useUpdateProductOrder();
  const [items, setItems] = useState([]);
  const [dragIndex, setDragIndex] = useState(null);

  useEffect(() => {
    if (data?.products) {
      setItems(data.products);
    }
  }, [data]);

  const handleDragStart = (index) => () => {
    setDragIndex(index);
  };

  const handleDragOver = (index) => (e) => {
    e.preventDefault();
    if (dragIndex === null || dragIndex === index) {
      return;
    }
    setItems((prev) => {
      const next = [...prev];
      const [moved] = next.splice(dragIndex, 1);
      next.splice(index, 0, moved);
      return next;
    });
    setDragIndex(index);
  };

  const handleDragEnd = () => {
    setDragIndex(null);
  };

  const handleSave = () => {
    mutate(items.map((p) => p.id), { onSuccess: onDone });
  };

  return (
    <div className="p-6 space-y-4">
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-3">
          <Button variant="outline" size="sm" onClick={onDone}>
            <ArrowLeft className="h-4 w-4" />
          </Button>
          <h1 className="text-2xl font-bold">Reorder Products</h1>
        </div>
        <Button size="sm" onClick={handleSave} disabled={isPending || isLoading}>
          {isPending ? 'Saving…' : 'Save Order'}
        </Button>
      </div>

      <p className="text-sm text-muted-foreground">
        Drag products into the order you want them to appear in the Product Grid widget and shortcode.
      </p>

      <Card>
        <CardContent className="p-0">
          {isLoading ? (
            <div className="p-6 text-muted-foreground">Loading products…</div>
          ) : items.length === 0 ? (
            <div className="p-6 text-muted-foreground">No products synced yet.</div>
          ) : (
            <ul>
              {items.map((p, index) => (
                <li
                  key={p.id}
                  draggable
                  onDragStart={handleDragStart(index)}
                  onDragOver={handleDragOver(index)}
                  onDragEnd={handleDragEnd}
                  className={`flex items-center gap-3 px-4 py-3 border-b last:border-0 bg-background cursor-grab active:cursor-grabbing ${
                    dragIndex === index ? 'opacity-50' : ''
                  }`}
                >
                  <GripVertical className="h-4 w-4 text-muted-foreground shrink-0" />
                  {p.image_url && (
                    <img src={p.image_url} alt={p.title} className="h-10 w-10 rounded object-cover border" />
                  )}
                  <span className="font-medium text-sm">{p.title}</span>
                </li>
              ))}
            </ul>
          )}
        </CardContent>
      </Card>
    </div>
  );
}
