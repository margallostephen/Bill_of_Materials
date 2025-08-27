$("#logoutBtn").click(function () {
    $.ajax({
        url: `${BACKEND_PATH}/auth/logout_user.php`,
        type: "POST",
        dataType: "json",
        success: function (response) {
            if (response.status) {
                if (!localStorage.getItem('session expired')) {
                    showToast("success", response.message);
                }
                localStorage.clear();
                setTimeout(() => {
                    window.location.href = `${BASE_URL}`;
                }, 1000);
            }
        }
    });
});
