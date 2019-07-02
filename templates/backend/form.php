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
                                <span>Tên Form</span><span class="sorting-indicator"></span>
                            </a>
                        </th>
                        <th scope="col" id="toCareSoftNow" class="manage-column column-toCareSoftNow">Tiêu đề CareSoft</th>
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
            <form id="<?php echo $pageDataDetail[$formPage]['formId']; ?>" class="webManagerForm" method="post" action="">
                <input type="hidden" name="form_id" value="">
                <table id="" class="form-table">
                    <tr>
                        <th><span>Tên Form</span></th>
                        <td>
                            <input name="name" type="text" value="" placeholder="Nhập tên form" class="regular-text" placeholder="">
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
                   <!--  <tr>
                        <th><span>ID CareSoft</span></th>
                        <td><input name="caresoft_id" maxlength="5" type="text" value="" class="small-text" placeholder="ID nguồn lead CareSoft hoặc để trống trống"></td>
                    </tr> -->
                    <tr>
                        <th><span>Tiêu đề CareSoft</span></th>
                        <td>
                            <input name="title" type="text" value="" class="regular-text" placeholder="VD: SEO">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><span>Mặc Định</span></th>
                        <td>
                            <fieldset>
                                <label for="">
                                    Nguồn Phiếu
                                    <select id="nguon_phieu" name="nguon_phieu" class="mr-3">
                                        <option value="">   Bỏ Qua   </option>
                                        <option value="41890">182 - Quảng cáo</option>
                                        <option value="41893">188 - Social</option>
                                        <option value="41896">173 - SEO</option>
                                        <option value="41899">179 - Đối tác</option>
                                        <option value="41902">176 - Offline</option>
                                        <option value="41905">186 - CSKH</option>
                                        <option value="42106">175 - Hotline</option>
                                        <option value="42109">174 - Livechat</option>
                                        <option value="42178">187 - TVOL</option>
                                    </select>
                                    Chi Tiết Nguồn Phiếu
                                    <select id="chi_tiet_nguon_phieu" name="chi_tiet_nguon_phieu" class="">
                                        <option value="">   Bỏ Qua   </option>
                                        <option value="42112">Chưa phân loại</option>
                                        <option value="42115">MB</option>
                                        <option value="42118">MT</option>
                                        <option value="42121">MN</option>
                                        <option value="42124">HCM</option>
                                        <option value="41920">SEO 1</option>
                                        <option value="41923">SEO 2 SMS</option>
                                        <option value="41926">Criteo</option>
                                        <option value="41929">T04</option>
                                        <option value="41932">T05</option>
                                        <option value="41935">T06</option>
                                        <option value="42490">T07</option>
                                        <option value="44116">KOLs</option>
                                        <option value="44119">Khác</option>
                                    </select>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    <!--<tr>
                        <th><span>Nguồn phiếu</span></th>
                        <td>
                            <select style="    width: 35%;"  id="nguon_phieu" name="nguon_phieu" class="">
                            <option value="">Vui lòng chọn Nguồn phiếu ghi</option>
                                    <option value="41890">182 - Quảng cáo</option>
                                    <option value="41893">188 - Social</option>
                                    <option value="41896">173 - SEO</option>
                                    <option value="41899">179 - Đối tác</option>
                                    <option value="41902">176 - Offline</option>
                                    <option value="41905">186 - CSKH</option>
                                    <option value="42106">175 - Hotline</option>
                                    <option value="42109">174 - Livechat</option>
                                    <option value="42178">187 - TVOL</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><span>Chi tiết nguồn phiếu</span></th>
                        <td>
                            <select style="    width: 35%;" id="chi_tiet_nguon_phieu" name="chi_tiet_nguon_phieu" class="">
                                <option value="">Vui lòng chọn chi tiết Nguồn phiếu ghi</option>
                                <option value="42112">Chưa phân loại</option>
                                <option value="42115">MB</option>
                                <option value="42118">MT</option>
                                <option value="42121">MN</option>
                                <option value="42124">HCM</option>
                                <option value="41920">SEO 1</option>
                                <option value="41923">SEO 2 SMS</option>
                                <option value="41926">Criteo</option>
                                <option value="41929">T04</option>
                                <option value="41932">T05</option>
                                <option value="41935">T06</option>
                                <option value="42490">T07</option>
                                <option value="44116">KOLs</option>
                                <option value="44119">Khác</option>
                            </select>
                        </td>
                    </tr>-->
                    <tr>
                        <th scope="row">Nâng Cao</th>
                        <td>
                            <!--<fieldset>
                                <legend class="screen-reader-text"><span>CareSoft Detail</span></legend>
                                <label for="has_detail_CareSoft">
                                    <input type="checkbox" id="has_detail_CareSoft" value="1">
                                    Phân Loại Chi Tiết
                                </label>
                            </fieldset>-->
                            <div class="advanced_setting_caresoft_wrap">
                            </div>
                            <button type="button" id="addCondition" class="button button-default">
                                Thêm điều kiện
                            </button>
                        </td>
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