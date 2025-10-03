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
    <?php require_once 'modals/editModal.php'; ?>
</body>

<script type="text/javascript" src="<?php getAjaxPath('bill_of_materials_list/populateTable.js') ?>"></script>
<script type="text/javascript" src="<?php getAjaxPath('bill_of_materials_list/importExcel.js') ?>"></script>
<script type="text/javascript" src="<?php getAjaxPath('bill_of_materials_list/exportExcel.js') ?>"></script>
<script type="text/javascript" src="<?php getAjaxPath('bill_of_materials_list/addItem.js') ?>"></script>
<script type="text/javascript" src="<?php getAjaxPath('bill_of_materials_list/editItem.js') ?>"></script>
<script type="text/javascript" src="<?php getAjaxPath('bill_of_materials_list/archiveRow.js') ?>"></script>
<script type="text/javascript" src="<?php getJSHelper('createTable.js') ?>"></script>
<script type="text/javascript" src="<?php getJSHelper('dataTreeInputFilter.js') ?>"></script>
<script type="text/javascript" src="<?php getJSHelper('addResetFilter.js') ?>"></script>
<script type="text/javascript" src="<?php getJSHelper('dateRangePicker.js') ?>"></script>
<script type="text/javascript" src="<?php getJSHelper('resetLoader.js') ?>"></script>
<script type="text/javascript" src="<?php getJSHelper('modalActions.js') ?>"></script>
<script type="text/javascript" src="<?php getJSHelper('resetModal.js') ?>"></script>
<script type="text/javascript" src="<?php getJSHelper('errorFunction.js') ?>"></script>
<script type="text/javascript">
    function setCellAttr(cell, table, column = "") {
        const el = cell.getElement();

        el.setAttribute("data-table", table);

        if (column != "") {
            el.setAttribute("data-column", column);
        }

        return cell.getValue();
    }

    function cellClick(e, cell) {
        const rowData = cell.getRow().getData();
        const value = cell.getValue();
        const title = cell.getColumn().getDefinition().title;
        const field = cell.getField();
        const el = $(cell.getElement());
        let tableAttr = el.attr("data-table");
        let columnAttr = el.attr("data-column");
        let colType = "";
        let rowId = "";
        let table = "";
        const type = rowData.DIVISION == "1";
        const $modalEdit = $(`#modalEdit`);

        if ((!type && ["CUSTOMER", "MODEL", "PART_CODE"].includes(field) ||
                (type && ["DIVISION", "CODE"].includes(field)))) {
            return;
        }

        columnAttr = columnAttr ?? field;

        if (columnAttr == "MATERIAL_CODE" && type) {
            columnAttr = "PART_CODE"
        }

        if (columnAttr == "DESCRIPTION" && type) {
            columnAttr = "PART_NAME"
        }

        if (columnAttr == "DESCRIPTION" && !type) {
            columnAttr = "MATERIAL_NAME"
        }

        if (type) {
            if (["CUSTOMER", "MODEL", "PART_CODE", "ERP_CODE", "PART_NAME"].includes(columnAttr)) {
                rowId = rowData.PART_ID;
                table = "part";
            } else {
                rowId = rowData.RID;
                table = "details";
            }
        }

        if (!type) {
            if (["MODEL", "ERP_CODE", "MATERIAL_CODE", "MATERIAL_NAME"].includes(columnAttr)) {
                rowId = rowData.MATERIAL_ID;
                table = "material";
            } else {
                rowId = rowData.RID;
                table = "details";
            }
        }

        if (tableAttr == "weight_ct") {
            table = "weight_ct";

            if (field.includes("QT")) {
                colType = 0;
                rowId = rowData.RID_QT;
            } else if (field.includes("AT")) {
                colType = 1;
                rowId = rowData.RID_AT;
            } else {
                colType = 2;
                rowId = rowData.RID_AP;
            }
        }

        if (tableAttr == "mc") {
            table = "mc";

            if (field.includes("1")) {
                colType = 0;
                rowId = rowData.RID_MC_1;
            } else if (field.includes("2")) {
                colType = 1;
                rowId = rowData.RID_MC_2;
            } else if (field.includes("3")) {
                colType = 2;
                rowId = rowData.RID_MC_3;
            } else if (field.includes("4")) {
                colType = 3;
                rowId = rowData.RID_MC_4;
            } else {
                colType = 4;
                rowId = rowData.RID_MC_5;
            }

            if (field.includes("1_AP_4M")) {
                colType = 5;
                rowId = rowData.RID_MC_1_AP_4M;
            }

            if (field.includes("2_AP_4M")) {
                colType = 6;
                rowId = rowData.RID_MC_2_AP_4M;
            }
        }

        $modalEdit.find("#table").val(table);
        $modalEdit.find("#rowType").val(type);
        $modalEdit.find("#columnType").val(colType);
        $modalEdit.find("#columnTitle").val(title);
        $modalEdit.find("#columnField").val(columnAttr);
        $modalEdit.find("#partSurrogate").val(rowData.PART_SURROGATE);
        $modalEdit.find("#materialSurrogate").val(rowData.MATERIAL_SURROGATE);
        $modalEdit.find("#rowId").val(rowId);
        $modalEdit.find("#previousValue").val(value);
        $(`#modalEdit`).modal("show");
    }

    const bomColumns = [{
            columns: [{
                    title: 'DIVISION',
                    field: 'DIVISION',
                    hozAlign: "middle",
                    vertAlign: "middle",
                    formatter: cell => {
                        const el = cell.getElement();
                        const v = cell.getValue();

                        el.setAttribute("data-table", "details");

                        return (!v || v == 0) ? "" : v;
                    },
                    headerFilter: "input",
                    headerFilterFunc: deepMatchHeaderFilter,
                    headerFilterFuncParams: {
                        columnName: "DIVISION"
                    },
                    cellDblClick: cellClick,
                    cssClass: "clickable-cell"

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
                    formatter: cell => setCellAttr(cell, "part"),
                    cellDblClick: cellClick,
                    cssClass: "clickable-cell"

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
                    formatter: cell => setCellAttr(cell, "part"),
                    cellDblClick: cellClick,
                    cssClass: "clickable-cell"

                },
                {
                    title: 'MASTER CODE',
                    field: 'PART_CODE',
                    hozAlign: "middle",
                    vertAlign: "middle",
                    headerFilter: "input",
                    headerFilterFunc: deepMatchHeaderFilter,
                    formatter: cell => setCellAttr(cell, "part"),
                    headerFilterFuncParams: {
                        columnName: "PART_CODE"
                    },
                    cellDblClick: cellClick,
                    cssClass: "clickable-cell"

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
                    formatter: cell => setCellAttr(cell, "part"),
                    cellDblClick: cellClick,
                    cssClass: "clickable-cell"

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
                    formatter: cell => setCellAttr(cell, "material", "MATERIAL_CODE"),
                    cellDblClick: cellClick,
                    cssClass: "clickable-cell"

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
                    cellDblClick: cellClick,
                    cssClass: "clickable-cell"

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
                    formatter: cell => setCellAttr(cell, "details"),
                    cellDblClick: cellClick,
                    cssClass: "clickable-cell"

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
                    formatter: cell => setCellAttr(cell, "details"),
                    cellDblClick: cellClick,
                    cssClass: "clickable-cell"

                },
                {
                    title: 'SUPPLIER',
                    field: 'SUPPLIER',
                    hozAlign: "middle",
                    vertAlign: "middle",
                    formatter: cell => setCellAttr(cell, "details"),
                    cellDblClick: cellClick,
                    cssClass: "clickable-cell"

                },
                {
                    title: 'USAGE',
                    columns: [{
                            title: "QTY",
                            field: "QTY",
                            hozAlign: "right",
                            vertAlign: "middle",
                            formatter: cell => setCellAttr(cell, "details"),
                            cellDblClick: cellClick,
                            cssClass: "clickable-cell"

                        },
                        {
                            title: "UNIT",
                            field: "UNIT",
                            hozAlign: "middle",
                            vertAlign: "middle",
                            formatter: cell => setCellAttr(cell, "details"),
                            cellDblClick: cellClick,
                            cssClass: "clickable-cell"

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
                    formatter: cell => setCellAttr(cell, "details"),
                    cellDblClick: cellClick,
                    cssClass: "clickable-cell"

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
                                const el = cell.getElement();
                                const v = cell.getValue();

                                el.setAttribute("data-table", "details");

                                return (!v || v == 0) ? "" : v;
                            },
                            cellDblClick: cellClick,
                            cssClass: "clickable-cell"

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
                            formatter: cell => setCellAttr(cell, "details"),
                            cellDblClick: cellClick,
                            cssClass: "clickable-cell"

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
                            formatter: cell => setCellAttr(cell, "details"),
                            cellDblClick: cellClick,
                            cssClass: "clickable-cell"

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
                            formatter: cell => setCellAttr(cell, "details"),
                            cellDblClick: cellClick,
                            cssClass: "clickable-cell"

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
                        formatter: cell => setCellAttr(cell, "weight_ct", "PROD_G"),
                        cellDblClick: cellClick,
                        cssClass: "clickable-cell"


                    }, {
                        title: "S&R(G)",
                        field: "S_R_QT",
                        hozAlign: "middle",
                        vertAlign: "middle",
                        formatter: cell => setCellAttr(cell, "weight_ct", "S_R_G"),
                        cellDblClick: cellClick,
                        cssClass: "clickable-cell"

                    }, {
                        title: "TOTAL(G)",
                        field: "TOTAL_QT",
                        hozAlign: "middle",
                        vertAlign: "middle",
                        formatter: cell => setCellAttr(cell, "weight_ct", "TOTAL"),
                        cellDblClick: cellClick,
                        cssClass: "clickable-cell"

                    }, {
                        title: "G/PCS",
                        field: "G_PCS_QT",
                        hozAlign: "middle",
                        vertAlign: "middle",
                        formatter: cell => setCellAttr(cell, "weight_ct", "G_PCS"),
                        cellDblClick: cellClick,
                        cssClass: "clickable-cell"

                    }, {
                        title: "C/TIME",
                        field: "C_TIME_QT",
                        hozAlign: "middle",
                        vertAlign: "middle",
                        formatter: cell => setCellAttr(cell, "weight_ct", "C_TIME"),
                        cellDblClick: cellClick,
                        cssClass: "clickable-cell"

                    }]
                },
                {
                    title: 'WEIGTH+CT / ACTUAL',
                    columns: [{
                        title: "PROD(G)",
                        field: "PROD_AT",
                        hozAlign: "middle",
                        vertAlign: "middle",
                        formatter: cell => setCellAttr(cell, "weight_ct", "PROD_G"),
                        cellDblClick: cellClick,
                        cssClass: "clickable-cell"

                    }, {
                        title: "S&R(G)",
                        field: "S_R_AT",
                        hozAlign: "middle",
                        vertAlign: "middle",
                        formatter: cell => setCellAttr(cell, "weight_ct", "S_R_G"),
                        cellDblClick: cellClick,
                        cssClass: "clickable-cell"

                    }, {
                        title: "TOTAL(G)",
                        field: "TOTAL_AT",
                        hozAlign: "middle",
                        vertAlign: "middle",
                        formatter: cell => setCellAttr(cell, "weight_ct", "TOTAL"),
                        cellDblClick: cellClick,
                        cssClass: "clickable-cell"

                    }, {
                        title: "G/PCS",
                        field: "G_PCS_AT",
                        hozAlign: "middle",
                        vertAlign: "middle",
                        formatter: cell => setCellAttr(cell, "weight_ct", "G_PCS"),
                        cellDblClick: cellClick,
                        cssClass: "clickable-cell"

                    }, {
                        title: "C/TIME",
                        field: "C_TIME_AT",
                        hozAlign: "middle",
                        vertAlign: "middle",
                        formatter: cell => setCellAttr(cell, "weight_ct", "C_TIME"),
                        cellDblClick: cellClick,
                        cssClass: "clickable-cell"

                    }]
                },
                {
                    title: 'WEIGTH+CT / APPROVAL',
                    columns: [{
                        title: "PROD(G)",
                        field: "PROD_AP",
                        hozAlign: "middle",
                        vertAlign: "middle",
                        formatter: cell => setCellAttr(cell, "weight_ct", "PROD_G"),
                        cellDblClick: cellClick,
                        cssClass: "clickable-cell"

                    }, {
                        title: "S&R(G)",
                        field: "S_R_AP",
                        hozAlign: "middle",
                        vertAlign: "middle",
                        formatter: cell => setCellAttr(cell, "weight_ct", "S_R_G"),
                        cellDblClick: cellClick,
                        cssClass: "clickable-cell"

                    }, {
                        title: "TOTAL(G)",
                        field: "TOTAL_AP",
                        hozAlign: "middle",
                        vertAlign: "middle",
                        formatter: cell => setCellAttr(cell, "weight_ct", "TOTAL"),
                        cellDblClick: cellClick,
                        cssClass: "clickable-cell"

                    }, {
                        title: "G/PCS",
                        field: "G_PCS_AP",
                        hozAlign: "middle",
                        vertAlign: "middle",
                        formatter: cell => setCellAttr(cell, "weight_ct", "G_PCS"),
                        cellDblClick: cellClick,
                        cssClass: "clickable-cell"

                    }, {
                        title: "C/TIME",
                        field: "C_TIME_AP",
                        hozAlign: "middle",
                        vertAlign: "middle",
                        formatter: cell => setCellAttr(cell, "weight_ct", "C_TIME"),
                        cellDblClick: cellClick,
                        cssClass: "clickable-cell"

                    }]
                },
                {
                    title: '1ST MC',
                    columns: [{
                        title: "MC#",
                        field: "MC_1",
                        hozAlign: "middle",
                        vertAlign: "middle",
                        cellDblClick: cellClick,
                        cssClass: "clickable-cell",
                        formatter: cell => setCellAttr(cell, "mc", "MC")
                    }, {
                        title: "TON",
                        field: "TON_1",
                        hozAlign: "middle",
                        vertAlign: "middle",
                        cellDblClick: cellClick,
                        cssClass: "clickable-cell",
                        formatter: cell => setCellAttr(cell, "mc", "TON")
                    }]
                },
                {
                    title: '2ND MC',
                    columns: [{
                        title: "MC#",
                        field: "MC_2",
                        hozAlign: "middle",
                        vertAlign: "middle",
                        cellDblClick: cellClick,
                        cssClass: "clickable-cell",
                        formatter: cell => setCellAttr(cell, "mc", "MC")
                    }, {
                        title: "TON",
                        field: "TON_2",
                        hozAlign: "middle",
                        vertAlign: "middle",
                        cellDblClick: cellClick,
                        cssClass: "clickable-cell",
                        formatter: cell => setCellAttr(cell, "mc", "TON")
                    }]
                },
                {
                    title: '3RD MC',
                    columns: [{
                        title: "MC#",
                        field: "MC_3",
                        hozAlign: "middle",
                        vertAlign: "middle",
                        cellDblClick: cellClick,
                        cssClass: "clickable-cell",
                        formatter: cell => setCellAttr(cell, "mc", "MC")
                    }, {
                        title: "TON",
                        field: "TON_3",
                        hozAlign: "middle",
                        vertAlign: "middle",
                        cellDblClick: cellClick,
                        cssClass: "clickable-cell",
                        formatter: cell => setCellAttr(cell, "mc", "TON")
                    }]
                },
                {
                    title: '4TH MC',
                    columns: [{
                        title: "MC#",
                        field: "MC_4",
                        hozAlign: "middle",
                        vertAlign: "middle",
                        cellDblClick: cellClick,
                        cssClass: "clickable-cell",
                        formatter: cell => setCellAttr(cell, "mc", "MC")
                    }, {
                        title: "TON",
                        field: "TON_4",
                        hozAlign: "middle",
                        vertAlign: "middle",
                        cellDblClick: cellClick,
                        cssClass: "clickable-cell",
                        formatter: cell => setCellAttr(cell, "mc", "TON")
                    }]
                },
                {
                    title: '5TH MC',
                    columns: [{
                        title: "MC#",
                        field: "MC_5",
                        hozAlign: "middle",
                        vertAlign: "middle",
                        cellDblClick: cellClick,
                        cssClass: "clickable-cell",
                        formatter: cell => setCellAttr(cell, "mc", "MC")
                    }, {
                        title: "TON",
                        field: "TON_5",
                        hozAlign: "middle",
                        vertAlign: "middle",
                        cellDblClick: cellClick,
                        cssClass: "clickable-cell",
                        formatter: cell => setCellAttr(cell, "mc", "TON")
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
                        cellDblClick: cellClick,
                        cssClass: "clickable-cell approve-4m-col",
                        formatter: cell => setCellAttr(cell, "mc", "MC")
                    }, {
                        title: "TON",
                        field: "TON_1_AP_4M",
                        hozAlign: "middle",
                        vertAlign: "middle",
                        cellDblClick: cellClick,
                        cssClass: "clickable-cell approve-4m-col",
                        formatter: cell => setCellAttr(cell, "mc", "TON")
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
                        cellDblClick: cellClick,
                        cssClass: "clickable-cell approve-4m-col",
                        formatter: cell => setCellAttr(cell, "mc", "MC")
                    }, {
                        title: "TON",
                        field: "TON_2_AP_4M",
                        hozAlign: "middle",
                        vertAlign: "middle",
                        cellDblClick: cellClick,
                        cssClass: "clickable-cell approve-4m-col",
                        formatter: cell => setCellAttr(cell, "mc", "TON")
                    }]
                },
            ]
        },
        {
            title: 'REGISTERED BY',
            field: 'REGISTERED_BY',
            hozAlign: "middle",
            vertAlign: "middle",
            headerFilter: "list",
            headerFilterPlaceholder: "Select",
            headerFilterParams: {
                valuesLookup: true,
            },
            cssClass: "not-clickable",
        },
        {
            title: 'REGISTERED DATE',
            field: 'REGISTERED_DATE',
            hozAlign: "middle",
            vertAlign: "middle",
            headerFilter: "input",
            headerFilterPlaceholder: "YYYY-MM-DD to YYYY-MM-DD",
            headerFilterFunc: (value, rowValue) => setDateRangeFilter(value, rowValue),
            cssClass: "not-clickable",
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
                    DIVISION: division
                } = cell.getData();

                const isPart = division == 1;

                // $ {
                //     isPart ? '<button type="button" class="btn btn-sm btn-white" id="addMaterialBtn"><i class="ace-icon fa fa-square-plus"></i><span>Add New Material</span></button>' : ''
                // }

                return `
                <button class="btn btn-sm btn-danger archiveBtn" data-type="${isPart}" data-type-id="${isPart ? p_id : m_id}">
                    <i class="fa fa-archive"></i> Acrhive
                </button>`;
            },
            minWidth: "40px"
        }
    ];

    let bomTable = createTable(
        "bomTable", {
            visible: false,
            headerFilter: "",
        },
        bomColumns, {
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
        populateTable(bomTable, "bill_of_materials/get_data");
        addResetFilter(bomTable);
        addDateRangePicker(bomTable, ["REGISTERED_DATE"]);
        modalOpen("addBtn", "modalAdd");
        modalOpen("importExcelBtn", "modalImport");
        modalClose("closeModalBtn");
    });
</script>

</html>