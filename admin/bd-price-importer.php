<?php

global $wpdb;
$addons = $this->get_product_addons();
$product_categories = $this->get_products_categories();

//
$term_id = array();
foreach ($product_categories as $key => $category) {
	foreach ($category['terms'] as $term) {
		$term_id[] = $term->term_id;
	}
}

$product_cat_sql = 'SELECT DISTINCT(cpt.term_id), wt.name, wt.slug FROM'
	. " `wp_woocommerce_cat_price_table` cpt"
	. ' LEFT OUTER JOIN wp_terms wt ON cpt.term_id=wt.term_id'
	. ' WHERE cpt.term_id IN(' . implode(',', $term_id) . ')'
	. ' ORDER BY wt.name';
$product_cat_result = $wpdb->get_results($product_cat_sql);
//
$slugs = array();
foreach ($addons as $slug => $addon) {
	$slugs[] = $slug;
}
$addons_sql = 'SELECT field_label, choice FROM'
	. " `wp_woocommerce_addon_price_table` "
	. ' WHERE field_label IN (\'' . implode("','", $slugs) . '\')'
	. ' GROUP BY field_label, choice'
	. ' ORDER BY field_label';
$addons_result = $wpdb->get_results($addons_sql);
//

?>
<div class="woo_product_importer_wrapper wrap">
	<h3 class="page-title">Import Price Sheets</h3><hr />
	<form class="uploadform" enctype="multipart/form-data" method="post" action="<?php echo get_admin_url() ?>admin.php?page=bd-price-import-preview">
		<p>
			<h4>Choose a file to Import</h4>
		</p>
		<p>
			<input type="file" name="import_csv">
		</p>
		<p>
			<button class="button-primary" type="submit">Upload and Preview</button>
		</p>
	</form>

	<form style="display:none;" class="uploadformshutters" enctype="multipart/form-data" method="post" action="<?php echo get_admin_url() ?>admin.php?page=bd-price-import-preview">
		<p>
			<h4>Choose a file to Import</h4>
		</p>
		<p>
			<input type="file" name="import_csv">
		</p>
		<p>
			<button class="button-primary" type="submit">Upload and Preview</button>
		</p>
	</form>


<?php if (count($product_cat_result) || count($addons_result)): ?>
	<hr>
	<h3 class="page-title">Uploaded Price Sheets</h3>
	<table border="1" cellspaing="0" cellpadding="5" style="border-collapse:collapse;min-width:500px">
<?php if (count($product_cat_result)): ?>
		<tr>
			<th style="text-align:left">Product categories</th>
		</tr>
		<tr>
			<th>Name</th>
			<th></th>
<?php endif; ?>
<?php foreach ($product_cat_result as $category): ?>
		<tr>
			<td ><?=$category->name?></td>
			<td align="center"><a class="button button-primary button-small" href="?page=<?=$_REQUEST['page']?>&action=remove&term=<?=$category->term_id?>">remove</a></td>
		</tr>
<?php endforeach; ?>
<?php if (count($addons_result)): ?>
		<tr>
			<th style="text-align:left">Price Sheets</th>
		</tr>
		<tr>
			<th>Name</th>
			<th></th>
		</tr>
<?php endif; ?>
<?php foreach ($addons_result as $addon): ?>
		<tr>
			<td><?=$this->normalize_taxonomy_name($addon->field_label)?></td>
			<td align="center"><a class="button button-primary button-small" href="?page=<?=$_REQUEST['page']?>&action=remove&addon=<?=$addon->field_label?>&choice=<?=$addon->choice?>">remove</a></td>
		</tr>
<?php endforeach; ?>
	</table>
<?php endif; ?>
</div>

<?php
if ($_GET['action'] == 'remove') {

	global $wpdb;

	$term = array_key_exists('term', $_GET) ? $_GET['term'] : 0;
	$addon = array_key_exists('addon', $_GET) ? $_GET['addon'] : '';
	$choice = array_key_exists('choice', $_GET) ? $_GET['choice'] : '';
	if ($term) {
		$table = 'wp_woocommerce_cat_price_table';
		$where = array('term_id' => $term);
		$where_format = array('%d');
		$wpdb->delete($table, $where, $where_format);
	} elseif ($addon && $choice) {
		$table = 'wp_woocommerce_addon_price_table';
		$where = array(
			'field_label' => $addon,
			//'choice' => $choice
		);
		$where_format = array('%s', '%s');
		$wpdb->delete($table, $where, $where_format);
	} else {
	?>
	<script>
	alert('Invalid parameters.');
	</script>
	<?php
	}

	?>
	<script>
	window.location.href = '?page=<?=$_REQUEST['page']?>&tab=upload';
	</script>
<?php
}
?>