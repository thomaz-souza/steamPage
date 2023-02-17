<?php
	//Adicionar TAG do Google Fonts no final da página
	$this->addTagCSS("https://fonts.googleapis.com/css?family=Nunito:400,600,700,900&display=swap", false);

	//Adicionar TAG do jQuery no início da página
	$this->addTagHeadJS("https://code.jquery.com/jquery-3.4.1.slim.min.js", false, 
		["integrity" => "sha256-pasqAKBDmFT4eHoN2ndd6lN370kFiGUFyTiUHWhU7k8=",
		"crossorigin" => "anonymous"]);

	//Adicionar tag das FontAwesome
	$this->addTagCSS("fontawesome/css/all.min.css");
?>

<style>
	body{
		font-family: 'Nunito', sans-serif;
	}
	.container{
		width: 95%;
		max-width: 900px;
		margin: 20px auto;
	}
	h1{
		text-align: center;
	}
	h2{
		text-align: center;
		border-top: 1px dotted #E7E7E7;
		margin-top: 40px;
		padding-top: 40px;	
	}
	h2 i{
		display: block;
	}
	p, li{
		line-height: 22px;
	}
	ul{
		list-style: none;
	}
	li{
		padding-top: 20px;
	}
	li.step{
		display: flex;
		align-items: center;
		transition: 0.5s all ease;
		flex-wrap: wrap;
	}
	li label{
		flex: 0 0 90%;
		padding-left: 2%;
	}
	li.done{
		/*text-decoration: line-through;*/
		color: #CCC;
	}
	em{
		padding: 5px 8px;
		background-color: #F5F5F5;
		color: #00994d;
		font-size: 13px;
		transition: 0.5s all ease;
	}
	em.code{
		color: #cc0052;
		font-family: monospace;
		font-variant: normal;
	}
	li.done em{
		background-color: #FCFCFC;
		color: #CCC;
	}
	.version{
		padding: 5px 8px;
		border: 1px solid #00994d;
		color: #00994d;
		font-size: 13px;
		display: inline-block;
		margin: 0px auto;
	}
	.flag{
		flex: 0 0 100%;
		display: block;
		margin-bottom: 1px;
		font-size: 10px;
		color: #e60000;
		transition: 0.5s all ease;
	}
	.flag.re{
		color:#ff8c1a;
	}
	.flag.op{
		color:#9966ff;
	}
	li.done .flag{
		color: #CCC;
	}
	button{
		border: 1px solid #999;
		color: #999;
		border-radius: 2px;
		padding: 8px;
		display: block;
		cursor: pointer;
		text-decoration: none !important;
		background-color: #FFF;
	}
	button:hover{
		text-decoration: none !important;
	}

	a:hover{
		text-decoration: none !important;
	}

	li .howto{
		white-space: nowrap;
	    flex: 0 0 100%;
	    text-align: right;
	}

	li .howto a{
	    padding: 3px 5px;
	    color: #999;
	    font-size: 12px;
	    border-radius: 5px;
	    transition: 0.5s all ease;
	    display: inline-block;
	}

	li .howto a:hover{
	    color: #e6b800;
	}

	li .howto a i{
		color: #e6b800;
	}

	a{
		color: #00b377;
		text-decoration: none;
	}
	a:hover{
		text-decoration: underline;	
	}

</style>
<div class="container">
	<h1>Bem-vindo ao Framework Postali!</h1>
	<p class="version">Versão 2.0</p>
	
	<?php if(version_compare(PHP_VERSION, REQUIRED_PHP_VERSION) < 1): ?>

		<h2><i class="fas fa-exclamation-triangle"></i> Ops!</h2>
		<h3>Antes de iniciarmos você precisa atualizar a versão do seu PHP!</h3>
		<p>A versão do PHP necessária para funcionamento é <em><?= REQUIRED_PHP_VERSION ?></em> ou maior. A versão atual do seu servidor é <?= PHP_VERSION ?></p>
		<p>Por favor, atualize sua versão para que possamos continuar!</p>

	<?php else: ?>

	<p>Parace que você iniciou um novo projeto no Framework Postali. Se você está vendo essa tela significa que está tudo corretamente configurado.</p>
	<p>A partir de agora você já pode iniciar a criação da sua aplicação web.</p>

	
	<p>Para te ajudar, vamos te dar uma mãozinha:</p>


	<h2><i class="fas fa-shoe-prints"></i> Próximos passos</h2>

	<h3><i class="fas fa-server"></i> Sistema</h3>
	<ul>
		<li class="step">
			<input type="checkbox" id="ch2">
			<label for="ch2"><p class="flag re">RECOMENDADO</p>Acesse a pasta <em>Modules/Custom</em> e crie uma nova pasta com o nome do seu projeto, por exemplo: "Site". Essa pasta conterá todas as funções principais da sua aplicação. </label>
			<div class="howto">
				<a href="https://fw.postali.com.br/docs#MduloseMtodos" class="howto"><i class="fas fa-lightbulb"></i> Como fazer isso?</a>
			</div>
		</li>

		<li class="step">
			<input type="checkbox" id="ch21">
			<label for="ch21"><p class="flag re">RECOMENDADO</p>Acesse a pasta <em>Controllers</em> e crie um novo Controller para sua aplicação. A partir disso, você pode criar funções importantes para executar serviços comuns a todas as páginas.</label>
			<div class="howto">
				<a href="https://fw.postali.com.br/docs#MduloseMtodos" class="howto"><i class="fas fa-lightbulb"></i> Como fazer isso?</a>
			</div>
		</li>

		<li class="step">
			<input type="checkbox" id="ch3">
			<label for="ch3"><p class="flag op">OPCIONAL</p>Caso sua aplicação tenha módulos específicos no CMS, crie um arquivo de configuração na pasta <em>Config</em> para futuramente inserir as instruções do módulo CMS.</label>
			<div class="howto">
				<a href="https://fw.postali.com.br/docs#CMS" class="howto"><i class="fas fa-lightbulb"></i> Como fazer isso?</a>
			</div>
		</li>
	</ul>

	<h3><i class="fas fa-clipboard-check"></i> Módulos</h3>
	<ul>
		<li class="step">
			<input type="checkbox" id="chdb">
			<label for="chdb"><p class="flag">OBRIGATÓRIO</p>Configure o módulo de Banco de Dados no arquivo <em>Config/db.config</em></label>
			<div class="howto">
				<a href="https://fw.postali.com.br/docs#BancodeDados_Configurando" class="howto"><i class="fas fa-lightbulb"></i> Como fazer isso?</a>
			</div>
		</li>		
		<li class="step">
			<input type="checkbox" id="chgoo">
			<label for="chgoo"><p class="flag re">RECOMENDADO</p>Configure o módulo do Google no arquivo <em>Config/google.config</em></label>
		</li>
		<li class="step">
			<input type="checkbox" id="ch-email">
			<label for="ch-email"><p class="flag op">OPCIONAL</p>Configure o módulo de E-mail no arquivo <em>Config/mail.config</em></label>
			<div class="howto">
				<a href="https://fw.postali.com.br/docs#E-mail" class="howto"><i class="fas fa-lightbulb"></i> Como fazer isso?</a>
			</div>
		</li>
	</ul>

	<h3><i class="fas fa-shapes"></i> Páginas e blocos</h3>
	<ul>
		<li class="step">
			<input type="checkbox" id="ch-edtp">
			<label for="ch-edtp"><p class="flag">OBRIGATÓRIO</p>Acesse o arquivo <em>Config/page.config</em> e inicie a criação das suas páginas e blocos. O cabeçalho e rodapé já existentes são ótimos modelos para você utilizar. Utilize-os sempre que possível.</label>
			<div class="howto">
				<a href="https://fw.postali.com.br/docs#Rotas" class="howto"><i class="fas fa-lightbulb"></i> Como fazer isso?</a>
			</div>
		</li>
		<li class="step">
			<input type="checkbox" id="ch-edtpy">
			<label for="ch-edtpy"><p class="flag re">RECOMENDADO</p>Configure suas páginas de acordo com os recursos de página, incluindo Tags Dinâmicas.</label>
			<div class="howto">
				<a href="https://fw.postali.com.br/docs#RenderizaodePginas" class="howto"><i class="fas fa-lightbulb"></i> Como fazer isso?</a>
			</div>
		</li>
	</ul>

	<h3><i class="fas fa-tools"></i> CMS</h3>
	<ul>
		<li class="step">
			<input type="checkbox" id="cms-inst">
			<label for="cms-inst"><p class="flag">OBRIGATÓRIO</p>Se você for utilizar um CMS nesse projeto, instale o CMS com o comando <em class="code">php console CMS/Console:install</em>. Lembre-se de ter uma conexão ativa com o banco de dados.</label>
			<div class="howto">
				<a href="https://fw.postali.com.br/docs#Rotas" class="howto"><i class="fas fa-lightbulb"></i> Como fazer isso?</a>
			</div>
		</li>
		<li class="step">
			<input type="checkbox" id="ch-edtpy">
			<label for="ch-edtpy"><p class="flag op">OPCIONAL</p>Acesse o arquivo <em>Config/cms.config</em> e personalize as cores do seu CMS.</label>
			<div class="howto">
				<a href="https://fw.postali.com.br/docs#RenderizaodePginas" class="howto"><i class="fas fa-lightbulb"></i> Como fazer isso?</a>
			</div>
		</li>
	</ul>


	<h2><i class="fas fa-archive"></i> Ferramentas disponíveis</h2>

	<h3><i class="fas fa-box-open"></i> Módulos nativos</h3>
		<ul>
			<li><strong>ORM Icecream</strong><br>É um poderoso e completo módulo de banco de dados que permite a manipulação de maneira fácil e dinâmica, facilitando, inclusive, a leitura do código. <br><a target="_blank" href="https://fw.postali.com.br/docs#Icecream">Ler documentação</a></li>

			<li><strong>Google</strong><br>Os módulos principais do Google já vêm preparados para uso, tais como o Analytics, Youtube, Maps e Captcha. <br><a target="_blank" href="https://fw.postali.com.br/docs#Google">Ler documentação</a></li>

			<li><strong>Pacote SEO</strong><br>Realiza a geração automática de Robots e Sitemap de acordo com as instruções de página, podendo também ler dados dinâmicos. <br><a target="_blank" href="https://fw.postali.com.br/docs#SEO">Ler documentação</a></li>

			<li><strong>E-mail</strong><br>Permite o envio de e-mails facilmente dentro do site. Basta configurar seu arquivo e então criar templates para enviar. <br><a target="_blank" href="https://fw.postali.com.br/docs#E-mail">Ler documentação</a></li>
			<li>E muito mais...</li>
		</ul>

	<h3><i class="fas fa-folder-open"></i> Estrutura e Arquivos</h3>
		<ul>
			<li><strong>Cabeçalho e Rodapé</strong><br>
				Os arquivos padrão de Cabeçalho e Rodapé já estão disponíveis na pasta <em>View</em> e configurados no <em>Config/page.config</em>. Caso esteja desenvolvendo uma estrutura padrão, recomendamos que você utilize os mesmos para garantir a uniformidade do código.
				<br><a target="_blank" href="https://fw.postali.com.br/docs#Rotas">Ler documentação</a>
			</li>
			<li><strong>Público</strong><br>
				A pasta <em>public</em> recebe todos os arquivos públicos, tais como imagens, fontes, CSS, JS e outros. Há uma estrutura pronta de pastas e arquivos criads para atender às necessidades de qualquer projeto.
				<br><a target="_blank" href="https://fw.postali.com.br/docs#Introduo_Estrutura">Ler documentação</a>
			</li>
			<li><strong>CMS</strong><br>
				Os arquivos do CMS já estão disponíveis na pasta <em>View/Admin</em> e configurados no <em>Config/page.config</em>. Caso prefira, você pode mudar o caminho do CMS no arquivo de configuração <em>Config/page.config</em>.
				<br><a target="_blank" href="https://fw.postali.com.br/docs#CMS">Ler documentação</a>
			</li>
			<li><strong>FontAwesome</strong><br>
				Dentro da pasta <em>public\fontawesome</em> você terá todos os arquivos necessários para a inclusão do FontAwesome no site. Sua utilização não é obrigatória.
				<br><a target="_blank" href="https://fontawesome.com/icons">Ler documentação</a>
			</li>
		</ul>

	<h2><i class="fas fa-question-circle"></i> Dúvidas</h2>
	<p>Se você estiver com dúvidas, não se preocupe. Acesse a documentação <a target="_blank" href="https://fw.postali.com.br/docs">clicando aqui</a> ou, se preferir, encontre abaixo o guia de página para consulta:</p>


	<h2 style="margin-bottom: 100px;">Agora você já pode excluir essa página e iniciar a criação do seu próprio conteúdo ou, se preferir, mantenha ela guardadinha e longe do acesso para futuras consultas. Foi bom estar com você, espero ter ajudado!</h2>

	<?php endif; ?>
</div>

<script>

	jQuery(document).ready(function()
	{
		$('li [type=checkbox]').click(function(){
			let e = $(this);
			if(e.prop("checked"))
				e.parent().addClass('done');
			else
				e.parent().removeClass('done');
		});
	})

</script>