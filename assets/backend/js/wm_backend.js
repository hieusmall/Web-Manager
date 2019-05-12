(function ($) {
    var wmBags = {};

    wmBags.pluginPageUrl = $("#pluginPageUrl").data("pluginPageUrl");

    wmBags.acceptPage = ['formList', 'formNew', 'formUpdate', 'formDelete','popupList'];

    wmBags.getPageActive = (page) => {
        var $allPageWraps = $(document).find('.wmAdminWrap');
        var active = false;
        page = typeof page == "string" ? page : false;
        if (page) {
            $allPageWraps.each(function (i, wrap) {
                var $wrap = $(wrap);
                var pageCheck = $wrap.data('pageActive');
                if (page != pageCheck) {
                    $wrap.removeClass('wmActive');
                } else {
                    $wrap.addClass('wmActive');
                }
            });
            return page;
        }

        /*$allPageWraps.each(function (i, wrap) {
            var done = i == $allPageWraps.length - 1;
            var $wrap = $(wrap);
            var acceptPage = wmBags.acceptPage;
            var pageActive = $wrap.data('pageActive');
            if ($wrap.hasClass('wmActive')) {
                $wrap.show();
                active = typeof pageActive == "string" && acceptPage.includes(pageActive) ? pageActive : false;
            } else {
                $wrap.hide();
            }
        });
        return active;*/
        var $adminWrap = $(document).find('.wmAdminWrap');
        if ($adminWrap) {
            var acceptPage = wmBags.acceptPage;
            var pageActive = $adminWrap.data('pageActive');
            pageActive = typeof pageActive == "string" && acceptPage.includes(pageActive) ? pageActive : false;
            return pageActive;
        } else {
            alert("Không tìm thấy trang này");
            return false;
        }
    }

    wmBags.loadDataOnPage = () => {
        var page = wmBags.getPageActive();
        var accept = wmBags.acceptPage;
        page = typeof page == "string" && accept.includes(page) ? page : false;
        if (!page) {
            alert("Không thấy page này");
        } else {
            var fn = page + 'Page';
            wmBags[fn]();
        }
        return false;
    }

    wmBags.formListPage = () => {
        var $formListTable = $(".wmListFormTable");
        var $tbody = $formListTable.find('tbody');

        // Ajax get all the form
        var options = {
            type: 'get',
            data: {
                action: 'listForm'
            }
        };
        wmBags.jsonTransPortData(options, function (err, res) {
            if (!err && res) {
                var forms = ['object','array'].includes(typeof res.data) ? res.data : [];
                makeDataToTable(forms);
            } else {
                alert(err);
            }
        });

        function makeDataToTable(forms) {
            $tbody.html("");
            var pluginPageUrl = wmBags.pluginPageUrl;
            forms.forEach(function (formData) {
                var {form_id, title, directional, to_caresoft_now, created_at, caresoft_id} = formData;
                var shortcode = `[wpForm form_id="${form_id}"]`;
                var tr = `<tr class="trformItem" data-form_id="${form_id}">
                                <th scope="row" class="check-column">			
                                <label class="screen-reader-text" for="cb-select-${form_id}">Chọn ${title}</label>
                                    <input id="cb-select-${form_id}" type="checkbox" name="forms[]" value="${form_id}">
                                    <div class="locked-indicator">
                                        <span class="locked-indicator-icon" aria-hidden="true"></span>
                                        <span class="screen-reader-text">“${title}” đã bị khóa</span>
                                    </div>
                                </th>
                                <td>
                                    <strong><a id="detailFormByTitleBtn" class="row-title" href="#" aria-label="${title}">${title}</a></strong>
                                    <div class="row-actions">
                                        <span class="edit">
                                            <a class="updateFormItem" href="#" aria-label="Sửa “${title}”">Chỉnh sửa</a> | 
                                        </span>
                                        <span class="trash">
                                            <a class="deleteFormItem" href="#" class="submitdelete" aria-label="Bỏ “${title}” vào thùng rác">Xóa</a> | 
                                        </span>
                                    </div>
                                </td>
                                <td>${to_caresoft_now.toUpperCase()}</td>
                                <td>${directional ? directional : "Không"}</td>
                                <td>${new Date(created_at)}</td>
                                <td><code>${shortcode}</code></td>
                            </tr>`;
                $tbody.append(tr);
            });

            var $trs = $tbody.find('tr.trformItem');
            $trs.off('click');
            $trs.on('click', function (e) {
                var target = e.target;
                var $target = $(target);
                var $tr = $(target).closest('tr');
                var form_id = $tr.data('form_id');

                if ($target.hasClass('updateFormItem')) {
                    window.location = `${pluginPageUrl}&currentPage=formUpdate&form_id=${form_id}` ;
                }
            })
        }

        // Add event new form
        /*$('#newFormBtn').off('click');
        $('#newFormBtn').on('click', function (e) {
            e.preventDefault(e);
            wmBags.getViewHtml('formNew');
        });*/
    }

    wmBags.formNewPage = () => {
        // Listen form submit
        var $form = $("#formNewItem");
        $form.on('submit', function (e) {
            e.preventDefault(e);
            var formData = {};
            var $inputs = $form.find(':input');
            $inputs.each(function (i, field) {
                var $field = $(field);
                var {name, type, disabled, value} = field;
                if (['submit', 'button'].includes(type)) return;
                if (name) {
                    formData[name] = $field.val();
                }
            });

            var a = Object.keys(formData).length > 0 ? true : false;
            if (a) {
                var options = {
                    type: 'post',
                    data: {
                        action: 'newForm',
                        form : formData
                    }
                }
                wmBags.jsonTransPortData(options, function (err, res) {
                    if (!err && res) {
                        var pluginPageUrl = $("#pluginPageUrl").data("pluginPageUrl");
                        window.location = `${pluginPageUrl}&currentPage=formList`;
                    } else {
                        alert("Không thể tạo form này");
                        return false
                    }
                });
            } else {
                alert("Rỗng");
            }
        })

        // Back to List Form
        /*var $backToListFormBtn = $('#backToListFormBtn');
        $backToListFormBtn.on('click', function (e) {
            e.preventDefault(e);
            wmBags.getViewHtml('formList');
        });*/
    };

    wmBags.formUpdatePage = () => {

    }

    wmBags.getViewHtml = (view) => {
        var b = wmBags.getPageActive(view);
        if (b) {
            wmBags.loadDataOnPage();
        } else {
            alert("Không tìm thấy page này đâu !");
        }
    }

    wmBags.checkThisPagePlugin = () => {
        var search = typeof window.location.search == "string" && window.location.search.trim().length > 0 ? window.location.search : false;
        if (!search) return false;
        var query = wmBags.queryStringToObj(search);
        if (query.hasOwnProperty('page') && query.page == 'webManagerForm')
            return true;
        return false;
    }

    wmBags.init = () => {
        var check = wmBags.checkThisPagePlugin();
        if (check)
            wmBags.loadDataOnPage();
        return false;
    }

    wmBags.queryStringToObj = () => {
        var search = location.search.substring(1);
        var x = JSON.parse('{"' + decodeURI(search).replace(/"/g, '\\"').replace(/&/g, '","').replace(/=/g,'":"') + '"}');
        return x;
    }

    /**
     * @function jsonTransPortData
     * @param type
     * @param data
     * @param callback
     */
    wmBags.jsonTransPortData = ({type, data}, callback) => {
        var { origin, pathname } = window.location;
        var url = `${origin + pathname}`;
        url = 'https://localhost/ngocdung/beta.thammyvienngocdung.com/public_html/wp-admin/admin-ajax.php';

        $.ajax({
            type : type, //Phương thức truyền post hoặc get
            dataType : "json", //Dạng dữ liệu trả về xml, json, script, or html
            url : url, //Đường dẫn chứa hàm xử lý dữ liệu. Mặc định của WP như vậy
            data : data,
            success: function(response) {
                callback(false, response);
            },
            error: function( jqXHR, textStatus, errorThrown ){
                console.log(jqXHR)
                //Làm gì đó khi có lỗi xảy ra
                console.log( 'The following error occured: ' + textStatus, errorThrown );
                callback("Có lỗi xảy ra");
            }
        });
    }

    $(document).ready(function () {
        wmBags.init();
    });
})(jQuery);