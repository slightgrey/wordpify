import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { apiFetch } from './useApi';

export function useSyncStatus() {
  return useQuery({
    queryKey: ['sync-status'],
    queryFn: () => apiFetch('/sync/status'),
    refetchInterval: (data) => data?.last_sync_status === 'running' ? 2000 : false,
  });
}

export function useRunSync() {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: () => apiFetch('/sync/run', { method: 'POST' }),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['sync-status'] });
      qc.invalidateQueries({ queryKey: ['products'] });
    },
  });
}

export function useSyncSettings() {
  return useQuery({
    queryKey: ['settings'],
    queryFn: () => apiFetch('/settings'),
  });
}

export function useSaveSettings() {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: (data) => apiFetch('/settings', { method: 'POST', body: data }),
    onSuccess: () => qc.invalidateQueries({ queryKey: ['settings'] }),
  });
}
