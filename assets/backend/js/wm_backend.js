(function ($) {
    var wmBags = {};

    wmBags.acceptPage = ['formList','popupList'];

    wmBags.getPageActive = () => {
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
            forms.forEach(function (formData) {
                var {form_id, title, directional, to_caresoft_now, created_at, caresoft_id} = formData;
                var shortcode = `[wpForm form_id="${form_id}"]`;
                var tr = `<tr>
                                <th scope="row" class="check-column">			
                                <label class="screen-reader-text" for="cb-select-${form_id}">Chọn ${title}</label>
                                    <input id="cb-select-${form_id}" type="checkbox" name="forms[]" value="${form_id}">
                                    <div class="locked-indicator">
                                        <span class="locked-indicator-icon" aria-hidden="true"></span>
                                        <span class="screen-reader-text">“${title}” đã bị khóa</span>
                                    </div>
                                </th>
                                <td>
                                    <strong><a class="row-title" href="#" aria-label="${title}">${title}</a></strong>
                                </td>
                                <td>${to_caresoft_now.toUpperCase()}</td>
                                <td>${directional ? directional : "Không"}</td>
                                <td>${new Date(created_at)}</td>
                                <td><code>${shortcode}</code></td>
                            </tr>`;
                $tbody.append(tr);
            });
        }
    }

    wmBags.checkThisPagePlugin = () => {
        var search = typeof window.location.search == "string" && window.location.search.trim().length > 0 ? window.location.search : false;
        if (!search) return false;
        var query = wmBags.queryStringToObj(search);
        if (!query.hasOwnProperty('page') && query.page != 'webManagerForm')
            return false;
        return true;
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