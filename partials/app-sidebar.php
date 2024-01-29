<?php
$user = $_SESSION['user'];
?>
<div class="dashboard_sidebar" id="dashboard_sidebar">
    <h3 class="dashboard_logo" id="dashboard_logo">BSM</h3>
    <div class="dashboard_sidebar_user">
        <img src="images/User/Sanam.jpg" alt="User image." id="userImage" />
        <span><?= $user['first_name'] . ' ' . $user['last_name'] ?></span>
    </div>
    <div class="dashboard_sidebar_menus">
        <ul class="dashboard_menu_lists">
            <!-- class="menuActive" -->
            <li class="liMainMenu">
                <a href="./dashboard.php"><i class="fa fa-dashboard"></i> <span class="menuText"> Dashboard</span></a>
            </li>
            <li class="liMainMenu">
                <a href="./report.php"><i class="fa fa-file"></i> <span class="menuText"> Reports</span></a>
            </li>
            <li class="liMainMenu">
                <a href="javascript:void(0);" class="showHideSubMenu">
                    <i class="fa fa-book showHideSubMenu"></i>
                    <span class="menuText showHideSubMenu"> Book</span>
                    <i class="fa fa-angle-left mainMenuIconArrow showHideSubMenu"></i>
                </a>
                <ul class="subMenus">
                    <li><a class="subMenuLink" href="./book-view.php"> <i class="fa fa-circle-o"> View Book</i></a></li>
                    <li><a class="subMenuLink" href="./book-add.php"> <i class="fa fa-circle-o"> Add Book</i></a></li>
                </ul>
            </li>
            <li class="liMainMenu">
                <a href="javascript:void(0);" class="showHideSubMenu">
                    <i class="fa fa-truck showHideSubMenu"></i>
                    <span class="menuText showHideSubMenu"> Supplier</span>
                    <i class="fa fa-angle-left mainMenuIconArrow showHideSubMenu"></i>
                </a>
                <ul class="subMenus">
                    <li><a class="subMenuLink" href="./supplier-view.php"> <i class="fa fa-circle-o"> View Supplier</i></a></li>
                    <li><a class="subMenuLink" href="./supplier-add.php"> <i class="fa fa-circle-o"> Add Supplier</i></a></li>
                </ul>
            </li>
            <li class="liMainMenu">
                <a href="javascript:void(0);" class="showHideSubMenu">
                    <i class="fa fa-shopping-cart showHideSubMenu"></i>
                    <span class="menuText showHideSubMenu"> Purchase Order</span>
                    <i class="fa fa-angle-left mainMenuIconArrow showHideSubMenu"></i>
                </a>
                <ul class="subMenus">
                    <li><a class="subMenuLink" href="./book-order.php"> <i class="fa fa-circle-o"> Create Order</i></a></li>
                    <li><a class="subMenuLink" href="./view-order.php"> <i class="fa fa-circle-o"> View Order</i></a></li>
                </ul>
            </li>
            <li class="liMainMenu showHideSubMenu">
                <a href="javascript:void(0);" class="showHideSubMenu">
                    <i class="fa fa-user-plus showHideSubMenu"></i>
                    <span class="menuText showHideSubMenu"> User</span>
                    <i class="fa fa-angle-left mainMenuIconArrow showHideSubMenu"></i>
                </a>
                <ul class="subMenus">
                    <li><a class="subMenuLink" href="./users-view.php"> <i class="fa fa-circle-o"> View Users</i></a></li>
                    <li><a class="subMenuLink" href="./users-add.php"> <i class="fa fa-circle-o"> Add Users</i></a></li>
                </ul>
            </li>
        </ul>
    </div>
</div>