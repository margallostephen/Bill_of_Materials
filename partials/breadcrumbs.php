<?php
$path = basename($_SERVER['REQUEST_URI']);
$label = ucwords(
    str_replace('_', ' ', preg_replace('/[^a-zA-Z0-9_ ]/', '', $path))
);
?>

<div class="breadcrumbs ace-save-state" id="breadcrumbs">
    <div class="ace-settings-container" id="ace-settings-container" style="display:none;">
        <div class="btn btn-app btn-xs btn-warning ace-settings-btn" id="ace-settings-btn">
            <i class="ace-icon fa fa-cog bigger-130"></i>
        </div>
        <div class="ace-settings-box clearfix" id="ace-settings-box">
            <div class="pull-left width-50">
                <div class="ace-settings-item">
                    <input type="checkbox" class="ace ace-checkbox-2 ace-save-state" id="ace-settings-navbar"
                        autocomplete="off">
                    <label class="lbl" for="ace-settings-navbar"> Fixed Navbar</label>
                </div>
                <div class="ace-settings-item">
                    <input type="checkbox" class="ace ace-checkbox-2 ace-save-state" id="ace-settings-sidebar"
                        autocomplete="off">
                    <label class="lbl" for="ace-settings-sidebar"> Fixed Sidebar</label>
                </div>
                <div class="ace-settings-item">
                    <input type="checkbox" class="ace ace-checkbox-2 ace-save-state" id="ace-settings-breadcrumbs"
                        autocomplete="off">
                    <label class="lbl" for="ace-settings-breadcrumbs"> Fixed Breadcrumbs</label>
                </div>
            </div>
            <div class="pull-left width-50">
                <div class="ace-settings-item">
                    <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-hover" autocomplete="off">
                    <label class="lbl" for="ace-settings-hover"> Submenu on Hover</label>
                </div>
                <div class="ace-settings-item">
                    <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-compact" autocomplete="off">
                    <label class="lbl" for="ace-settings-compact"> Compact Sidebar</label>
                </div>
                <div class="ace-settings-item">
                    <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-highlight" autocomplete="off">
                    <label class="lbl" for="ace-settings-highlight"> Alt. Active Item</label>
                </div>
            </div>
        </div>
    </div>
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <div class="ace-settings-container" id="ace-settings-container" style="display:none;">
            <div class="btn btn-app btn-xs btn-warning ace-settings-btn" id="ace-settings-btn">
                <i class="ace-icon fa fa-cog bigger-130"></i>
            </div>
            <div class="ace-settings-box clearfix" id="ace-settings-box">
                <div class="pull-left width-50">
                    <div class="ace-settings-item">
                        <input type="checkbox" class="ace ace-checkbox-2 ace-save-state" id="ace-settings-navbar"
                            autocomplete="off">
                        <label class="lbl" for="ace-settings-navbar"> Fixed Navbar</label>
                    </div>
                    <div class="ace-settings-item">
                        <input type="checkbox" class="ace ace-checkbox-2 ace-save-state" id="ace-settings-sidebar"
                            autocomplete="off">
                        <label class="lbl" for="ace-settings-sidebar"> Fixed Sidebar</label>
                    </div>
                    <div class="ace-settings-item">
                        <input type="checkbox" class="ace ace-checkbox-2 ace-save-state" id="ace-settings-breadcrumbs"
                            autocomplete="off">
                        <label class="lbl" for="ace-settings-breadcrumbs"> Fixed Breadcrumbs</label>
                    </div>
                </div>
                <div class="pull-left width-50">
                    <div class="ace-settings-item">
                        <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-hover" autocomplete="off">
                        <label class="lbl" for="ace-settings-hover"> Submenu on Hover</label>
                    </div>
                    <div class="ace-settings-item">
                        <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-compact" autocomplete="off">
                        <label class="lbl" for="ace-settings-compact"> Compact Sidebar</label>
                    </div>
                    <div class="ace-settings-item">
                        <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-highlight"
                            autocomplete="off">
                        <label class="lbl" for="ace-settings-highlight"> Alt. Active Item</label>
                    </div>
                </div>
            </div>
        </div>
        <ul class="breadcrumb" style="display: flex; align-items: center;">
            <i id="bc-icon" class="fa"></i>
            <li>
                <a href="<?php echo BASE_URL ?>"><?php echo SYSTEM_NAME ?></a>
            </li>
            <li class="active"><?php echo $label ?></li>
        </ul>
    </div>
</div>