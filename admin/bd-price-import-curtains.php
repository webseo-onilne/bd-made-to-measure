<?php 

/**
 * No direct access
 */

if ( ! defined( 'ABSPATH' ) ) {
    die( 'No script kiddies please!' );
};

?>

<?php 
global $wpdb;
if (isset($_GET['preview'])) {

	$postData = array(
		'style_type' => $_POST['style_type'],
		'lining_type' => $_POST['lining_type'],
		'price_group' => $_POST['price_group']
	);

	extract($postData);	

	if (isset($_FILES['import_csv']['tmp_name'])) {
		$import_data = array();
		$error_messages = array();
	        
		if (function_exists('wp_upload_dir')) {
			$upload_dir = wp_upload_dir();
			$upload_dir = $upload_dir['basedir'] . '/csv_import';
		}
		else {
			$upload_dir = dirname(__FILE__) . '/uploads';
		}
	        
		if (!file_exists($upload_dir)) {
			$old_umask = umask(0);
			mkdir($upload_dir, 0755, true);
			umask($old_umask);
		}

		if (!file_exists($upload_dir)) {
			$error_messages[] = "Could not create upload directory '{$upload_dir}'";
		}

		//gets uploaded file extension for security check.
		$uploaded_file_ext = strtolower(pathinfo($_FILES['import_csv']['name'], PATHINFO_EXTENSION));

		//full path to uploaded file. slugifys the file name in case there are weird characters present.
		$sanitized_title = sanitize_title(basename($_FILES['import_csv']['name'], '.' . $uploaded_file_ext));
		$uploaded_file_path = "{$upload_dir}/{$sanitized_title}.{$uploaded_file_ext}";

		if ($uploaded_file_ext != 'csv') {
			$error_messages[] = "The file extension '{$uploaded_file_ext}' is not allowed.";
		}
		else if (move_uploaded_file($_FILES['import_csv']['tmp_name'], $uploaded_file_path)) {
			//now that we have the file, grab contents
			$handle = fopen( $uploaded_file_path, 'r' );

			if ($handle !== FALSE) {
				while (($line = fgetcsv($handle)) !== FALSE) {
					$import_data[] = $line;
				}

				fclose($handle);
			}
			else {
				$error_messages[] = 'Could not open file.';
			}
		}
		else {
			$error_messages[] = 'move_uploaded_file() returned false.';
		}
	     
		if (sizeof($import_data) == 0) {
			$error_messages[] = 'No data to import.';
		}
	    // Widths
		$header_row = array_shift($import_data);
		// Prices
		$header_row2 = array_shift($import_data);
		// Row count
		$row_count = sizeof($import_data);

		$wpdb->query(
			$wpdb->prepare("DELETE FROM `wp_woocommerce_curtain_price_table` WHERE price_group = %s AND lining_type = %s AND style_type = %s",
						$price_group,
						$lining_type,
						$style_type
			)
		);

		if ($deleted === FALSE)
			$error_messages = $wpdb->last_error;		

		if ($header_row) {
			foreach ($header_row as $key => $value) {
				$insert_object = array();
				if ($key == 0) continue;

				$insert_object = array(
					'price_group' => $_POST['price_group'],
					'lining_type' => $_POST['lining_type'],
					'style_type' => $_POST['style_type'],
					'width' => $value,
					'price' => $header_row2[$key]
				);

				if (!$wpdb->insert('wp_woocommerce_curtain_price_table', $insert_object))
					$error_messages = "<div id='message' class='error'><p>Error, ".$wpdb->last_error."</p></div>";
				else
					$error_messages = "<div id='message' class='notice notice-success is-dismissible'><p>Success, ".$row_count ." rows added</p></div>";

			}
		}
		//var_dump($header_row[1]);
		//var_dump($insert_object);
		echo $error_messages;
	}
	
}
?>

<div class="wrap woocommerce" ng-app="curtainManager" ng-controller="curtainCtrl">

	<hr />
	<div class="table-wrapper">
		<h3 class="page-title">Check Curtain Prices</h3>
		<hr>
		<?php 

			$variations = $this->get_product_addons();
			$names = array();

			//echo "<h3 class='page-title'>Price Group</h3>";
			echo "<select class='variation-options_check' ng-model='curtaingroup' ng-change='getCurtainPrices(curtaingroup)'>";
				echo "<option value='undefined'>Please Select</option>"; 
				foreach ($variations as $attname => $addon) {
					$names[] = $attname;
					if (strpos($attname, 'pa_curtains') === false) continue;
					echo "<option value='attribute_". $attname ."'>" . str_replace(array("pa_", "-"), " ", $attname) . "</option>";
				}

			echo "</select>";
		?>
		<label for="filter">Filter by Lining Type</label>
		<select id="filter" ng-model="filterby" ng-change="filterBy = filterby">
			<option value="">No Filter</option>
			<option value="lining_type_standard">Standard</option>
			<option value="lining_type_blockout">Blockout</option>
			<option value="lining_type_none">No Lining</option>
		</select>

		<label for="stylefilter">Filter by Style Type</label>
		<select id="stylefilter" ng-model="styleFilter" ng-change="styleFilter = styleFilter">
			<option value="">No Filter</option>
			<option value="style_type_ep">Eyelet/Pencil</option>
			<option value="style_type_wpf">Wave/Pinch/French</option>
		</select>		
		<hr>		
		<table id="variation-table" class="wp-list-table widefat fixed striped posts">
			<thead>
				<tr>
					<td>Width (mm)</td>
					<td>Lining</td>
					<td>Style</td>
					<td>Price</td>
					<td>Marked Up Price <sub>(Excl VAT)</sub></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="item in allPrices | filter: filterBy | filter: styleFilter">
					<td data-id="{{item.id}}" ng-cloak>{{item.width}}</td>
					<td ng-cloak>{{item.lining_type == 'lining_type_standard' ? 'Standard Lining' : item.lining_type == 'lining_type_blockout' ? 'Blockout Lining' : item.lining_type == 'lining_type_none' ? 'No Lining' : 'N/A' }}</td>
					<td ng-cloak>{{item.style_type == 'style_type_ep' ? 'Eyelet/Pencil' : item.style_type == 'style_type_wpf' ? 'Wave/Pinch/French' : 'N/A'}}</td>
					<td ng-cloak>R {{item.price}}</td>
					<td ng-cloak><strong>R {{item.marked_up_price}}</strong></td>
				</tr>
			</tbody>
		</table>
	</div>

    <div style="width: 48%; float:left;">
    	<h3 class="page-title">Import Curtains Price Sheet</h3>
    	<hr>
    	<form enctype="multipart/form-data" method="post" action="<?php echo get_admin_url() ?>admin.php?page=bd-curtain-price-import&preview=true">
		<table id="" class="wp-list-table widefat fixed striped posts">
			<thead>
				<tr>
					<td>Import Price Sheet</td>
				</tr>
			</thead>
			<tbody>

				<tr class="">
					<td><input type="file" name="import_csv"></td>
				</tr>

				<tr class="">
					<td>
						<?php 

						$variations = $this->get_product_addons();
						$names = array();

						echo "Price Group";
						echo "<select name='price_group' class='variation-options' >";
						echo "<option value='undefined'>Please Select</option>"; 
						foreach ($variations as $attname => $addon) {
							$names[] = $attname;
							if (strpos($attname, 'pa_curtains') === false) continue;
							echo "<option value='attribute_". $attname ."'>" . str_replace(array("pa_", "-"), " ", $attname) . "</option>";
						}

						echo "</select>";
						?>						
					</td>
				</tr>

				<tr class="">
					<td>
						<label for="style_type">Style</label>
						<select name="style_type">
							<option value="style_type_ep">Eyelet/Pencit</option>
							<option value="style_type_wpf">Wave/pinch/french</option>
						</select>						
					</td>
				</tr>

				<tr class="">
					<td>
						<label for="lining_type">Lining</label>
						<select name="lining_type">
							<option value="lining_type_blockout">Blockout</option>
							<option value="lining_type_standard">Standard</option>
							<option value="lining_type_none">No Lining</option>
						</select>						
					</td>
				</tr>

				<tr class="">
					<td>
						<button class="button-primary" type="submit">Upload</button>						
					</td>
				</tr>

			</tbody>
		</table>
		</form>

		<hr>
		<h3 class="page-title">Add Mark Up {{selectedGroup}}</h3>
		<hr>
		<table id="input-table" class="wp-list-table widefat fixed striped posts">
			<thead>
				<tr>
					<td>Price From</td>
					<td>Price To</td>
					<td>Markup By %</td>
					<td>Save</td>
				</tr>
			</thead>
			<tbody>
				<tr class="markup_range_1">
					<td><input id="range__from" ng-model="range1.from" class="markup-input range_1_from" type="text" /></td>
					<td><input id="range__to" ng-model="range1.to" class="markup-input range_1_to" type="text" /></td>
					<td><input id="range__by" ng-model="range1.markup_by" class="markup-input range_1_markup_by" type="text" /></td>
					<td><button class="markup-button button button-primary button-small" ng-disabled="!selectedGroupActaual" ng-click="saveMarkup(range1, selectedGroupActaual, 'markup_range_1')">Save</button></td>
				</tr>

				<tr class="markup_range_2">
					<td><input id="range__from" ng-model="range2.from" class="markup-input range_2_from" type="text" /></td>
					<td><input id="range__to" ng-model="range2.to" class="markup-input range_2_to" type="text" /></td>
					<td><input id="range__by" ng-model="range2.markup_by" class="markup-input range_2_markup_by" type="text" /></td>
					<td><button class="markup-button button button-primary button-small" ng-disabled="!selectedGroupActaual" ng-click="saveMarkup(range2, selectedGroupActaual, 'markup_range_2')">Save</button></td>
				</tr>

				<tr class="markup_range_3">
					<td><input id="range__from" ng-model="range3.from" class="markup-input range_3_from" type="text" /></td>
					<td><input id="range__to" ng-model="range3.to" class="markup-input range_3_to" type="text" /></td>
					<td><input id="range__by" ng-model="range3.markup_by" class="markup-input range_3_markup_by" type="text" /></td>
					<td><button class="markup-button button button-primary button-small" ng-disabled="!selectedGroupActaual" ng-click="saveMarkup(range3, selectedGroupActaual, 'markup_range_3')">Save</button></td>
				</tr>
			</tbody>
		</table>

    </div>
	
</div>

<style type="text/css">
.table-wrapper {
    width: 48%;
    float: right;
}
table#input-table label {
    padding-right: 38px;
}
</style>