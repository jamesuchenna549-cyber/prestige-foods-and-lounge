const ordersContainer = document.querySelector('.orders-container');
const ordersMessage = document.querySelector('.orders-message');


// =========================
// CHANGE ORDER STATUS
// =========================

ordersContainer.addEventListener('change', async function(event) {

    if (!event.target.classList.contains('order-status')) {
        return;
    }

    const orderId = event.target.dataset.id;
    const status = event.target.value;

    const formData = new FormData();

    formData.append('order_id', orderId);
    formData.append('status', status);

    try {

        const response = await fetch(
            '../backend/update_order_status.php',
            {
                method: 'POST',
                body: formData,
                credentials: 'include'
            }
        );

        const result = await response.json();

        console.log('Status update:', result);

        if (!result.success) {

            console.log(result.message);

            return;
        }

        console.log('Order status updated.');

        event.target.className =
            'order-status status-' + status;

    } catch (error) {

        console.log(
            'Status update error:',
            error
        );

    }

});


// =========================
// GET ORDERS
// =========================

async function getOrders() {

    try {

        const response = await fetch(
            '../backend/admin_orders.php',
            {
                credentials: 'include'
            }
        );

        const result = await response.json();

        console.log(result);

        if (!result.success) {

            ordersMessage.textContent =
                result.message;

            ordersMessage.classList.add(
                'admin-show-message'
            );

            return;
        }

        let html = '';

        result.orders.forEach(function(order) {

            html += `
                <div class="admin-order">

                    <h2>
           Order #${order.id}
                    </h2>

                    <p>
                        Customer ID: ${order.user_id}
                    </p>

    <p>
 Total:₦${
Number( order.total_amount).toLocaleString()
}
</p>

<p>
    Payment:
    <strong class="payment-status payment-${order.payment_status}">
        ${order.payment_status}
    </strong>
</p>

<p>
    Status: ${order.status}
</p>


<select
                        class="order-status status-${order.status}"
                        data-id="${order.id}"
                    >

                        <option
                            value="pending"
                            ${order.status === 'pending'
                                ? 'selected'
                                : ''}
                        >
                            Pending
                        </option>

                        <option
                            value="processing"
                            ${order.status === 'processing'
                                ? 'selected'
                                : ''}
                        >
                            Processing
                        </option>

                        <option
                            value="completed"
                            ${order.status === 'completed'
                                ? 'selected'
                                : ''}
                        >
                            Completed
                        </option>

                        <option
                            value="cancelled"
                            ${order.status === 'cancelled'
                                ? 'selected'
                                : ''}
                        >
                            Cancelled
                        </option>

                    </select>

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
            'Orders error:',
            error
        );

        ordersMessage.textContent =
            'Unable to load orders.';

        ordersMessage.classList.add(
            'admin-show-message'
        );

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
            'order_details.html?order_id=' + orderId;

    }
);


// =========================
// START
// =========================

getOrders();


// =========================
// LOGOUT
// =========================

const logoutLink =
    document.querySelector('.logout-link');

logoutLink.addEventListener(
    'click',
    async function(event) {

        event.preventDefault();

        try {

            const response = await fetch(
                '../backend/admin_logout.php',
                {
                    credentials: 'include'
                }
            );

            const result =
                await response.json();

            if (result.success) {

                window.location.href =
                    'index.html';

            }

        } catch (error) {

            console.log(
                'Logout error:',
                error
            );

        }

    }
);
