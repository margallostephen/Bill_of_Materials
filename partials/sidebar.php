<?php require_once CONFIG_PATH . 'constants.php'; ?>

<div id="sidebar" class="sidebar responsive ace-save-state">
    <ul class="nav nav-list">
        <li class="sidebar-btn" id="dashboard">
            <a href="<?php echo BASE_URL . 'dashboard' ?>">
                <i class="menu-icon fa fa-tachometer"></i>
                <span class="menu-text">Dashboard</span>
            </a>
        </li>
        <?php if (isset($_SESSION['ROLE']) && in_array($_SESSION['ROLE'], $routes['bill_of_materials/list'])): ?>
            <li>
                <a href="#" class="dropdown-toggle">
                    <i class="menu-icon fa fa-boxes-packing"></i>
                    <span class="menu-text">
                        Bill of Materials
                    </span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>
                <b class="arrow"></b>
                <ul class="submenu nav-hide" style="display: none;">
                    <li class="sidebar-btn" id="list">
                        <a href="<?php echo BASE_URL . 'bill_of_materials/list' ?>">
                            <i class="menu-icon fa fa-list"></i>
                            <span class="menu-text">List</span>
                        </a>
                    </li>
                    <li class="sidebar-btn" id="list">
                        <a href="<?php echo BASE_URL . 'bill_of_materials/revision' ?>">
                            <i class="menu-icon fa fa-edit"></i>
                            <span class="menu-text">Revision</span>
                        </a>
                    </li>
                </ul>
            </li>
        <?php endif; ?>
    </ul>

    <div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
        <i id="sidebar-toggle-icon" class="ace-save-state ace-icon fa fa-angle-double-left" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
    </div>
</div>