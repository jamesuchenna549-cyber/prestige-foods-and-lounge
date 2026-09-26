import {
  currentProducts,
  renderUI
} from './01-products.js';


export function showAll() {

  renderUI(currentProducts);

}


export function showCategory(category) {

  let selectedProducts =
    currentProducts.filter(function(product) {

      return product.category === category;

    });

  renderUI(selectedProducts);

}


export function categoryListener() {

  let navigationMenus =
    document.querySelector('#navigation-menus');

  navigationMenus.addEventListener('click', function(event) {

    if (event.target.tagName === 'BUTTON') {

      let category =
        event.target.dataset.category;

      if (category === 'all') {

        showAll();

      } else {

        showCategory(category);

      }

    }

  });

}