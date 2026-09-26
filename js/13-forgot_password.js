const forgotPasswordForm =
    document.querySelector("#forgot-password-form");

const forgotEmail =
    document.querySelector("#forgot-email");

const forgotMessage =
    document.querySelector("#forgot-message");

const resetButton =
    document.querySelector("#reset-button");

// =========================================
// CHECK THAT JAVASCRIPT IS LOADED
// =========================================

console.log(
    "Forgot password JavaScript loaded."
);

// =========================================
// SUBMIT FORM
// =========================================

forgotPasswordForm.addEventListener(
    "submit",
    async function(event) {

        event.preventDefault();

        // =========================
        // GET EMAIL
        // =========================

        const email =
            forgotEmail.value.trim();

        if (!email) {

            forgotMessage.textContent =
                "Please enter your email.";

            return;
        }

        // =========================
        // SHOW PROCESSING
        // =========================

        forgotMessage.textContent =
            "Processing request...";

        resetButton.disabled =
            true;

        resetButton.textContent =
            "Sending...";

        // =========================
        // CREATE FORM DATA
        // =========================

        const formData =
            new FormData();

        formData.append(
            "email",
            email
        );

        // =========================
        // SEND TO PHP
        // =========================

        try {

            const response =
                await fetch(
                    "backend/forgot_password.php",
                    {
                        method: "POST",
                        body: formData
                    }
                );

            // =========================
            // GET JSON RESPONSE
            // =========================

            const result =
                await response.json();

            // =========================
            // CHECK RESULT
            // =========================

            if (!result.success) {

                forgotMessage.textContent =
                    result.message;

                resetButton.disabled =
                    false;

                resetButton.textContent =
                    "Send Reset Link";

                return;
            }

            // =========================
            // SUCCESS
            // =========================

            forgotMessage.textContent =
                result.message;

            resetButton.textContent =
                "Reset Link Sent";

        } catch (error) {

            console.log(
                "Forgot password error:",
                error
            );

            forgotMessage.textContent =
                "Unable to process your request.";

            resetButton.disabled =
                false;

            resetButton.textContent =
                "Send Reset Link";
        }
    }
);