import { RefreshCw, Package, Wifi, WifiOff, AlertCircle } from 'lucide-react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { useConnection } from '@/hooks/useConnection';
import { useSyncStatus, useRunSync } from '@/hooks/useSync';

export default function Dashboard() {
  const { data: connection, isLoading: connLoading } = useConnection();
  const { data: sync, isLoading: syncLoading } = useSyncStatus();
  const runSync = useRunSync();

  const statusBadge = {
    idle: <Badge variant="secondary">Idle</Badge>,
    running: <Badge variant="warning">Syncing…</Badge>,
    success: <Badge variant="success">Success</Badge>,
    error: <Badge variant="destructive">Error</Badge>,
  };

  return (
    <div className="p-6 space-y-6">
      <h1 className="text-2xl font-bold">Wordpify Dashboard</h1>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        {/* Connection status */}
        <Card>
          <CardHeader className="pb-2">
            <CardTitle className="text-sm font-medium text-muted-foreground">Shopify Connection</CardTitle>
          </CardHeader>
          <CardContent>
            {connLoading ? (
              <span className="text-muted-foreground text-sm">Loading…</span>
            ) : connection?.connected ? (
              <div className="flex items-center gap-2">
                <Wifi className="h-5 w-5 text-green-600" />
                <span className="font-semibold text-green-700">Connected</span>
              </div>
            ) : (
              <div className="flex items-center gap-2">
                <WifiOff className="h-5 w-5 text-destructive" />
                <span className="font-semibold text-destructive">Disconnected</span>
              </div>
            )}
            {connection?.store_domain && (
              <p className="text-xs text-muted-foreground mt-1">{connection.store_domain}</p>
            )}
          </CardContent>
        </Card>

        {/* Product count */}
        <Card>
          <CardHeader className="pb-2">
            <CardTitle className="text-sm font-medium text-muted-foreground">Synced Products</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="flex items-center gap-2">
              <Package className="h-5 w-5 text-primary" />
              <span className="text-2xl font-bold">{sync?.product_count ?? 0}</span>
            </div>
          </CardContent>
        </Card>

        {/* Sync status */}
        <Card>
          <CardHeader className="pb-2">
            <CardTitle className="text-sm font-medium text-muted-foreground">Last Sync</CardTitle>
          </CardHeader>
          <CardContent className="space-y-2">
            {syncLoading ? (
              <span className="text-muted-foreground text-sm">Loading…</span>
            ) : (
              <>
                <div>{statusBadge[sync?.last_sync_status ?? 'idle']}</div>
                {sync?.last_sync_at && (
                  <p className="text-xs text-muted-foreground">
                    {new Date(sync.last_sync_at).toLocaleString()}
                  </p>
                )}
                {sync?.last_sync_message && (
                  <div className="flex items-start gap-1 text-xs text-destructive">
                    <AlertCircle className="h-3 w-3 mt-0.5 shrink-0" />
                    {sync.last_sync_message}
                  </div>
                )}
              </>
            )}
          </CardContent>
        </Card>
      </div>

      <div>
        <Button
          onClick={() => runSync.mutate()}
          disabled={runSync.isPending || sync?.last_sync_status === 'running' || !connection?.connected}
        >
          <RefreshCw className={`h-4 w-4 ${runSync.isPending ? 'animate-spin' : ''}`} />
          {runSync.isPending ? 'Syncing…' : 'Sync Now'}
        </Button>
        {!connection?.connected && (
          <p className="text-xs text-muted-foreground mt-2">Connect to Shopify before syncing.</p>
        )}
      </div>
    </div>
  );
}
