function sessionValidityChecker(response, table) {
    if (response.status == "invalid") {
        table.replaceData([]);
        resetLoader($(table.element).attr("id"));
        localStorage.setItem('session expired', true);
        showToast("info", response.message);
        setTimeout(() => $("#logoutBtn").trigger("click"), 1000);
        return false;
    }
    return true;
}
