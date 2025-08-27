$(document).ready(function () {
    $("#loginForm").on("submit", function (e) {
        e.preventDefault();

        const loginBtn = $(this).find("button[type='submit']").prop("disabled", true);

        $.ajax({
            url: `${BACKEND_PATH}/auth/login_user.php`,
            type: "POST",
            dataType: "json",
            data: {
                rfid: $("#rfid").val(),
                password: $("#password").val()
            },
            success: function (response) {
                if (response.status) {
                    showToast("success", response.message, loginBtn);
                    $("#loginForm")[0].reset();
                    setTimeout(() => {
                        window.location.href = `${BASE_URL}`;
                    }, 1000);
                } else {
                    showToast("warning", response.message, loginBtn);
                }
            },
            error: (error) => errorFunction(error, loginBtn)
        });
    });
});