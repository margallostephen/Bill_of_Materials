function modalOpen(openBtn, modalTarget) {
    $(`#${openBtn}`).click(function () {
        $(`#${modalTarget}`).modal("show");
    });
}

function modalClose(closeBtn) {
    $(`.${closeBtn}`).click(function () {
        let $modal = $(this).closest(".modal");
        $modal.modal("hide");
        $modal.find("form")[0].reset();
    });
}
