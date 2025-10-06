$(document).on("click", ".unarchiveBtn", function () {
    const unarchiveBtn = $(this);
    const rowData = JSON.parse(decodeURIComponent(unarchiveBtn.attr("data-row")));
    const label = rowData.division == "1" ? "Part" : "Material";

    Swal.fire({
        title: `Unarchive ${label}`,
        text: `Are you sure you want to unarchive this ${label.toLowerCase()}?`,
        icon: "warning",
        iconColor: "#D15B47",
        showCancelButton: true,
        confirmButtonColor: "#d15b47",
        cancelButtonColor: "#428bca",
        confirmButtonText: "Yes",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `${BACKEND_PATH}/bill_of_materials/unarchive_data.php`,
                type: "POST",
                dataType: "json",
                data: { row_data: rowData, type: label },
                success: function (response) {
                    if (!sessionValidityChecker(response, archivedBomTable)) return;

                    const responseResult = response.status;
                    const toastText = responseResult ? "success" : "warning";

                    if (responseResult) {
                        populateTable(archivedBomTable, "bill_of_materials/get_data");
                    }

                    showToast(toastText, response.message);
                },
                error: (error) => errorFunction(error)
            });
        }
    });
});
