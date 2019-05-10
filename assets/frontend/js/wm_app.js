(function($){
    alert(123);
    var $forms = $('.wm-campaign-form');
    $forms.on('submit', function (e) {
        e.preventDefault();
        alert(456)
        var $f = $(this);
        var bags = {};
        var $fields = $f.find(":input");
        $fields.each(function (i, field) {
            var loopDone = i == $fields.length - 1;
            var $field = $(field);
            var {type, disabled, value, name, id} = field;

            if (['button', 'submit'].includes(type) || disabled ) return;

            if (name) {
                bags[name] = $field.val();
            }

            // when The Loop done
            if (loopDone) {
                transPort(bags);
            }
        });

        function transPort(bags) {
            console.log(bags)
        }
    })
})(jQuery);