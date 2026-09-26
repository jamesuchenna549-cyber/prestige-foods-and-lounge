
const loginForm = document.querySelector('.admin-login-form');
const loginMessage = document.querySelector('.admin-login-message');

loginForm.addEventListener('submit', async function(event) {
event.preventDefault();

const email = loginForm.querySelector('input[name="email"]').value.trim();
const password = loginForm.querySelector('input[name="password"]').value.trim();

const formData = new FormData();

formData.append('email', email);
formData.append('password', password);

try {

    const response = await fetch('../backend/admin_user.php', {
        method: 'POST',
        body: formData,
        credentials: 'include'
    });

    const result = await response.json();

    console.log(result);

    loginMessage.textContent = result.message;
    loginMessage.classList.add('admin-show-message');

    if (result.success) {
        window.location.href = 'dashboard.html';
    }

} catch (error) {

    console.log('Admin login error:', error);

    loginMessage.textContent = 'Unable to connect to the server.';
    loginMessage.classList.add('admin-show-message');

}

});
