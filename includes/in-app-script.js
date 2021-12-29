jQuery(document).ready(function ($) {
    $('header, #masthead').hide();
    $('div.storefront-breadcrumb').hide();
    $("footer").hide();
    $("aside").hide();
    $("nav.woocommerce-breadcrumb").hide();
    $("div.storefront-handheld-footer-bar").hide();
    $("#glt-translate-trigger").hide();

    setInterval(() => {
        $("div.storefront-handheld-footer-bar").hide();
        $("#glt-translate-trigger").hide();
    }, 1000);
    
});