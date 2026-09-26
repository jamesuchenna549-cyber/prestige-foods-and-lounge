const ordersContainer =
    document.querySelector('.orders-container');

const ordersMessage =
    document.querySelector('.my-orders-message');


// =========================
// GET CUSTOMER ORDERS
// =========================

async function getMyOrders() {

    try {

        const response = await fetch(
            'backend/my_orders.php',
            {
                credentials: 'include'
            }
        );

        const result =
            await response.json();

        console.log('My orders:', result);


        if (!result.success) {

            ordersMessage.textContent =
                result.message;

            return;
        }


        if (result.orders.length === 0) {

            ordersMessage.textContent =
                'You have no orders yet.';

            return;
        }


        let html = '';


        result.orders.forEach(function(order) {

            const total =
                Number(order.total_amount)
                    .toLocaleString();

            const status =
                order.status.charAt(0).toUpperCase() +
                order.status.slice(1);


            html += `
                <div class="order-card">

                    <h2>
                        Order #${order.id}
                    </h2>

                    <p>
                        Total:
                        <strong>₦${total}</strong>
                    </p>

                    <p>
                        Status:
                        <strong>${status}</strong>
                    </p>

                    <button
                        class="view-order"
                        data-id="${order.id}"
                    >
                        View Order
                    </button>

                </div>
            `;
        });


        ordersContainer.innerHTML = html;


    } catch (error) {

        console.log(
            'My orders error:',
            error
        );

        ordersMessage.textContent =
            'Unable to load your orders.';
    }
}


// =========================
// VIEW ORDER
// =========================

ordersContainer.addEventListener(
    'click',
    function(event) {

        if (
            !event.target.classList.contains(
                'view-order'
            )
        ) {
            return;
        }


        const orderId =
            event.target.dataset.id;


        window.location.href =
            'order_success.html?order_id=' +
            orderId;
    }
);


// =========================
// START
// =========================

getMyOrders();