function createTable(id, ridOptions = [], columns = [], options = {}) {
    return new Tabulator(`#${id}`, {
        autoResize: true,
        columns: [
            {
                field: "RID",
                hozAlign: "center",
                vertAlign: "middle",
                ...ridOptions
            },
            ...columns
        ],
        height: "100%",
        ...options
    });
}