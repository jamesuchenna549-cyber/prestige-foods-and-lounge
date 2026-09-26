//cart engine file

import {renderCartUI} from './06-cartUI.js';
import {eventListener2,updateCartQuantity, updateUi} from './02-logic.js';
renderCartUI();
eventListener2();
updateCartQuantity();
updateUi();



