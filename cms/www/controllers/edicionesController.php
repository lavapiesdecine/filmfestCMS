<? 
	namespace www\controllers;

	class edicionesController extends \core\Controller{
		
	    public function __construct($data) {
	    	parent::__construct($data);
	    }
	    
	    public function index(){
	    	$this->_title = _("ediciones.title");
	    	$this->_description = _("ediciones.description");
			$this->addData(array("ediciones" => $this->_dao->edicionesDAO()));
			$this->loadView();
	        
	    }
	}