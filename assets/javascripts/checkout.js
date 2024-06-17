jQuery(document).ready(function () {
    quantity_plus_minus();
    coupon_code();
    steps();
    input_fields_checkout();
});
function input_fields_checkout() {
	jQuery('.remove-first-option-value select option:first-child').attr('value', '');

	jQuery(".wpforms-field input, .wpforms-field select, .wpforms-field textarea").on("blur input focus", function () {
		if (this.value) {
			jQuery(this).parent().addClass("filled");
		} else {
			jQuery(this).parent().removeClass("filled");
		}
	});

	jQuery(".wpforms-field input, .wpforms-field select,  .wpforms-field textarea").on("focus", function () {
		if (this) {
			jQuery(this).parent().addClass("filled");
		} else {
			jQuery(this).parent().removeClass("filled");
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
    jQuery('.continue').click(function (e) {
        validate();
    });
    jQuery('.back-to').click(function (e) {
        jQuery('.step-box.active').addClass('d-none').removeClass('active').prev().removeClass('d-none').addClass('active');

        jQuery('.nav-box ul li.active').removeClass('active').prev().addClass('active');
    });
}

function validate() {
    if (jQuery('.step-box.active .woocommerce-invalid').length > 1) {

    } else {
        jQuery('.step-box.active').addClass('d-none').removeClass('active').next().removeClass('d-none').addClass('active');

        jQuery('.nav-box ul li.active').removeClass('active').next().addClass('active');
    }
}