<?php
/**
 * Upload Path Updater Module
 *
 * This module allows admins to update the upload paths used on the site in one 
 * place.
 * 
 * @author James McFall <james@96black.co.nz>
 * @version 1.0
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Wbop_practice_upload_upd
 * 
 * This class contains the control panel processing code for this module. It 
 * allows a user to do a search/replace on the upload paths, which is useful
 * when migrating between servers.
 */
class Upload_path_updater_mcp {

    # Class Properties
    public $className     = "Upload_path_updater";
    private $_uploadPrefs = null;
    public $modulePath    = null;
    public $start         = null;
    
    
    /**
     * Constructore
     */
    public function __construct() {

        # Load up a few used libraries
        ee()->load->library('table');
        ee()->load->library('input');
        ee()->load->helper('form');

        # Set the page title
        ee()->view->cp_page_title = lang('upload_path_updater_module_page_title');
        
        # Set the module path
        $this->modulePath = 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=' . strtolower($this->className)  . AMP . 'method=';
        
        # Load the upload preferences into memory
        if (is_null($this->_uploadPrefs)) {
            $this->_loadUploadPreferences();
        }
    }

    
    /**
     * This action is responsible for just showing the upload path data.
     * 
     * @return <string> View data
     */
    public function index() {
        
        # View Data
        $viewData                      = array();
        $viewData['actionUrl']         = $this->modulePath . 'updatePaths';
        $viewData['uploadPreferences'] = $this->_uploadPrefs;
        
        return ee()->load->view('index', $viewData, TRUE);
    }

    
    /**
     * This action is responsible for processing the update path form and setting
     * some flash data used for feedback.
     * 
     * @return <void>
     */
    public function updatePaths() {
        
        # Only start processing if the form is submitted.
        if (ee()->input->post("submit")) {
            
            # Check both of the required fields are supplied.
            if (strlen(ee()->input->post('replaceThisSection')) == 0 || strlen(ee()->input->post('replacementSection')) == 0) {
                
                # No path supplied. Set an error and redirect back to index.
                ee()->session->set_flashdata('message_error', lang('upload_path_updater_error_no_paths'));
                ee()->functions->redirect(BASE . AMP . $this->modulePath . "index");
            }
               
            # Loop through each of the upload preference and update the paths
            foreach ($this->_uploadPrefs as $pathObject) {

                # If we can't find the section to replace in the server path, set an error
                if (!strstr($pathObject->server_path, ee()->input->post('replaceThisSection'))) {
                    ee()->session->set_flashdata($pathObject->id, "error");
                    continue;
                }

                # Build replacement path
                $newPath = str_replace(ee()->input->post('replaceThisSection'), ee()->input->post('replacementSection'), $pathObject->server_path);

                # Update the row in the database
                ee()->db->where("name", $pathObject->name);
                ee()->db->limit(1);
                ee()->db->update("exp_upload_prefs", array("server_path" => $newPath));

                # Set success flash message
                ee()->session->set_flashdata($pathObject->id, "success");
            }

            # Successful update.
            ee()->session->set_flashdata('message_success', lang('upload_path_updater_success'));
        }
        
        # Redirect back to the index.
        ee()->functions->redirect(BASE . AMP . $this->modulePath . "index");
    }
    
    
    /**
     * Load the upload preference rows into a class property.
     * 
     * @return boolean
     */
    private function _loadUploadPreferences() {
        
        # Get all the upload prefs
        $result = ee()->db->get("exp_upload_prefs");
        
        # No upload preferences? Nothing to show here.
        if ($result->num_rows() == 0) {
            return false;
        }
        
        # Load each of the upload preferences found into the class property
        foreach ($result->result() as $row) {
            
            # We use this status to show if the row has been updated or failed to
            $row->status = "OK";
            
            # Add the DB row to the array.
            $this->_uploadPrefs[] = $row;
        }
        
        return true;
    }
}
?>
