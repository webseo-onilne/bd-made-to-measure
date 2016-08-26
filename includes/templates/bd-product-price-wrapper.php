<?php


/**
 * No direct access
 */

if ( ! defined( 'ABSPATH' ) ) {
    die( 'No script kiddies please!' );
};

$product  = get_product( $post->ID );				
$currency = get_woocommerce_currency();
$stock    = ( $product->is_in_stock() ? 'InStock' : 'OutOfStock' );

?>

<div itemprop="offers" itemscope itemtype="http://schema.org/Offer">

	<p itemprop="price" class="price">
		<span class="amount final-price" id="wpti-product-price">
			{{currencySymbol}} {{finalPrice}} 
			<img src="/custom/price/ajax-loader.gif" ng-show="showLoader" ng-cloak />
		</span>
	</p>

	<meta itemprop="priceCurrency" content="'.$currency.'" />

	<link itemprop="availability" href="http://schema.org/'.$stock.'" />

</div>
