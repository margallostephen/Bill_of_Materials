function deepMatchHeaderFilter(headerValue, rowValue, rowData, filterParams) {
    if (!headerValue || headerValue.toString().trim() === "") {
        return true;
    }

    const search = headerValue.toLowerCase();
    const columnName = filterParams.columnName;
    const matchType = filterParams.matchType || "includes";

    const stack = [rowData];

    while (stack.length) {
        const node = stack.pop();
        const value = node[columnName];

        if (value != null) {
            const cellValue = String(value).toLowerCase();

            if (
                (matchType === "includes" && cellValue.includes(search)) ||
                (matchType === "exact" && cellValue === search)
            ) {
                return true;
            }
        }

        if (node._children && node._children.length) {
            stack.push(...node._children);
        }
    }

    return false;
}
