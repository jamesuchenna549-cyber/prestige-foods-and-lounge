

const orderDetailsContainer =
    document.querySelector('.order-details-container');

const orderDetailsMessage =
    document.querySelector('.order-details-message');


// =========================
// GET ORDER ID FROM URL
// =========================

const urlParams =
    new URLSearchParams(window.location.search);

const orderId =
    urlParams.get('order_id');


// =========================
// GET ORDER DETAILS
// =========================

async function getOrderDetails() {

    if (!orderId) {

        orderDetailsMessage.textContent =
            'No order was selected.';

        orderDetailsMessage.classList.add(
            'admin-show-message'
        );

        return;
    }


    try {

        const response = await fetch(
            '../backend/admin_order_items.php?order_id=' + orderId,
            {
                credentials: 'include'
            }
        );


        const result =
            await response.json();


        console.log('Order details:', result);


        if (!result.success) {

            orderDetailsMessage.textContent =
                result.message;

            orderDetailsMessage.classList.add(
                'admin-show-message'
            );

            return;
        }


        let html = `
            <h2>Order #${orderId}</h2>
        `;


        result.items.forEach(function(item) {

            const price =
                Number(item.price);

            const quantity =
                Number(item.quantity);

            const itemTotal =
                price * quantity;


            html += `
  <div class="order-item">



<img src="${item.image}"alt="${item.name}">

<div class="order-item-info">

            <h3>
        ${item.name}
           </h3>

     <p>
                            Quantity: ${quantity}
      </p>

      <p>
  Unit Price: ₦${price.toLocaleString()}
    </p>

          <p>
    Item Total:
  ₦${itemTotal.toLocaleString()
}
                        </p>

                    </div>

                </div>
            `;

        });


        orderDetailsContainer.innerHTML =
            html;


    } catch (error) {

        console.log(
            'Order details error:',
            error
        );

        orderDetailsMessage.textContent =
            'Unable to load order details.';

        orderDetailsMessage.classList.add(
            'admin-show-message'
        );

    }

}


// =========================
// START
// =========================

getOrderDetails();


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
