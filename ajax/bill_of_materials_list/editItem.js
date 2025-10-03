$("#modalEdit").on("submit", function (e) {
    e.preventDefault();

    Swal.fire({
        title: "Edit Item Info",
        text: "Are you sure you want to edit this info?",
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
            const $form = $modal.find("#editItemForm");
            const $editItemBtn = $modal.find("button[type='submit']");
            const $spinner = $modal.find('#execute_spinner');
            const btnText = $modal.find('#execute_btn_text');

            $.ajax({
                url: `${BACKEND_PATH}/bill_of_materials/edit_item.php`,
                type: "POST",
                dataType: "json",
                data: $form.serialize(),
                beforeSend: () => {
                    $spinner.show();
                    btnText.text('Submitting...');
                    $editItemBtn.prop('disabled', true);
                },
                success: function (response) {
                    if (!sessionValidityChecker(response, bomTable)) return;

                    if (response.status) {
                        showToast("success", response.message, $editItemBtn);
                        resetModal("modalEdit", "editItemForm");
                        populateTable(bomTable, "bill_of_materials/get_data");
                    } else {
                        showToast("warning", response.message, $editItemBtn);
                    }
                },
                error: (error) => errorFunction(error, $editItemBtn),
                complete: () => {
                    $spinner.hide();
                    btnText.text('Submit');
                    $editItemBtn.prop('disabled', false);
                }
            });
        }
    });
});