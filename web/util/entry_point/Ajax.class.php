<?php

    /**
     * @file 
     * @brief Ajax entry point
     */

    namespace util\entry_point;
    
    /**
     * @class Ajax
     * @brief This class must be implemented by any request handler
     */
    use ct\controllers\ajax\PrivateEventController;

				use util\superglobals\Superglobal;

				class Ajax implements EntryPoint
    {
    	protected $sg_get; /**< @brief Superglobal object for $_GET */
        /**
         * @copydoc EntryPoint::get_controller
         */
        public function __construct(){
        	$this->sg_get = new SG_Get();
        	 
        }
    	public function get_controller()
        {
			if($this->sg_get->check("req") == Superglobal::ERR_OK){
				$req = $this->sg_get->value("req");
				switch($req){
					case 101:
						return new PrivateEventController();
						break;
					default:
						return null;
						break;
				}
			}
        }
    };