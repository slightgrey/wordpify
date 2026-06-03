import React from 'react';
import { createRoot } from 'react-dom/client';
import App from './App';
import './index.css';

const root = document.getElementById('wordpify-admin-root');
if (root) {
  createRoot(root).render(<App />);
}
