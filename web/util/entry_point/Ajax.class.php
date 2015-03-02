<?php

    /**
     * @file 
     * @brief Ajax entry point
     */

    namespace util\entry_point;
    
    use ct\controllers\ajax\PrivateEventController;
    use util\superglobals\Superglobal;
    use util\superglobals\SG_Get;

    /**
     * @class Ajax
     * @brief This class must be implemented by any request handler
     */
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
			if($this->sg_get->check("req") !== Superglobal::ERR_OK)
                return null;

			switch($this->sg_get->value("req"))
            {
				case "61":
					return new PrivateEventController();
				default:
					return null;

			}
        }
    };