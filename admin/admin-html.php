<?php 

/**
 * No direct access
 */

if ( ! defined( 'ABSPATH' ) ) {
    die( 'No script kiddies please!' );
};

?>

<h3 class="page-title">Markup Manager</h3>
<div class="wrap woocommerce" ng-app="curtainManager" ng-controller="curtainCtrl">
		
	<hr />

	<h4 class='header'>Select a Price Sheet</h4>
    <selectize style="width:500px" placeholder='Search for, or Select a Price Sheet' options='variations' config="myConfig" ng-model="curtaingroup" ng-disabled='disable'></selectize>

    <hr />
	<div class="table-wrapper">
		<h4 class="sub-title">Prices for: <span class="sheet-name" ng-cloak>{{friendlyName}}</span></h4> 
		<label style="float: right;margin-top: -34px;margin-right: 92px;" for="show-pagelimit">Show</label>
		<select id="show-pagelimit" style="float: right;margin-top: -38px;" ng-model="pageLimit">
			<option value="10">10</option>
			<option value="20">20</option>
			<option value="30">30</option>
			<option value="40">40</option>
			<option value="50">50</option>
			<option ng-value="showAll">Show All</option>
		</select>
		<hr>
		<table id="variation-table" class="wp-list-table widefat fixed striped posts">
			<thead>
				<tr>
					<td>Width (mm)</td>
					<td>Drop (mm)</td>
					<td>Price</td>
					<td>Marked Up Price (Inc VAT)</td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="item in allPrices | limitTo: pageLimit">
					<td ng-cloak>{{item.width}}</td>
					<td ng-cloak>{{item.height}}</td>
					<td ng-cloak>R {{item.price}}</td>
					<td ng-cloak><strong>R {{item.marked_up_price}}</strong></td>
				</tr>
			</tbody>
		</table>
	</div>

    <div style="width: 48%; float:left;">

		<h4 class="sub-title">Add Mark Up <span class="sheet-name" ng-cloak>{{friendlyName}}</span></h4>
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

		<hr />

		<h4 class="sub-title">Price Sheet Dimensions <span class="sheet-name" ng-cloak>{{friendlyName}}</span></h4>
		<table id="input-table" class="wp-list-table widefat fixed striped posts">
			<thead>
				<tr>
					<td>Maximum Width</td>
					<td>Minimum Width</td>
					<td>Maximum Drop</td>
					<td>Minimum Drop</td>
					<td>Save</td>
				</tr>
			</thead>
			<tbody>
				<tr class="price_dims">
					<td><input id="" ng-model="dimRestraints.maxWidth" class="dims-input range_1_from" type="text" /></td>
					<td><input id="" ng-model="dimRestraints.minWidth" class="dims-input range_1_to" type="text" /></td>
					<td><input id="" ng-model="dimRestraints.maxDrop" class="dims-input range_1_markup_by" type="text" /></td>
					<td><input id="" ng-model="dimRestraints.minDrop" class="dims-input range_1_markup_by" type="text" /></td>
					<td><button class="markup-button button button-primary button-small" ng-disabled="!dimRestraints" ng-click="saveDims(dimRestraints)">Save</button></td>
				</tr>
			</tbody>
		</table>		

<!-- 		<div class="meta-details">
		<h4 ng-cloak>Price Sheet Meta Details: <span class="sheet-name" ng-cloak>{{friendlyName}}</span></h4>
			<p ng-cloak>Min-Width: <strong>{{dimRestraints.minWidth}}<small>mm</small></strong></p>
			<p ng-cloak>Max-Width: <strong>{{dimRestraints.maxWidth}}<small>mm</small></strong></p>
			<p ng-cloak>Min-Drop: <strong>{{dimRestraints.minDrop}}<small>mm</small></strong></p>
			<p ng-cloak>Max-Drop: <strong>{{dimRestraints.maxDrop}}<small>mm</small></strong></p>
		</div> -->

		<hr />

		<div class="meta-details">
		<h4 ng-cloak>Swatches in this Group: <span class="sheet-name" ng-cloak>{{friendlyName}}</span></h4>
			<p ng-repeat="swatch in swatches" class="tag" ng-cloak>{{swatch.name}}</p>
		</div>

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