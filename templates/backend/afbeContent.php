<?php

$update = webManagerLib::abContentHandler($_REQUEST);

$oldContent = webManagerLib::getABContentItem();
$method = $_SERVER['REQUEST_METHOD'];
if (strtolower($method)  == "post") {
    $update = webManagerLib::abContentHandler($_REQUEST);
    if ($update) {
        wp_redirect($_SERVER['HTTP_REFERER']);
    } else {
        $errorHTML = "<div class=\"update-nag\" style='border-left-color: red'>
            Không thể cập nhật nội dung , vui lòng kiểm tra lại
        </div>";
    }
}
?>

<style>
    .CodeMirror {
        border: 1px solid #ddd;
    }
</style>


<div class="wmAdminWrap wmActive wrap" data-page-active="">
    <h1 class="wp-heading-inline mb-5">After Before Content</h1>
    <hr class="wp-header-end">
    <div class="container-fluid">
        <form action="" method="post">
            <h5>Thêm HTML</h5>
            <div class="form-group">
                <textarea name="content" id="wm-code-html-textarea" class="form-control"><?php if($oldContent && $oldContent->content) : echo $oldContent->content; endif; ?></textarea>
            </div>
            <div class="form-group">
                <?php
                $location = $oldContent && is_array($oldContent->location) ? $oldContent->location : [];
                ?>
                <label class="checkbox-inline mr-2"><input name="location[]" type="checkbox" value="top" <?php if(in_array("top",$location)) : echo "checked" ;endif; ?>>Đặt Trên bài Viết</label>
                <label class="checkbox-inline"><input name="location[]" type="checkbox" value="bottom" <?php if(in_array("bottom",$location)) : echo "checked" ;endif; ?>>Đặt Dưới bài Viết</label>
            </div>
            <div class="form-group">
                <button type="submit" class="button button-primary button-large">
                    <span>Lưu Lại</span>
                </button>
            </div>
        </form>
    </div>
</div>


<script>
    jQuery(document).ready(function($) {
        // wp.codeEditor.initialize($('#wm-code-css-textarea'), _wm.css);
        // wp.codeEditor.initialize($('#wm-code-js-textarea'), _wm.js);
        wp.codeEditor.initialize($('#wm-code-html-textarea'), _wm.html);

    })
</script>