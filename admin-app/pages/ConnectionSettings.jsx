import { useState } from 'react';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import { useConnection, useSaveConnection, useDisconnect } from '@/hooks/useConnection';

export default function ConnectionSettings() {
  const { data: connection } = useConnection();
  const saveConnection = useSaveConnection();
  const disconnect = useDisconnect();

  const [domain, setDomain] = useState('');
  const [token, setToken] = useState('');
  const [testResult, setTestResult] = useState(null);

  const handleSave = async (e) => {
    e.preventDefault();
    setTestResult(null);
    try {
      const result = await saveConnection.mutateAsync({ store_domain: domain, access_token: token });
      setTestResult(result);
      if (result.connected) {
        setDomain('');
        setToken('');
      }
    } catch (err) {
      setTestResult({ connected: false, error: err.message });
    }
  };

  return (
    <div className="p-6 space-y-6">
      <h1 className="text-2xl font-bold">Connection Settings</h1>

      {/* Two-column grid: left = Current Connection + form; right = instructions */}
      <div className="grid grid-cols-2 gap-6 items-start">

        {/* Left column */}
        <div className="space-y-6">

          {/* Current Connection */}
          <Card>
            <CardHeader>
              <CardTitle>Current Connection</CardTitle>
              <CardDescription>Your Shopify Storefront API credentials.</CardDescription>
            </CardHeader>
            <CardContent className="space-y-3">
              {connection?.connected ? (
                <>
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="font-medium">{connection.store_domain}</p>
                      <p className="text-sm text-muted-foreground">Token: {connection.access_token}</p>
                      {connection.last_tested_at && (
                        <p className="text-xs text-muted-foreground">
                          Last tested: {new Date(connection.last_tested_at).toLocaleString()}
                        </p>
                      )}
                    </div>
                    <Badge variant="success">Connected</Badge>
                  </div>
                  <Button
                    variant="destructive"
                    size="sm"
                    onClick={() => disconnect.mutate()}
                    disabled={disconnect.isPending}
                  >
                    Disconnect
                  </Button>
                </>
              ) : (
                <Badge variant="secondary">Not connected</Badge>
              )}
            </CardContent>
          </Card>

          {/* Connect to Shopify form */}
          <Card>
            <CardHeader>
              <CardTitle>{connection?.connected ? 'Update Credentials' : 'Connect to Shopify'}</CardTitle>
              <CardDescription>
                Enter your Shopify store domain and Storefront API access token.
              </CardDescription>
            </CardHeader>
            <CardContent>
              <form onSubmit={handleSave} className="space-y-4">
                <div className="space-y-1.5">
                  <Label htmlFor="domain">Store Domain</Label>
                  <Input
                    id="domain"
                    placeholder="your-store.myshopify.com"
                    value={domain}
                    onChange={(e) => setDomain(e.target.value)}
                    required
                  />
                  <p className="text-xs text-muted-foreground">Use your .myshopify.com domain, not your custom domain.</p>
                </div>
                <div className="space-y-1.5">
                  <Label htmlFor="token">Storefront API Access Token</Label>
                  <Input
                    id="token"
                    type="password"
                    placeholder="xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
                    value={token}
                    onChange={(e) => setToken(e.target.value)}
                    required
                  />
                </div>

                {testResult && (
                  <div className={`rounded-md p-3 text-sm ${testResult.connected ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800'}`}>
                    {testResult.connected
                      ? `Connected to "${testResult.shop_name}" successfully.`
                      : `Connection failed: ${testResult.error}`}
                  </div>
                )}

                <Button type="submit" disabled={saveConnection.isPending}>
                  {saveConnection.isPending ? 'Testing…' : 'Test & Save'}
                </Button>
              </form>
            </CardContent>
          </Card>

        </div>

        {/* Right column — instructions, aligned to top (same level as Current Connection) */}
        <Card className="border-blue-200 bg-blue-50">
          <CardHeader className="pb-2">
            <CardTitle className="text-sm text-blue-800">How to find your credentials</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4 text-sm text-blue-900">
            <div>
              <p className="font-semibold mb-1">1. Store Domain</p>
              <p className="mb-1">Your store domain is your internal Shopify address — <strong>not</strong> your custom domain.</p>
              <ol className="list-decimal list-inside space-y-0.5 text-blue-800">
                <li>Go to your <strong>Shopify Admin</strong></li>
                <li>Click <strong>Settings → Domains</strong></li>
                <li>Find the entry ending in <code className="bg-blue-100 px-1 rounded">.myshopify.com</code></li>
              </ol>
              <p className="mt-1.5 text-blue-700">e.g. <code className="bg-blue-100 px-1 rounded">your-store.myshopify.com</code></p>
            </div>
            <div>
              <p className="font-semibold mb-1">2. Storefront API Token</p>
              <ol className="list-decimal list-inside space-y-0.5 text-blue-800">
                <li>Go to <strong>Settings → Apps and sales channels</strong></li>
                <li>Click <strong>Develop apps</strong></li>
                <li>Open or create an app</li>
                <li>Go to the <strong>API credentials</strong> tab</li>
                <li>Go to the <strong>Storefront API integration</strong> tab, save scopes, then copy the <strong>public access token</strong></li>
              </ol>
              <p className="mt-1.5 text-blue-700">Looks like: <code className="bg-blue-100 px-1 rounded">a1b2c3d4e5f6...</code> (32 hex characters, no prefix)</p>
            </div>
          </CardContent>
        </Card>

      </div>
    </div>
  );
}
