<?php
$popupPages = ["popupList","popupUpdate","popupNew"];
$popupPage = isset($_GET['currentPage']) && strlen($_GET['currentPage']) > 0 && in_array($_GET['currentPage'] , $popupPages) ? $_GET['currentPage'] : "popupList";

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$pluginPageUrl = $protocol . "$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]?page=webManagerPopup";
?>

<span display="none" id="pluginPageUrl" data-plugin-page-url="<?php echo $pluginPageUrl ?>"></span>

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
                <?php
                $popups = [];
                webManagerLib::wmReadAllPopup(function ($err, $resultData) use (&$popups) {
                    if (!$err && $resultData)
                        $popups = $resultData;
                });

                foreach ($popups as $key => $popup) {
                    $popup_id = $popup->popup_id;
                    $title = $popup->title;
                    $tdTitle = '<td>
                                <strong><a id="detailPopupByTitleBtn" class="row-title" href="'.$pluginPageUrl.'&currentPage=popupUpdate&popup_id='.$popup_id.'" aria-label="'.$title.'">'.$title.'</a></strong>
                                <div class="row-actions">
                                    <span class="edit">
                                        <a class="updatePopupItem" href="'.$pluginPageUrl.'&currentPage=popupUpdate&popup_id='.$popup_id.'" aria-label="Sửa “'.$title.'”">Chỉnh sửa</a> |
                                    </span>
                                    <span class="trash">
                                        <a class="deletePopupItem" href="#" class="submitdelete" aria-label="Bỏ “'.$title.'” vào thùng rác">Xóa</a> |
                                    </span>
                                </div>
                            </td>';

                    $tdForm = '<td><span>Không</span></td>';
                    $form = false;
                    $form_id = !is_null($popup->form_id) ? $popup->form_id : false;
                    if ($form_id)
                        webManagerLib::wmReadForm($form_id, function ($err, $resultData) use (&$form) {
                            if (!$err && $resultData) {
                                $form = $resultData;
                            }
                        });
                    if ($form)
                        $tdForm = '<td class="categories column-categories" data-colname="Chuyên mục">
                                    <a href="#">'.$form->title.'</a>
                                </td>';

                    $tdDelay_show_time = '<td></td>';
                    $delay_show_time = $popup->delay_show_time;
                    if ($delay_show_time)
                        $tdDelay_show_time = '<td class="delayShowTime column-delay_show_time" data-colname="Delay Show">
                                                <a href="#">'.$delay_show_time.' Giây</a>
                                            </td>';
                    $created_at = webManagerLib::dateTimeToYMD($popup->created_at);
                    $tdCreated_at = '<td class="delayShowTime column-delay_show_time" data-colname="Delay Show">
                                        <span>'.$created_at.'</span>
                                    </td>';

                    $shortCode = '[wmPopup popup_id="'.$popup->popup_id.'"]';
                    $tdShortCode = '<td><code>'.$shortCode.'</code></td>';

                    $tr = '<tr class="trPopupItem" data-popup_id="'.$popup_id.'">
                            <th scope="row" class="check-column">
                                <label class="screen-reader-text" for="cb-select-'.$form_id.'">Chọn $title</label>
                                <input id="cb-select-'.$form_id.'" type="checkbox" name="forms[]" value="'.$form_id.'">
                                <div class="locked-indicator">
                                    <span class="locked-indicator-icon" aria-hidden="true"></span>
                                    <span class="screen-reader-text">“'.$title.'” đã bị khóa</span>
                                </div>
                            </th>
                            '. $tdTitle . $tdForm . $tdDelay_show_time . $tdCreated_at . $tdShortCode .'
                        </tr>';

                    echo $tr;
                } ?>
            </tbody>
        </table>
    </form>
</div>

<?php elseif (in_array($popupPage, ["popupUpdate","popupNew"])) :
    $pageDataDetail = [
        'popupNew' => array(
            'heading' => "Popup Mới",
            'popupId' => "popupNewItem",
            "submitText" => "Tạo Ngay"
        ),
        'popupUpdate' => array(
            'heading' => "Cập nhật Popup",
            'popupId' => "popupUpdateItem",
            "submitText" => "Cập Nhật"
        )
    ];


$hasPopup = false;
$popup_id = isset($_REQUEST['popup_id']) && (int)$_REQUEST['popup_id'] > 0 ? $_REQUEST['popup_id'] : false;
$popup = false;
if ($popup_id)
    webManagerLib::wmReadPopup($popup_id, function ($err, $result) use (&$popup, &$hasPopup) {
        if (!$err && $result)
            $hasPopup = true;
        $popup = $result;
    });
$popupId = $popupTitle = $popupBGImage = $form_id = $popupContent = $delay_show_time = $direction_background = "";
if ($popupPage == 'popupUpdate' && $hasPopup) {
    $popupId = $popup->popup_id;
    $popupTitle = $popup->title;
    $popupBGImage = !is_null($popup->bg_image_id) ? $popup->bg_image_id/*wp_get_attachment_image_src( $popup->bg_image_id, 'normal', false )[0]*/ : "";
    $popupFormId = $popup->form_id;
    $popupContent = !is_null($popup->content) ? $popup->content : "";
    $popupDelayShowTime = !is_null($popup->delay_show_time) ? $popup->delay_show_time : "";
    $popupDirectionBackground = !is_null($popup->direction_background) ? $popup->direction_background : "" ;
}

?>
<div class="wmAdminWrap wrap" data-page-active="<?php echo $popupPage ?>">
    <h1 class="wp-heading-inline"><?php echo $pageDataDetail[$popupPage]['heading']; ?></h1>

    <a href="<?php echo $pluginPageUrl . '&currentPage=popupList' ?>" id="backToListPopupBtn" class="page-title-action">Tất cả Popup</a>
    <form id="<?php echo $pageDataDetail[$popupPage]['popupId']; ?>" class="popupForm" method="post" action="">
        <input type="hidden" name="popup_id" value="<?php echo $popupId ?>">
        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content" style="position: relative;">
                    <div id="titlediv">
                        <div id="titlewrap">
                            <label class="" id="title-prompt-text" for="title"></label>
                            <input type="text" name="title" value="<?php echo $popupTitle ?>" id="title">
                        </div>
                        <div class="inside">
                            <div id="edit-slug-box" class="hide-if-no-js">
                            </div>
                        </div>
                    </div>

                    <?php
                    $content = $popupContent;
                    $editor_id = 'content';
                    wp_editor( $content, $editor_id , array(
//                            'media_buttons' => false,
                            'textarea_rows' => 7
                        )
                    );
                    ?>
                </div>

                <div id="postbox-container-1" class="postbox-container">
                    <div id="side-sortables" class="meta-box-sortables ui-sortable">

                        <div id="popup_bg_image_id" class="postbox" style="display: block;">
                            <button type="button" class="handlediv" aria-expanded="true">
                                <span class="screen-reader-text">Chuyển đổi bảng điều khiển: Background Image</span>
                                <span class="toggle-indicator" aria-hidden="true"></span>
                            </button>
                            <h2 class="hndle ui-sortable-handle">
                                <span>Background</span>
                            </h2>
                            <div class="inside">
                                <p class="hide-if-no-js">
                                    <?php echo webManagerLib::wm_image_uploader_field('bg_image_id', $popupBGImage); ?>
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
                                    <input style="width: 100%" name="direction_background" type="text" value="<?php echo $popupDirectionBackground ?>" class="" placeholder="http://example.vn">
                                </div>
                            </div>
                        </div>

                        <div id="popup_submit_wrap" class="postbox">
                            <button type="button" class="handlediv" aria-expanded="true">
                                <span class="screen-reader-text">Chuyển đổi bảng điều khiển: Lưu Lại</span>
                                <span class="toggle-indicator" aria-hidden="true"></span>
                            </button>
                            <h2 class="hndle ui-sortable-handle">
                                <span>Lưu Lại</span>
                            </h2>
                            <div class="inside">
                                <div>
                                    <button type="submit" name="submit" id="submit" class="button button-primary"><?php echo $pageDataDetail[$popupPage]['submitText'] ?></button>
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
                                            $selected = $popupFormId == $form_id ? "selected" : "";
                                            $title = $form->title;
                                            $options .= '<option value="'.$form_id.'" '.$selected.'>'.$title.' => (FormID : '.$form_id.')</option>';
                                        }
                                        ?>
                                        <tr>
                                            <th><span>Form đăng kí</span></th>
                                            <td>
                                                <select name="form_id" id="">
                                                    <option value="0">Không</option>
                                                    <?php echo $options; ?>
                                                </select>
                                            </td>
                                        </tr>
                                    <?php endif;

                                    // Delay show time
                                    $hasDelay = (int)$popupDelayShowTime > 0;
                                    ?>
                                    <tr>
                                        <th><span>Hiển thị</span></th>
                                        <td>
                                            <label for="isAuto">
                                                <input name="isAuto" type="checkbox" id="isAuto" <?php echo $hasDelay ? "checked" : "" ?>> Tự động Sau
                                            </label>
                                            <input name="delay_show_time" type="number" min="0" value="<?php echo $popupDelayShowTime  ?>" placeholder="Mặc định là 0" class="small-text"> Giây
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
