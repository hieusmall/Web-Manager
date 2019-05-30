<?php
$formPages = ["formList","formUpdate","formNew"];
$formPage = isset($_GET['currentPage']) && strlen($_GET['currentPage']) > 0 && in_array($_GET['currentPage'] , $formPages) ? $_GET['currentPage'] : "formList";


$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$pluginPageUrl = $protocol . "$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]?page=webManagerForm";
?>
<span display="none" id="pluginPageUrl" data-plugin-page-url="<?php echo $pluginPageUrl ?>"></span>

<?php
    if ($formPage == 'formList' || $formPage == '') { ?>
        <div class="wmAdminWrap wmActive wrap" data-page-active="formList">
            <h1 class="wp-heading-inline">Danh sách Form</h1>
            <a href="<?php echo $pluginPageUrl . '&currentPage=formNew' ?>" id="newFormBtn" class="page-title-action">Thêm Form mới</a>
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
    <?php } elseif (in_array($formPage, ['formNew', 'formUpdate'])) {
        $pageDataDetail = [
             'formNew' => array(
                'heading' => "New form",
                 'formId' => "formNewItem",
             ),
             'formUpdate' => array(
                 'heading' => "Update Form",
                 'formId' => "formUpdateItem"
             )
        ]; ?>
        <div class="wmAdminWrap wrap" data-page-active="<?php echo $formPage ?>">
            <h1 class="wp-heading-inline"><?php echo $pageDataDetail[$formPage]['heading']; ?></h1>
            <a href="<?php echo $pluginPageUrl . '&currentPage=formList' ?>" id="backToListFormBtn" class="page-title-action">Quay Lại Danh Sách</a>
            <form id="<?php echo $pageDataDetail[$formPage]['formId']; ?>" method="post" action="">
                <input type="hidden" name="form_id" value="">
                <table id="" class="form-table">
                    <tr>
                        <th><span>Tên Form</span></th>
                        <td>
                            <input name="name" type="text" value="" class="regular-text" placeholder="">
                        </td>
                    </tr>
                    <tr>
                        <th><span>Tiêu đề CareSoft</span></th>
                        <td>
                            <input name="title" type="text" value="" class="regular-text" placeholder="VD: SEO - Chiến dịch xyz">
                            <p class="description">Cấu Trúc Tiêu Đề CareSoft : Tiêu Đề - Tên Khách Hàng - URL Bài Viết.</p>
                        </td>
                    </tr>
                    <tr>
                        <th><span>Gửi lên CareSoft ngay</span></th>
                        <td>
                            <select name="to_caresoft_now" id="">
                                <option value="off" selected="selected">Tắt</option>
                                <option value="on">Bật</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><span>url chuyển hướng</span></th>
                        <td>
                            <input name="directional" type="text" value="" class="regular-text" placeholder="http://example.vn">
                        </td>
                    </tr>
                    <tr>
                        <th><span>ID CareSoft</span></th>
                        <td><input name="caresoft_id" maxlength="5" type="text" value="" class="small-text" placeholder="ID nguồn lead CareSoft hoặc để trống trống"></td>
                    </tr>
                </table>
                <p class="submit form-action-group">
                    <button type="submit" name="submit" id="submit" class="button button-primary">
                        Cập nhật
                    </button>
                    <span class="spinner"></span>
                </p>
            </form>

            <h2>Form Advance Custom</h2>
            <div class="card makeMagicContactForm wm-card-full wrapper-Editor">
                <div class="card-body">
                    <div id="fb-editor" class="wm-fb-editor"></div>
                </div>
            </div>
        </div>
    <?php }
?>