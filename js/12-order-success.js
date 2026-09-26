const makePaymentButton =
    document.querySelector('#make-payment-btn');

const orderItemsContainer =
    document.querySelector('.order-items-container');

const orderNumber =
    document.querySelector('.order-number');

const totalPrice =
    document.querySelector('.total-price');

const orderStatus =
    document.querySelector('.order-status');

const paymentStatus =
    document.querySelector('.payment-status');


const urlParams =
    new URLSearchParams(window.location.search);

const orderId =
    urlParams.get('order_id');


console.log("Order ID:", orderId);


// =========================
// GET ORDER
// =========================

async function getOrderItems() {

    try {

        const response = await fetch(
            "backend/order_items.php?order_id=" + orderId,
            {
                credentials: "include"
            }
        );

        const result =
            await response.json();

        console.log("Order:", result);


        if (!result.success) {

            orderItemsContainer.textContent =
                result.message;

            return;
        }


        // =========================
        // ORDER NUMBER
        // =========================

        orderNumber.textContent =
            "Order #" + orderId;


        // =========================
        // ORDER STATUS
        // =========================

        const status =
            result.status.charAt(0).toUpperCase() +
            result.status.slice(1);

        orderStatus.textContent =
            "Status: " + status;

        orderStatus.className =
            "order-status status-" + result.status;


        // =========================
        // PAYMENT STATUS
        // =========================

        const payment =
            result.payment_status;

        const paymentText =
            payment.charAt(0).toUpperCase() +
            payment.slice(1);

        paymentStatus.textContent =
            "Payment: " + paymentText;

        paymentStatus.className =
            "payment-status payment-" + payment;


        // =========================
        // PAYMENT BUTTON
        // =========================

        if (payment === "paid") {

            makePaymentButton.style.display =
                "none";

        } else {

            makePaymentButton.style.display =
                "block";
        }


        // =========================
        // ORDER ITEMS
        // =========================

        let html = "";


        result.items.forEach(function(item) {

            const price =
                Number(item.price);

            const quantity =
                Number(item.quantity);

            const itemTotal =
                price * quantity;


            html += `
                <div class="order-item">

                    <div class="order-item-image">

                        <img
                            src="${item.image}"
                            alt="${item.name}"
                        >

                    </div>

                    <div class="order-item-details">

                        <h3>
                            ${item.name}
                        </h3>

                        <p>
                            Quantity:
                            <strong>${quantity}</strong>
                        </p>

                        <p>
                            Unit Price:
                            ₦${price.toLocaleString()}
                        </p>

                    </div>

                    <div class="item-total">

                        ₦${itemTotal.toLocaleString()}

                    </div>

                </div>
            `;
        });


        orderItemsContainer.innerHTML =
            html;


        // =========================
        // TOTAL
        // =========================

        totalPrice.textContent =
            "₦" +
            Number(result.total)
                .toLocaleString();


    } catch (error) {

        console.log(
            "Order error:",
            error
        );

        orderItemsContainer.textContent =
            "Unable to load your order.";
    }
}


getOrderItems();


// =========================
// MAKE PAYMENT
// =========================

async function initializePayment() {

    makePaymentButton.textContent =
        "Connecting to Payment...";

    makePaymentButton.disabled =
        true;


    const formData =
        new FormData();

    formData.append(
        "order_id",
        orderId
    );


    try {

        const response =
            await fetch(
                "backend/initialize_payment.php",
                {
                    method: "POST",
                    credentials: "include",
                    body: formData
                }
            );


        const result =
            await response.json();


        console.log(
            "Payment initialization:",
            result
        );


        if (!result.success) {

            alert(result.message);

            makePaymentButton.textContent =
                "Pay for This Order";

            makePaymentButton.disabled =
                false;

            return;
        }


        window.location.href =
            result.authorization_url;


    } catch (error) {

        console.log(
            "Payment error:",
            error
        );

        alert(
            "Unable to connect to payment."
        );


        makePaymentButton.textContent =
            "Pay for This Order";

        makePaymentButton.disabled =
            false;
    }
}


makePaymentButton.addEventListener(
    "click",
    initializePayment
);