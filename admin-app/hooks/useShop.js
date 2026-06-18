import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { apiFetch } from './useApi';

export function useShopSettings() {
  return useQuery({
    queryKey: ['shop-settings'],
    queryFn: () => apiFetch('/shop'),
  });
}

export function useSaveShopSettings() {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: (data) => apiFetch('/shop', { method: 'POST', body: data }),
    onSuccess: () => qc.invalidateQueries({ queryKey: ['shop-settings'] }),
  });
}
