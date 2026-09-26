let loginAndRegisteContainer=
document.querySelector('.checkout-form-container');

function showUserDetail(category){
  let selectedItem=
loginAndRegisteContainer.querySelectorAll('.category');
selectedItem.forEach(function(userDetails){
  if(userDetails.classList.contains(category)){
    userDetails.style.display="block";
  }else{
    userDetails.style.display="none";
  }
})
  
}


let buttonContainer=document.querySelector('.login-register-button');

buttonContainer.addEventListener('click',function(event){
 if(event.target.classList.contains('login')) {
    let category=event.target.dataset.category;
    showUserDetail(category);
  }
if(event.target.classList.contains('register')) {
    let category=event.target.dataset.category;
    showUserDetail(category);
  }
})

showUserDetail('login');







function shownColor(){
  let btnContainer=document.querySelectorAll('.button-container');
  btnContainer.forEach(function(button){
    button.addEventListener('click',function(){
       btnContainer.forEach(function(item){
        item.classList.remove('button-color');
      })
     
      button.classList.add('button-color');
    })
  })
}

shownColor();


const params = new URLSearchParams(window.location.search);

if (params.get("registered") === "success") {

    showUserDetail("login");

    const message = document.querySelector("#registration-message");
    const logMessage = document.querySelector(".log-text");

message.textContent = "Registration successful! Please login.";

    logMessage.style.display = "none";

    
}






const registerForm = document.querySelector('.register form');

const registerMessage =
    document.querySelector('.register .message');


registerForm.addEventListener(
    'submit',
    async function(event) {

        event.preventDefault();

        const email =
            registerForm
                .querySelector('input[name="email"]')
                .value
                .trim();

        const password =
            registerForm
                .querySelector('input[name="password"]')
                .value
                .trim();

        const formData = new FormData();

        formData.append('email', email);
        formData.append('password', password);

        try {

            const response =
                await fetch(
                    '../backend/register.php',
                    {
                        method: 'POST',
                        body: formData
                    }
                );

            const result =
                await response.text();


            registerMessage.textContent =
                result;

            registerMessage.classList.add(
                'show-message'
            );


if (result === "Registration successful.") {
    showUserDetail("login");
}



        } catch (error) {

            registerMessage.textContent =
                'Unable to connect to the server.';

            registerMessage.classList.add(
                'show-message'
            );

            console.log(error);
        }

    }
);

/*login form*/

const loginForm =
    document.querySelector('.login form');

const loginMessage =
    document.querySelector('.login-message');


loginForm.addEventListener(
    'submit',
    async function(event) {
        event.preventDefault();

        const email =
            loginForm.querySelector('input[name="email"]').value.trim();

        const password =
            loginForm.querySelector('input[name="password"]').value.trim();

        const formData = new FormData();

        formData.append('email', email);
        formData.append('password', password);

        try {
            const response =
                await fetch('../backend/login.php', {
                    method: 'POST',
                    body: formData
                });

            if (response.redirected) {
                window.location.href = response.url;
                return;
            }

            const result = await response.text();

            loginMessage.textContent = result;
            loginMessage.classList.add('show-message');

        } catch (error) {
            loginMessage.textContent =
                'Unable to connect to the server.';

            loginMessage.classList.add('show-message');

            console.log(error);
        }
    }
);













































