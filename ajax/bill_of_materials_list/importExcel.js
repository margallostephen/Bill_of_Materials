$("#importExcelForm").submit(function (e) {
    e.preventDefault();

    const fileInput = $("#excelFileImport")[0];
    const importBtn = $(this).find("button[type='submit']").prop("disabled", true);

    if (!fileInput.files.length) {
        showToast("warning", "Please select a file.", importBtn);
        return;
    }

    const formData = new FormData();
    formData.append("file", fileInput.files[0]);

    $.ajax({
        url: `${BACKEND_PATH}/bill_of_materials/import_excel.php`,
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: () => {
            $('#execute_spinner').show();
            $('#execute_btn_text').text('Importing...');
            $('#submitImportExcelBtn').prop('disabled', true);
        },
        success: (response) => {
            if (!sessionValidityChecker(response, bomTable)) return;

            console.log(response);

            if (response.status) {
                showToast("success", response.message, importBtn);

                populateTable(bomTable, "bill_of_materials/get_data");

                resetModal("modalImport", "importExcelForm")

                console.log(response.data);
            } else {
                showToast("warning", response.message, importBtn);
            }
        },
        error: (error) => errorFunction(error, importBtn),
        complete: () => {
            $('#execute_spinner').hide();
            $('#execute_btn_text').text('Submit');
            $('#submitImportExcelBtn').prop('disabled', false).text("Submit");
        }
    });
});