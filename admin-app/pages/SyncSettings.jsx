import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import { useSyncSettings, useSaveSettings, useSyncStatus } from '@/hooks/useSync';

const INTERVALS = [
  { value: 'off', label: 'Disabled' },
  { value: 'hourly', label: 'Every hour' },
  { value: 'twicedaily', label: 'Twice a day' },
  { value: 'daily', label: 'Once a day' },
];

export default function SyncSettings() {
  const { data: settings, isLoading } = useSyncSettings();
  const { data: status } = useSyncStatus();
  const save = useSaveSettings();

  const handleIntervalChange = (value) => {
    save.mutate({ interval: value });
  };

  const handleDeleteToggle = (checked) => {
    save.mutate({ delete_on_uninstall: checked });
  };

  if (isLoading) return <div className="p-6 text-muted-foreground">Loading…</div>;

  const syncSettings = settings?.sync_settings ?? {};
  const deleteOnUninstall = settings?.delete_on_uninstall ?? false;

  return (
    <div className="p-6 space-y-6 max-w-2xl">
      <h1 className="text-2xl font-bold">Sync Settings</h1>

      <Card>
        <CardHeader>
          <CardTitle>Auto-Sync Schedule</CardTitle>
          <CardDescription>How often Wordpify should automatically pull products from Shopify.</CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="space-y-1.5">
            <Label>Sync Interval</Label>
            <Select value={syncSettings.interval ?? 'daily'} onValueChange={handleIntervalChange}>
              <SelectTrigger className="w-48">
                <SelectValue />
              </SelectTrigger>
              <SelectContent>
                {INTERVALS.map((i) => (
                  <SelectItem key={i.value} value={i.value}>{i.label}</SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Sync Log</CardTitle>
        </CardHeader>
        <CardContent className="space-y-2 text-sm">
          <div className="flex items-center gap-2">
            <span className="text-muted-foreground">Status:</span>
            <StatusBadge status={status?.last_sync_status} />
          </div>
          {status?.last_sync_at && (
            <p><span className="text-muted-foreground">Last run:</span> {new Date(status.last_sync_at).toLocaleString()}</p>
          )}
          <p><span className="text-muted-foreground">Products synced:</span> {status?.product_count ?? 0}</p>
          {status?.last_sync_message && (
            <p className="text-destructive">{status.last_sync_message}</p>
          )}
        </CardContent>
      </Card>

      <Card className="border-destructive/30">
        <CardHeader>
          <CardTitle className="text-destructive">Danger Zone</CardTitle>
          <CardDescription>
            If enabled, uninstalling Wordpify will permanently delete all synced products, collections, settings, and customer links.
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div className="flex items-center gap-3">
            <Switch
              id="delete-on-uninstall"
              checked={deleteOnUninstall}
              onCheckedChange={handleDeleteToggle}
            />
            <Label htmlFor="delete-on-uninstall">Delete all data on uninstall</Label>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}

function StatusBadge({ status }) {
  const map = {
    idle: <Badge variant="secondary">Idle</Badge>,
    running: <Badge variant="warning">Running</Badge>,
    success: <Badge variant="success">Success</Badge>,
    error: <Badge variant="destructive">Error</Badge>,
  };
  return map[status] ?? <Badge variant="secondary">Idle</Badge>;
}
