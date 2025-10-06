$(document).on("click", ".archiveBtn", function () {
    const archiveBtn = $(this);
    const type = archiveBtn.attr("data-type");
    const label = type == "true" ? "Part" : "Material";
    const dataId = archiveBtn.attr("data-type-id");

    Swal.fire({
        title: `Archive ${label}`,
        text: `Are you sure you want to archive this ${label.toLowerCase()}?`,
        icon: "warning",
        iconColor: "#D15B47",
        showCancelButton: true,
        confirmButtonColor: "#d15b47",
        cancelButtonColor: "#428bca",
        confirmButtonText: "Yes",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `${BACKEND_PATH}/bill_of_materials/archive_data.php`,
                type: "POST",
                dataType: "json",
                data: { data_id: dataId, type: label },
                success: function (response) {
                    if (!sessionValidityChecker(response, bomTable)) return;

                    const responseResult = response.status;
                    const toastText = responseResult ? "success" : "warning";

                    if (responseResult) {
                        populateTable(bomTable, "bill_of_materials/get_data");
                    }

                    showToast(toastText, response.message);
                },
                error: (error) => errorFunction(error)
            });
        }
    });
});
