<!DOCTYPE html>
<html lang="en">
<?php require_once PARTIALS_PATH . '/header.php'; ?>

<body class="no-skin">
    <?php require_once PARTIALS_PATH . '/navbar.php'; ?>
    <div class="main-container ace-save-state" id="main-container">
        <?php require_once PARTIALS_PATH . '/sidebar.php'; ?>
        <div class="main-content">
            <div class="main-content-inner">
                <?php require_once PARTIALS_PATH . '/breadcrumbs.php'; ?>
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
                                            <div class="table-btn-container">
                                                <button type="button" class="btn btn-sm btn-success"
                                                    id="addBtn">
                                                    <i class="ace-icon fa fa-plus"></i>
                                                    <span>
                                                        Add New Item
                                                    </span>
                                                </button>
                                                <div class="side-btn-container">
                                                    <button type="button" class="btn btn-sm btn-primary"
                                                        id="importExcelBtn">
                                                        <i class="ace-icon fa fa-upload"></i>
                                                        <span>
                                                            Import Data
                                                        </span>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-success"
                                                        id="exportExcelBtn">
                                                        <i class="ace-icon fa fa-download"></i>
                                                        <span>
                                                            Export Data
                                                        </span>
                                                    </button>
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
                                                <div id="bomTable" hidden></div>
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
        <?php require_once PARTIALS_PATH . '/footer.php'; ?>
    </div>

    <?php require_once 'modals/importModal.php'; ?>
    <?php require_once 'modals/addModal.php'; ?>
</body>

<script type="text/javascript" src="<?php getAjaxPath('bill_of_materials_list/populateTable.js') ?>"></script>
<script type="text/javascript" src="<?php getAjaxPath('bill_of_materials_list/importExcel.js') ?>"></script>
<script type="text/javascript" src="<?php getAjaxPath('bill_of_materials_list/exportExcel.js') ?>"></script>
<script type="text/javascript" src="<?php getJSHelper('createTable.js') ?>"></script>
<script type="text/javascript" src="<?php getJSHelper('addResetFilter.js') ?>"></script>
<script type="text/javascript" src="<?php getJSHelper('resetLoader.js') ?>"></script>
<script type="text/javascript" src="<?php getJSHelper('modalActions.js') ?>"></script>
<script type="text/javascript" src="<?php getJSHelper('resetModal.js') ?>"></script>
<script type="text/javascript" src="<?php getJSHelper('errorFunction.js') ?>"></script>

<script type="text/javascript">
    const bomColumns = [{
        columns: [{
                title: 'DIVISION',
                field: 'DIVISION',
                hozAlign: "middle",
                vertAlign: "middle",
                formatter: cell => {
                    let v = cell.getValue();
                    return (!v || v == 0) ? "" : v;
                }
            },
            {
                title: 'CUSTOMER',
                field: 'CUSTOMER',
                hozAlign: "middle",
                vertAlign: "middle",
                headerFilter: "list",
                headerFilterPlaceholder: "Select",
                headerFilterParams: {
                    valuesLookup: true,
                },
            },
            {
                title: 'MODEL',
                field: 'MODEL',
                hozAlign: "middle",
                vertAlign: "middle",
                headerFilter: "input",

            },
            {
                title: 'MASTER CODE',
                field: 'PART_CODE',
                hozAlign: "middle",
                vertAlign: "middle",
                headerFilter: "input",
            },
            {
                title: 'ERP CODE',
                field: 'ERP_CODE',
                hozAlign: "middle",
                vertAlign: "middle",
                headerFilter: "input",
            },
            {
                title: 'PART CODE',
                field: 'CODE',
                hozAlign: "middle",
                vertAlign: "middle",
                headerFilter: "input",
            },
            {
                title: 'PART NAME',
                field: 'DESCRIPTION',
                hozAlign: "left",
                vertAlign: "middle",
                headerFilter: "input",
            },
            {
                title: 'PROCESS',
                field: 'PROCESS',
                hozAlign: "middle",
                vertAlign: "middle",
                headerFilter: "list",
                headerFilterPlaceholder: "Select",
                headerFilterParams: {
                    valuesLookup: true,
                },
            },
            {
                title: 'CLASS',
                field: 'CLASS',
                hozAlign: "middle",
                vertAlign: "middle",
                headerFilter: "list",
                headerFilterPlaceholder: "Select",
                headerFilterParams: {
                    valuesLookup: true,
                },
            },
            {
                title: 'SUPPLIER',
                field: 'SUPPLIER',
                hozAlign: "middle",
                vertAlign: "middle",
            },
            {
                title: 'USAGE',
                columns: [{
                        title: "QTY",
                        field: "QTY",
                        hozAlign: "right",
                        vertAlign: "middle",
                    },
                    {
                        title: "UNIT",
                        field: "UNIT",
                        hozAlign: "middle",
                        vertAlign: "middle",
                    }
                ]
            },
            {
                title: 'STATUS',
                field: 'STATUS',
                hozAlign: "middle",
                vertAlign: "middle",
                headerFilter: "list",
                headerFilterPlaceholder: "Select",
                headerFilterParams: {
                    valuesLookup: true,
                },
            },
            {
                title: 'MOLD',
                columns: [{
                        title: "CAVITY",
                        field: "CAV_NUM",
                        hozAlign: "middle",
                        vertAlign: "middle",
                        headerFilter: "list",
                        headerFilterPlaceholder: "Select",
                        headerFilterParams: {
                            valuesLookup: true,
                        },
                        formatter: cell => {
                            let v = cell.getValue();
                            return (!v || v == 0) ? "" : v;
                        }
                    },
                    {
                        title: "TOOL",
                        field: "TOOL_NUM",
                        hozAlign: "middle",
                        vertAlign: "middle",
                        headerFilter: "list",
                        headerFilterPlaceholder: "Select",
                        headerFilterParams: {
                            valuesLookup: true,
                        },
                    }
                ]
            },
            {
                title: 'LABEL',
                columns: [{
                        title: "BARCODE",
                        field: "BARCODE",
                        hozAlign: "middle",
                        vertAlign: "middle",
                        headerFilter: "input",
                    },
                    {
                        title: "CUSTOMER",
                        field: "LABEL_CUSTOMER",
                        hozAlign: "middle",
                        vertAlign: "middle",
                        headerFilter: "list",
                        headerFilterPlaceholder: "Select",
                        headerFilterParams: {
                            valuesLookup: true,
                        },
                    }
                ]
            },
            {
                title: 'WEIGTH+CT / QUOTATION',
                columns: [{
                    title: "PROD(G)",
                    field: "PROD_QT",
                    hozAlign: "middle",
                    vertAlign: "middle",
                }, {
                    title: "S&R(G)",
                    field: "S_R_QT",
                    hozAlign: "middle",
                    vertAlign: "middle",
                }, {
                    title: "TOTAL(G)",
                    field: "TOTAL_QT",
                    hozAlign: "middle",
                    vertAlign: "middle",
                }, {
                    title: "G/PCS",
                    field: "G_PCS_QT",
                    hozAlign: "middle",
                    vertAlign: "middle",
                }, {
                    title: "C/TIME",
                    field: "C_TIME_QT",
                    hozAlign: "middle",
                    vertAlign: "middle",
                }]
            },
            {
                title: 'WEIGTH+CT / ACTUAL',
                columns: [{
                    title: "PROD(G)",
                    field: "PROD_AT",
                    hozAlign: "middle",
                    vertAlign: "middle",
                }, {
                    title: "S&R(G)",
                    field: "S_R_AT",
                    hozAlign: "middle",
                    vertAlign: "middle",
                }, {
                    title: "TOTAL(G)",
                    field: "TOTAL_AT",
                    hozAlign: "middle",
                    vertAlign: "middle",
                }, {
                    title: "G/PCS",
                    field: "G_PCS_AT",
                    hozAlign: "middle",
                    vertAlign: "middle",
                }, {
                    title: "C/TIME",
                    field: "C_TIME_AT",
                    hozAlign: "middle",
                    vertAlign: "middle",
                }]
            },
            {
                title: 'WEIGTH+CT / APPROVAL',
                columns: [{
                    title: "PROD(G)",
                    field: "PROD_AP",
                    hozAlign: "middle",
                    vertAlign: "middle",
                }, {
                    title: "S&R(G)",
                    field: "S_R_AP",
                    hozAlign: "middle",
                    vertAlign: "middle",
                }, {
                    title: "TOTAL(G)",
                    field: "TOTAL_AP",
                    hozAlign: "middle",
                    vertAlign: "middle",
                }, {
                    title: "G/PCS",
                    field: "G_PCS_AP",
                    hozAlign: "middle",
                    vertAlign: "middle",
                }, {
                    title: "C/TIME",
                    field: "C_TIME_AP",
                    hozAlign: "middle",
                    vertAlign: "middle",
                }]
            },
            {
                title: '1ST MC',
                columns: [{
                    title: "MC#",
                    field: "MC_1",
                    hozAlign: "middle",
                    vertAlign: "middle",
                }, {
                    title: "TON",
                    field: "TON_1",
                    hozAlign: "middle",
                    vertAlign: "middle",
                }]
            },
            {
                title: '2ND MC',
                columns: [{
                    title: "MC#",
                    field: "MC_2",
                    hozAlign: "middle",
                    vertAlign: "middle",
                }, {
                    title: "TON",
                    field: "TON_2",
                    hozAlign: "middle",
                    vertAlign: "middle",
                }]
            },
            {
                title: '3RD MC',
                columns: [{
                    title: "MC#",
                    field: "MC_3",
                    hozAlign: "middle",
                    vertAlign: "middle",
                }, {
                    title: "TON",
                    field: "TON_3",
                    hozAlign: "middle",
                    vertAlign: "middle",
                }]
            },
            {
                title: '4TH MC',
                columns: [{
                    title: "MC#",
                    field: "MC_4",
                    hozAlign: "middle",
                    vertAlign: "middle",
                }, {
                    title: "TON",
                    field: "TON_4",
                    hozAlign: "middle",
                    vertAlign: "middle",
                }]
            },
            {
                title: '5TH MC',
                columns: [{
                    title: "MC#",
                    field: "MC_5",
                    hozAlign: "middle",
                    vertAlign: "middle",
                }, {
                    title: "TON",
                    field: "TON_5",
                    hozAlign: "middle",
                    vertAlign: "middle",
                }]
            },
        ],
    }, {
        title: 'INTERNAL APPROVE 4M',
        cssClass: "approve-4m-col",
        columns: [{
                title: "1ST MC",
                cssClass: "approve-4m-col",
                columns: [{
                    title: "MC#",
                    field: "MC_1_AP_4M",
                    hozAlign: "middle",
                    vertAlign: "middle",
                    cssClass: "approve-4m-col",
                }, {
                    title: "TON",
                    field: "TON_1_AP_4M",
                    hozAlign: "middle",
                    vertAlign: "middle",
                    cssClass: "approve-4m-col",
                }]
            },
            {
                title: "2ND MC",
                cssClass: "approve-4m-col",
                columns: [{
                    title: "MC#",
                    field: "MC_2_AP_4M",
                    hozAlign: "middle",
                    vertAlign: "middle",
                    cssClass: "approve-4m-col",
                }, {
                    title: "TON",
                    field: "TON_2_AP_4M",
                    hozAlign: "middle",
                    vertAlign: "middle",
                    cssClass: "approve-4m-col",
                }]
            }
        ]
    }];

    let bomTable = createTable(
        "bomTable",
        bomColumns, {
            layout: "fitDataFill",
            pagination: "local",
            paginationSize: 100,
            paginationCounter: "rows",
            paginationSizeSelector: [100, 250, 500, 1000, true],
            dataTree: true,
            dataTreeFilter: false,
            dataTreeStartExpanded: true,
            dataTreeChildField: "children",
            rowFormatter: function(row) {
                let data = row.getData();

                if (data.DIVISION == 1) {
                    row.getCells().forEach((cell, index) => {
                        if (index >= 0 && index <= 17) {
                            $(cell.getElement()).css("background-color", "#92D050");
                        }
                    });
                }
            },
        },
        function(cell) {
            const {
                RID: id,
                DIVISION: division
            } = cell.getData();

            if (division == 1) {
                $(cell.getElement()).css("background-color", "#FFF");
            }

            return division == 1 ? `
                <button class="btn btn-sm btn-warning editModalBtn" data-id="${id}">
                    <i class="fa fa-edit"></i> Edit
                </button>
                <button class="btn btn-sm btn-danger deleteBtn" data-id="${id}">
                    <i class="fa fa-trash"></i> Delete
                </button>
            ` : "";
        }
    );

    $(document).ready(function() {
        populateTable(bomTable, "bill_of_materials/get_data");
        addResetFilter(bomTable);
        modalOpen("addBtn", "modalAdd");
        modalOpen("importExcelBtn", "modalImport");
        modalClose("closeModalBtn");
    });
</script>

</html>