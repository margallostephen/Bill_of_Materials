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
                        <div class="col-sm-12">
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
                                                <div class="side-btn-container">
                                                    <button type="button" class="btn btn-sm btn-primary"
                                                        id="importExcelBtn">
                                                        <i class="ace-icon fa fa-upload"></i>
                                                        <span>
                                                            Import Data
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
</body>

<script type="text/javascript" src="<?php getAjaxPath('bill_of_materials_list/importExcel.js') ?>"></script>
<script type="text/javascript" src="<?php getJSHelper('createTable.js') ?>"></script>
<script type="text/javascript" src="<?php getJSHelper('resetLoader.js') ?>"></script>
<script type="text/javascript" src="<?php getJSHelper('errorFunction.js') ?>"></script>

<script type="text/javascript">
    let bomTable;

    $(document).ready(function () {
        bomTable = createTable("bomTable");

        $("#importExcelBtn").click(function () {
            $("#modalImport").modal("show");
        });

        $(".closeModalBtn").click(function () {
            const $modal = $(`#modalImport`);

            $modal.modal("hide");
            $(`#importExcelForm`)[0].reset();
        });
    });
</script>

</html>