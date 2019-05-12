(function($){
    // Define the client;
    var client = {};


    client.lead = false;

    /**
     * @function listenPopupHandler
     */
    client.listenPopupHandler = () => {
        var $showPopupBtns = $('.wmCampaignShowPopup');
        $showPopupBtns.on('click', function (e) {
            var button = this;
            var $b = $(button);
            var buttonData = $b.data();
            console.log(buttonData);
        })
        var $popups = $('.wm-campaign-popup');
        $popups.each(function (i, popup) {
            var $popup = popup;


        });
    }

    /**
     * @function listenFormSubmit
     */
    client.listenFormSubmit = () => {
        var $forms = $('.wm-campaign-form');
        $forms.on('submit', function (e) {
            e.preventDefault();
            var $f = $(this);
            // var snapShot = $f.children().clone();
            var ticket = $f.data();

            var $fields = $f.find(":input");
            $fields.each(function (i, field) {
                // when The Loop done
                var loopDone = i == $fields.length - 1;
                if (loopDone) {
                    transPortTicket(ticket);
                }
                var $field = $(field);
                var {type, disabled, required, value, name, id} = field;

                if (['button', 'submit'].includes(type) || disabled ) return;

                if (name) {
                    ticket[name] = $field.val();
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

            function transPortTicket(ticket, callback) {
                // add some optional data
                var { href, origin, pathname, search } = window.location;
                var detail = {
                    href : href,
                    origin: origin,
                    pathname: pathname,
                    search : search
                };
                ticket.detail = detail;

                var options = {
                    type: 'post',
                    data: {
                        action: 'newTicket',
                        ticket : ticket
                    }
                };

                client.jsonTransPortData(options, function (err, data) {
                    if (!err && data) {
                        var donePage = ticket.directional;
                        if (donePage) {
                            window.location = donePage;
                        } else {
                            alert("Đăng kí thành công");
                        }

                        reduceThisForm();
                    }
                    return false;
                });
            }
        })
    }

    /**
     * @function jsonTransPortData
     * @param type
     * @param data
     * @param callback
     */
    client.jsonTransPortData = ({type, data}, callback) => {
        var { origin, pathname } = window.location;
        var url = `${origin + pathname}wp-admin/admin-ajax.php`;
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

    /**
     * @function init
     */
    client.init = () => {
        client.listenPopupHandler();
        client.listenFormSubmit();
    }

    /**
     * When the page is ready
     */
    $(document).ready(function () {
        client.init();
    })
})(jQuery);