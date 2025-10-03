$("#modalAdd").on("submit", function (e) {
    e.preventDefault();

    Swal.fire({
        title: "Add New Item/s.",
        text: "Have you verified all the information entered in the form?",
        icon: "question",
        iconColor: "#3498DB",
        showDenyButton: true,
        confirmButtonColor: "#87B87F",
        denyButtonColor: "#D15B47 ",
        confirmButtonText: "Yes",
        denyButtonText: "No",
    }).then((result) => {
        if (!result.isDismissed && !result.isDenied) {
            const $modal = $(this);
            const $form = $modal.find("#addItemForm");
            const $addItemBtn = $modal.find("button[type='submit']");
            const $spinner = $modal.find('#execute_spinner');
            const btnText = $modal.find('#execute_btn_text');

            $.ajax({
                url: `${BACKEND_PATH}/bill_of_materials/add_item.php`,
                type: "POST",
                dataType: "json",
                data: $form.serialize(),
                beforeSend: () => {
                    $spinner.show();
                    btnText.text('Submitting...');
                    $addItemBtn.prop('disabled', true);
                },
                success: function (response) {
                    if (!sessionValidityChecker(response, bomTable)) return;

                    if (response.status) {
                        showToast("success", response.message, $addItemBtn);
                        resetModal("modalAdd", "addItemForm");
                        populateTable(bomTable, "bill_of_materials/get_data");
                    } else {
                        showToast("warning", response.message, $addItemBtn);
                    }
                },
                error: (error) => errorFunction(error, $addItemBtn),
                complete: () => {
                    $spinner.hide();
                    btnText.text('Submit');
                    $addItemBtn.prop('disabled', false);
                }
            });
        }
    });
});