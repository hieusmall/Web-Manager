<?php $formPage = ["formList","formEdit","formNew"] ?>

<div class="wmAdminWrap wrap" data-page-active="formList">
    <h1 class="wp-heading-inline">Danh sách Form</h1>
    <a href="#" class="page-title-action">Thêm Form mới</a>
    <hr class="wp-header-end">
    <form id="form-filter" method="get" action="">
        <table class="wmListFormTable wp-list-table widefat fixed striped posts">
            <thead>
                <tr>
                    <td id="cb" class="manage-colunm column-cb check-column">
                        <label class="screen-reader-text" for="cb-select-all-1">Chọn toàn bộ</label>
                        <input id="cb-select-all-1" type="checkbox">
                    </td>
                    <th scope="col" id="title" class="manage-column column-title column-primary sortable">
                        <a href="#">
                            <span>Tiêu đề</span><span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <th scope="col" id="toCareSoftNow" class="manage-column column-toCareSoftNow">To CareSoft Now</th>
                    <th scope="col" id="directional" class="manage-column column-directional">Chuyển Hướng</th>
                    <th scope="col" id="date" class="manage-column column-date sortable asc">
                        <a href="#">
                            <span class="">Ngày Tạo</span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <th scope="col" id="shortCode" class="manage-column column-shortCode">
                        <a href="#">
                            <strong>ShortCode</strong>
                        </a>
                    </th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </form>
</div>


<div class="wmAdminWrap wrap" data-page-active="formNew">
    <h1 class="wp-heading-inline">New Form</h1>
    <hr class="wp-header-end">
    <form id="formNewItem" method="post" action="">
        <table id="" class="form-table">
            <tr>
                <th><span>Tiêu đề Form</span></th>
                <td><input name="title" type="text" id="title" value="" class="form-control"></td>
            </tr>
        </table>
    </form>
</div>