async function setList(key, path, data = []) {
    if (path !== null) {
        try {
            const response = await $.ajax({
                url: `${BACKEND_PATH}/${path}.php`,
                type: "POST",
                dataType: "json",
            });

            if (!response.status) {
                showToast("warning", response.message);
                data = [];
            } else {
                data = response.data || [];
            }
        } catch (error) {
            showToast("error", "Something went wrong.");
            console.error("AJAX request failed:", error);
            data = [];
        }
    }

    localStorage.setItem(`${key}`, JSON.stringify(data));
}