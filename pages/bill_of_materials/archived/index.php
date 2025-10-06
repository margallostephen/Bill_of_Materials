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
                                            <div class="table-btn-container right">
                                                <div class="side-btn-container ">
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
                                                <div id="archivedBomTable" hidden></div>
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
</body>

<script type="text/javascript" src="<?php getAjaxPath('bill_of_materials_archived/populateTable.js') ?>"></script>
<script type="text/javascript" src="<?php getAjaxPath('bill_of_materials_archived/unarchiveRow.js') ?>"></script>
<script type="text/javascript" src="<?php getJSHelper('createTable.js') ?>"></script>
<script type="text/javascript" src="<?php getJSHelper('dataTreeInputFilter.js') ?>"></script>
<script type="text/javascript" src="<?php getJSHelper('addResetFilter.js') ?>"></script>
<script type="text/javascript" src="<?php getJSHelper('dateRangePicker.js') ?>"></script>
<script type="text/javascript" src="<?php getJSHelper('resetLoader.js') ?>"></script>
<script type="text/javascript" src="<?php getJSHelper('errorFunction.js') ?>"></script>

<script type="text/javascript">
    const archivedBomColumns = [{
            columns: [{
                    title: 'DIVISION',
                    field: 'DIVISION',
                    hozAlign: "middle",
                    vertAlign: "middle",
                    formatter: cell => {
                        const v = cell.getValue();

                        return (!v || v == 0) ? "" : v;
                    },
                    headerFilter: "input",
                    headerFilterFunc: deepMatchHeaderFilter,
                    headerFilterFuncParams: {
                        columnName: "DIVISION"
                    },
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
                    headerFilterFunc: deepMatchHeaderFilter,
                    headerFilterFuncParams: {
                        columnName: "MODEL"
                    },
                },
                {
                    title: 'MASTER CODE',
                    field: 'PART_CODE',
                    hozAlign: "middle",
                    vertAlign: "middle",
                    headerFilter: "input",
                    headerFilterFunc: deepMatchHeaderFilter,
                    headerFilterFuncParams: {
                        columnName: "PART_CODE"
                    },
                },
                {
                    title: 'ERP CODE',
                    field: 'ERP_CODE',
                    hozAlign: "middle",
                    vertAlign: "middle",
                    headerFilter: "input",
                    headerFilterFunc: deepMatchHeaderFilter,
                    headerFilterFuncParams: {
                        columnName: "ERP_CODE"
                    },
                },
                {
                    title: 'PART CODE',
                    field: 'CODE',
                    hozAlign: "middle",
                    vertAlign: "middle",
                    headerFilter: "input",
                    headerFilterFunc: deepMatchHeaderFilter,
                    headerFilterFuncParams: {
                        columnName: "CODE"
                    },
                },
                {
                    title: 'PART NAME',
                    field: 'DESCRIPTION',
                    hozAlign: "left",
                    vertAlign: "middle",
                    headerFilter: "input",
                    headerFilterFunc: deepMatchHeaderFilter,
                    headerFilterFuncParams: {
                        columnName: "DESCRIPTION"
                    },
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
                                const v = cell.getValue();

                                return (!v || v == 0) ? "" : v;
                            },
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
                            headerFilterFunc: deepMatchHeaderFilter,
                            headerFilterFuncParams: {
                                columnName: "BARCODE"
                            },
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
                },
            ]
        },
        {
            title: 'ARCHIVED BY',
            field: 'ARCHIVED_BY',
            hozAlign: "middle",
            vertAlign: "middle",
            headerFilter: "list",
            headerFilterPlaceholder: "Select",
            headerFilterParams: {
                valuesLookup: true,
            },
        },
        {
            title: 'ARCHIVED DATE',
            field: 'ARCHIVED_DATE',
            hozAlign: "middle",
            vertAlign: "middle",
            headerFilter: "input",
            headerFilterPlaceholder: "YYYY-MM-DD to YYYY-MM-DD",
            headerFilterFunc: (value, rowValue) => setDateRangeFilter(value, rowValue),
            minWidth: "238px"
        },
        {
            title: "ACTIONS",
            field: "ACTIONS",
            hozAlign: "center",
            headerSort: false,
            frozen: true,
            cssClass: "action-column",
            formatter: function(cell) {
                const {
                    RID: id,
                    PART_ID: p_id,
                    MATERIAL_ID: m_id,
                    RID_QT: qt_id,
                    RID_AT: at_id,
                    RID_AP: ap_id,
                    RID_MC_1: mc1_id,
                    RID_MC_2: mc2_id,
                    RID_MC_3: mc3_id,
                    RID_MC_4: mc4_id,
                    RID_MC_5: mc5_id,
                    RID_MC_1_AP_4M: mc1_ap_id,
                    RID_MC_2_AP_4M: mc2_ap_id,
                    DIVISION: division,
                    DELETE_STATUS: delete_status,
                } = cell.getData();

                const rowData = {
                    id,
                    p_id,
                    m_id,
                    qt_id,
                    at_id,
                    ap_id,
                    mc1_id,
                    mc2_id,
                    mc3_id,
                    mc4_id,
                    mc5_id,
                    mc1_ap_id,
                    mc2_ap_id,
                    division,
                    delete_status
                };

                return delete_status == 1 ? `
                <button class="btn btn-sm btn-danger unarchiveBtn"
                    data-row="${encodeURIComponent(JSON.stringify(rowData))}" >
                    <i class="fa fa-archive"></i> Unarchive
                </button>` : "";
            },
            minWidth: "40px"
        }
    ];

    let archivedBomTable = createTable(
        "archivedBomTable", {
            visible: false,
            headerFilter: "",
        },
        archivedBomColumns, {
            layout: "fitDataFill",
            pagination: "local",
            paginationSize: 50,
            paginationCounter: "rows",
            paginationSizeSelector: [50, 100, 250, 500, true],
            dataTree: true,
            dataTreeFilter: true,
            dataTreeStartExpanded: true,
            cellEditable: true,
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
    );

    $(document).ready(function() {
        populateTable(archivedBomTable, "bill_of_materials/get_archived");
        addResetFilter(archivedBomTable);
        addDateRangePicker(archivedBomTable, ["ARCHIVED_DATE"]);
    });
</script>