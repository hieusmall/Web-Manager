<?php
$formPages = ["ticketList","ticketUpdate","ticketNew"];
$formPage = isset($_GET['currentPage']) && strlen($_GET['currentPage']) > 0 && in_array($_GET['currentPage'] , $formPages) ? $_GET['currentPage'] : "formList";
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$pluginPageUrl = $protocol . "$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]?page=webManagerForm";
?>
<span display="none" id="pluginPageUrl" data-plugin-page-url="<?php echo $pluginPageUrl ?>"></span>

<?php

?>
<div class="wmAdminWrap wrap" data-page-active="ticketList">
    <h1 class="wp-heading-inline">Danh sách Ticket</h1>
    <hr class="wp-header-end">
    <form action="" id="form-filter">
        <?php echo webManagerLib::wmTableTicket() ?>
    </form>
</div>