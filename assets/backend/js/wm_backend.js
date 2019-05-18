(function ($) {
    var wmBags = {};

    wmBags.pluginPageUrl = $("#pluginPageUrl").data("pluginPageUrl");

    wmBags.acceptPage = ['formList', 'formNew', 'formUpdate',
        'popupList','popupNew','popupUpdate',
    'ticketList','ticketNew','ticketUpdate'];

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

    wmBags.ticketListPage = () => {
        alert(123);
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
            var forms = false;

            if (!err && res) {
                forms = ['object','array'].includes(typeof res.data) ? res.data : [];
            }
            makeDataToTable(forms);
        });

        function makeDataToTable(forms) {
            forms = typeof forms == 'object' && forms instanceof Array && forms.length > 0 ? forms : [];
            $tbody.html("");
            var pluginPageUrl = wmBags.pluginPageUrl;
            forms.forEach(function (formData) {
                var {form_id, title, directional, to_caresoft_now, created_at, caresoft_id} = formData;
                var shortcode = `[wmForm form_id="${form_id}"]`;
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

                // If this action is update form
                if ($target.hasClass('updateFormItem')) {
                    e.preventDefault(e);
                    window.location = `${pluginPageUrl}&currentPage=formUpdate&form_id=${form_id}` ;
                }

                // If this action is delete form
                if ($target.hasClass("deleteFormItem")) {
                    e.preventDefault(e);

                    var cf = confirm("Bạn có muốn xóa form này ~");
                    if (cf) {
                        var options = {
                            type: "get",
                            data: {
                                action: "deleteForm",
                                form_id: form_id
                            }
                        }

                        wmBags.jsonTransPortData(options, function (err, res) {
                            if (!err && res) {
                                wmBags.formListPage();
                            } else {
                                alert("Không thể xóa form này");
                            }
                        })
                    } else {
                        return false;
                    }
                };
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
        // setup fb-editor
        var $formEditor = $("fb-editor");
        $formEditor.html("");
        var formBuilder = wmBags.formBuilder('fb-editor', {
            disabledActionButtons: [/*'data'*/,'save'],
            /*actionButtons: [
                {
                    id: 'saveFormCustom',
                    className: 'btn btn-primary',
                    label: 'Lưu lại',
                    type: 'button',
                    events: {
                        click: saveCustomForm
                    }
                },
            ],*/
            controlOrder: [
                'text',
                'textarea',
                'checkbox',
                'number',
                'button'
            ],
            formData: [
                {
                    "type": "text",
                    "required": true,
                    "label": "Họ Tên",
                    "placeholder": "Tên của bạn",
                    "className": "form-control",
                    "name": "name",
                    "subtype": "text",
                    "maxlength": "200"
                },
                {
                    "type": "text",
                    "subtype": "tel",
                    "required": true,
                    "label": "Số Điện Thoại",
                    "placeholder": "Số điện thoại của bạn là",
                    "className": "form-control",
                    "name": "phone",
                    "maxlength": "15"
                },
                {
                    "type": "text",
                    "subtype": "email",
                    "label": "Email",
                    "placeholder": "vd : example@email.com",
                    "className": "form-control",
                    "name": "email",
                    "maxlength": "200"
                },
                {
                    "type": "textarea",
                    "required": true,
                    "label": "Lời nhắn",
                    "placeholder": "Bạn quan tâm đến dịch vụ nào của chúng tôi",
                    "className": "form-control",
                    "name": "note",
                    "subtype": "textarea",
                    "maxlength": "500",
                    "rows": "3"
                }
            ]
        });

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

            var customFormTemplate = formBuilder.actions.getData();
            formData.form_custom_template = customFormTemplate;

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
        // setup fb-editor
        var $formEditor = $("#fb-editor");
        $formEditor.html("");
        var formBuilder = wmBags.formBuilder('fb-editor', {
            disabledActionButtons: ['data','save'],
            controlOrder: [
                'text',
                'textarea',
                'checkbox',
                'number',
                'button'
            ],
        });

        // Load data to form input
        var $form = $("#formUpdateItem");
        var form = $form.get(0);
        var $inputs = $form.find(':input');
        var query = typeof window.location.search == "string" && window.location.search.trim().length > 0 ? wmBags.queryStringToObj(window.location.search) : false;
        if (!query) {
            alert("Không tìm thấy page nài roài");
        } else {
            if (query.hasOwnProperty("form_id") && query.form_id) {
                var {form_id} = query;
                var options = {
                    type: 'get',
                    data: {
                        action: 'readForm',
                        form_id: form_id
                    }
                };
                wmBags.jsonTransPortData(options, function (err, res) {
                    var {success, data} = typeof res == "object" ? res : {};
                    if (!err && success && data) {
                        var insertData = wmBags.insertDataToForm(form, data);
                        if (insertData) {
                            var {form_custom_template} = data;
                            form_custom_template = typeof form_custom_template == "object" && form_custom_template instanceof Array  ? form_custom_template : [];
                            formBuilder.actions.setData(form_custom_template)
                        } else {
                            alert("Không thể thêm dữ liệu vào form");
                        }
                    } else {
                        alert("Không tìm thấy form này");
                    }
                });

                $form.on("submit", function (e) {
                    e.preventDefault(e);
                    var formData = {};
                    $inputs.each(function (i, field) {
                        var {name, type, disabled, value} = field;

                        if (["button", "submit"].includes(type) || disabled) {
                            return
                        }

                        if (name && value) {
                            formData[name] = $(field).val();
                        }
                    });

                    var customFormTemplate = formBuilder.actions.getData();
                    formData.form_custom_template = customFormTemplate;

                    var options = {
                        type: "post",
                        data: {
                            action: "updateForm",
                            form : formData
                        }
                    };
                    wmBags.jsonTransPortData(options, function (err, res) {
                        var {success, data} = typeof res == "object" ? res : {};
                        if (!err && success && data) {
                            var urlRidirect = wmBags.pluginPageUrl;
                            window.location.href = `${urlRidirect}&currentPage=popupList`
                        } else {
                            alert("Cannot update this form");
                        }
                    });
                })
            } else {
                alert("missing required field");
            }
        }
        return false;
    }

    wmBags.popupListPage = () => {
        var $wrap = $(".wmAdminWrap");
        var $table = $wrap.find(".wmListFormTable");
        var $tbody = $table.find('tbody');
        var $trs = $tbody.find('tr.trPopupItem');
        $trs.off('click');
        $trs.on('click', function (e) {
            var target = e.target;
            var $target = $(target);
            var $tr = $(target).closest('tr');
            var popup_id = $tr.data('popup_id');

            // If this action is update form
            if ($target.hasClass('updatePopupItem')) {
                e.preventDefault(e);
                var pluginPageUrl = wmBags.pluginPageUrl,
                    {popup_id} = $(this).closest('tr.trPopupItem').data();
                if (popup_id)
                    window.location = `${pluginPageUrl}&currentPage=popupUpdate&popup_id=${popup_id}`;
                else
                    alert("Không tìm thấy popup này!");
                return false;
            }

            // If this action is delete form
            if ($target.hasClass("deletePopupItem")) {
                e.preventDefault(e);
                var cf = confirm("Bạn có muốn xóa popup này ~");
                if (cf) {
                    var options = {
                        type: "post",
                        data: {
                            action: "deletePopup",
                            popup_id: popup_id
                        }
                    }
                    wmBags.jsonTransPortData(options, function (err, res) {
                        var {success, data} = typeof res == "object" ? res : {};
                        if (!err && success && data) {
                            // wmBags.popupListPage();
                            window.location.reload();
                        } else {
                            alert("Không thể xóa popup này");
                        }
                    })
                } else {
                    return false;
                }
            };
        })

        /*var $updateBtn = $wrap.find(".updatePopupItem");
        $updateBtn.off('click');
        $updateBtn.on('click', function (e) {

        });

        var $deleteBtn = $wrap.find(".deletePopupItem");
        $deleteBtn.off('click');
        $deleteBtn.on('click', function (e) {

        });*/
    }

    wmBags.popupNewPage = () => {
        wmBags.listenUploadMedia();
        var $form = $(".wmAdminWrap").find("form.popupForm");

        $form.off('submit');
        $form.on('submit', function (e) {
            var form = this,
                $form = $(form);

            var submitAction = {
                'popupUpdateItem': updatePopupByForm,
                'popupNewItem' : newPopupByForm
            }

            var formId = $form.attr("id");
            formId = typeof formId == "string" && Object.keys(submitAction).includes(formId) ? formId : false;
            if (formId) {
                e.preventDefault(e);
                var formData = wmBags.getObjFormData(form);
                submitAction[formId](formData);
            }

            // Else return false
            return false;
        });


        function newPopupByForm(data) {
            var options = {
                type: "post",
                data: {
                    action: "newPopup",
                    popup: data
                }
            }
            wmBags.jsonTransPortData(options, function (err, res) {
                var {success, data} = typeof res == "object" ? res : {};
                if (!err && success && data) {
                    var hrefListPopup = wmBags.pluginPageUrl,
                        url = `${hrefListPopup}&currentPage=popupList`;
                    window.location = url;
                } else {
                    alert("Không thể tạo mới popup");
                }
            });
        }
        function updatePopupByForm(data) {
            var {isAuto, delay_show_time} = data;
            if (!isAuto) {
                delete data.delay_show_time;
            }

            var options = {
                type: "post",
                data: {
                    action: "updatePopup",
                    popup: data
                }
            };

            wmBags.jsonTransPortData(options, function (err, res) {
                var {success, data} = typeof res == "object" ? res : {};
                if (!err && success && data) {
                    var hrefListPopup = wmBags.pluginPageUrl,
                        url = `${hrefListPopup}&currentPage=popupList`;
                    window.location = url;
                } else {
                    alert("Không thể cập nhật popup");
                }
            });
        }


        //
        $('body .meta-box-sortables.ui-sortable').sortable();
    }

    wmBags.popupUpdatePage = () => {
        wmBags.popupNewPage();
    };

    wmBags.listenUploadMedia = () => {
        // idElem = typeof idElem == "string" && idElem.trim().length > 0 ? idElem : '.wm_upload_image_button';
        /*
         * Select/Upload image(s) event
         */
        $('body').on('click', '.wm_upload_image_button', function(e){
            e.preventDefault();

            var button = $(this),
                custom_uploader = wp.media({
                    title: 'Background Image',
                    library : {
                        // uncomment the next line if you want to attach image to the current post
                        // uploadedTo : wp.media.view.settings.post.id,
                        type : 'image'
                    },
                    button: {
                        text: 'Use this image' // button label text
                    },
                    multiple: false // for multiple image selection set to true
                }).on('select', function() { // it also has "open" and "close" events
                    var attachment = custom_uploader.state().get('selection').first().toJSON();
                    $(button).removeClass('button')
                        .html('<img class="wm_true_pre_image_upload" src="' + attachment.url + '" style="display:block;width: 100%;margin-bottom: 10px;" />')
                        .next().val(attachment.id).next().show();
                    /* if you sen multiple to true, here is some code for getting the image IDs
                    var attachments = frame.state().get('selection'),
                        attachment_ids = new Array(),
                        i = 0;
                    attachments.each(function(attachment) {
                         attachment_ids[i] = attachment['id'];
                        console.log( attachment );
                        i++;
                    });
                    */
                }).open();
        });

        /*
         * Remove image event
         */
        $('body').on('click', '.wm_remove_image_button', function(){
            $(this).hide().prev().val('').prev().addClass('button').html('Upload image');
            return false;
        });
    }

    wmBags.getObjFormData = (form) => {
        var $form = $(form)
        var $inputs = $form.find(':input'),
            formData = {};

        function get_tinymce_content(id) {
            var content;
            var inputid = id;
            var editor = tinyMCE.get(inputid);
            var textArea = jQuery('textarea#' + inputid);
            if (textArea.length>0 && textArea.is(':visible')) {
                content = textArea.val();
            } else {
                content = editor.getContent();
            }
            return content;
        }

        $inputs.each(function (i, field) {
            var {type, name, disabled} = field;
            if (["submit","button"].includes(type) || disabled) return;

            if (name) {
                if ($(field).hasClass('wp-editor-area')) {
                    try {
                        formData[name] = get_tinymce_content(field.id);
                    } catch (e) {
                        formData[name] = $(field).val()
                    }
                    return;
                }

                if (type == 'checkbox') {
                    formData[name] = $(field).prop("checked");
                    return;
                }

                formData[name] = $(field).val();
            }
        });

        return formData;
    }

    wmBags.insertDataToForm = (form, data) => {
        form = typeof form == 'object' && form.tagName.toLowerCase() == 'form' ? form : false;
        data = typeof data == 'object' && data instanceof Object && Object.keys(data).length > 0 ? data : false;
        // If missing require param

        if (!form && !data) return false;

        var $form = $(form);
        var $inputs = $form.find(':input');
        $inputs.each(function (i, field) {
            var {name, type, value, disabled} = field;

            if (["button","submit"].includes(type) || disabled) return false;
            if (name && data.hasOwnProperty(name)) {
                $(field).val(data[name]);
            }
        });
        return true;
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
        if (query.hasOwnProperty('page') && ['webManagerForm','webManagerPopup','webManagerTicket'].includes(query.page))
            return true;
        return false;
    }

    wmBags.init = () => {
        var check = wmBags.checkThisPagePlugin();
        if (check)
            wmBags.loadDataOnPage();
        window.wmBags = wmBags;

        console.log(check);
        return false;


        return false;
    }

    wmBags.formBuilder = (id, options = null) => {
        var element = typeof id == "string" && document.getElementById(id) ? document.getElementById(id) : false;
        var options = typeof options == "object" ? options : {};
        if (element)
            return $(element).formBuilder = $(element).formBuilder(options);
        


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
        var { protocol,origin, hostname,pathname } = window.location;
        var url = `${origin}/wp-admin/admin-ajax.php`;
        if (hostname == "localhost") {
            url = `${origin}${pathname.replace("admin.php", "admin-ajax.php")}`;
        }
        // var path = '/wp-admin/admin-ajax.php';
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