require('./bootstrap');

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

import setupSellingPriceCalculator from './sale.js';

document.addEventListener('DOMContentLoaded', () => {
    setupSellingPriceCalculator();
});



