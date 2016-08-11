<script type="text/javascript">
    jQuery(document).ready(function($){
        
        $("#show_debug").click(function(){
            $("#debug").show();
            $(this).hide();
        });
        
        doAjaxImport(<?php echo intval($_POST['limit']); ?>, 0);
        
        function doAjaxImport(limit, offset) {
            var data = {
                "action"             : "bd_price_import_ajax",
                "uploaded_file_path" : <?php echo json_encode($_POST['uploaded_file_path']); ?>,
                "limit"              : limit,
                "offset"             : offset,
                'type'               : "<?php echo $_POST['type']; ?>",
                <?php if (isset($_POST['term_id'])): ?>
                    'term_id'           : "<?php echo $_POST['term_id']; ?>",
                <?php elseif (isset($_POST['field_choice'])): ?>
                    'field_choice'      : "<?php echo $_POST['field_choice']; ?>",
                <?php endif; ?>
            };
            //console.log(data);
            //ajaxurl is defined by WordPress
            $.post(ajaxurl, data, ajaxImportCallback, 'json');
        }
        
        function ajaxImportCallback(response_text) {
            console.log(response_text);
            $("#debug").append($(document.createElement("p")).text(response_text));
            
            var response = response_text;
            
            $("#insert_count").text(response.insert_count + " (" + response.insert_percent +"%)");
            $("#remaining_count").text(response.remaining_count);
            $("#row_count").text(response.row_count);
            
            //show inserted rows
            for(var row_num in response.inserted_rows) {
                var tr = $(document.createElement("tr"));
                
                if(response.inserted_rows[row_num]['success'] == true) {
                    if(response.inserted_rows[row_num]['has_errors'] == true) {
                        tr.addClass("error");
                    } else {
                        tr.addClass("success");
                    }
                } else {
                    tr.addClass("fail");
                }
                
                tr.append($(document.createElement("td")).append($(document.createElement("span")).addClass("icon")));
                tr.append($(document.createElement("td")).text(response.inserted_rows[row_num]['row_id']));
                <?php if (isset($_POST['term_id'])): ?>
                    tr.append($(document.createElement("td")).text(response.inserted_rows[row_num]['term_id']));
                    tr.append($(document.createElement("td")).text(response.inserted_rows[row_num]['category_name']));
                <?php else: ?>
                    tr.append($(document.createElement("td")).text(response.inserted_rows[row_num]['field_label']));
                    tr.append($(document.createElement("td")).text(response.inserted_rows[row_num]['field_choice']));
                <?php endif; ?>
                
                
                var result_messages = "";
                if(response.inserted_rows[row_num]['has_errors'] == true) {
                    result_messages += response.inserted_rows[row_num]['errors'].join("\n") + "\n";
                } else {
                    result_messages += "No errors.";
                }
                tr.append($(document.createElement("td")).text(result_messages));
                
                tr.appendTo("#inserted_rows tbody");
            }
            
            //show error messages
            for(var message in response.error_messages) {
                $(document.createElement("li")).text(response.error_messages[message]).appendTo(".import_error_messages");
            }
            
            //move on to the next set!
            if(parseInt(response.remaining_count) > 0) {
                doAjaxImport(response.limit, response.new_offset);
            } else {
                $("#import_status").addClass("complete");
            }
        }
    });
</script>

<div class="woo_product_importer_wrapper wrap">
    <div id="icon-tools" class="icon32"><br /></div>
    <?php echo 'Results' ?>

    <ul class="import_error_messages"></ul>

    <div id="import_status">
        <div id="import_in_progress">
            <img src="<?php echo plugin_dir_url(__FILE__); ?>img/ajax-loader.gif"
                alt="Importing. Please do not close this window or click your browser's stop button."
                title="Importing. Please do not close this window or click your browser's stop button.">
            
            <strong>Importing. Please do not close this window or click your browser's stop button.</strong>
        </div>
        <div id="import_complete">
            <img src="<?php echo plugin_dir_url(__FILE__) ?>img/complete.png"
                alt="Import complete!"
                title="Import complete!">
            <strong>Import Complete! Results below.</strong>
        </div>
        
        <table>
            <tbody>
                <tr>
                    <th>Processed</th>
                    <td id="insert_count">0</td>
                </tr>
                <tr>
                    <th>Remaining</th>
                    <td id="remaining_count"><?php echo $_POST['row_count'] ?></td>
                </tr>
                <tr>
                    <th>Total</th>
                    <td id="row_count"><?php echo $_POST['row_count'] ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <table id="inserted_rows" class="wp-list-table widefat fixed pages" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 30px;"></th>
                <th style="width: 80px;">CSV Row</th>
                <?php if (isset($_POST['term_id'])): ?>
                    <th style="width: 150px;">Term ID</th>
                    <th style="width: 500px;">Category</th>
                <?php else: ?>
                    <th style="width: 150px;">Addon Type</th>
                    <th style="width: 500px;">Addon</th>
                <?php endif; ?>
                <th>Result</th>
            </tr>
        </thead>
        <tbody><!-- rows inserted via AJAX --></tbody>
    </table>
    
    <p><a id="show_debug" href="#" class="button">Show Raw AJAX Responses</a></p>
    <div id="debug"><!-- server responses get logged here --></div>
    
</div>
<style type="text/css">
    .woo_product_importer_wrapper form { padding: 20px 0; }
    
    .woo_product_importer_wrapper .import_error_messages {
        margin: 6px 0;
        padding: 0;
    }
    
    .woo_product_importer_wrapper .import_error_messages li {
        margin: 2px 0;
        padding: 4px;
        background-color: #f9dede;
        border: 1px solid #ff8e8e;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border-radius: 4px;
    }
    
    .woo_product_importer_wrapper #import_status {
        padding: 8px 8px 8px 82px;
        min-height: 66px;
        position: relative;
        margin: 6px 0;
        background-color: #fff5d1;
        border: 1px solid #ffc658;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border-radius: 4px;
    }
    .woo_product_importer_wrapper #import_status.complete {
        background-color: #ecfdbe;
        border: 1px solid #a1dd00;
    }
    
    .woo_product_importer_wrapper #import_status img {
        position: absolute;
        top: 8px;
        left: 8px;
    }
    
    .woo_product_importer_wrapper #import_status strong {
        font-size: 18px;
        line-height: 1.2em;
        padding: 6px 0;
        display: block;
    }
    
    .woo_product_importer_wrapper #import_status #import_in_progress { display: block; }
    .woo_product_importer_wrapper #import_status.complete #import_in_progress { display: none; }
    
    .woo_product_importer_wrapper #import_status #import_complete { display: none; }
    .woo_product_importer_wrapper #import_status.complete #import_complete { display: block; }
    
    .woo_product_importer_wrapper #import_status td,
    .woo_product_importer_wrapper #import_status th {
        text-align: left;
        font-size: 13px;
        line-height: 1em;
        padding: 4px 10px 4px 0;
    }
    
    .woo_product_importer_wrapper table th { vertical-align: top; }
    
    .woo_product_importer_wrapper table th.narrow,
    .woo_product_importer_wrapper table td.narrow { width: 65px; }
    .woo_product_importer_wrapper table input { margin: 1px 0; }
    
    .woo_product_importer_wrapper table tr.header_row th {
        background-image: none;
        vertical-align: middle;
        font-weight: bold;
    }
    
    .woo_product_importer_wrapper .map_to_settings {
        margin: 2px 0;
        padding: 2px;
        overflow: hidden;
    }
    
    .woo_product_importer_wrapper .field_settings {
        display: none;
        margin: 2px 0;
        padding: 4px;
        background-color: #e0e0e0;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;
    }
    .woo_product_importer_wrapper .field_settings h4 {
        margin: 0;
        font-size: 0.9em;
        line-height: 1.2em;
    }
    .woo_product_importer_wrapper .field_settings p {
        margin: 4px 0;
        overflow: hidden;
        font-size: .9em;
        line-height: 1.3em;
    }
    .woo_product_importer_wrapper .field_settings input[type="text"] { width: 98%; }
    
    .woo_product_importer_wrapper #inserted_rows tr.error td { background-color: #FFF6D3; }
    .woo_product_importer_wrapper #inserted_rows tr.fail td { background-color: #FFA8A8; }
    
    .woo_product_importer_wrapper #inserted_rows .icon {
        display: block;
        width: 16px;
        height: 16px;
        background-position: 0 0;
        background-repeat: no-repeat;
    }
    .woo_product_importer_wrapper #inserted_rows tr.success .icon { background-image: url('<?php echo plugin_dir_url(__FILE__); ?>img/accept.png'); }
    .woo_product_importer_wrapper #inserted_rows tr.error .icon { background-image: url('<?php echo plugin_dir_url(__FILE__); ?>img/error.png'); }
    .woo_product_importer_wrapper #inserted_rows tr.fail .icon { background-image: url('<?php echo plugin_dir_url(__FILE__); ?>img/exclamation.png'); }
    
    .woo_product_importer_wrapper #debug {
        display: none;
        font-family: monospace;
        font-size: 14px;
        line-height: 16px;
        color: #333;
        background-color: #f5f5f5;
        border: 1px solid #efefef;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;
        padding: 0 10px;
    }
</style>