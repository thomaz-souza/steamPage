<?php
/**
* Classe para objeto de falha
*
* @package	Core
* @author 	Lucas/Postali
*/
	class Fault
	{
		private $message;
		private $code;
		private $object;
		private $details;

		function __construct (string $message, string $code, $object = null, $details = null)
		{
			$this->message = $message;
			$this->code = $code;
			$this->object = $object;
			$this->details = $details;

			$class = is_object($object) ? get_class($object) : 'Fault';

			trace($message, $class, $details, TRACE_ERROR);
		}

		public function getMessage ()
		{
			return $this->message;
		}

		public function getCode ()
		{
			return $this->code;
		}

		public function getObject ()
		{
			return $this->object;
		}

		public function getDetails ()
		{
			return $this->details;
		}

		public function toArray ()
		{
			return array(
				"message" => $this->getMessage(),
				"code" => $this->getCode(),
				"object" => $this->getObject(),
				"details" => $this->getDetails()
			);
		}

		public function __toString ()
		{
			return "FAULT [".$this->code."] ".$this->message;
		}
	}

?>