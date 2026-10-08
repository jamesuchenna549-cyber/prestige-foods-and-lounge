async function checkAdmin() {
    try {
        const response = await fetch('check_user.php', {
            credentials: 'include'
        });

        const result = await response.json();

        console.log(result);

        if (!result.success) {
            window.location.href = 'index.html';
            return;
        }

        console.log('Admin is authenticated.');

       await getAdminInfo();

    } catch (error) {
        console.log('Error:', error);
        window.location.href = 'index.html';
    }
}

async function getAdminInfo() {
    try {
        const response = await fetch('admin_info.php', {
            credentials: 'include'
        });

        const result = await response.json();

        console.log(result);

        if (result.success) {
            const welcome = document.querySelector('.admin-welcome');
            welcome.textContent = 'Welcome, ' + result.email;
        }

    } catch (error) {
        console.log('Admin info error:', error);
    }
}

checkAdmin();





const productsButton = document.querySelector('.products-button');

productsButton.addEventListener('click', function() {
    window.location.href = 'products.html';
});




const ordersButton = document.querySelector('.orders-button');
const customersButton = document.querySelector('.customers-button');

ordersButton.addEventListener('click', function() {
    window.location.href = 'orders.html';
});

customersButton.addEventListener('click', function() {
    window.location.href = 'customers.html';
});

const snookerButton = document.querySelector('.snooker-button');

snookerButton.addEventListener('click', function() {
    window.location.href = 'snooker.html';
});



const logoutButton = document.querySelector('.logout-button');

logoutButton.addEventListener('click', async function() {
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





























