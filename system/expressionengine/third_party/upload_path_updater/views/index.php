<style>
    .cpError {
        float: right;
        padding: 4px 7px;
        border:1px solid #AB0903; 
        background:#FFB1AB; 
        color: #AB0903; 
        border-radius: 7px;
    }
    .cpSuccess {
        float: right;
        padding: 4px 7px;
        border:1px solid #07AC00; 
        background:#B5FFAD; 
        color: #07AC00; 
        border-radius: 7px;
    }
</style>
<?php
/**
 * Use the built in table and forms helpers to build the back end UI.
 */
$this->table->set_template($cp_table_template);

# Column Headings
$this->table->set_heading(
    array('style' => 'width: 33%', 'data' => "Upload Location Name"), 
    "Server Path"
);

# Build a table row for each of the upload preferences found
if (is_array($uploadPreferences) && count($uploadPreferences) > 0):
foreach ($uploadPreferences as $prefObj) {
    
    # This is to show the flash data after the form has been processed
    $status = ee()->session->flashdata($prefObj->id);
    $updated = false;
    switch ($status) {
        case "success":
            $updated = '<span class="cpSuccess">Updated</span>';
            break;
        case "error":
            $updated = '<span class="cpError">Not Matched</span>';
            break;
        default:
            $updated = false;
            break;
    }
   
    # Add a table row for each of the upload paths.
    $this->table->add_row(
        ucwords(strtolower($prefObj->name)) . " " . $prefObj->status, 
        $prefObj->server_path . $updated
    );
}
else:
    $this->table->add_row(array(
        "data" => "No upload paths detected",
        "colspan" => 2
        ));
endif;

echo $this->table->generate();

# Second table used to position the input fields and house the form.
$this->table->set_template($cp_table_template);
echo form_open($actionUrl, '') ;

# Column Headings
$this->table->set_heading(
    array('style' => 'width: 50%', 'data' => "Replace This"), 
    "With This"
);

$this->table->add_row(
        
    # Replace Me String Field
    ee()->load->view('_formInput', array(
        "fieldName" => "replaceThisSection",
        "fieldValue" => ""
        ), TRUE),
        
    # Replacement String Field
    ee()->load->view('_formInput', array(
        "fieldName" => "replacementSection",
        "fieldValue" => isset($_SERVER["DOCUMENT_ROOT"]) ? $_SERVER["DOCUMENT_ROOT"] : ""
        ), TRUE)
);
?>

<? echo $this->table->generate();?>

<div style="padding: 7px 0;">
    <?=form_submit(array('name' => 'submit', 'value' => lang('submit'), 'class' => 'submit'));?>
</div>
<?= form_close() ?>

