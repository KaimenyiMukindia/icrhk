(function ($) {
    "use strict";

    $( document ).ready(function() {

    
    });
   elementor.hooks.addAction("panel/open_editor/widget/goodsoul_gellery", function (panel, model, view) {

    $("input:hidden[value='select_show']").parents('.elementor-control').prev().find('select').on('change', function () {
       

        if ('style_2' == $(this).val()) {
            $("input:hidden[value='tagline_show']").parents(".elementor-control").prev().show();
        } else {
            $("input:hidden[value='tagline_show']").parents(".elementor-control").prev().hide();
        }
    });
    
    
    if ('style_2' == $("input:hidden[value='select_show']").parents('.elementor-control').prev().find('select').val()) {
        $("input:hidden[value='tagline_show']").parents(".elementor-control").prev().show();
    } else {
        $("input:hidden[value='tagline_show']").parents(".elementor-control").prev().hide();
    }


});    

})(jQuery);
