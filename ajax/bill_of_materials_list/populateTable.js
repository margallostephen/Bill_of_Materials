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
        dataType: 'json',
        success: function (response) {
            console.log(response);

            if (!sessionValidityChecker(response, tabulatorObject)) return;

            const tableData = response.bomList;

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