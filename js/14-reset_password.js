const resetPasswordForm =
    document.querySelector("#reset-password-form");

const newPassword =
    document.querySelector("#new-password");

const confirmPassword =
    document.querySelector("#confirm-password");

const resetMessage =
    document.querySelector("#reset-message");

const resetPasswordButton =
    document.querySelector("#reset-password-button");


// =========================
// GET TOKEN FROM URL
// =========================

const urlParams =
    new URLSearchParams(window.location.search);

const token =
    urlParams.get("token");


// =========================
// CHECK TOKEN
// =========================

if (!token) {

    resetMessage.textContent =
        "Invalid or missing reset link.";

    resetPasswordButton.disabled =
        true;
}


// =========================
// SUBMIT RESET FORM
// =========================

resetPasswordForm.addEventListener(
    "submit",
    async function(event) {

        event.preventDefault();


        if (!token) {
            return;
        }


        const password =
            newPassword.value;

        const confirm =
            confirmPassword.value;


        // =========================
        // CHECK PASSWORD
        // =========================

        if (!password || !confirm) {

            resetMessage.textContent =
                "Please enter your new password.";

            return;
        }


        if (password !== confirm) {

            resetMessage.textContent =
                "Passwords do not match.";

            return;
        }


        resetPasswordButton.disabled =
            true;

        resetPasswordButton.textContent =
            "Changing Password...";


        // =========================
        // CREATE FORM DATA
        // =========================

        const formData =
            new FormData();

        formData.append(
            "token",
            token
        );

        formData.append(
            "password",
            password
        );


        // =========================
        // SEND TO PHP
        // =========================

        try {

            const response =
                await fetch(
                    "backend/reset_password.php",
                    {
                        method: "POST",
                        body: formData
                    }
                );


            const result =
                await response.json();


            // =========================
            // CHECK RESPONSE
            // =========================

            if (!result.success) {

                resetMessage.textContent =
                    result.message;

                resetPasswordButton.disabled =
                    false;

                resetPasswordButton.textContent =
                    "Change Password";

                return;
            }


            resetMessage.textContent =
                "Password changed successfully.";

            resetPasswordButton.textContent =
                "Password Changed";


            // =========================
            // GO TO LOGIN
            // =========================

            setTimeout(function() {

                window.location.href =
                    "index.html";

            }, 2000);


        } catch (error) {

            console.log(
                "Reset password error:",
                error
            );

            resetMessage.textContent =
                "Unable to reset password.";

            resetPasswordButton.disabled =
                false;

            resetPasswordButton.textContent =
                "Change Password";
        }

    }
);