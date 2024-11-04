jQuery(document).ready(function () {
    quantity_plus_minus();
    steps();
    apply_coupon_custom();
    input_fields_checkout();
    phone_input();
});

var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
    return false;
};

function utm_parameters() {
    utm_val('utm_source');
}

function utm_val(name) {
    var val = GetURLParameter(name);
    jQuery('input[name="' + name + '"]').val(val);
    console.log(val);
}

function phone_input() {
    const input = document.querySelector("#billing_phone");
    window.intlTelInput(input, {
        initialCountry: "gb",
        onlyCountries: countries,
        strictMode: true,
        countrySearch: false,
    });
}
function input_fields_checkout() {

    jQuery(".form-row input, .form-row select, .form-row textarea").each(function (index, element) {
        if (this.value) {
            jQuery(this).parent().parent().addClass("filled");
        } else {
            jQuery(this).parent().parent().removeClass("filled");
        }
    });

    jQuery(".form-row input, .form-row select, .form-row textarea").on("blur input focus", function () {
        if (this.value) {
            jQuery(this).parent().parent().addClass("filled");
        } else {
            jQuery(this).parent().parent().removeClass("filled");
        }
    });

    jQuery(".form-row input, .form-row select,  .form-row textarea").on("focus", function () {
        if (this) {
            jQuery(this).parent().parent().addClass("filled");
        } else {
            jQuery(this).parent().parent().removeClass("filled");
        }
    });

}
function coupon_code() {
    jQuery('.checkout_coupon_form input[name="coupon_code"]').keypress(function (e) {
        jQuery('input[name="coupon_code"]').val(jQuery(this).val());
        console.log(jQuery(this).val());
    });
}

function quantity_plus_minus() {
    jQuery(document).on('click', 'button.plus, button.minus', function () {
        target = jQuery(this).attr('target');
        var qty = jQuery('input[name="' + target + '"]');
        var val = parseFloat(qty.val());
        var max = parseFloat(qty.attr('max'));
        var min = parseFloat(qty.attr('min'));
        var step = parseFloat(qty.attr('step'));

        if (jQuery(this).is('.plus')) {
            if (max && (max <= val)) {
                qty.val(max);
            } else {
                qty.val(val + step);
            }
        } else {
            if (min && (min >= val)) {
                qty.val(min);
            } else if (val > 1) {
                qty.val(val - step);
            }
        }

        qty.change();


    });

    jQuery(document).on('click', '.remove-item', function () {
        target = jQuery(this).attr('target');
        var qty = jQuery('input[name="' + target + '"]');
        qty.val(0);
        console.log(qty.val());
        qty.change();
    });


}

function steps() {
    jQuery(document).on('click', '.continue', function () {
        validate();
    });
    jQuery(document).on('click', '.back-to', function () {
        jQuery('.steps-ui').addClass('active');
        jQuery('html').addClass('disable-scrolling');

        jQuery('.step-box.active').addClass('d-none').removeClass('active').prev().removeClass('d-none').addClass('active');

        jQuery('.nav-box ul li.active').removeClass('active').prev().addClass('active');
        $step = jQuery('.step-box.active').attr('step');

        previous_step($step);
        setTimeout(function () {
            jQuery('.steps-ui').removeClass('active');
            jQuery('html').removeClass('disable-scrolling');
        }, 1000);
    });
}


function validate() {
    jQuery('#place_order').click();
    jQuery('.steps-ui').addClass('active');
    jQuery('html').addClass('disable-scrolling');
    jQuery('.error-lists').remove();

    if (jQuery('.step-box.active .woocommerce-invalid').length == 0) {
        jQuery('.step-box.active').addClass('d-none').removeClass('active').next().removeClass('d-none').addClass('active');
        jQuery('.nav-box ul li.active').removeClass('active').next().addClass('active');
        $step = jQuery('.step-box.active').attr('step');
        previous_step($step);
    } else {
        $errors = '';
        jQuery('.step-box.active .woocommerce-invalid').each(function (index, element) {

            $label = jQuery(this).find('label').html();
            $error_text = '<li>' + $label + '</li>';
            $errors = $errors + $error_text;
        });
        jQuery('<div class="error-lists"><p>Please check the following fields are correct and valid</p><ul>' + $errors + '</ul></div>').insertAfter('.nav-box');
    }

    setTimeout(function () {
        jQuery('.steps-ui').removeClass('active');
        jQuery('html').removeClass('disable-scrolling');
    }, 1000);
}



function previous_step($step) {
    if ($step == 1) {
        jQuery('.previous-step').addClass('d-none');
    } else if ($step == 2) {
        $email = jQuery('input[name="billing_email"]').val();
        jQuery('.previous-step.contact .email').text($email);
        jQuery('.previous-step.contact').removeClass('d-none');
        jQuery('.previous-step.address').addClass('d-none');

    } else if ($step == 3) {
        $billing_address_1 = jQuery('input[name="billing_address_1"]').val();
        $billing_address_2 = jQuery('input[name="billing_address_2"]').val();
        $billing_city = jQuery('input[name="billing_city"]').val();
        $billing_state = jQuery('input[name="billing_state"]').val();
        $billing_postcode = jQuery('input[name="billing_postcode"]').val();
        $address = $billing_address_1 + ' ' + $billing_address_2 + ' ' + $billing_city + ' ' + $billing_state + ' ' + $billing_postcode;

        jQuery('.previous-step.address').removeClass('d-none');
        jQuery('.previous-step.address .address').text($address);

    }
}

function apply_coupon_custom() {

    jQuery(document).on('click', '.apply_coupon_custom', function () {
        coupon_ajax();
    });

    jQuery(document).on('click', '.woocommerce-remove-coupon', function () {

        jQuery(document.body).on('updated_checkout', function () {
            jQuery('.coupon-message').html('<span>Coupon has been removed.</span>');

        });
    });

}


function coupon_ajax() {
    $coupon_code = jQuery('input[name="coupon_code_custom"]').val();
    if ($coupon_code) {
        jQuery('.blockUI.coupon-ui').addClass('active');
        jQuery.ajax({
            type: "POST",


            url: "/wp-admin/admin-ajax.php",

            data: {
                action: 'coupon_ajax',
                coupon_code: $coupon_code,
            },

            success: function (response) {
                jQuery('body').trigger('update_checkout');
                jQuery(document.body).on('updated_checkout', function () {
                    jQuery('.coupon-message').html(response);
                });
                jQuery('.blockUI.coupon-ui').removeClass('active');

            },
            error: function (e) {
                console.log(e);
            }

        });

    } else {
        jQuery('.coupon-message').html('<span class="failed">Please enter a valid coupon code.</span>');

    }
}