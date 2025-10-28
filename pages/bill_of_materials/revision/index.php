<!DOCTYPE html>
<html lang="en">
<?php require_once PARTIALS_PATH . 'header.php'; ?>

<body class="no-skin">
    <?php require_once PARTIALS_PATH . 'navbar.php'; ?>
    <div class="main-container ace-save-state" id="main-container">
        <?php require_once PARTIALS_PATH . 'sidebar.php'; ?>
        <div class="main-content">
            <div class="main-content-inner">
                <?php require_once PARTIALS_PATH . 'breadcrumbs.php'; ?>
                <div class="page-content">
                    <div class="row">
                        <div class="content-container">
                            <div class="widget-box widget-color-orange">
                                <div class="widget-header widget-header-small">
                                    <h6 class="widget-title" style=" display: inline-flex;">
                                        <b id="importLabel" style="color:black;"> </b>
                                    </h6>
                                </div>
                                <div class="widget-body">
                                    <div class="widget-main">
                                        <div class="p-4">
                                            <div class="table-btn-container right">
                                                <div class="side-btn-container">
                                                    <button class="btn btn-sm btn-warning" id="clearAllFilterBtn"
                                                        disabled>
                                                        <i class="fa-solid fa-arrow-rotate-right"></i>
                                                        <span id="btn-clear-text">
                                                            Reset Filter
                                                        </span>
                                                    </button>
                                                </div>
                                            </div>
                                            <div id="loader" class="loader-container">
                                                <div class="spinner"></div>
                                                <strong id="loadingText">Loading</strong>
                                            </div>
                                            <div>
                                                <div id="revisionTable" hidden></div>
                                                <div class="no-data-message" hidden>
                                                    <strong>No Data Available</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php require_once PARTIALS_PATH . 'footer.php'; ?>
    </div>
</body>

<script type="text/javascript" src="<?php getAjaxPath('bill_of_materials_revision/populateTable.js') ?>"></script>
<script type="text/javascript" src="<?php getJSHelper('createTable.js') ?>"></script>
<script type="text/javascript" src="<?php getJSHelper('addResetFilter.js') ?>"></script>
<script type="text/javascript" src="<?php getJSHelper('dateRangePicker.js') ?>"></script>
<script type="text/javascript" src="<?php getJSHelper('resetLoader.js') ?>"></script>
<script type="text/javascript" src="<?php getJSHelper('errorFunction.js') ?>"></script>
<script type="text/javascript">
    const revisionColumns = [{
            title: 'COLUMN',
            field: 'DATA_TYPE',
            hozAlign: "middle",
            vertAlign: "middle",
            headerFilter: "list",
            headerFilterPlaceholder: "Select",
            headerFilterParams: {
                valuesLookup: true,
            },
        },
        {
            title: 'PREVIOUS VALUE',
            field: 'PREV_VAL',
            hozAlign: "middle",
            vertAlign: "middle",
            headerFilter: "list",
            headerFilterPlaceholder: "Select",
            headerFilterParams: {
                valuesLookup: true,
            },
        },
        {
            title: 'NEW VALUE',
            field: 'NEW_VAL',
            hozAlign: "middle",
            vertAlign: "middle",
            headerFilter: "input",
        },
        {
            title: 'REMARKS',
            field: 'REMARKS',
            hozAlign: "middle",
            vertAlign: "middle",
        },
        {
            title: 'REVISED BY',
            field: 'REVISED_BY',
            hozAlign: "middle",
            vertAlign: "middle",
            headerFilter: "list",
            headerFilterPlaceholder: "Select",
            headerFilterParams: {
                valuesLookup: true,
            },
        },
        {
            title: 'REVISED DATE',
            field: 'REVISED_AT',
            hozAlign: "middle",
            vertAlign: "middle",
            headerFilter: "input",
            headerFilterPlaceholder: "YYYY-MM-DD to YYYY-MM-DD",
            headerFilterFunc: (value, rowValue) => setDateRangeFilter(value, rowValue),
        }
    ];

    let revisionTable = createTable(
        "revisionTable", {
            title: 'REVISION NO.',
            visible: true,
            headerFilter: "input",
        },
        revisionColumns, {
            layout: "fitColumns",
            pagination: "local",
            paginationSize: 50,
            paginationCounter: "rows",
            paginationSizeSelector: [50, 100, 250, 500, true]
        }
    );

    $(document).ready(function() {
        populateTable(revisionTable, "bill_of_materials/get_revision");
        addResetFilter(revisionTable);
        addDateRangePicker(revisionTable, ["REVISED_AT"]);
    });
</script>

</html>