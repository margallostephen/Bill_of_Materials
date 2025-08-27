$(document).ready(function () {
    $("#registerForm").on("submit", function (e) {
        e.preventDefault();

        const registerBtn = $(this).find("button[type='submit']").prop("disabled", true);

        const password = $("#passwordReg").val().trim();
        const confirmPassword = $("#confirmPassword").val().trim();

        if (password !== confirmPassword) {
            return showToast("warning", "Confirmation password does not match.", registerBtn)
        }

        $.ajax({
            url: `${BACKEND_PATH}/auth/register_user.php`,
            type: "POST",
            dataType: "json",
            data: {
                rfid: $("#rfidReg").val(),
                password: password,
                confirm_password: confirmPassword
            },
            success: function (response) {
                if (response.status) {
                    showToast("success", response.message, registerBtn);
                    $("#registerForm")[0].reset();
                    setTimeout(() => {
                        $("#registerForm").hide();
                        $("#loginForm").show();
                    }, 1000);
                } else {
                    showToast("warning", response.message, registerBtn);
                }
            },
            error: (error) => errorFunction(error, registerBtn)
        });
    });
});