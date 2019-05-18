(function($){
    // Define the client;
    var client = {};


    client.lead = false;

    /**
     * @function listenPopupHandler
     */
    client.listenPopupHandler = () => {
        var $popups = $('.wm-campaign-popup');
        var $showPopupBtns = $('.wmCampaignShowPopup/*, .popup-link.action_button*/');
        // $showPopupBtns.off("click");
        $showPopupBtns.on('click', function (e) {
            e.preventDefault(e);
            var button = this;
            var $b = $(button);
            var buttonData = $b.data();
            var {popupName} = buttonData;
            $popups.hide();
            if (!popupName) {
                popupName = typeof $b.attr('href') == "string" ? $b.attr('href').replace("#","") : popupName;
            }

            $popups.each(function (p, popup) {
                var $popup = $(popup);
                var {directional, delayShowTime, popupId} = $popup.data();
                if (popupName != $popup.attr('id')) {
                    console.log("Không tìm thấy chiến dịch!");
                    return;
                }
                $popup.modal('show');
                return false;
            });
        });

        $popups.each(function (i, popup) {
            var $popup = $(popup);
            var {directional, delayShowTime, popupId} = $popup.data();
            var $popupContent = $popup.find('.wm-popup-content');
            var $popupBody = $popupContent.find('.wm-popup-body');

            $popup.hide();

            popupId = typeof popupId == 'number' && parseInt(popupId) > 0 ? popupId : false ;
            delayShowTime = typeof delayShowTime == 'number' && parseInt(delayShowTime) > 0 ? delayShowTime * 1000 : false;
            directional  = typeof directional == 'string' && client.validURL(directional) ? directional : false;

            if (directional) {
                $popupBody.wrap(`<a href="${directional}" target="_blank" style="display: block;width: 100%;height: 100%;"></a>`)
            }

            // Set delaytime
            if (!delayShowTime) {
                return;
            } else {
                setTimeout(function () {
                    $popup.modal("show");
                }, delayShowTime);
            }
        });
    }

    /**
     * @function listenFormSubmit
     */
    client.listenFormSubmit = () => {
        var $forms = $('.wm-campaign-form');
        $forms.each(function (k, form) {
            var $form = $(form);
            var $elements = $form.find(':input');

            var {directional,form_id} = $form.data();

            // Create formElemnts
            /*var options = {
                type: 'get',
                data :{
                    'action': 'readForm',
                    'form_id': form_id
                }
            }
            client.jsonTransPortData(options, function (err, res) {
                var {success, data} = res;
                if (!err && success && data) {
                    var {form_custom_template} = data;
                    form_custom_template = typeof form_custom_template == "object" && form_custom_template instanceof Array ? form_custom_template : false;
                    var $newForm = $form.clone();
                    // var elementStr = '';
                    if (form_custom_template) {
                        // $form.after($newForm.html(""));
                        form_custom_template.forEach(function (inputObj) {
                            var {className, label, maxlength, name, placeholder, required, subtype, type} = inputObj;
                            var elem = "<input />";
                            if (['text','checkbox','number','email'].includes(type)) {
                                elem = "<input />";
                            } else if (type == 'textarea') {
                                elem = "<textarea />";
                            } else if (type == 'button') {
                                elem = "<button />";
                            }
                            var $field = $(elem).attr(inputObj);
                            $newForm.append($field)
                            console.log($newForm);
                            // $field.appendTo('');
                            $form.after($newForm);
                            $form.remove();
                        });
                        // $form.remove();
                        /!*$form.find(':input').each(function (o, input) {
                            var {type, name, id, value, disabled, required} = input;

                        })
                        console.log(form_custom_template, $newForm.find(':input'));*!/
                    } else {
                        return
                    }
                }
            });*/

            // Check required data element
            $elements.each(function (i, element) {
                var loopDone = i == $elements.length - 1;
                var {name, type,id, value, disabled, hidden} = element;
                var $element = $(element);

                /*// Disabled field
                if (['note'].includes(name)) {
                    $element.attr("disabled", true);
                    $element.closest(".form-group").hide();
                }*/

                // required field
                if (["name","phone"].includes(name)) {
                    $element.attr("required", true)
                    $element.closest('.form-group').find("label")
                        .append(`<span class="required">   *</span>`);
                }
            });

            $form.on('submit', function (e) {
                e.preventDefault();
                var $f = $(this);
                var $submitBtn = $f.find(".btnSubmit");
                // var snapShot = $f.children().clone();
                var ticket = $f.data();
                var $popup = $f.closest('.wm-campaign-popup');


                // Disabled button submit
                $submitBtn.attr('disabled', true);

                var $fields = $f.find(":input");
                $fields.each(function (i, field) {
                    // when The Loop done
                    var loopDone = i == $fields.length - 1;
                    if (loopDone) {
                        transPortTicket(ticket);
                    }
                    var $field = $(field);
                    var {type, disabled, required, value, name, id} = field;

                    if (['button', 'submit'].includes(type) || disabled ) {
                        return;
                    }

                    if (name) ticket[name] = $field.val();
                });

                function reduceThisForm() {
                    $fields.each(function (k, input) {
                        if (['button','submit'].includes(input.type))
                            return;

                        if (['text','number','textarea','email'].includes(input.type))
                            $(input).val("");

                    });
                }

                function transPortTicket(ticket) {
                    // add some optional data
                    var { href, origin, pathname, search } = window.location;
                    var detail = {
                        href : href,
                        origin: origin,
                        pathname: pathname,
                        search : search
                    };
                    detail.meta = client.metaTagsDataObj();
                    ticket.detail = detail;

                    var options = {
                        type: 'post',
                        data: {
                            action: 'newTicket',
                            ticket : ticket
                        }
                    };
                    client.jsonTransPortData(options, function (err, res) {
                        var {success, data} = typeof res == "object" ? res : {};
                        if (!err && success && data) {
                            var donePage = ticket.directional;
                            if (donePage) {
                                window.location = donePage;
                            } else {
                                alert("Đăng kí thành công");
                            }

                            reduceThisForm();
                            if ($popup.get(0)) {
                                $popup.modal('hide');
                            }
                        } else {
                            alert("Đăng kí thất bại");
                        }

                        $submitBtn.attr("disabled", false);
                        return false;
                    });
                }
            })
        })
    }

    /**
     * @function jsonTransPortData
     * @param type
     * @param data
     * @param callback
     */
    client.jsonTransPortData = ({type, data}, callback) => {
        var { protocol,origin, hostname,pathname } = window.location;
        var url = `${origin}/wp-admin/admin-ajax.php`;
        if (hostname == "localhost") {
            url = `${origin}${pathname.replace("admin.php", "admin-ajax.php")}`;
        }
        $.ajax({
            type : type, //Phương thức truyền post hoặc get
            dataType : "json", //Dạng dữ liệu trả về xml, json, script, or html
            url : url, //Đường dẫn chứa hàm xử lý dữ liệu. Mặc định của WP như vậy
            data : data,
            success: function(response) {
                callback(false, response);
            },
            error: function( jqXHR, textStatus, errorThrown ){
                //Làm gì đó khi có lỗi xảy ra
                console.log( 'The following error occured: ' + textStatus, errorThrown );
                callback("Có lỗi xảy ra");
            }
        });
    }

    client.validURL = (str) => {
        var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
            '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
            '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
            '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
            '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
            '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
        return !!pattern.test(str);
    }

    client.metaTagsDataObj = () => {
        var metaDetails = [];
        $('meta').each(function(i, meta) {
            var attributes = meta.attributes;
            var atts = {};
            for (var k = 0;k < attributes.length;k++) {
                var metaAtts = attributes[k];
                var $metaAtts = $(metaAtts);
                var obj = {};
                for (var j = 0;j < $metaAtts.length;j++) {
                    var a = $metaAtts[j];
                    obj[a.name] = a.value;
                }
            }
            atts = obj;
            metaDetails.push(atts);
        });
        return metaDetails;
    }


    /**
     * @function init
     */
    client.init = () => {
        client.listenPopupHandler();
        client.listenFormSubmit();
        window.client = client;
    }

    /**
     * When the page is ready
     */
    $(document).ready(function () {
        client.init();
    })
})(jQuery);