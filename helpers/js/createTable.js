function createTable(id, columns = [], options = {}, actionFormatter) {
    return new Tabulator(`#${id}`, {
        autoResize: true,
        columns: [
            {
                field: "RID",
                hozAlign: "center",
                vertAlign: "middle",
                visible: false,
            },
            ...columns,
            {
                title: "ACTIONS",
                field: "ACTIONS",
                hozAlign: "center",
                headerSort: false,
                frozen: true,
                cssClass: "action-column",
                formatter: actionFormatter
            }
        ],
        height: "712px",
        ...options
    });
}