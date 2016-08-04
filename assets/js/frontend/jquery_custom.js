jQuery(document).ready(function ($) {

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
            $('#wpti-product-x').attr({'disabled': false, 'title': '', 'placeholder': 'Enter a width'});
            $('#wpti-product-y').attr({'disabled': false, 'title': '', 'placeholder': 'Enter a drop'});
            $('.calculate-price').attr({'disabled': false, 'title': ''})

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

});