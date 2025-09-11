function resetModal(modalId, formId, selectIds, localStorageKeys = null) {
    const $modal = $(`#${modalId}`);

    $modal.modal("hide");
    $(`#${formId}`)[0].reset();

    if (localStorageKeys) {
        for (const key of localStorageKeys) {
            localStorage.removeItem(key);
        }
    }

    $(selectIds).val("").trigger("change");
}
