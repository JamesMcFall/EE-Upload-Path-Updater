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
 * This class handles the installation, removal and update process for the 
 * Upload_path_updater plugin.
 * 
 * @author James McFall <james@96black.co.nz>
 */
class Upload_path_updater_upd {

    public $version     = '1.0';
    public $className   = "Upload_path_updater";

    /**
     * Install the module & register the action ID's
     * 
     * @return <boolean> True as per docs - http://ellislab.com/expressionengine/user-guide/development/module_tutorial.html
     */
    function install() {
        
        ee()->load->dbforge();

        # Install the module
        $data = array(
            'module_name' => $this->className,
            'module_version' => $this->version,
            'has_cp_backend' => 'y',
            'has_publish_fields' => 'n'
        );
        ee()->db->insert('modules', $data);

        # Set up the action ID for the upload method
        $data = array(
            'class' => $this->className,
            'method' => 'updatePaths'
        );
        ee()->db->insert('actions', $data);


        return true;
    }

    /**
     * Uninstall the module and free the action ID's
     * 
     * @return <boolean> True as per docs - http://ellislab.com/expressionengine/user-guide/development/module_tutorial.html
     */
    function uninstall() {
        ee()->load->dbforge();

        # Removing the module
        ee()->db->select('module_id');
        $query = ee()->db->get_where('modules', array('module_name' => $this->className));

        # Remove from module_member_groups
        ee()->db->where('module_id', $query->row('module_id'));
        ee()->db->delete('module_member_groups');

        # Remove from modules
        ee()->db->where('module_name', 'Upload_path_updater');
        ee()->db->delete('modules');

        # Remove the action from the actions table
        ee()->db->where('class', $this->className);
        ee()->db->delete('actions');

        return true;
    }

    /**
     * Update Method - Currently not in use
     * 
     * @param <string> $current
     * @return <boolean>
     */
    function update($current = '') {
        return FALSE;
    }

}

?>
