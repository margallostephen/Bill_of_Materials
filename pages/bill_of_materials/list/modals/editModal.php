<div id="modalEdit" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" id="modalTop">
                <h4 class="modal-title">Edit Item</h4>
            </div>
            <form id="editItemForm">
                <div class="modal-body">
                    <div class="form-group">
                        <div class="row" hidden>
                            <div class="col-sm-12 item-field-con">
                                <label>Table</label>
                                <input type="input" class="form-control" id="table" name="table" readonly />
                            </div>
                        </div>
                        <div class="row" hidden>
                            <div class="col-sm-12 item-field-con">
                                <label>Row Type</label>
                                <input type="input" class="form-control" id="rowType" name="rowType" readonly />
                            </div>
                        </div>
                        <div class="row" hidden>
                            <div class="col-sm-12 item-field-con">
                                <label>Column Type (Weight/CT, MC)</label>
                                <input type="input" class="form-control" id="columnType" name="columnType" readonly />
                            </div>
                        </div>
                        <div class="row" hidden>
                            <div class="col-sm-12 item-field-con">
                                <label>Column Title</label>
                                <input type="input" class="form-control" id="columnTitle" name="columnTitle" readonly />
                            </div>
                        </div>
                        <div class="row" hidden>
                            <div class="col-sm-12 item-field-con">
                                <label>Column Field</label>
                                <input type="input" class="form-control" id="columnField" name="columnField" readonly />
                            </div>
                        </div>
                        <div class="row" hidden>
                            <div class="col-sm-12 item-field-con">
                                <label>Row ID</label>
                                <input type="input" class="form-control" id="rowId" name="rowId" readonly />
                            </div>
                        </div>
                        <div class="row" hidden>
                            <div class="col-sm-12 item-field-con">
                                <label>Part Surrogate</label>
                                <input type="input" class="form-control" id="partSurrogate" name="partSurrogate" readonly />
                            </div>
                        </div>
                        <div class="row" hidden>
                            <div class="col-sm-12 item-field-con">
                                <label>Material Surrogate</label>
                                <input type="input" class="form-control" id="materialSurrogate" name="materialSurrogate" readonly />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 item-field-con">
                                <label>Current Value</label>
                                <input type="input" class="form-control" id="previousValue" name="previousValue" readonly />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 item-field-con">
                                <label>New Value</label>
                                <input type="input" class="form-control" id="newValue" name="newValue" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 item-field-con">
                                <label>Remarks</label>
                                <textarea class="form-control" name="remarks"></textarea>
                            </div>
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