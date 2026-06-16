import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { apiFetch } from './useApi';

export function useProducts(page = 1, perPage = 20) {
  return useQuery({
    queryKey: ['products', page, perPage],
    queryFn: () => apiFetch(`/products?page=${page}&per_page=${perPage}`),
  });
}

export function useProductOrder() {
  return useQuery({
    queryKey: ['products', 'order'],
    queryFn: () => apiFetch('/products/order'),
  });
}

export function useUpdateProductOrder() {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: (order) => apiFetch('/products/order', { method: 'POST', body: { order } }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['products'] });
    },
  });
}
