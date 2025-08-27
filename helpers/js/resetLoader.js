function resetLoader(tableId) {
    setTimeout(() => {
        $(`#${tableId}`).show();
        $("#loader").hide();
    }, 100);
}