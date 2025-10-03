function attachDateRangePickerToField(table, fieldName) {
    const $input = $(table.getColumn(fieldName)?.getElement()).find('input[type="search"]');
    if (!$input.length || $input.data('daterangepicker')) return;

    $input.daterangepicker({
        autoUpdateInput: false,
        showDropdowns: true,
        maxDate: moment(),
        locale: { format: 'YYYY-MM-DD' }
    });

    $input.on('apply.daterangepicker', (ev, picker) => {
        const value = `${picker.startDate.format('YYYY-MM-DD')} to ${picker.endDate.format('YYYY-MM-DD')}`;
        $input.val(value);
        table.setHeaderFilterValue(fieldName, value);
    });
}

function addDateRangePicker(table, dateFields) {
    const init = () => dateFields.forEach(field => attachDateRangePickerToField(table, field));
    table.on("tableBuilt", init);
    table.on("dataFiltered", init);
}

function setDateRangeFilter(value, rowValue) {
    if (!value) return true;

    const [start, end] = value.split(" to ").map(d => d.trim());

    const rowDateStr = rowValue ? rowValue.split(" ")[0] : null;
    const date = rowDateStr ? new Date(rowDateStr) : null;

    const from = start ? new Date(start) : null;
    const to = end ? new Date(end) : null;

    if (!date || isNaN(date)) return false;
    if (from && date < from) return false;
    if (to && date > to) return false;
    return true;
}
