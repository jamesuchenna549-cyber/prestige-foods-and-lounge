const message =
    document.querySelector('#payment-message');

const urlParams =
    new URLSearchParams(window.location.search);

const reference =
    urlParams.get('reference');

console.log("Payment reference:", reference);


if (!reference) {

    message.textContent =
        "Payment reference was not found.";

} else {

    message.textContent =
        "Verifying your payment...";

    verifyPayment();
}


// =========================
// VERIFY PAYMENT
// =========================

async function verifyPayment() {

    const formData =
        new FormData();

    formData.append(
        "reference",
        reference
    );


    try {

        const response =
            await fetch(
                "backend/verify_payment.php",
                {
                    method: "POST",
                    credentials: "include",
                    body: formData
                }
            );


        const result =
            await response.json();


        console.log(
            "Payment verification:",
            result
        );


        if (!result.success) {

            message.textContent =
                result.message;

            return;
        }


        message.textContent =
            "Payment verified successfully!";

    } catch (error) {

        console.log(
            "Verification error:",
            error
        );

        message.textContent =
            "Unable to verify payment.";
    }
}