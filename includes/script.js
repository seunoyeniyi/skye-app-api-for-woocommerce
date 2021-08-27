jQuery(document).ready(function ($) {
    $('header, #masthead').hide();
    $('div.storefront-breadcrumb').hide();
    $("footer").hide();
    $("aside").hide();
    $("nav.woocommerce-breadcrumb").hide();
    // $("div.woocommerce-form-coupon-toggle").hide();
    // $("input#payment_method_stripe").prop("checked",true);
    // $("input#payment_method_stripe").attr("checked","checked");
    // $("div.payment_box.payment_method_stripe").show();
    $("div.storefront-handheld-footer-bar").hide();
    $("#glt-translate-trigger").hide();

    setInterval(() => {
        // $("ul.wc_payment_methods > :not(li.payment_method_stripe), ul.payment_methods methods > :not(li.payment_method_stripe)").hide();
        // $("div.payment_box.payment_method_stripe").show();
        $("div.storefront-handheld-footer-bar").hide();
        $("#glt-translate-trigger").hide();
    }, 1000);
    
});