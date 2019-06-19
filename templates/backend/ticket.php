<?php
$formPages = ["ticketList","ticketUpdate","ticketNew"];
$formPage = isset($_GET['currentPage']) && strlen($_GET['currentPage']) > 0 && in_array($_GET['currentPage'] , $formPages) ? $_GET['currentPage'] : "formList";
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$pluginPageUrl = $protocol . "$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]?page=webManagerForm";
?>
<span display="none" id="pluginPageUrl" data-plugin-page-url="<?php echo $pluginPageUrl ?>"></span>
<span display="none" id="adminAjaxUrl" data-admix-ajax-url="<?php echo admin_url( 'admin-ajax.php' ); ?>"></span>
<?php

?>
<!--<div class="text-center wmfullLoading">
    <div class="spinner-border text-danger" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>-->
<div class="wmAdminWrap wrap" data-page-active="ticketList">
    <h1 class="wp-heading-inline">Ticket</h1>
    <hr class="wp-header-end">
    <form action="" id="form-filter">
        <?php // echo webManagerLib::wmTableTicket() ?>
    </form>

    <div class="card cardTicketsChart wm-card-full wm-whiteframe-4dp">
        <h5 class="text-capitalize font-weight-normal">Thống kê Ticket</h5>
        <form id="filters_chart_ticket_wrapper" class="mt-5">
            <div class="row">
                <div class="form-group">
                    <label for="chartFilterByDateRange">Thời gian</label>
                    <!--  <input type="text" class="form-control" id="chartFilterByDateRange" placeholder="Ngày" hidden>-->
                    <input type="hidden" class="form-control" id="startdate" name="startdate">
                    <input type="hidden" class="form-control" id="enddate" name="enddate">
                    <div id="chartFilterDateRange" style="background: #fff; cursor: pointer;
                    padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                        <i class="fa fa-calendar"></i>
                        <span></span> <i class="fa fa-caret-down"></i>
                    </div>
                </div>
                <div class="form-group col-md-3">
                    <label for="chartFilterByForm">Form</label>
                    <select multiple class="form-control filterSelectpicker" name="form_id" id="chartFilterByForm"></select>
                </div>

                <div class="form-group col-md-12">
                    <button type="submit" class="btn btn-outline-primary">
                        <span><i class="fa fa-filter" aria-hidden="true"></i></span>
                        <span>Lọc</span>
                    </button>
                </div>
            </div>
        </form>

        <div id="ticketChart">
        </div>
    </div>

    <div class="card cardLeadTables wm-card-full wm-whiteframe-4dp">
        <h5 class="text-capitalize font-weight-normal">Danh sách Ticket</h5>

        <div class="ticketTableWrapper mt-5 table-responsive">
            <!-- Ticket form filter -->
            <form class="mb-5" id="filters_ticket_wrapper">
                <!--<select class="selectpicker" multiple data-selected-text-format="count > 3"
                        name="form_id" id="ticketFilterByForm"></select>-->
                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="ticketFilterByForm">Form</label>
                        <select multiple class="form-control filterSelectpicker" name="form_id[]" id="ticketFilterByForm"></select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="ticketFilterByCareSoftStt">Tình Trạng CareSoft</label>
                        <select class="form-control filterSelectpicker" name="caresoft_ticket" id="ticketFilterByCareSoftStt"></select>
                    </div>
                    <!--<div class="form-group col-md-3">
                        <label for="ticketFilterByCreated">Ngày Đăng Ký</label>
                        <select class="form-control" name="created_at" id="ticketFilterByCreated"></select>
                    </div>-->
                    <div class="form-group">
                        <label for="ticketFilterByDateRange">Ngày Đăng Kí</label>
                        <!--  <input type="text" class="form-control" id="chartFilterByDateRange" placeholder="Ngày" hidden>-->
                        <input type="hidden" class="form-control" id="ticketCreatedStartDate" name="startdate">
                        <input type="hidden" class="form-control" id="ticketCreatedEndDate" name="enddate">
                        <div id="ticketFilterByDateRange" style="background: #fff; cursor: pointer;
                    padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                            <i class="fa fa-calendar"></i>
                            <span></span> <i class="fa fa-caret-down"></i>
                        </div>
                    </div>
                </div>
            </form>

            <table id="ticketDTable" class="ticketDTable table table-bordered wp-list-table widefat fixed striped">
            </table>
        </div>
    </div>


<!--    <button class="" type="button" data-toggle="modal" data-target="#detailLeadPopup">click vào đayaa</button>-->
    <!-- Modal -->
    <div class="modal fade wmTicketDetailPopup" id="detailLeadPopup" tabindex="-1" role="dialog" aria-labelledby="detailLeadPopupLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailLeadPopupLabel">Chi Tiết Ticket</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <span class="dashicons dashicons-id"></span> Họ Tên :
                            <span class="ticketName">Nguyễn Văn A</span>
                        </li>
                        <li class="list-group-item">
                            <span class="dashicons dashicons-smartphone"></span> Số Điện Thoại :
                            <span class="ticketPhone">0123456789</span>
                        </li>
                        <li class="list-group-item">
                            <span class="dashicons dashicons-email"></span> Email :
                            <span class="ticketEmail">example@gmail.com</span>
                        </li>
                        <li class="list-group-item">
                            <span class="dashicons dashicons-testimonial"></span>Lời Nhắn :
                            <span class="ticketNote">Tôi muốn được tư vấn dịch vụ trị thâm nách</span>
                        </li>
                        <li class="list-group-item">
                            <span class="dashicons dashicons-clock"></span> Ngày Đăng Kí :
                            <span class="ticketCreated">25 - 10 - 2019</span>
                        </li>
                        <li class="list-group-item">
                            <span class="dashicons dashicons-admin-links"></span> Link Đăng Ký :
                            <a class="ticketHrefUrl" target="_blank" href="#"><span>Xem Page</span></a>
                        </li>
                        <li class="list-group-item">
                            <span class="dashicons dashicons-flag"></span> Tiêu Đề Page :
                            <span class="ticketPostTitle">TMV Ngọc Dung - Viện Thẩm Mỹ Quốc Tế Hàng Đầu Thế Giới</span>
                        </li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary wm-whiteframe-3dp" data-dismiss="modal">Quay Lại</button>
<!--                    <button type="button" class="btn btn-primary">Lưu Lại</button>-->
                </div>
            </div>
        </div>
    </div>
</div>