<?php
class block_gismo extends block_list {
	function init() {
		$this->title = get_string ( 'block_title', 'block_gismo' );
		$this->version = 2010110300;
		$this->content_type = BLOCK_TYPE_LIST;
        $this->cron = 900;
	}
	
	function has_config() {
		return false;
	}
	
	function specialization() {
		$this->course = get_record ( 'course', 'id', $this->instance->pageid );
	}
	
	function get_content() {
		global $CFG;
		
        // server data
        $data = new stdClass();
        $data->block_instance_id = $this->instance->id;
        $data->course_id = $this->course->id;
        $srv_data_encoded = urlencode(base64_encode(serialize($data)));
        
		if ($this->content !== NULL) {
			return $this->content;
		}
		
		$this->content = new stdClass ( );
		if (empty ( $this->instance ) or ! $this->has_view_capability()) {
			return $this->content;
		}
		$this->content->items = array ( );
		$this->content->icons = array ( );
		$this->content->footer = '';
        
		// Gismo
		$this->content->items [] = ($this->check_data() === true) ? '<a href="../blocks/gismo/main.php?srv_data='.$srv_data_encoded.'" target="_blank">GISMO</a>' : "GISMO (disabled)" . helpbutton("gismo", "Gismo requirements", "block_gismo", true, false, null, true);   // TODO: add help button if disabled
		$this->content->icons [] = '<img src="../blocks/gismo/images/gismo.gif" class="icon" alt="" />';
			
		return $this->content;
	}
	
	function instance_allow_multiple() {
		return false;
	}
	
	function instance_allow_config() {
		return false;
	}
	
	function config_print() {
		parent::config_print();
	}
	
	function config_save() {
		global $CFG;
		
		// useful variables
		$block_config_page = (isset ( $_REQUEST ["block"] )) ? sprintf ( "block.php?block=%u", intval ( $_REQUEST ["block"] ) ) : "blocks.php";
		
		// redirect to block config page
		header ( sprintf ( "Location: %s", $block_config_page ) );
	}
	
	function applicable_formats() {
		return array ('site' => false, 'course-view' => true );
	}
		
	/**
	 * has send capability for this block
	 **/
	function has_view_capability() { /// only teachers
		return has_capability ( 'block/gismo:view', get_context_instance ( CONTEXT_BLOCK, $this->instance->id ) );
	}
    
    function check_data() {
        global $CFG;
        // libraries
        $lib_dir = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'gismo' . DIRECTORY_SEPARATOR . 'server_side' . DIRECTORY_SEPARATOR;
        require_once $lib_dir . "GISMOutil.php";
        require_once $lib_dir . "FetchStaticDataMoodle.php"; 
        // FetchStaticDataMoodle instance
        $gismo_static_data = new FetchStaticDataMoodle($this->course->id);
        $gismo_static_data->init();
        // check
        return $gismo_static_data->checkData();
    }
    
    function cron() {
        global $CFG;
        // libraries
        $lib_dir = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'gismo' . DIRECTORY_SEPARATOR . 'server_side' . DIRECTORY_SEPARATOR;
        require_once $lib_dir . "GISMOutil.php";
        require_once $lib_dir . "GISMOdata_manager.php";

        // trace start
        mtrace("GISMO - cron (start)!");
        
        $gdm = new GISMOdata_manager(false);
        
        // purge
        $purge_check = $gdm->purge_data();
        if ($purge_check === true) {
            mtrace("Gismo data has been purged successfully!");        
        } else {
            mtrace($purge_check);    
        }
        
        // sync
        $sync_check = $gdm->sync_data();
        if ($sync_check === true) {
            mtrace("Gismo data has been syncronized successfully!");    
        } else {
            mtrace($sync_check);    
        }
        
        // trace end
        mtrace("GISMO - cron (end)!");
        
        // ok     
        return true;
    }
}
?>
