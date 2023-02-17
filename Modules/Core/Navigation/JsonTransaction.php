<?php
/**
* Classe de resposta em JSON
*
* @package		Navigation
* @author 		Lucas/Postali
*/
namespace Navigation;

use CMS\CMS;

class JsonTransaction extends Navigation
{
	use \ValidationTrait;

	/**
	 * @var array Resposta a ser dada ao usuário
	 */
	private $outputResponse;

	/**
	* Instancia a função
	*
	* @param string $source Recebe a identificação da fonte de dados: get, post, request, json ou null
	* @return null
	*/
	function __construct ()
	{	
		//Inicia a função pai (_core)
		parent::__construct();

		$this->timeStart = microtime(true);

		//Incluir o status padrão na resposta
		$this->outputResponse = [
			'status' => 200
		];

		ob_start();
	}

	/**
	* Incluir os dados enviados na resposta (para fins de debug)
	*
	* @return null
	*/
	public function addRequestToResponse ()
	{
		$this->outputResponse['request'] = $this->getData();
	}

	/**
	* Implementa segurança de transação
	*
	* @return null
	*/
	public function transactionSecurity ()
	{
		$headers = array(
			"Access-Control-Allow-Origin: " . $this->getURL(),
			"Vary: Origin",
			"Content-Type: application/json; charset=UTF-8",
			"Access-Control-Max-Age: 3600",
			"Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With, Transaction-Token",
			"Access-Control-Allow-Methods: POST, OPTIONS"
		);

		$this->includeHeadersArray($headers);

		$headers = $this->getHeaders();

		//Verifica se o pedido foi feito por XMLHttpRequest/Ajax
		if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || !$_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
			$this->outputError('You are not allowed to make request', 403, 'notAllowedRequest');

		//Verifica se o token passado corresponde ao token de session
		if(!isset($headers['Transaction-Token']) || $headers['Transaction-Token'] !== $this->getTransactionToken())
			$this->outputError('You are not allowed to make request', 403, 'invalidToken');
	}

	/**
	* Exige que a requisição venha do admin
	*
	* @param integer $status Recebe o status a ser incluído na resposta
	* @return null
	*/
	public function requireAdmin ($die = true)
	{
		if(CMS::validateSession() !== true)
		{
			if($die)
				$this->outputError($this->write("You are not allowed to make this request", 'validation'), 403, 'invalidToken');
			else
				return false;
		}

		return true;
	}

	/**
	* Exige que o admin possua determinadas permissões
	*
	* @param string|array $permissions Lista de permissões exigidas
	* @param bool $die Se true, interrompe a execução, se não, retorna o status
	* 
	* @return bool
	*/
	public function requireAdminPermission ($permissions, $die = true)
	{
		if(!$this->requireAdmin($die))
			return false;

		$cms = new CMS();
		$userPermissions = $cms->getUserPermissions();

		//Normalizar permissões
		if(!is_array($permissions))
			$permissions = [$permissions];

		//Para cada permissão solicitada
		foreach ($permissions as $permission)
		{	
			//Verificar se a mesma existe
			if(!in_array($permission, $userPermissions))
			{
				//Se não existir, retornar erro
				if($die)
					$this->outputError($this->write("You are not allowed to make this request", 'validation'), 403, 'noPermission');
				else 
					return false;
			}
		}

		return true;			
	}

	/**
	* Define o status da resposta
	*
	* @param integer $status Recebe o status a ser incluído na resposta
	* @return null
	*/
	public function status ($status)
	{
		$this->outputResponse['status'] = $status;
		$this->setStatus($status);
	}

	/**
	* Definir dados da resposta.
	*
	* @param array $data Recebe os dados a serem incluídos na resposta
	* @param boolean $overwrite Caso true, a resposta será 'substituida' pela variável $data
	* @return array
	*/
	public function data ($data, $overwrite = false)
	{
		if($data instanceof \Fault)
			return $this->outputError($data);

		if(!isset($this->outputResponse['data']) || $overwrite === true)
			return $this->outputResponse['data'] = $data;

		return $this->outputResponse['data'] = array_merge($this->outputResponse['data'], $data);
	}

	/**
	* Exibir os dados em JSON e encerra o script
	*
	* @param array $data Recebe os dados crus a serem inseridos na resposta
	* @return null
	*/
	public function output ($data = null)
	{

		$timeEnd = microtime(true);

		//dividing with 60 will give the execution time in minutes otherwise seconds
		$executionTime = ($timeEnd - $this->timeStart);

		if(self::isTraceEnabled())
		{
			$this->outputResponse['executionTime'] = $executionTime;
			$this->outputResponse['trace'] = trace();
		}

		if($data)
			$this->outputJson($data);

		$this->outputJson($this->outputResponse);
	}

	/**
	* Exibe um erro em JSON
	*
	* @param string $message Recebe a mensagem de erro
	* @param integer $error Código de erro HTTP
	* @param string $code Código do erro interno
	* @return null
	*/
	public function outputError ($message = null, $error = 400, $code = null, $details = null)
	{
		//Se o erro estiver recebendo um Fault
		if($message instanceof \Fault)
		{
			$code = $message->getCode();
			$details = $message->toArray();
			$message = $message->getMessage();
		}

		//Mudar status
		$this->status($error);

		//Incluir dados do erro
		$this->outputResponse['error'] = array();

		//Se foi enviado uma mensagem, incluir
		if($message)
			$this->outputResponse['error']['message'] = $message;

		//Se foi enviado um código de erro, incluir
		if($code)
			$this->outputResponse['error']['code'] = $code;

		//Se foram enviado detalhes, incluir
		if($details)
			$this->outputResponse['error']['details'] = $details;
		
		//Exibir dados e encessar a página
		$this->output();
	}

	/**
	* Validar dados recebidos
	*
	* @param array $rulesMap Mapa de regras de dados
	* @param array $data Recebe os dados a serem validados
	* @return null
	*/
	public function validateRequest ($rulesMap, &$data = null)
	{
		//Validar respostas
		if($data === null)
			$data = $this->getData();
		
		$validateResponse = $this->validate($rulesMap, $data);

		//Se a resposta for um erro
		if($validateResponse instanceof \Fault)
			return $this->outputError($validateResponse);

		return true;
	}

	public function setError ($a, $b, $c)
	{
		return $this->outputError($b, 500, $a);
	}
		
}
