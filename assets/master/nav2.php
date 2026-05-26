<nav class="page-sidebar" id="sidebar">
    <div id="sidebar-collapse">
        <div class="admin-block d-flex">
            <div>
                <img src="assets/img/admin-avatar.png" width="45px" />
            </div>
            <div class="admin-info">
                <div class="font-strong"><?php echo $username; ?></div><small><?php echo $emailadd; ?></small></div>
        </div>
        <ul class="side-menu metismenu">
            <li class="heading">FEATURES</li>
            <li>
                <a class="<?php echo $index_act;?>" href="index.php"><i class="sidebar-item-icon ti-clipboard"></i>
                    <span class="nav-label">Data Entry</span>
                </a>
            </li>
            <li>
                <a class = "<?php echo $view_act;?>"href="view.php"><i class="sidebar-item-icon fa fa-th-large"></i>
                    <span class="nav-label">Data View</span>
                </a>
            </li>
           
        </ul>
    </div>
</nav>