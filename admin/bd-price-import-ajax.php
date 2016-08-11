<?php
//echo "string";
//Get required globals
global $wpdb;

$post_data = array(
	'uploaded_file_path' => $_POST['uploaded_file_path'],
	'limit' => $_POST['limit'],
	'offset' => $_POST['offset'],
	'type' => $_POST['type'],
	'term_id' => isset($_POST['term_id']) ? $_POST['term_id'] : false,
	'field_choice' => isset($_POST['field_choice']) ? $_POST['field_choice'] : false
);

//echo json_encode($post_data);

extract($post_data);

//initialise variables for response
$rows_remaining = 0;
$row_count = 0;
$insert_count = 0;
$insert_percent = 0;
$inserted_rows = 0;
$error_messages = array();
$limit = $_POST['limit'];
$offset = $_POST['offset'];

if (isset($post_data['uploaded_file_path'])) {
	$error_messages = array();

	//now that we have the file, grab contents
	$temp_file_path = $post_data['uploaded_file_path'];
	$handle = fopen($temp_file_path, 'r');
	$import_data = array();

	if ($handle !== FALSE) {
		while (($line = fgetcsv($handle)) !== FALSE) {
			$import_data[] = $line;
		}
		fclose($handle);
	}
	else {
		$error_messages[] = 'Could not open CSV file.';
	}

	if (sizeof($import_data) == 0) {
		$error_messages[] = 'No data found in CSV file.';
	}

	//discard header rows from data set and get widths
	$widths = array_shift($import_data);
	array_shift($import_data);

	//total size of data to import (not just what we're doing on this pass)
	$row_count = sizeof($import_data);

	//slice down our data based on limit and offset params
	$limit = intval($post_data['limit']);
	$offset = intval($post_data['offset']);

	if ($limit > 0 || $offset > 0) {
		$import_data = array_slice($import_data, $offset , ($limit > 0 ? $limit : null), true);
	}

	//a few stats about the current operation to send back to the browser.
	$rows_remaining = ($row_count - ($offset + $limit)) > 0 ? ($row_count - ($offset + $limit)) : 0;
	$insert_count = ($row_count - $rows_remaining);
	$insert_percent = number_format(($insert_count / $row_count) * 100, 1);

	//arrays that will be sent back to the browser with info about what we inserted.
	$inserted_rows = array();
	$insert_errors = array();

	//echo json_encode($import_data);

	//Delete existing price entries for product or addon being updated
	if ($term_id) {
		if ($offset == 0) {
			$deleted = $wpdb->query(
				$wpdb->prepare("DELETE FROM `wp_woocommerce_cat_price_table` WHERE term_id = %d",
					$term_id));
			if ($deleted === FALSE)
				$error_messages[] = $wpdb->last_error;
		}
	}
	else if ($type != 'product_cat' && $field_choice) {
		if ($offset == 0) {
			$deleted = $wpdb->query(
				$wpdb->prepare(
					"DELETE FROM `wp_woocommerce_addon_price_table` WHERE field_label = %s AND choice = %s",
					$type,
					$field_choice));

			if ($deleted === FALSE)
				$error_messages[] = $wpdb->last_error;
		}
	}
	else {
		$error_messages[] = "Invalid price table target";
	}

	//echo json_decode($insert_count);
	$test = array();
	foreach ($import_data as $row_id => $row) {
		$success = false;

		//get height from first column
		$height = $row[0];

		//foreach price in the row add a new price entry in the price table
		$logical_row = array();

		foreach ($row as $col_id => $price) {
			$insert_errors = array();

			//skip row header
			if ($col_id == 0)
				continue;

			//Insert price table values into the correct table
			if ($term_id) {
				$actual_row = array(
					'term_id' => $term_id,
					'width' => $widths[$col_id],
					'height' => $height,
					'price' => $price
				);

				if (!$wpdb->insert('wp_woocommerce_cat_price_table', $actual_row))
					$insert_errors[] = $wpdb->last_error;
				else 
					$success = true;
			}
			else if ($type != 'product_cat' && $field_choice) {
				$actual_row = array(
					'field_label' => $type,
					'choice' => $type,
					'width' => $widths[$col_id],
					'height' => $height,
					'price' => $price
				);

				if (!$wpdb->insert('wp_woocommerce_addon_price_table', $actual_row))
					$insert_errors[] = $wpdb->last_error;
				else 
					$success = true;
			}
			else
				$insert_errors[] = 'Invalid price table target';

			$logical_row[$widths[$col_id]] = $price;                
		}
            
		// this is returned back to the results page.
		// any fields that should show up in results should be added to this array.
		$inserted_rows[] = array(
			'row_id'        => $row_id,
			'term_id'       => $term_id,
			'field_label'   => $this->optionize_labels($type),
			'field_choice'  => $field_choice,
			'category_name' => $term_id ? get_term($term_id, 'product_cat')->name : false,
			'logical_row'   => $logical_row,
			'has_errors'    => (sizeof($insert_errors) > 0),
			'errors'        => $insert_errors,
			'success'       => $success,
		);
	}

	//echo json_encode($inserted_rows);
}
    
echo json_encode(array(
	'remaining_count' => $rows_remaining,
	'row_count' => $row_count,
	'insert_count' => $insert_count,
	'insert_percent' => $insert_percent,
	'inserted_rows' => $inserted_rows,
	'error_messages' => $error_messages,
	'limit' => $limit,
	'new_offset' => ($limit + $offset)
));

?>