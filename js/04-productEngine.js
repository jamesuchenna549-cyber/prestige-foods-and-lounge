import { getProducts } from './01-products.js';

import {
  eventListener,
  updateCartQuantity,
  persistColor,
  getData
} from './02-logic.js';

import { categoryListener } from './05-show-category.js';

async function startApp() {

  await getProducts();

  eventListener();

  categoryListener();

  updateCartQuantity();

  persistColor();

  getData();
}

startApp();