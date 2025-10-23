localStorage.setItem("ace_state_id-breadcrumbs", '{"class":{"breadcrumbs-fixed":0}}');

$(document).ready(function () {
    const path = location.pathname;

    $(".sidebar-btn a").each(function () {
        if (this.pathname === path) {
            const $li = $(this).closest("li");

            $li.addClass("active")
                .parents("li").addClass("active open")
                .end().parents("ul.submenu").addClass("nav-show").css("display", "block");

            const icon = $li.find("i.menu-icon").attr("class");
            localStorage.setItem("activeIcon", icon);
            return false;
        }
    });

    const icon = `fa ${localStorage.getItem("activeIcon") || "fa-dashboard"} bigger-125`;
    $("#bc-icon").attr("class", icon);

    $(".sidebar-btn a").on("click", () => {
        localStorage.removeItem("activeIcon");
    });
});

function showToast(type, message, button = null) {
    toastr[type](message, type[0].toUpperCase() + type.slice(1), {
        closeButton: true,
        progressBar: true,
        positionClass: "toast-top-right",
        timeOut: "2000",
        extendedTimeOut: "1000",
        showDuration: "500",
        hideDuration: "2000",
        showMethod: "fadeIn",
        hideMethod: "fadeOut",
        onHidden: () => button?.prop("disabled", false)
    });
}
