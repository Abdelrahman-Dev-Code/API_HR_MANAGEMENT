import React from 'react';
import { createRoot } from 'react-dom/client';
import App from './src/App';

const container = document.getElementById('app');

if (container) {
    createRoot(container).render(React.createElement(App));
}
