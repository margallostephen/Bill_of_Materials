function populateTable(tabulatorObject, path, data = []) {
    const tableId = $(tabulatorObject.element).attr("id");
    const tableElement = $(`#${tableId}`);
    const loaderElement = $("#loader");
    const noDataElement = $(".no-data-message");

    noDataElement.hide();
    tableElement.hide();
    loaderElement.show();

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
                });
            } else {
                loaderElement.hide();
                tableElement.hide();
                noDataElement.show();
            }
        },
        error: (error) => {
            resetLoader(tableId);
            errorFunction(error);
        }
    });
}