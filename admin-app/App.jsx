import { HashRouter, Routes, Route, NavLink } from 'react-router-dom';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { LayoutDashboard, Link2, RefreshCw, Package, Code2, BookOpen } from 'lucide-react';
import { cn } from '@/lib/utils';
import Dashboard from '@/pages/Dashboard';
import ConnectionSettings from '@/pages/ConnectionSettings';
import SyncSettings from '@/pages/SyncSettings';
import ProductsList from '@/pages/ProductsList';
import ShortcodesPage from '@/pages/ShortcodesPage';
import FeaturesGuide from '@/pages/FeaturesGuide';

const queryClient = new QueryClient({
  defaultOptions: { queries: { staleTime: 30_000 } },
});

const navItems = [
  { to: '/', label: 'Dashboard', icon: LayoutDashboard, end: true },
  { to: '/connection', label: 'Connection', icon: Link2 },
  { to: '/sync', label: 'Sync Settings', icon: RefreshCw },
  { to: '/products', label: 'Products', icon: Package },
  { to: '/shortcodes', label: 'Shortcodes', icon: Code2 },
  { to: '/guide', label: 'Features & Setup', icon: BookOpen },
];

function Sidebar() {
  return (
    <aside className="w-52 min-h-full border-r bg-card flex flex-col">
      <div className="px-4 py-5 border-b">
        <div className="flex items-center gap-2">
          <div className="h-8 w-8 rounded bg-primary flex items-center justify-center text-primary-foreground font-bold text-sm">W</div>
          <div>
            <div className="font-semibold text-sm leading-tight">Wordpify</div>
            <div className="text-[10px] text-muted-foreground leading-tight">by Web of D</div>
          </div>
        </div>
      </div>
      <nav className="flex-1 p-2 space-y-0.5">
        {navItems.map(({ to, label, icon: Icon, end }) => (
          <NavLink
            key={to}
            to={to}
            end={end}
            className={({ isActive }) =>
              cn(
                'flex items-center gap-2.5 rounded-md px-3 py-2 text-sm font-medium transition-colors',
                isActive
                  ? 'bg-primary !text-white focus:!text-white active:!text-white'
                  : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground'
              )
            }
          >
            <Icon className="h-4 w-4" />
            {label}
          </NavLink>
        ))}
      </nav>
      <div className="p-3 border-t">
        <p className="text-xs text-muted-foreground">v{window.wordpifyAdmin?.version ?? '1.0.0'}</p>
      </div>
    </aside>
  );
}

export default function App() {
  return (
    <QueryClientProvider client={queryClient}>
      <HashRouter>
        <div className="flex min-h-screen bg-background">
          <Sidebar />
          <main className="flex-1 overflow-auto">
            <Routes>
              <Route path="/" element={<Dashboard />} />
              <Route path="/connection" element={<ConnectionSettings />} />
              <Route path="/sync" element={<SyncSettings />} />
              <Route path="/products" element={<ProductsList />} />
              <Route path="/shortcodes" element={<ShortcodesPage />} />
              <Route path="/guide" element={<FeaturesGuide />} />
            </Routes>
          </main>
        </div>
      </HashRouter>
    </QueryClientProvider>
  );
}
