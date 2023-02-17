<!--begin::Body-->
	<body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
		
		<link href="~public/cms/assets/css/pages/login/login-4.css" rel="stylesheet" type="text/css" />

		<!--begin::Main-->
		<div class="d-flex flex-column flex-root">
			<!--begin::Login-->
			<div class="login login-4 login-signin-on d-flex flex-column flex-lg-row flex-row-fluid bg-white" id="kt_login">
				<!--begin::Aside-->
				<div class="login-aside order-2 order-lg-1 d-flex flex-column-fluid flex-lg-row-auto bgi-size-cover bgi-no-repeat p-7 p-lg-10">
					<!--begin: Aside Container-->
					<div class="d-flex flex-row-fluid flex-column justify-content-between">
						<!--begin::Aside body-->
						<div class="d-flex flex-column-fluid flex-column flex-center mt-5 mt-lg-0">
							<a href="#" class="mb-15 text-center">
								<img src="~<?= $this->Login->getLogoImage() ?>" class="max-h-75px max-w-200px" alt="" />
							</a>
							<!--begin::Signin-->
							<div class="login-form login-signin">
								<div class="text-center mb-10 mb-lg-20">
									<h2 class="font-weight-bold"><su: write="Sign in" scope="admin" /></h2>
									<p class="text-muted font-weight-bold"><su: write="Enter your username and password" scope="admin" /></p>
								</div>
								<!--begin::Form-->
								<form class="form" novalidate="novalidate" id="kt_login_signin_form">
									<div class="form-group py-3 m-0">
										<input class="form-control h-auto placeholder-dark-75" name="login" type="text" placeholder="<su: write='Username' scope='admin' />" name="username" autocomplete="off" />
									</div>
									<div class="form-group py-3">
										<input class="form-control h-auto placeholder-dark-75" name="password" type="Password" placeholder="<su: write='Password' scope='admin' />" name="password" />
									</div>
									<div class="form-group py-3m-0">
										<span id="login-message"></span>
									</div>
									<div class="form-group d-flex flex-wrap justify-content-between align-items-center mt-3">
										<!-- <label class="checkbox checkbox-outline m-0 text-muted">
										<input type="checkbox" name="remember" />Remember me
										<span></span></label>
										<a href="javascript:;" id="kt_login_forgot" class="text-muted text-hover-primary">Forgot Password ?</a> -->
									</div>
									<div class="form-group d-flex flex-wrap justify-content-between align-items-center mt-2">
										<div class="my-3 mr-2">
											<!-- <span class="text-muted mr-2">Don't have an account?</span>
											<a href="javascript:;" id="kt_login_signup" class="font-weight-bold">Signup</a> -->
										</div>
										
										<?php
											$captcha = new Google\ReCaptcha($this);
											//Incluir recaptcha se estiver disponível
											if($captcha->isAvailable()):

												echo $captcha->invisibleButton('button', $this->write("Sign in", "admin"), [
													'id' => "kt_login_signin_submit",
													'class' => "btn btn-primary font-weight-bold px-9 py-4 my-3",
													'data-callback' => 'submitLogin'
												]);

											else:
										?>

										<button id="kt_login_signin_submit" class="btn btn-primary font-weight-bold px-9 py-4 my-3" onclick="submitLogin()"><su: write="Sign in" scope="admin" /></button>

										<?php endif;?>
									</div>
								</form>
								<!--end::Form-->
							</div>
							<!--end::Signin-->
							<!--begin::Signup-->
							<div class="login-form login-signup">
								<div class="text-center mb-10 mb-lg-20">
									<h3 class="">Sign Up</h3>
									<p class="text-muted font-weight-bold">Enter your details to create your account</p>
								</div>
								<!--begin::Form-->
								<form class="form" novalidate="novalidate" id="kt_login_signup_form">
									<div class="form-group py-3 m-0">
										<input class="form-control h-auto border-0 px-0 placeholder-dark-75" type="text" placeholder="Fullname" name="fullname" autocomplete="off" />
									</div>
									<div class="form-group py-3 border-top m-0">
										<input class="form-control h-auto border-0 px-0 placeholder-dark-75" type="password" placeholder="Email" name="email" autocomplete="off" />
									</div>
									<div class="form-group py-3 border-top m-0">
										<input class="form-control h-auto border-0 px-0 placeholder-dark-75" type="password" placeholder="Password" name="password" autocomplete="off" />
									</div>
									<div class="form-group py-3 border-top m-0">
										<input class="form-control h-auto border-0 px-0 placeholder-dark-75" type="password" placeholder="Confirm password" name="cpassword" autocomplete="off" />
									</div>
									<div class="form-group mt-5">
										<label class="checkbox checkbox-outline">
										<input type="checkbox" name="agree" />I Agree the
										<a href="#">terms and conditions</a>.
										<span></span></label>
									</div>
									<div class="form-group d-flex flex-wrap flex-center">
										<button id="kt_login_signup_submit" class="btn btn-primary font-weight-bold px-9 py-4 my-3 mx-2">Submit</button>
										<button id="kt_login_signup_cancel" class="btn btn-outline-primary font-weight-bold px-9 py-4 my-3 mx-2">Cancel</button>
									</div>
								</form>
								<!--end::Form-->
							</div>
							<!--end::Signup-->
							<!--begin::Forgot-->
							<div class="login-form login-forgot">
								<div class="text-center mb-10 mb-lg-20">
									<h3 class="">Forgotten Password ?</h3>
									<p class="text-muted font-weight-bold">Enter your email to reset your password</p>
								</div>
								<!--begin::Form-->
								<form class="form" novalidate="novalidate" id="kt_login_forgot_form">
									<div class="form-group py-3 border-bottom mb-10">
										<input class="form-control h-auto border-0 px-0 placeholder-dark-75" type="email" placeholder="Email" name="email" autocomplete="off" />
									</div>
									<div class="form-group d-flex flex-wrap flex-center">
										<button id="kt_login_forgot_submit" class="btn btn-primary font-weight-bold px-9 py-4 my-3 mx-2">Submit</button>
										<button id="kt_login_forgot_cancel" class="btn btn-light-primary font-weight-bold px-9 py-4 my-3 mx-2">Cancel</button>
									</div>
								</form>
								<!--end::Form-->
							</div>
							<!--end::Forgot-->
						</div>
						<!--end::Aside body-->
						<!--begin: Aside footer for desktop-->
						<div class="d-flex flex-column-auto justify-content-between mt-15">
							<div class="text-dark-50 font-weight-bold order-2 order-sm-1 my-2">© 2020 Postali</div>
							<!-- <div class="d-flex order-1 order-sm-2 my-2">
								<a href="#" class="text-muted text-hover-primary">Privacy</a>
								<a href="#" class="text-muted text-hover-primary ml-4">Legal</a>
								<a href="#" class="text-muted text-hover-primary ml-4">Contact</a>
							</div> -->
						</div>
						<!--end: Aside footer for desktop-->
					</div>
					<!--end: Aside Container-->
				</div>
				<!--begin::Aside-->
				<!--begin::Content-->
				<div class="order-1 order-lg-2 flex-column-auto flex-lg-row-fluid d-flex flex-column p-7" style="background-image: url(~<?= $this->Login->getLoginImage() ?>);background-position: center; background-size: cover">
					<!--begin::Content body-->
					<div class="d-flex flex-column-fluid flex-lg-center">
						<!-- <div class="d-flex flex-column justify-content-center">
							<h3 class="display-3 font-weight-bold my-7 text-white">Welcome to Metronic!</h3>
							<p class="font-weight-bold font-size-lg text-white opacity-80">The ultimate Bootstrap, Angular 8, React &amp; VueJS admin theme
							<br />framework for next generation web apps.</p>
						</div> -->
					</div>
					<!--end::Content body-->
				</div>
				<!--end::Content-->
			</div>
			<!--end::Login-->
		</div>
		<!--end::Main-->

<!-- <script src="~public/cms/assets/js/pages/custom/login/login.js"></script> -->

<style type="text/css">
	
/*Botão de login*/
#kt_login_signin_submit{
	color: <?= $this->Login->getStyle('login-btn-tx'); ?>;
    background-color: <?= $this->Login->getStyle('login-btn-bg'); ?> ;
    border-color: <?= $this->Login->getStyle('login-btn-bg'); ?>;
}
#kt_login_signin_submit.btn.btn-primary:hover:not(.btn-text),
#kt_login_signin_submit.btn.btn-primary:focus:not(.btn-text),
#kt_login_signin_submit.btn.btn-primary.focus:not(.btn-text)
{
	color: <?= $this->Login->getStyle('login-btn-tx-active'); ?>;
    background-color: <?= $this->Login->getStyle('login-btn-bg-active'); ?>;
    border-color: <?= $this->Login->getStyle('login-btn-bg-active'); ?>;
}
</style>


<script type="text/javascript">

	function submitLogin ()
	{
		$('input').removeClass('is-invalid');
		//document.getElementById('kt_login_signin_submit').disabled = true;
		document.getElementById('login-message').innerHTML = "<su: write='Loading' scope='admin'/>";
		request.parseForm(document.getElementById('kt_login_signin_form'), loginCallback, Login.doLogin);
	}

	function loginCallback (response) 
	{
		$('input').removeClass('is-invalid');
		if(response.status == 200)
			return window.location.href = '<?= !empty($_GET['redir']) ? $_GET['redir'] : $this->getRouteURL('cms') ?>';

		if(!response['error'] || !response['error']['message'])
			response['error'] = {'message' : '<su: write="loginGenericError" scope="admin"/>'};

		if(response['error']['details']['object'])
			$('input[name='+response['error']['details']['object']+']').addClass('is-invalid');
		
		document.getElementById('login-message').innerHTML = response.error.message;
		document.getElementById('kt_login_signin_submit').disabled = false;

		grecaptcha.reset();
	}
</script>
<!-- end::Body -->