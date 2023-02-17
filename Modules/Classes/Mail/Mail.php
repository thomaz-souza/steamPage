<?php
/**
 * Classe de envio de mensagens
 *
 * @package Mail
 * @author Lucas/Postali
 */

namespace Mail;

use \PHPMailer\PHPMailer\PHPMailer;

class Mail extends \Core
{

	/**
	* @var object $PHPMailer Instância do PHP Mailer
	*/
	public $PHPMailer;

	/**
	* @var array $PHPMailer Instância do PHP Mailer
	*/
	public $debugOutput = [];

	function __construct($accountId = null)
	{
		parent::__construct();

		//Instanciar o PHP Mailer
		$this->PHPMailer = new PHPMailer();
		return $this->defineCredentials($accountId);
	}

	const MAIL_TEMPLATE_PATH = 'Resources/Mail/Templates/';

	/**
	* Define as credenciais de acesso
	*	
	* @param mixed $account ID da conta ou array com as configurações
	*
	* @return Mail
	*/
	public function defineCredentials($account = null)
	{
		//Busca as configurações de e-mail
		$config = $this->_config('mail');

		//Se o $account não for um array com dados, buscar as configurações de acordo com o ID da conta
		if(!is_array($account))
			$account = $account !== null && $config['accounts'][$account] ?
				$config['accounts'][$account] : reset($config['accounts']);

		//Iniciar SMTP do PHP Mailer
		$this->PHPMailer->isSMTP();

		//Definições de conexão
		$this->PHPMailer->Host = $account['host'];
		$this->PHPMailer->Username = $account['username'];
		$this->PHPMailer->Password = $account['password'];
		$this->PHPMailer->Port = intval($account['port']);

		//Definição de autenticação
		$this->PHPMailer->SMTPAuth = isset($account['SMTPAuth']) && $account['SMTPAuth'] == "1" ? true : false;

		//Definições de codigficação
		$this->PHPMailer->CharSet = isset($account['charSet']) ? $account['charSet'] : 'UTF-8';
		$this->PHPMailer->Encoding = isset($account['encoding']) ? $account['encoding'] : 'base64';

		//Se foi solicitada conexão com segurança
		if(isset($account['SMTPSecure']))
		{
			//Se houver SSL Especial
			if(($account['SMTPSecure'] == "tls-special" || $account['SMTPSecure'] == "ssl-special" ))
				$this->PHPMailer->SMTPOptions = [
				    'ssl' => [
				        'verify_peer' => false,
	                    'verify_peer_name' => false,
	                    'allow_self_signed' => true
				    ],
				];

			$this->PHPMailer->SMTPSecure = strtolower(str_replace("-special", "", $account['SMTPSecure']));
		}
		else
		{
			$this->PHPMailer->SMTPSecure = false;
		}

		//Incluir opção de Debug
		$this->PHPMailer->SMTPDebug = isset($account['SMTPDebug']) ? intval($account['SMTPDebug']) : 0;

		//Incluir função de captura de Debug
		$this->PHPMailer->Debugoutput = function($str, $level){
			$this->debugOutput[] = htmlspecialchars($str);
		};

		//Incluir endereço do remetente padrão
		if (isset($account['fromEmail']) && isset($account['fromName']))
		{
			$this->PHPMailer->setFrom($account['fromEmail'], $account['fromName']);
		}

		return $this;
	}

	/**
	* Resgata uma lista de destinatários. Retorna false se não houver tal lista
	*	
	* @param string $list ID da lista de destinatários
	*
	* @return mixed
	*/
	public function getRecipients ($list)
	{
		$config = $this->_config('mail');
		return isset($config['recipients'][$list]['addresses']) ? $config['recipients'][$list]['addresses'] : false;
	}

	/**
	* Definir uma lista de destinatários
	*	
	* @param string $list ID da lista de destinatários
	*
	* @return Mail
	*/
	public function setRecipients ($list)
	{
		//Busca destinatários
		$recipients = $this->getRecipients($list);
		
		if(!$recipients)
			return false;

		//Insere um por um
		foreach ($recipients as $email => $name)
			$this->addAddress($email, $name);

		return $this;
	}

	private $addresses = [];

	/**
	* Adiciona um destinatário
	*	
	* @param mixed $addresses E-mail único ou lista de endereços
	* @param string $name (Opcional) Nome do destinatário (a ser ignorado caso $addresses seja uma lista)
	*
	* @return Mail
	*/
	public function addAddress ($addresses, $name = null)
	{
		if (!is_array($addresses)) {
			$addresses = [['address' => $addresses, 'name' => $name]];
		}

		foreach ($addresses as $recipient)
		{
			$this->addresses[] = isset($recipient['address']) ? $recipient['address'] : $recipient;
			$this->PHPMailer->addAddress(isset($recipient['address']) ? $recipient['address'] : $recipient, isset($recipient['name']) ? $recipient['name'] : null);
		}

		return $this;
	}

	/**
	* Adiciona cópia oculta
	*	
	* @param mixed $addresses E-mail único ou lista de endereços
	* @param string $name (Opcional) Nome do destinatário (a ser ignorado caso $addresses seja uma lista)
	*
	* @return Mail
	*/
	public function addBcc ($addresses, $name = null)
	{
		if (!is_array($addresses)) {
			$addresses = [['address' => $addresses, 'name' => $name]];
		}

		foreach ($addresses as $recipient)
		{
			$this->addresses[] = isset($recipient['address']) ? $recipient['address'] : $recipient;
			$this->PHPMailer->addBcc(isset($recipient['address']) ? $recipient['address'] : $recipient, isset($recipient['name']) ? $recipient['name'] : null);
		}

		return $this;
	}

	/**
	* Define remetente
	*	
	* @param string $address E-mail do remetente
	* @param string $name (Opcional) Nome do remetente
	*
	* @return Mail
	*/
	public function setFrom($address, $name = null)
	{
		$this->PHPMailer->setFrom($address, $name);
		return $this;
	}

	/**
	* @var array $templates Templates já carregados
	*/
	private $templates = [];

	/**
	* Carrega um template. Retorna false caso template não exista
	*	
	* @param string $templateName Nome do arquivo do template
	*
	* @return string
	*/
	protected function _loadTemplate($templateName)
	{
		//Carregar template caso ainda não tenha sido carreagdo
		if (!isset($templates[$templateName]))
		{
			$file = $this->getPath(self::MAIL_TEMPLATE_PATH . $templateName . ".html");

			//Confirmar se arquivo existe
			if (!file_exists($file))
				return false;			

			$templates[$templateName] = file_get_contents($file);
		}

		return $templates[$templateName];
	}

	/**
	 *	Definir as variáveis no template
	 *
	 * @param string $template Recebe o contéudo do template
	 * @param array $vars Variáveis a serem inseridas
	 *
	 * @return null
	 */
	protected function _setVar(&$template, $vars = array())
	{
		foreach ($vars as $key => $var) {
			$template = preg_replace("/\<\!--\%" . $key . "\%--\>/", $var, $template);
		}
	}

	/**
	 * Converte o template para estilo antigo (aceitação no IE)
	 * 
	 * @param string $content Conteúdo atual
	 * 
	 * @return string
	 */
	protected function convertTemplateToOld ($content)
	{
		$incorporate = [];
		
		if(preg_match_all("/<style>([\s\S]+?)<\\/style>/", $content, $styles))
		{
			for($s=0; $s < count($styles[0]); $s++)
			{
				$style = preg_replace("/[\n]|[\s]{2,}/", "", $styles[1][$s]);

				if(preg_match_all("/([^\}\{]+)\{([\s\S]+?)\}/", $style, $rules))
				{
					for($r=0; $r < count($rules[0]); $r++)
					{
						$selectors = explode(",", $rules[1][$r]);
						$rule = $rules[2][$r];

						foreach ($selectors as $selector)
						{
							if(!isset($incorporate[$selector]))
								$incorporate[$selector] = "";
							$incorporate[$selector] .= $rule;
						}
					}
				}
			}
		}

		foreach ($incorporate as $selector => $rule)
		{
			$type = substr($selector,0,1);

			if($type == ".")
				$regex = "/<[^\>]+?class=[\\\"\\']" . substr($selector,1) . "[\\\"\\'][^\>]{0,}>/";

			else if($type == "#")
				$regex = "/<[^\>]+?id=[\\\"\\']" . substr($selector,1) . "[\\\"\\'][^\>]{0,}>/";

			else
				$regex = "/<". $selector . ">|<". $selector . " [^>]{0,}>/";

			$content = preg_replace_callback($regex, function($match) use ($rule) {

				if(preg_match("/ style\=/", $match[0]))
					return preg_replace("/style\=\"/", "style=\"" . $rule , $match[0]);
				
				else
					return preg_replace("/\>$/", " style=\"".$rule."\">" , $match[0]);

			}, $content);

		}		
		return $content;
	}


	/**
	 * Busca um template e insere as variáveis
	 *
	 * @param string $templateName Recebe o nome do template
	 * @param array $vars (Opcional) Variáveis a serem inseridas
	 *
	 * @return string
	 */
	public function template ($templateName, $vars = array())
	{
		$template = $this->_loadTemplate($templateName);
		$this->_setVar($template, $vars);
		return $this->convertTemplateToOld($template);
	}

	/**
	 * Define o corpo como template e insere as variáveis
	 *
	 * @param string $templateName Recebe o nome do template
	 * @param array $vars (Opcional) Variáveis a serem inseridas
	 *
	 * @return Mail
	 */
	public function setTemplate ($templateName, $vars = array())
	{
		$this->PHPMailer->Body = $this->template($templateName, $vars);
		return $this;
	}


	/**
	 * Busca um template e insere as variáveis
	 *
	 * @param string $templateName Recebe o nome do template
	 * @param array $vars (Opcional) Variáveis a serem inseridas
	 *
	 * @return mixed
	 */
	public function send ($subject, $content = null, $html = true)
	{
		//Definir como HTML
		$this->PHPMailer->isHTML($html);

		//Definir conteúdo, se passado
		if($content !== null)
			$this->PHPMailer->Body = $content;
		
		//Definir assundo
		$this->PHPMailer->Subject = $subject;

		//Enviar
		$send = $this->PHPMailer->send();


		//Caso tenha havido erro		
		if(!$send)
		{
			//Salvar no log
			@$this->_toLog();

			//Se o output está ligado, retornar um Fault com o debug
			if($this->PHPMailer->SMTPDebug != 0)
				return new \Fault( implode("<br>", $this->debugOutput), 'mail-connection', $this->debugOutput);

			//Caso o debug esteja desligado, emitir um Fault informando
			return new \Fault($this->write('It was not possible to send this email', 'admin'), 'mail-sending');
		}

		//Caso tenha havido sucesso no envio, mas o Debug esteja ligado, retornar o conteúdo do Debug
		if($this->PHPMailer->SMTPDebug != 0)
			return $this->debugOutput;

		return true;
	}

	const MAIL_LOG_PATH = 'Var/mail-log.json';

	private function _toLog ()
	{
		$file = $this->getPath(self::MAIL_LOG_PATH);

		$log = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

		$log[] = [
			'd' => date('d-m-Y H:i:s'),			//Data 
			'z' => $this->getTimezoneValue(),	//Timezone atual
			'l' => $this->currentLanguage(),	//Língua atual
			's' => $this->PHPMailer->Subject,	//Assunto
			'o' => $this->debugOutput,			//Debug (se houver)
			'f' => $this->PHPMailer->From,		//Remetente
			'n' => $this->addresses 			//Destinatários
		];

		file_put_contents($file, json_encode($log));
	}

}

?>