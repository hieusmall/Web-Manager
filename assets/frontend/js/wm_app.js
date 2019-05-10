(function($){
    function listenFormSubmit() {
        var $forms = $('.wm-campaign-form');
        $forms.on('submit', function (e) {
            e.preventDefault();
            var $f = $(this);
            // var snapShot = $f.children().clone();
            var bags = $f.data();
            var $fields = $f.find(":input");
            $fields.each(function (i, field) {
                // when The Loop done
                var loopDone = i == $fields.length - 1;
                if (loopDone) {
                    console.log(bags)
                    // transPortBags(bags);
                    return false;
                }
                var $field = $(field);
                var {type, disabled, required, value, name, id} = field;

                if (['button', 'submit'].includes(type) || disabled ) return;

                if (name) {
                    bags[name] = $field.val();
                }
            });

            function reduceThisForm() {
                $fields.each(function (k, input) {
                    if (['button','submit'].includes(input.type))
                        return;

                    if (['text','number','textarea','email'].includes(input.type))
                        $(input).val("");

                });
            }

            function transPortBags(bags) {
                // add some optional data
                var {href,origin,pathname, search} = window.location;
                var detail = {
                    href : href,
                    origin: origin,
                    pathname: pathname,
                    search : search
                };
                bags.detail = detail;
                var ajaxUrl = `${detail.origin + detail.pathname}wp-admin/admin-ajax.php`;
                var data = {
                    action: 'newTicket',
                    ticket : bags
                }

                $.ajax({
                    type : "post", //Phương thức truyền post hoặc get
                    dataType : "json", //Dạng dữ liệu trả về xml, json, script, or html
                    url : ajaxUrl, //Đường dẫn chứa hàm xử lý dữ liệu. Mặc định của WP như vậy
                    data : data,
                    success: function(response) {
                        alert("Đăng kí thành công!");
                        reduceThisForm();
                    },
                    error: function( jqXHR, textStatus, errorThrown ){
                        //Làm gì đó khi có lỗi xảy ra
                        console.log( 'The following error occured: ' + textStatus, errorThrown );
                        alert("Đăng kí thất bại");
                    }
                });
            }
        })
    }

    $(document).ready(function () {
        listenFormSubmit();
    })
})(jQuery);