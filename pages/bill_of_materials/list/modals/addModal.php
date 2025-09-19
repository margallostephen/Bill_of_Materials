<div id="modalAdd" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" id="modalTop">
                <h4 class="modal-title">Add New Item</h4>
            </div>
            <div class="modal-scrollspy">
                <div class="scrollspy-group">
                    <a class="scrollspy-item list-group-item" href="#modalTop">ADD ITEM</a>
                    <a class="scrollspy-item list-group-item active" href="#tabContent">MAIN INFORMATION</a>
                    <a class="scrollspy-item list-group-item" href="#weightCtInfo0">WEIGHT / CT INFORMATION</a>
                    <a class="scrollspy-item list-group-item" href="#mcInfo0">MACHINE INFORMATION</a>
                    <a class="scrollspy-item list-group-item" href="#materialFormsCon0">MATERIALS LIST</a>
                    <a class="scrollspy-item list-group-item" href="#modalActions">MODAL ACTIONS</a>
                </div>
            </div>
            <form id="addForm">
                <div class="modal-body">
                    <div class="modal-header-action-tab">
                        <ul class="nav nav-tabs" role="tablist">
                            <li>
                                <a href="#" id="addNewItemTab"><span class="ace-icon fa fa-plus"></span></a>
                            </li>
                        </ul>
                        <a href="javascript:void(0)" class="btn btn-sm btn-danger" id="removeItemBtn">
                            <i class="fa fa-xmark"></i>
                        </a>
                    </div>
                    <div class="tab-content item-tab-content" id="tabContent">
                        <div class="modal-no-data-label" hidden>
                            <strong>No Item Form Added</strong>
                        </div>
                    </div>
                </div>

                <div class="modal-footer" id="modalActions">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <span id="execute_spinner" hidden>
                            <i class="ace-icon fa fa-spinner fa-spin white"></i>
                        </span>
                        <span id="execute_btn_text">Submit</span>
                    </button>
                    <button type="button" class="btn btn-sm btn-default closeModalBtn" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="tab-pane" id="tab-pane-item-template" data-material-count="-1" hidden>
    <div class="row">
        <div class="col-sm-3 item-field-con"><label>Customer</label><input type="text" name="items[itemIndex][customer]" class="form-control"></div>
        <div class="col-sm-3 item-field-con"><label>Model</label><input type="text" name="items[itemIndex][model]" class="form-control"></div>
        <div class="col-sm-3 item-field-con"><label>Master Code</label><input type="text" name="items[itemIndex][master_code]" class="form-control"></div>
        <div class="col-sm-3 item-field-con"><label>ERP Code</label><input type="text" name="items[itemIndex][erp_code]" class="form-control"></div>
    </div>
    <div class="row">
        <div class="col-sm-12 item-field-con"><label>Item Name</label><input type="text" name="items[itemIndex][part_name]" class="form-control"></div>
    </div>
    <div class="row">
        <div class="col-sm-4 item-field-con"><label>Process</label><input type="text" name="items[itemIndex][process]" class="form-control"></div>
        <div class="col-sm-4 item-field-con"><label>Class</label><input type="text" name="items[itemIndex][class]" class="form-control"></div>
        <div class="col-sm-4 item-field-con"><label>Supplier</label><input type="text" name="items[itemIndex][supplier]" class="form-control"></div>
    </div>
    <div class="row">
        <div class="col-sm-3 item-field-con"><label>Quantity</label><input type="number" name="items[itemIndex][qty]" class="form-control"></div>
        <div class="col-sm-3 item-field-con"><label>Unit</label><input type="text" name="items[itemIndex][unit]" class="form-control"></div>
        <div class="col-sm-3 item-field-con"><label>Cavity</label><input type="text" name="items[itemIndex][cavity]" class="form-control"></div>
        <div class="col-sm-3 item-field-con"><label>Tool</label><input type="text" name="items[itemIndex][tool]" class="form-control"></div>
    </div>
    <div class="row">
        <div class="col-sm-4 item-field-con"><label>Status</label><input type="text" name="items[itemIndex][status]" class="form-control"></div>
        <div class="col-sm-4 item-field-con"><label>Barcode</label><input type="text" name="items[itemIndex][barcode]" class="form-control"></div>
        <div class="col-sm-4 item-field-con"><label>Label Customer</label><input type="text" name="items[itemIndex][label_customer]" class="form-control"></div>
    </div>
    <hr>
    <h5>Quotation</h5>
    <div class="row weight-ct-group-con weightCtInfo"
        id="weightCtInfo">
        <div class="col-xs-6 col-sm-2 weight-ct-field-con item-field-con"><input type="text" name="items[itemIndex][q_prod_g]" class="form-control" placeholder="Prod (g)"></div>
        <div class="col-xs-6 col-sm-2 weight-ct-field-con item-field-con"><input type="text" name="items[itemIndex][q_sr_g]" class="form-control" placeholder="S&R (g)"></div>
        <div class="col-xs-6 col-sm-2 weight-ct-field-con item-field-con"><input type="text" name="items[itemIndex][q_total_g]" class="form-control" placeholder="Total (g)"></div>
        <div class="col-xs-6 col-sm-2 weight-ct-field-con item-field-con"><input type="text" name="items[itemIndex][q_gpcs]" class="form-control" placeholder="G/PCS"></div>
        <div class="col-xs-6 col-sm-2 weight-ct-field-con item-field-con"><input type="text" name="items[itemIndex][q_ctime]" class="form-control" placeholder="C/Time"></div>
    </div>
    <h5>Actual</h5>
    <div class="row weight-ct-group-con">
        <div class="col-xs-6 col-sm-2 weight-ct-field-con item-field-con"><input type="text" name="items[itemIndex][a_prod_g]" class="form-control" placeholder="Prod (g)"></div>
        <div class="col-xs-6 col-sm-2 weight-ct-field-con item-field-con"><input type="text" name="items[itemIndex][a_sr_g]" class="form-control" placeholder="S&R (g)"></div>
        <div class="col-xs-6 col-sm-2 weight-ct-field-con item-field-con"><input type="text" name="items[itemIndex][a_total_g]" class="form-control" placeholder="Total (g)"></div>
        <div class="col-xs-6 col-sm-2 weight-ct-field-con item-field-con"><input type="text" name="items[itemIndex][a_gpcs]" class="form-control" placeholder="G/PCS"></div>
        <div class="col-xs-6 col-sm-2 weight-ct-field-con item-field-con"><input type="text" name="items[itemIndex][a_ctime]" class="form-control" placeholder="C/Time"></div>
    </div>
    <h5>Approval</h5>
    <div class="row weight-ct-group-con">
        <div class="col-xs-6 col-sm-2 weight-ct-field-con item-field-con"><input type="text" name="items[itemIndex][ap_prod_g]" class="form-control" placeholder="Prod (g)"></div>
        <div class="col-xs-6 col-sm-2 weight-ct-field-con item-field-con"><input type="text" name="items[itemIndex][ap_sr_g]" class="form-control" placeholder="S&R (g)"></div>
        <div class="col-xs-6 col-sm-2 weight-ct-field-con item-field-con"><input type="text" name="items[itemIndex][ap_total_g]" class="form-control" placeholder="Total (g)"></div>
        <div class="col-xs-6 col-sm-2 weight-ct-field-con item-field-con"><input type="text" name="items[itemIndex][ap_gpcs]" class="form-control" placeholder="G/PCS"></div>
        <div class="col-xs-6 col-sm-2 weight-ct-field-con item-field-con"><input type="text" name="items[itemIndex][ap_ctime]" class="form-control" placeholder="C/Time"></div>
    </div>
    <hr>
    <h5>Machines</h5>
    <div class="row mc-group-con mcInfo" id="mcInfo">
        <div class="col-xs-6 col-sm-3 mc-field-con item-field-con"><input type="text" name="items[itemIndex][mc1_no]" class="form-control" placeholder="1st MC#"></div>
        <div class="col-xs-6 col-sm-3 mc-field-con item-field-con"><input type="text" name="items[itemIndex][mc1_ton]" class="form-control" placeholder="Ton"></div>
        <div class="col-xs-6 col-sm-3 mc-field-con item-field-con"><input type="text" name="items[itemIndex][mc2_no]" class="form-control" placeholder="2nd MC#"></div>
        <div class="col-xs-6 col-sm-3 mc-field-con item-field-con"><input type="text" name="items[itemIndex][mc2_ton]" class="form-control" placeholder="Ton"></div>
    </div>
    <div class="row mc-group-con" style="margin-top:10px;">
        <div class="col-xs-6 col-sm-3 mc-field-con item-field-con"><input type="text" name="items[itemIndex][mc3_no]" class="form-control" placeholder="3rd MC#"></div>
        <div class="col-xs-6 col-sm-3 mc-field-con item-field-con"><input type="text" name="items[itemIndex][mc3_ton]" class="form-control" placeholder="Ton"></div>
        <div class="col-xs-6 col-sm-3 mc-field-con item-field-con"><input type="text" name="items[itemIndex][mc4_no]" class="form-control" placeholder="4th MC#"></div>
        <div class="col-xs-6 col-sm-3 mc-field-con item-field-con"><input type="text" name="items[itemIndex][mc4_ton]" class="form-control" placeholder="Ton"></div>
    </div>
    <div class="row mc-group-con" style="margin-top:10px;">
        <div class="col-xs-6 col-sm-3 mc-field-con item-field-con"><input type="text" name="items[itemIndex][mc5_no]" class="form-control" placeholder="5th MC#"></div>
        <div class="col-xs-6 col-sm-3 mc-field-con item-field-con"><input type="text" name="items[itemIndex][mc5_ton]" class="form-control" placeholder="Ton"></div>
    </div>
    <h5 style="margin-top:25px;">Internal Approve</h5>
    <div class="row mc-group-con" style="margin-top:10px;">
        <div class="col-xs-6 col-sm-3 mc-field-con item-field-con"><input type="text" name="items[itemIndex][app_mc1_no]" class="form-control" placeholder="1st MC#"></div>
        <div class="col-xs-6 col-sm-3 mc-field-con item-field-con"><input type="text" name="items[itemIndex][app_mc1_ton]" class="form-control" placeholder="Ton"></div>
        <div class="col-xs-6 col-sm-3 mc-field-con item-field-con"><input type="text" name="items[itemIndex][app_mc2_no]" class="form-control" placeholder="2nd MC#"></div>
        <div class="col-xs-6 col-sm-3 mc-field-con item-field-con"><input type="text" name="items[itemIndex][app_mc2_ton]" class="form-control" placeholder="Ton"></div>
    </div>
    <hr class="material-upper-hr">
    <div class="materialFormsCon" id="materialFormsCon">
        <div class="material-header-con">
            <h5>Materials List</h5>
            <button type="button" class="btn btn-sm btn-primary addMaterial">Add Material</button>
        </div>
        <div id="accordion" class="accordion-style1 panel-group">
        </div>
    </div>
</div>

<div class="panel panel-default materialFormTemplate" hidden>
    <div class="panel-heading">
        <h4 class="panel-title">
            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true">
                <i class="bigger-110 ace-icon fa fa-angle-down" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                <span id="materialId"></span>
            </a>
            <a href="javascript:void(0)" class="btn btn-sm btn-danger removeMaterialBtn">
                <i class="fa fa-xmark"></i>
            </a>
        </h4>
    </div>

    <div class="panel-collapse collapse in" id="collapseOne" aria-expanded="true">
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-3 item-field-con"><label>Division</label><input type="text" name="items[itemIndex][material][materialIndex][division]" class="form-control"></div>
                <div class="col-sm-3 item-field-con"><label>Customer</label><input type="text" name="items[itemIndex][material][materialIndex][customer]" class="form-control"></div>
                <div class="col-sm-3 item-field-con"><label>Model</label><input type="text" name="items[itemIndex][material][materialIndex][model]" class="form-control"></div>
                <div class="col-sm-3 item-field-con"><label>Master Code</label><input type="text" name="items[itemIndex][material][materialIndex][master_code]" class="form-control"></div>
            </div>
            <div class="row">
                <div class="col-sm-12 item-field-con"><label>Item Name</label><input type="text" name="items[itemIndex][material][materialIndex][part_name]" class="form-control"></div>
            </div>
            <div class="row">
                <div class="col-sm-3 item-field-con"><label>ERP Code</label><input type="text" name="items[itemIndex][material][materialIndex][erp_code]" class="form-control"></div>
                <div class="col-sm-3 item-field-con"><label>Process</label><input type="text" name="items[itemIndex][material][materialIndex][process]" class="form-control"></div>
                <div class="col-sm-3 item-field-con"><label>Class</label><input type="text" name="items[itemIndex][material][materialIndex][class]" class="form-control"></div>
                <div class="col-sm-3 item-field-con"><label>Supplier</label><input type="text" name="items[itemIndex][material][materialIndex][supplier]" class="form-control"></div>
            </div>
            <div class="row">
                <div class="col-sm-3 item-field-con"><label>Quantity</label><input type="number" name="items[itemIndex][material][materialIndex][qty]" class="form-control"></div>
                <div class="col-sm-3 item-field-con"><label>Unit</label><input type="text" name="items[itemIndex][material][materialIndex][unit]" class="form-control"></div>
                <div class="col-sm-3 item-field-con"><label>Cavity</label><input type="text" name="items[itemIndex][material][materialIndex][cavity]" class="form-control"></div>
                <div class="col-sm-3 item-field-con"><label>Tool</label><input type="text" name="items[itemIndex][material][materialIndex][tool]" class="form-control"></div>
            </div>
            <div class="row">
                <div class="col-sm-4 item-field-con"><label>Status</label><input type="text" name="items[itemIndex][material][materialIndex][status]" class="form-control"></div>
                <div class="col-sm-4 item-field-con"><label>Barcode</label><input type="text" name="items[itemIndex][material][materialIndex][barcode]" class="form-control"></div>
                <div class="col-sm-4 item-field-con"><label>Label Customer</label><input type="text" name="items[itemIndex][material][materialIndex][label_customer]" class="form-control"></div>
            </div>
            <hr>
            <h5>Quotation</h5>
            <div class="row weight-ct-group-con">
                <div class="col-xs-6 col-sm-2 weight-ct-field-con item-field-con"><input type="text" name="items[itemIndex][material][materialIndex][q_prod_g]" class="form-control" placeholder="Prod (g)"></div>
                <div class="col-xs-6 col-sm-2 weight-ct-field-con item-field-con"><input type="text" name="items[itemIndex][material][materialIndex][q_sr_g]" class="form-control" placeholder="S&R (g)"></div>
                <div class="col-xs-6 col-sm-2 weight-ct-field-con item-field-con"><input type="text" name="items[itemIndex][material][materialIndex][q_total_g]" class="form-control" placeholder="Total (g)"></div>
                <div class="col-xs-6 col-sm-2 weight-ct-field-con item-field-con"><input type="text" name="items[itemIndex][material][materialIndex][q_gpcs]" class="form-control" placeholder="G/PCS"></div>
                <div class="col-xs-6 col-sm-2 weight-ct-field-con item-field-con"><input type="text" name="items[itemIndex][material][materialIndex][q_ctime]" class="form-control" placeholder="C/Time"></div>
            </div>
            <h5>Actual</h5>
            <div class="row weight-ct-group-con">
                <div class="col-xs-6 col-sm-2 weight-ct-field-con item-field-con"><input type="text" name="items[itemIndex][material][materialIndex][a_prod_g]" class="form-control" placeholder="Prod (g)"></div>
                <div class="col-xs-6 col-sm-2 weight-ct-field-con item-field-con"><input type="text" name="items[itemIndex][material][materialIndex][a_sr_g]" class="form-control" placeholder="S&R (g)"></div>
                <div class="col-xs-6 col-sm-2 weight-ct-field-con item-field-con"><input type="text" name="items[itemIndex][material][materialIndex][a_total_g]" class="form-control" placeholder="Total (g)"></div>
                <div class="col-xs-6 col-sm-2 weight-ct-field-con item-field-con"><input type="text" name="items[itemIndex][material][materialIndex][a_gpcs]" class="form-control" placeholder="G/PCS"></div>
                <div class="col-xs-6 col-sm-2 weight-ct-field-con item-field-con"><input type="text" name="items[itemIndex][material][materialIndex][a_ctime]" class="form-control" placeholder="C/Time"></div>
            </div>
            <h5>Approval</h5>
            <div class="row weight-ct-group-con">
                <div class="col-xs-6 col-sm-2 weight-ct-field-con item-field-con"><input type="text" name="items[itemIndex][material][materialIndex][ap_prod_g]" class="form-control" placeholder="Prod (g)"></div>
                <div class="col-xs-6 col-sm-2 weight-ct-field-con item-field-con"><input type="text" name="items[itemIndex][material][materialIndex][ap_sr_g]" class="form-control" placeholder="S&R (g)"></div>
                <div class="col-xs-6 col-sm-2 weight-ct-field-con item-field-con"><input type="text" name="items[itemIndex][material][materialIndex][ap_total_g]" class="form-control" placeholder="Total (g)"></div>
                <div class="col-xs-6 col-sm-2 weight-ct-field-con item-field-con"><input type="text" name="items[itemIndex][material][materialIndex][ap_gpcs]" class="form-control" placeholder="G/PCS"></div>
                <div class="col-xs-6 col-sm-2 weight-ct-field-con item-field-con"><input type="text" name="items[itemIndex][material][materialIndex][ap_ctime]" class="form-control" placeholder="C/Time"></div>
            </div>
            <hr>
            <h5>Machines</h5>
            <div class="row mc-group-con">
                <div class="col-xs-6 col-sm-3 mc-field-con item-field-con"><input type="text" name="items[itemIndex][material][materialIndex][mc1_no]" class="form-control" placeholder="1st MC#"></div>
                <div class="col-xs-6 col-sm-3 mc-field-con item-field-con"><input type="text" name="items[itemIndex][material][materialIndex][mc1_ton]" class="form-control" placeholder="Ton"></div>
                <div class="col-xs-6 col-sm-3 mc-field-con item-field-con"><input type="text" name="items[itemIndex][material][materialIndex][mc2_no]" class="form-control" placeholder="2nd MC#"></div>
                <div class="col-xs-6 col-sm-3 mc-field-con item-field-con"><input type="text" name="items[itemIndex][material][materialIndex][mc2_ton]" class="form-control" placeholder="Ton"></div>
            </div>
            <div class="row mc-group-con" style="margin-top:10px;">
                <div class="col-xs-6 col-sm-3 mc-field-con item-field-con"><input type="text" name="items[itemIndex][material][materialIndex][mc3_no]" class="form-control" placeholder="3rd MC#"></div>
                <div class="col-xs-6 col-sm-3 mc-field-con item-field-con"><input type="text" name="items[itemIndex][material][materialIndex][mc3_ton]" class="form-control" placeholder="Ton"></div>
                <div class="col-xs-6 col-sm-3 mc-field-con item-field-con"><input type="text" name="items[itemIndex][material][materialIndex][mc4_no]" class="form-control" placeholder="4th MC#"></div>
                <div class="col-xs-6 col-sm-3 mc-field-con item-field-con"><input type="text" name="items[itemIndex][material][materialIndex][mc4_ton]" class="form-control" placeholder="Ton"></div>
            </div>
            <div class="row" style="margin-top:10px;">
                <div class="col-xs-6 col-sm-3 mc-field-con item-field-con"><input type="text" name="items[itemIndex][material][materialIndex][mc5_no]" class="form-control" placeholder="5th MC#"></div>
                <div class="col-xs-6 col-sm-3 mc-field-con item-field-con"><input type="text" name="items[itemIndex][material][materialIndex][mc5_ton]" class="form-control" placeholder="Ton"></div>
            </div>
            <h5 style="margin-top:25px;">Internal Approve</h5>
            <div class="row mc-group-con" style="margin-top:10px;">
                <div class="col-xs-6 col-sm-3 mc-field-con item-field-con"><input type="text" name="items[itemIndex][material][materialIndex][app_mc1_no]" class="form-control" placeholder="1st MC#"></div>
                <div class="col-xs-6 col-sm-3 mc-field-con item-field-con"><input type="text" name="items[itemIndex][material][materialIndex][app_mc1_ton]" class="form-control" placeholder="Ton"></div>
                <div class="col-xs-6 col-sm-3 mc-field-con item-field-con"><input type="text" name="items[itemIndex][material][materialIndex][app_mc2_no]" class="form-control" placeholder="2nd MC#"></div>
                <div class="col-xs-6 col-sm-3 mc-field-con item-field-con"><input type="text" name="items[itemIndex][material][materialIndex][app_mc2_ton]" class="form-control" placeholder="Ton"></div>
            </div>
        </div>
    </div>
</div>

<script>
    let itemIndex = -1;

    $('#modalAdd').on('shown.bs.modal', () => {
        if (itemIndex < 0) $('#addNewItemTab').click();
    });

    $('#addNewItemTab').on('click', e => {
        e.preventDefault();
        const id = 'item' + (++itemIndex);
        const $clonedItemTemplate = $('#tab-pane-item-template').clone()
            .removeAttr('id hidden')
            .attr('id', id)
            .attr('data-tab-index', itemIndex);
        $clonedItemTemplate.find('[name]').each(function() {
            $(this).attr('name', $(this).attr('name').replace('itemIndex', itemIndex));
        });
        $('.nav-tabs li, .tab-content .tab-pane').removeClass('active');
        const itemTabCount = $('.nav-tabs').children('li:not(:has(a#addNewItemTab))').length + 1;
        const $newTabBtn = $(`<li class="active">
            <input type="checkbox" class="ace item-tab-checkbox">
            <a href="#${id}" role="tab" data-toggle="tab" data-index="${itemIndex}">Item #${itemTabCount}</a>
        </li>`);
        const lastTab = $('.nav-tabs').children().last();
        $newTabBtn.insertBefore(lastTab).find('a').tab('show');
        $clonedItemTemplate.find(".panel-group").attr('id', `accordion${itemIndex}`);
        $clonedItemTemplate.find("#weightCtInfo").attr('id', `weightCtInfo${itemIndex}`);
        $clonedItemTemplate.find("#mcInfo").attr('id', `mcInfo${itemIndex}`);
        $clonedItemTemplate.find("#materialFormsCon").attr('id', `materialFormsCon${itemIndex}`);
        $('.modal-header-action-tab #removeItemBtn').show();
        $('.tab-content .modal-no-data-label').hide();
        $clonedItemTemplate.addClass('active').appendTo('.tab-content');
        $('.nav-tabs li.active a').trigger('click');
    });

    $('#removeItemBtn').on('click', e => {
        e.preventDefault();

        const $checkedItems = $('.item-tab-checkbox:checked');

        if (!$checkedItems.length) {
            return showToast("warning", 'Select item tab first.');
        }

        Swal.fire({
            title: "Remove Item",
            text: "Do you want to remove selected item?",
            icon: "question",
            iconColor: "#3498DB",
            showDenyButton: true,
            confirmButtonColor: "#87B87F",
            denyButtonColor: "#D15B47 ",
            confirmButtonText: "Yes",
            denyButtonText: "No",
        }).then((result) => {
            if (!result.isDismissed) {
                if (result.isDenied) {
                    $('.item-tab-checkbox').prop('checked', false);
                } else {
                    $checkedItems.each(function() {
                        const tabPaneId = $(this).next().attr('href');

                        $(`${tabPaneId}`).remove();
                        $(this).parent().remove();
                    });

                    $('.nav-tabs li').removeClass('active');
                    $('.nav-tabs li:last-child').prev().addClass('active');

                    const lastTabId = $('.nav-tabs li:last-child').prev().find('a').attr('href');

                    $(".tab-pane").removeClass('active');
                    $(lastTabId).addClass('active');

                    if (!$(".tab-content .tab-pane").length) {
                        $('.modal-header-action-tab #removeItemBtn').hide();
                        $('.tab-content .modal-no-data-label').show();
                    }

                    let itemTabCount = 1;
                    $('.nav-tabs li:has(input[type="checkbox"])').each(function() {
                        $(this).find("a").text(`Item #${itemTabCount}`);
                        itemTabCount++;
                    });
                }
            }
        });
    });

    $(document).on('click', '.addMaterial', function() {
        const $tab = $(this).closest('.tab-pane');
        const itemIndex = $tab.attr('data-tab-index');

        let tabCurrentMaterialCount = $tab.attr('data-material-count');
        tabCurrentMaterialCount++;
        $tab.attr('data-material-count', tabCurrentMaterialCount);

        const materialIndex = tabCurrentMaterialCount;
        const $clonedMaterialForm = $('.materialFormTemplate').clone()
            .removeAttr('hidden')
            .removeClass('materialFormTemplate');

        $clonedMaterialForm.find('[name]').each(function() {
            $(this).attr('name', $(this).attr('name')
                .replace('itemIndex', itemIndex)
                .replace('materialIndex', materialIndex));
        });

        $clonedMaterialForm.find(".panel-title")
            .find("span").text(
                `Material #${$tab.find('.panel-group').children().length + 1}`
            );

        $clonedMaterialForm.find(".accordion-toggle")
            .attr('href', `#collapse${itemIndex}${materialIndex}`)
            .attr('data-parent', `#accordion${itemIndex}`);

        $clonedMaterialForm.find(".panel-collapse")
            .attr('id', `collapse${itemIndex}${materialIndex}`);

        $tab.find(`#accordion${itemIndex}`)
            .append($clonedMaterialForm);
    });

    $(document).on('click', '.nav-tabs li a:not(#addNewItemTab)', function() {
        const currentTabIndex = $(this).attr("data-index");
        const $scrollspyWeightCt = $(".scrollspy-group").children().eq(2);
        const $scrollspyMC = $(".scrollspy-group").children().eq(3);
        const $scrollspyMaterialForm = $(".scrollspy-group").children().eq(4);

        $scrollspyWeightCt.attr('href', `#weightCtInfo${currentTabIndex}`);
        $scrollspyMC.attr('href', `#mcInfo${currentTabIndex}`);
        $scrollspyMaterialForm.attr('href', `#materialFormsCon${currentTabIndex}`);
    });

    $(document).on('click', '.removeMaterialBtn', function() {
        const currentTabIndex = $(this).attr("data-index");
        const $allMaterials = $(this).closest('.panel-group');

        $(this).closest('.panel').remove();

        let materialPanelcount = 1;
        $allMaterials.children('.panel').each(function() {
            $(this).find(".panel-title span").text(`Material #${materialPanelcount}`);
            materialPanelcount++;
        });
    });

    $(function() {
        const srollspyItem = $(".modal-scrollspy .scrollspy-item");

        srollspyItem.on("click", function(e) {
            $(this).closest(".modal-scrollspy").find('.scrollspy-item').removeClass("active");
            $(this).addClass("active");
        });
    });

    $("#modalAdd").find('.closeModalBtn').on("click", function() {
        const $modal = $(this).closest('#modalAdd');

        $modal.find('.nav-tabs li:not(:last-child)').remove();
        $modal.find('.tab-content .tab-pane').remove();
        $modal.find('.modal-scrollspy .scrollspy-group').children().removeClass('active');
        $modal.find('.modal-scrollspy .scrollspy-group').children().eq(1).addClass('active');
        itemIndex = -1;
    });
</script>