(function($){
    // Define the client;
    var client = {},
    pageVideo = {},
    web = {};

    client.lead = false;

    /**
     * @function listenPopupHandler
     */
    client.listenPopupHandler = () => {
        var $popups = $('.wm-campaign-popup');
        var $showPopupBtns = $('.wmCampaignShowPopup, .popup-link.action_button, .wmBannerPopup a:first');
        $showPopupBtns.off("click");
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
            var options = {
                type: 'get',
                data :{
                    'action': 'readForm',
                    'form_id': form_id
                }
            }
            client.jsonTransPortData(options, function (err, res) {
                var {success, data} = typeof res == "object" ? res : {};
                if (!err && success && data) {
                    var {form_custom_template} = data,
                        $newForm = $form.clone().html("");
                    form_custom_template = typeof form_custom_template == "object" && form_custom_template instanceof Array ? form_custom_template : false;

                    // var elementStr = '';
                    if (form_custom_template) {
                        form_custom_template.forEach(function (inputObj) {
                            var {className, label, values, maxlength, name, placeholder, required, subtype, type} = inputObj,
                                $label = $(`<label>${label}</label>`);
                            inputObj.class = className ? className : "";
                            inputObj.type = subtype ? subtype : type;

                            delete inputObj.subtype;
                            delete inputObj.className;
                            delete inputObj.style;
                            delete inputObj.values;

                            var attrs = inputObj;
                            var $formGroup = $("<div class='form-group' />");
                            var $formCheck = $("<div class='form-check' />");
                            var elem = "<input />";
                            var $field = false;
                            // var attrs = {};
                            if (['text','checkbox','number','email'].includes(type)) {
                                elem = "<input />";
                                $field = $(elem).attr(attrs);
                                $formGroup.append($field);
                            } else if(['submit','button'].includes(type)) {
                                elem = `<button>${label}</button>`;
                                $field = $(elem).attr(attrs);
                                $formGroup.append($field);
                            } else if (['radio-group', 'radio'].includes(type)) {
                                var radios = '';
                                values = typeof values == 'object' && values instanceof Array ? values : [];
                                values.forEach(function (attr) {
                                    var {label, value} = attr;
                                    radios += `<div class="form-check">
                                        <label class="form-check-label"><input class="form-check-input" type="radio" name="${name}" value="${value}" />  ${label}</label>
                                    </div>`;
                                });
                                $field = $(radios);
                                $formGroup.append($field);
                            } else if (['checkbox-group','checkbox'].includes(type)) {

                            } else if (['select'].includes(type)) {
                                var options = '',
                                    $select = $("<select></select>");
                                $select.attr(attrs);
                                values = typeof values == 'object' && values instanceof Array ? values : [];
                                values.forEach(function (obj) {
                                    var {label, value, selected} = obj;
                                    selected = selected ? true : "";
                                    options += `<option value="${value}" selected="${selected}">${label}</option>`;
                                });
                                $field = $select.append($(options));
                                $formGroup.append($label)
                                                    .append($field);
                            } else if(['textarea'].includes(type)) {
                                $field = $(`<textarea></textarea>`).attr(attrs);
                                $formGroup.append($label).append($field);
                            } else if(['autocomplete'].includes(type)) {
                                attrs.type = "text";
                                $field = $("<input list='address-list'/>").attr(attrs);
                                var $datalist = $("<datalist/>").attr('id','address-list'),
                                    options = '';
                                values = typeof values == "object" && values instanceof Array ? values : [];
                                values.forEach(function (obj) {
                                    var {label, value} = obj;
                                    options += `<option value="${value}">${label}</option>`;
                                });
                                $field.append($datalist.html(options));
                                $formGroup.append($field);
                            } else if (['paragraph'].includes(type)) {
                                $field = $(`<p>${label}</p>`);
                                $formGroup.attr('class',"").append($field);
                            }


                            if ($field) {
                                $newForm.append($formGroup)
                            } else {
                                return;
                            }
                        });

                        // Insert new form to old form
                        $form.html($newForm.children());
                        $form.data('form-custom',true);
                    } else {
                        console.log("Khong the lay form");
                        return
                    }
                }
            });

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
                e.preventDefault(e);
                var $f = $(this),
                    form = $f.get(0);
                var $submitBtn = $f.find(".btnSubmit").length > 0 ? $f.find(".btnSubmit") : $f.find("[type=submit]"),
                    defaultTextSubmit = $submitBtn.text();
                // var snapShot = $f.children().clone();
                var ticket = $f.data();
                var $popup = $f.closest('.wm-campaign-popup');

                // Disabled button submit
                $submitBtn.html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span class="sr-only">Đang gửi!...</span>`);
                $submitBtn.attr('disabled', true);


                var {referer, ref_begin, started} = traffic.getInfoTraffic() ? traffic.getInfoTraffic() : {},
                isToday = new Date().toDateString() === new Date(started).toDateString();
                if (referer && started) {
                    if (isToday) {
                        ticket.sources = ref_begin;
                        ticket.referer = referer;
                    }
                }
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

                        if (['text','tel','number','textarea','email'].includes(input.type))
                            $(input).val("");

                    });
                }

                function transPortTicket(ticket) {
                    // add some optional data
                    var { href, origin, pathname, search } = window.location,
                        meta = client.metaTagsDataObj(),
                        postTitleMeta = meta.find(function (obj) {
                            var check = false,
                                xValue = Object.values(obj);
                            if (xValue.includes("og:title") || xValue.includes("twitter:title")) {
                                check = true;
                            }
                            return check;
                        }),
                        detail = {
                            href : href,
                            origin: origin,
                            pathname: pathname,
                            search : search,
                        },
                        options = {
                            type: 'post',
                            data: {
                                action: 'newTicket',
                                ticket : ticket
                            }
                        };
                    detail.meta = meta;
                    detail.postTitle = typeof postTitleMeta == "object"  ? postTitleMeta.content : undefined;
                    ticket.detail = detail;

                    console.log(ticket);
                    $submitBtn.html(defaultTextSubmit);
                    $submitBtn.attr("disabled", false);
                    return false;

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

                        $submitBtn.html(defaultTextSubmit);
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
            if (url.search('admin.php') > 0) {
                url = `${origin}${pathname.replace("admin.php", "admin-ajax.php")}`;
            } else if (pathname.includes('beta.thammyvienngocdung.com')) {
                url = `${origin}/ngocdung/beta.thammyvienngocdung.com/wp-admin/admin-ajax.php`;
            } else {
                url = `${origin}${pathname}wp-admin/admin-ajax.php`;
            }
        }
        url = typeof wmGlobal == "object" && wmGlobal instanceof Object && wmGlobal.hasOwnProperty("ajaxUrl") ? wmGlobal.ajaxUrl : url;

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
            var obj = {};
            var props = Object.values(attributes);
            props.forEach(function(prop) {
                var {name, value} = prop;
                obj[name] = value;
            });
            metaDetails.push(obj);
        });
        return metaDetails;
    }

    client.queryStringToObj = (str) => {
        if(!str) {
            return {};
        }
//         var search = !str ? location.search.substring(1) : str.substring(1);
        var x = JSON.parse('{"' + decodeURI(str.substring(1)).replace(/"/g, '\\"').replace(/&/g, '","').replace(/=/g,'":"') + '"}');
        return x;
    }

    // Setting the videos page
    pageVideo.assets = () => {
        var {vendorAssets} = wmGlobal;
        if (vendorAssets) {
            // Stylesheets
            loadFile(vendorAssets+"slick/slick.css", 'css');
            loadFile(vendorAssets+"fancybox/dist/jquery.fancybox.min.css", 'css');

            // Scripts
            loadFile(vendorAssets+"slick/slick.min.js", 'js');
            loadFile(vendorAssets+"fancybox/dist/jquery.fancybox.min.js", 'js');
        } else {
            console.log("Không tìm thấy đường dẫn plugin !");
        }
    }
    pageVideo.init = () => {
        if (window.location.pathname.includes("videos") || window.location.pathname.includes("video")) {
            pageVideo.assets();

            $("#Subheader").hide();
            var $videosWrapSlides = $(".wrap-videos-cat");
            $videosWrapSlides.each(function (i, elem) {
                var $wrap = $(elem);
                var $slideBox = $wrap.find(".card-videos-wrap");
                var boxItems = $slideBox.find(".box-item");
                var $prev = $wrap.find(".prev");
                var $next = $wrap.find(".next");
                /*if (boxItems.length < 4) {
                    return false;
                }*/
                $slideBox.slick({
                    // dots: true,
                    infinite: true,
                    speed: 500,
                    slidesToShow: 1,
                    autoplay: false,
                    variableWidth: true,
                    centerPadding: "20px",
                    focusOnSelect: true,
                    mobileFirst: true,
                    prevArrow: $prev,
                    nextArrow: $next,
                });
            });
        }
    }

    /**
     * @function init
     */
    client.init = () => {
        client.listenPopupHandler();
        client.listenFormSubmit();
        window.client = client;
    }


    var traffic = {};

    traffic.getInfoTraffic = () => {
        var t = localStorage.getItem("wm_traffic_info");
        t = t && typeof t == "string" ? t : false;
        try {
            t = JSON.parse(t);
            return t;
        } catch (e) {
            return false;
        }
    }

    traffic.setInfoTraffic = (data) => {
        var {ref_begin, started} = typeof data == "object" ? data : {},
        pagesViewed = [],
        ver = "v1";
        ref_begin = ref_begin ? ref_begin : window.location.search.substr(1, window.location.search.length);
        started = started ? started : Date.now();
        var trafficObj = {
            referer: client.queryStringToObj(window.location.search),
            ref_begin: ref_begin,
            started : started,
            pagesViewed : pagesViewed,
            ver : ver
        },
        trafficStr = JSON.stringify(trafficObj);
        localStorage.setItem("wm_traffic_info", trafficStr);
        return true;
    }

    traffic.updateInfoTraffic = (data) => {
        data = typeof data == "object" ? data : {};
        var trafficDetail = traffic.getInfoTraffic(),
        {pageViewed} = data;

        pageViewed = typeof pageViewed == "object" && pageViewed instanceof Object ? pageViewed : false;
        var count = trafficDetail.pagesViewed.length,
        key = count + 1;
        if (pageViewed) {
            var {time, url} = pageViewed;
            time = typeof time == "number" && time > 0 ? time : false;
            url = typeof url == "string" && url.length ? url : false;
            trafficDetail.pagesViewed[count] = pageViewed;
            localStorage.setItem("wm_traffic_info", trafficDetail);
        }

        return trafficDetail;
    }

    traffic.setupTracking = () => {
        var trafficDetail = traffic.getInfoTraffic();
        var isNewTraffic = trafficDetail && typeof trafficDetail == "object" ? false : true;
        if (isNewTraffic) {
            traffic.setInfoTraffic();
        } else if (trafficDetail && new Date(trafficDetail.started).toDateString() < new Date().toDateString()) {
            traffic.setInfoTraffic();
        }
    }

    traffic.init = () => {
        traffic.setupTracking();
        window.traffic = traffic;
    }


    var loadFile = (path, type) => {
        if( type == 'js') {
            $('head').append('<script type="text/javascript" src="'+path+'"></script>');
        }
        else if( type == 'css' ) {
            $('head').append('<link href="'+path+'" rel="stylesheet" type="text/css">');
        }
    }

    /**
     * When the page is ready
     */
    $(document).ready(function () {
        client.init();
        traffic.init();
        pageVideo.init();
    })
})(jQuery);