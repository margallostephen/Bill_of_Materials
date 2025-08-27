function mapData(data, key = "RID") {
    const dataMap = {};

    for (const element of data) {
        const rowKey = element[key];
        dataMap[rowKey] = element;
    }

    return dataMap;
}