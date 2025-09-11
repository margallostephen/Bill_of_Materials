function sessionValidityChecker(response, tabulatorObject) {
    if (response.status == "invalid") {
        tabulatorObject.replaceData([]);
        resetLoader($(tabulatorObject.element).attr("id"));
        localStorage.setItem('session expired', true);
        showToast("info", response.message);
        setTimeout(() => $("#logoutBtn").trigger("click"), 1000);
        return false;
    }
    return true;
}
