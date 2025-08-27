function errorFunction(error, btn) {
    if (!localStorage.getItem('session expired')) {
        showToast("error", "Something went wrong.", btn);
    }
    console.error('AJAX request failed:', error);
}