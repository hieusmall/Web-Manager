<?php
$popupPages = ["popupList","popupUpdate","popupNew"];
$popupPage = isset($_GET['currentPage']) && strlen($_GET['currentPage']) > 0 && in_array($_GET['currentPage'] , $popupPages) ? $_GET['currentPage'] : "popupList";

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$pluginPageUrl = $protocol . "$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]?page=webManagerPopup";
?>

<?php if ($popupPage == '' || $popupPage == 'popupList') : ?>
<div class="wmAdminWrap wrap" data-page-active="popupList">
    <h1 class="wp-heading-inline">Danh sách Popup</h1>
    <a href="<?php echo $pluginPageUrl . '&currentPage=popupNew' ?>" id="newPopupBtn" class="page-title-action">Thêm Popup mới</a>
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
                <th scope="col" id="wmForm" class="manage-column column-wmForm">Form</th>
                <th scope="col" id="delay_show_time" class="manage-column column-delay_show_time">Delay Show Time</th>
                <th scope="col" id="created_at" class="manage-column column-created_at sortable asc">
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

<?php elseif (in_array($popupPage, ["popupUpdate","popupNew"])) :
    $pageDataDetail = [
        'popupNew' => array(
            'heading' => "Popup Mới",
            'popupId' => "popupNewItem",
        ),
        'popupUpdate' => array(
            'heading' => "Cập nhật Popup",
            'popupId' => "popupUpdateItem"
        )
    ]; ?>
<div class="wmAdminWrap wrap" data-page-active="<?php echo $popupPage ?>">
    <h1 class="wp-heading-inline"><?php echo $pageDataDetail[$popupPage]['heading']; ?></h1>
    <a href="<?php echo $pluginPageUrl . '&currentPage=formList' ?>" id="backToListFormBtn" class="page-title-action">Tất cả Popup</a>
    <form id="<?php echo $pageDataDetail[$popupPage]['formId']; ?>" method="post" action="">
        <input type="hidden" name="popup_id" value="">
        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content" style="position: relative;">
                    <div id="titlediv">
                        <div id="titlewrap">
                            <label class="" id="title-prompt-text" for="title">Nhập tiêu đề Popup</label>
                            <input type="text" name="title" value="" id="title">
                        </div>
                        <div class="inside">
                            <div id="edit-slug-box" class="hide-if-no-js">
                            </div>
                        </div>
                    </div>

                    <?php
                    $content = '';
                    $editor_id = 'content';
                    wp_editor( $content, $editor_id );
                    ?>
                </div>

                <div id="postbox-container-1" class="postbox-container">
                    <div id="side-sortables" class="meta-box-sortables ui-sortable">

                        <div id="popup_bg_image" class="postbox" style="display: block;">
                            <button type="button" class="handlediv" aria-expanded="true">
                                <span class="screen-reader-text">Chuyển đổi bảng điều khiển: Background Image</span>
                                <span class="toggle-indicator" aria-hidden="true"></span>
                            </button>
                            <h2 class="hndle ui-sortable-handle">
                                <span>Background</span>
                            </h2>
                            <div class="inside">
                                <p class="hide-if-no-js">
                                    <?php echo webManagerLib::wm_image_uploader_field('bg_image'); ?>
                                </p>
                            </div>
                        </div>

                        <div id="popup_direction_background" class="postbox">
                            <button type="button" class="handlediv" aria-expanded="true">
                                <span class="screen-reader-text">Chuyển đổi bảng điều khiển: URL chuyển hướng</span>
                                <span class="toggle-indicator" aria-hidden="true"></span>
                            </button>
                            <h2 class="hndle ui-sortable-handle">
                                <span>URL chuyển hướng</span>
                            </h2>
                            <div class="inside">
                                <div>
                                    <input name="direction_background" type="text" value="" class="" placeholder="http://example.vn">
                                </div>
                            </div>
                        </div>

                        <div id="popup_direction_background" class="postbox">
                            <button type="button" class="handlediv" aria-expanded="true">
                                <span class="screen-reader-text">Chuyển đổi bảng điều khiển: Lưu Lại</span>
                                <span class="toggle-indicator" aria-hidden="true"></span>
                            </button>
                            <h2 class="hndle ui-sortable-handle">
                                <span>Lưu Lại</span>
                            </h2>
                            <div class="inside">
                                <div>
                                    <button type="submit" name="submit" id="submit" class="button button-primary">Tạo Ngay</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="postbox-container-2" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                        <div id="" class="postbox">
                            <button type="button" class="handlediv" aria-expanded="true">
                                <span class="screen-reader-text">Chuyển đổi bảng điều khiển: </span>
                                <span class="toggle-indicator" aria-hidden="true"></span>
                            </button>
                            <h2 class="hndle ui-sortable-handle"><span>Chi tiết Popup</span></h2>
                            <div class="inside">
                                <table id="" class="form-table">
                                    <!--<tr>
                                        <th><span>Tiêu đề Popup</span></th>
                                        <td>
                                            <input name="title" type="text" value="" class="regular-text" placeholder="Chiến dịch abc...">
                                        </td>
                                    </tr>-->
                                    <?php $forms = false;
                                    // Get the forms
                                    webManagerLib::wmReadAllForm(function ($err, $formResults) use (&$forms) {
                                        if (!$err && $formResults)
                                            $forms = $formResults;
                                    });
                                    if ($forms) :
                                        $options = ``;
                                        foreach ($forms as $key => $form) {
                                            $form_id = $form->form_id;
                                            $title = $form->title;
                                            $options .= '<option value="'.$form_id.'">'.$title.' => (FormID : '.$form_id.')</option>';
                                        }
                                        ?>
                                        <tr>
                                            <th><span>Form đăng kí</span></th>
                                            <td>
                                                <select name="to_caresoft_now" id="">
                                                    <option value="0">Không</option>
                                                    <?php echo $options; ?>
                                                </select>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <th><span>Hiển thị</span></th>
                                        <td>
                                            <label for="isAuto">
                                                <input name="isAuto" type="checkbox" id="isAuto"> Tự động Sau
                                            </label>
                                            <input name="delay_show_time" type="number" min="0" value="0" placeholder="Mặc định là 0" class="small-text"> Giây
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<?php endif; ?>
