function populateTable(tabulatorObject, path, refreshed = 0, data = []) {
    const tableId = $(tabulatorObject.element).attr("id");
    const tableElement = $(`#${tableId}`);
    const loaderElement = $("#loader");
    const noDataElement = $(".no-data-message");

    noDataElement.hide();
    tableElement.hide();
    loaderElement.show();

    const $btn = $("#refreshTableBtn");
    const $icon = $btn.find(".fa-solid.fa-refresh");
    const $text = $("#btn-refresh-text");

    if (refreshed) {
        $btn.prop("disabled", true);
        $icon.addClass("fa-spin");
        $text.text(" Refreshing...");
    }

    $.ajax({
        url: `${BACKEND_PATH}/${path}.php`,
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function (response) {
            if (!sessionValidityChecker(response, tabulatorObject)) return;

            const bomList = response.bomList;

            const flattened = Object.entries(bomList).flatMap(([customer, parts]) =>
                Object.entries(parts)
                    .filter(([key]) => !["RID", "DIVISION", "CUSTOMER"].includes(key))
                    .map(([partKey, partValue]) => ({
                        customer,
                        partKey,
                        ...partValue,
                    }))
            );

            const tableData = Object.values(flattened).flat();

            if (tableData.length > 0) {
                tabulatorObject.setData(tableData).then(() => {
                    loaderElement.hide();
                    noDataElement.hide();
                    tableElement.show();
                    if (refreshed) {
                        $icon.removeClass("fa-spin");
                        $text.text("Refresh Table");
                        $btn.prop("disabled", false);
                    }
                });
            } else {
                loaderElement.hide();
                tableElement.hide();
                noDataElement.show();
                if (refreshed) {
                    $icon.removeClass("fa-spin");
                    $text.text("Refresh Table");
                    $btn.prop("disabled", false);
                }
            }
        },
        error: (error) => {
            resetLoader(tableId);
            errorFunction(error);
        }
    });
}