import { useQuery } from '@tanstack/react-query';
import { apiFetch } from './useApi';

export function useProducts(page = 1, perPage = 20) {
  return useQuery({
    queryKey: ['products', page, perPage],
    queryFn: () => apiFetch(`/products?page=${page}&per_page=${perPage}`),
  });
}
