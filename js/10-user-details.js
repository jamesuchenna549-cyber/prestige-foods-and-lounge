// userdetails file

import { newCart } from './03-cart.js';


export function userAuthentication() {

    let userDetails = {
        fullName: '',
        phoneNumber: '',
        address: '',
        city: '',
        state: '',
        zipCode: '',
        deliveryNote: ''
    };


    let fullName =
        document.getElementById('full-name');

    let phoneNumber =
        document.getElementById('phone-number');

    let address =
        document.getElementById('address');

    let city =
        document.getElementById('city');

    let state =
        document.getElementById('state');

    let zipCode =
        document.getElementById('zip-code');

    let deliveryNote =
        document.getElementById('delivery-note');

    let continueButton =
        document.getElementById('continue-btn');

    let message =
        document.querySelector('.message');


    continueButton.addEventListener(
        'click',
        async function(event) {

            event.preventDefault();


            // Get user details

            userDetails.fullName =
                fullName.value.trim();

            userDetails.phoneNumber =
                phoneNumber.value.trim();

            userDetails.address =
                address.value.trim();

            userDetails.city =
                city.value.trim();

            userDetails.state =
                state.value.trim();

            userDetails.zipCode =
                zipCode.value.trim();

            userDetails.deliveryNote =
                deliveryNote.value.trim();


            // Validate required fields

            if (
                userDetails.fullName === '' ||
                userDetails.phoneNumber === '' ||
                userDetails.address === '' ||
                userDetails.city === '' ||
                userDetails.state === '' ||
                userDetails.zipCode === ''
            ) {

                message.textContent =
                    "Please complete all required fields.";

                message.classList.add(
                    'show-message'
                );

                return;
            }


            // Check cart

            if (newCart.length === 0) {

                message.textContent =
                    "Your cart is empty.";

                message.classList.add(
                    'show-message'
                );

                return;
            }


            // Prepare cart items

            const orderItems =
                newCart.map(function(item) {

                    return {
                        id: item.id,
                        quantity: item.quantity
                    };

                });


            // Prepare data for PHP

            const orderData = {

                items: orderItems,

                userDetails: userDetails

            };


            console.log(
                "Sending order:",
                orderData
            );


            // Send order to PHP

            try {

                const response =
                    await fetch(
                        "backend/create_orders.php",
                        {
                            method: "POST",

                            credentials: "include",

                            headers: {
                                "Content-Type":
                                    "application/json"
                            },

                            body:
                                JSON.stringify(
                                    orderData
                                )
                        }
                    );


                const result =
                    await response.json();


                console.log(
                    "Order response:",
                    result
                );


                if (result.success) {

                    window.location.href =
                        "order_success.html?order_id=" +
                        result.order_id;

                } else {

                    message.textContent =
                        "❌ " + result.message;

                    message.classList.add('show-message');

                }


            } catch (error) {

                console.log(
                    "Order error:",
                    error
                );

                message.textContent =
                    "ERROR: " +
                    error.message;

                message.classList.add(
                    'show-message'
                );

            }

        }
    );

}
