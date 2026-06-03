const api = window.wordpifyAdmin ?? { nonce: '', restUrl: '' };

export async function apiFetch(path, options = {}) {
  const url = api.restUrl.replace(/\/$/, '') + '/wordpify/v1' + path;
  const res = await fetch(url, {
    ...options,
    headers: {
      'Content-Type': 'application/json',
      'X-WP-Nonce': api.nonce,
      ...(options.headers ?? {}),
    },
    body: options.body ? JSON.stringify(options.body) : undefined,
  });

  if (!res.ok) {
    const err = await res.json().catch(() => ({ message: res.statusText }));
    throw new Error(err.message ?? `HTTP ${res.status}`);
  }

  return res.json();
}
