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

            if (response.status) {
                showToast("success", response.message, importBtn);
                $("#modalImport").modal("hide");
                $("#importExcelForm")[0].reset();
            } else {
                showToast("warning", response.message, importBtn);
            }
        },
        error: (error) => errorFunction(error, importBtn),
        complete: () => {
            $('#execute_spinner').hide();
            $('#execute_btn_text').text('EXECUTE');
            $('#submitImportExcelBtn').prop('disabled', false).text("Submit");
        }
    });
});