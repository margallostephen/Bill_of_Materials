$("#exportExcelBtn").on("click", function () {
    const $exportBtn = $(this);
    const $btnIcon = $exportBtn.find("i");
    const $btnText = $exportBtn.find("span");

    $btnText.text('Exporting...');
    $btnIcon.removeClass('fa-file-export').addClass('fa-spinner fa-spin');
    $exportBtn.prop("disabled", true);

    const data = {
        header: bomColumns,
        data: bomTable.getData()
    };

    fetch(`${BACKEND_PATH}/bill_of_materials/export_excel.php`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    }).then(async response => {
        if (response.headers.get("Content-Type")?.includes("application/json")) {
            if (!sessionValidityChecker(response, table)) return;

            if (!response.status)
                return showToast("warning", response.message, $exportBtn);
        }

        const blob = await response.blob();
        const url = URL.createObjectURL(blob);
        const y = new Date().getFullYear().toString().slice(-2) + "Y";
        const d = new Date().toLocaleDateString("en-US", { month: "short", day: "2-digit" })
            .toUpperCase().replace(",", "");
        const filename = `(${y}) NPI_INTERNAL BOM_${d}.xlsx`;

        Object.assign(document.createElement("a"), {
            href: url,
            download: `${filename}.xlsx`
        }).click();

        URL.revokeObjectURL(url);

        showToast("success", "Successfully exported to excel.", $exportBtn);
    }).catch(error => {
        errorFunction(error, $exportBtn)
    }).finally(() => {
        $btnText.text('Export Data');
        $btnIcon.removeClass('fa-spinner fa-spin').addClass('fa-file-export');
        $exportBtn.prop('disabled', false);
    });
});