$("#exportExcelBtn").on("click", async function () {
    const $exportBtn = $(this);
    const $btnIcon = $exportBtn.find("i");
    const $btnText = $exportBtn.find("span");

    $btnText.text('Exporting...');
    $btnIcon.removeClass('fa-file-export').addClass('fa-spinner fa-spin');
    $exportBtn.prop("disabled", true);

    try {
        const data = {
            header: bomColumns,
            data: bomTable.getData()
        };

        const response = await fetch(`${BACKEND_PATH}/bill_of_materials/export_excel.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const contentType = response.headers.get("Content-Type") || "";

        if (contentType.includes("application/json")) {
            const json = await response.json();

            if (!sessionValidityChecker(json, table)) return;

            if (json.status === "error") {
                errorFunction(json.message, $exportBtn);
                return;
            }

            if (json.status === "warning") {
                showToast(json.status, json.message, $exportBtn);
                return;
            }
        }

        if (response.ok && contentType.includes("spreadsheetml")) {
            const blob = await response.blob();
            const url = URL.createObjectURL(blob);

            const y = new Date().getFullYear().toString().slice(-2) + "Y";
            const d = new Date().toLocaleDateString("en-US", { month: "short", day: "2-digit" })
                .toUpperCase().replace(",", "");
            const filename = `(${y}) NPI_INTERNAL BOM_${d}.xlsx`;

            Object.assign(document.createElement("a"), {
                href: url,
                download: filename
            }).click();

            URL.revokeObjectURL(url);

            showToast("success", "Successfully exported to Excel.", $exportBtn);
        }
    } finally {
        $btnText.text('Export Data');
        $btnIcon.removeClass('fa-spinner fa-spin').addClass('fa-file-export');
        $exportBtn.prop('disabled', false);
    }
});
