<?php
/**
* Objeto de valor puro do Icecream
*
* @package	Icecream
* @author 	Lucas/Postali
*/	
	namespace Icecream;

	class Raw
	{	
		/**
		 * Valor
		 * @var null
		 */
		private $_value = null;

		public function __construct ($value = null)
		{
			$this->_value = $value;
		}

		public function __toString()
	    {
	        return $this->_value;
	    }

	    public function getValue ()
	    {
	        return $this->_value;	    	
	    }
	}