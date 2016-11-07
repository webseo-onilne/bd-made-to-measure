jQuery(document).ready(function ($) {

    $('[data-toggle="tooltip"]').tooltip()

    /*
    * Remove 'selected' class from all
    * other selected finishes
    * [Product page]
    */
    $('.variations tr').each(function(){
        $(this).on('click', '.select-option', function (e) {

            // On click of a finish, click the clear button
            // to keep the price of the selected variation
            // otherwise it adds the prices together
            // Also - activate the input feilds
            $('.reset_variations').trigger('click');
            $('.calculate-price').attr({'disabled': false, 'title': ''});

            // Remove any other finish that has the 'selected' class
            $(this).closest('form.variations_form').find('div.select').each(function() {
                $(this).find('div.select-option').each(function() {
                    if ($(this).hasClass('selected')) {

                        $(this).removeClass('selected');

                        // This is potentially useless - need to test
                        var select = $(this).closest('div.select');
                        select.data('value', '');
                    }
                });
            });

            setTimeout(function() {
                // Trigger changes on the inputs to make sure the dimesions
                // are correctly validated
                //angular.element(document.getElementById('wpti-product-x')).triggerHandler('change');
                // $('#wpti-product-x').trigger('change');
                // $('#wpti-product-y').trigger('change');
                $('.calculate-price').trigger('click');
            }, 500);
        });
    });


    /*
    * Allow only one variation to be added
    * to cart by forcing the variation id
    * to be set on change of the variations
    * [Product page]
    */
    $(document).on('wc_variation_form', function (e) {

        var $form = $(e.target);

        // Get the variation data from the DOM
        var variationObj = JSON.parse($form.attr('data-product_variations'));
        var variationId = variationObj[0].variation_id;

        // On change of the variations form,
        // insert the variation id onto the variation 
        // hidden form value for the add-to-cart button
        // (this allows only one variation to be selected)
        $form.on('woocommerce_variation_has_changed', function () {
            $('input[name="variation_id"]').val(variationId);
        });
    }); 


    /*
    * For Curtains only:
    * On change of the lining selection
    * trigger the calculate price button
    * [Product page]
    */
    $('select[name="addon-7400-lining"]').on('change', function() {
        $('.calculate-price').trigger('click');
    });


    /*
    * For Curtains only:
    * On change of the style selection
    * trigger the calculate price button
    * [Product page]
    */
    $('.product-addon-curtain-style').find('input').each(function() {
        $(this).on('change', function() {
            $('.calculate-price').trigger('click');
        });
    });


    $('.single_variation_wrap').after('<div class="new_price"></div>');
    $('.quantity input.qty').on('change', function() {
        var qty = $(this).val();
        var current_price = parseFloat($('.woocommerce-tabs').find('span.amount').text().split(' ')[1]);
        var new_price = current_price * qty;
        if (qty <= 1 ) {
            $('.new_price').empty();
            return;
        }
        else if (isNaN(new_price)) {
            return;
        };        
        $('.new_price').html('<p>R '+new_price.toFixed(2)+'</p>').css('color', 'red');

    });

});


