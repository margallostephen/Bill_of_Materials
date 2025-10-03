function deepMatchHeaderFilter(headerValue, rowValue, rowData, filterParams) {
    if (!headerValue || headerValue.toString().trim() === "") {
        return true;
    }

    const search = headerValue.toLowerCase();
    const columnName = filterParams.columnName;

    const stack = [rowData];

    while (stack.length) {
        const node = stack.pop();

        const value = node[columnName];
        if (value != null && String(value).toLowerCase().includes(search)) {
            return true;
        }

        if (node._children && node._children.length) {
            stack.push(...node._children);
        }
    }

    return false;
}