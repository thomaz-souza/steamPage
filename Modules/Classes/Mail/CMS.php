<?php
/**
 * Classe de envio de mensagens
 *
 * @package Mail
 * @author Lucas/Postali
 */

	namespace Mail;

	class CMS extends Mail
	{
		public function sideMenu (\CMS\InterfaceCMS $interface)
		{
			$interface->addSideMenu(array(
				"icon" => "fas fa-envelope-open-text",
				"title" => $this->write("Email", "admin"),
				"child" => [
					'configuration' => 
					[
						"icon" => "fas fa-at",
						"title" => $this->write("Accounts", "admin"),
						"action" => ["Email", "Accounts"],
					],
					'recipients' =>
					[
						"icon" => "fas fa-address-book",
						"title" => $this->write("Recipients", "admin"),
						"action" => ["Email","Recipients"],
					],
					'log' => 
					[
						"icon" => "fas fa-mail-bulk",
						"title" => $this->write("Mail log", "admin"),
						"action" => ["Email", "Log"],
					],
				]
			), 'email');

		}

		/**
		 * Envia um e-mail de teste
		 *
		 * @return mixed
		 */
		public function sendingTestEmail ($data, &$transaction)
		{
			//Definir credenciais
			$this->defineCredentials($data['account']);

			//Adicionar destinatário
			$this->addAddress($data['email']);

			//Assunto
			$subject = $this->write('Sending test', 'admin');

			//Mensagem
			$message = $this->write('This is a test message from Postali Framework. If you are reading this, then your account settings might be right!', 'admin');

			//Definir nível de debug
			$this->PHPMailer->SMTPDebug = 2;

			//Enviar
			return $this->send($subject, $message, true);
		}

		/**
		 * Resgata arquivo de log
		 *
		 * @return mixed
		 */
		public function getLog ()
		{
			$file = $this->getPath(self::MAIL_LOG_PATH);
			return file_exists($file) ? json_decode(file_get_contents($file)) : [];
		}

	}