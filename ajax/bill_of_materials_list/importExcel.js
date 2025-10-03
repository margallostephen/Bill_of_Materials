$("#importExcelForm").submit(function (e) {
    e.preventDefault();

    const $modal = $(this);
    const $fileInput = $("#excelFileImport")[0];
    const $importBtn = $modal.find("button[type='submit']");
    const $spinner = $modal.find('#execute_spinner');
    const btnText = $modal.find('#execute_btn_text');

    if (!$fileInput.files.length) {
        showToast("warning", "Please select a file.", $importBtn);
        return;
    }

    const formData = new FormData();
    formData.append("file", $fileInput.files[0]);

    $.ajax({
        url: `${BACKEND_PATH}/bill_of_materials/import_excel.php`,
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: () => {
            $spinner.show();
            btnText.text('Importing...');
            $importBtn.prop('disabled', true);
        },
        success: (response) => {
            if (!sessionValidityChecker(response, bomTable)) return;

            const responseResult = response.status;
            const toastText = responseResult ? "success" : "warning";

            if (responseResult) {
                resetModal("modalImport", "importExcelForm");
                populateTable(bomTable, "bill_of_materials/get_data");
            }

            showToast(toastText, response.message, $importBtn);
        },
        error: (error) => errorFunction(error, $importBtn),
        complete: () => {
            $spinner.hide();
            btnText.text('Submit');
            $importBtn.prop('disabled', false);
        }
    });
});