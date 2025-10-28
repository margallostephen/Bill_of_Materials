function treeSelectFilterValues(c, childKey = '_children') {
    const field = c.getField();
    const data = c.getTable().getData();

    const flatten = (rows) =>
        rows.flatMap(r => [r, ...(r[childKey] ? flatten(r[childKey]) : [])]);

    return [...new Set(
        flatten(data)
            .map(row => row[field])
            .filter(Boolean)
    )].sort();
}
