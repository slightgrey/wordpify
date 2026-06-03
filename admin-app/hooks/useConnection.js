import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { apiFetch } from './useApi';

export function useConnection() {
  return useQuery({
    queryKey: ['connection'],
    queryFn: () => apiFetch('/connection'),
  });
}

export function useSaveConnection() {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: (data) => apiFetch('/connection', { method: 'POST', body: data }),
    onSuccess: () => qc.invalidateQueries({ queryKey: ['connection'] }),
  });
}

export function useDisconnect() {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: () => apiFetch('/connection', { method: 'DELETE' }),
    onSuccess: () => qc.invalidateQueries({ queryKey: ['connection'] }),
  });
}
