const customersContainer = document.querySelector('.customers-container');
const customersMessage = document.querySelector('.customers-message');

async function getCustomers() {
try {
const response = await fetch('../backend/admin_customers.php', {
credentials: 'include'
});

    const result = await response.json();

    console.log(result);

    if (!result.success) {
        customersMessage.textContent = result.message;
        customersMessage.classList.add('admin-show-message');
        return;
    }

    let html = '';

    result.customers.forEach(function(customer) {

    html += `
        <div class="admin-customer">

            <h2>${customer.full_name || "No name"}</h2>

            <p>Email: ${customer.email}</p>

            <p>Customer ID: ${customer.id}</p>

            <p>Phone: ${customer.phone || "Not provided"}</p>

            <p>
                Address:
                ${customer.address || "Not provided"}
            </p>

            <p>City: ${customer.city || "Not provided"}</p>

            <p>State: ${customer.state || "Not provided"}</p>

            <p>
                ZIP Code:
                ${customer.zip_code || "Not provided"}
            </p>

            <p>
                Delivery Note:
                ${customer.delivery_note || "None"}
            </p>

            <p>Joined: ${customer.created_at}</p>

        </div>
    `;
});
    customersContainer.innerHTML = html;

} catch (error) {
    console.log('Customers error:', error);

    customersMessage.textContent = 'Unable to load customers.';
    customersMessage.classList.add('admin-show-message');
}

}

getCustomers();

const logoutLink = document.querySelector('.logout-link');

logoutLink.addEventListener('click', async function(event) {
    event.preventDefault();

    try {
        const response = await fetch('../backend/admin_logout.php', {
            credentials: 'include'
        });

        const result = await response.json();

        if (result.success) {
            window.location.href = 'index.html';
        }

    } catch (error) {
        console.log('Logout error:', error);
    }
});























