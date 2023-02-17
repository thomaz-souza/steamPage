
<!--begin::Header-->
<div class="d-flex align-items-center p-8 rounded-top">

	<!--begin::Symbol-->
	<!-- <div class="symbol symbol-md bg-light-primary mr-3 flex-shrink-0">
		<img src="assets/media/users/300_21.jpg" alt="" />
	</div> -->

	<!--end::Symbol-->

	<!--begin::Text-->
	<div class="text-dark m-0 flex-grow-1 mr-3 font-size-h5"><?= $this->Main->getUser('name'); ?></div>
	<!-- <span class="label label-light-success label-lg font-weight-bold label-inline">3 messages</span> -->
	<!--end::Text-->
</div>

<?php if(!empty($this->Main->Interface->getUserMenu())): ?>
<div class="separator separator-solid"></div>
<?php endif; ?>

<!--end::Header-->

<!--begin::Nav-->
<div class="navi navi-spacer-x-0 pt-5">

	<?php foreach ($this->Main->Interface->getUserMenu() as $id => $values): ?>

		<a data-id="<?= $id ?>" href="<?= isset($values['url']) && $values['url'] != "" ? $values['url'] : $this->getRouteURL('', $values['action']); ?>" class="navi-item px-8">
			<div class="navi-link">
				<div class="navi-icon mr-2">
					<i class="<?= $values['icon'] ?> text-<?= $values['color'] ?>"></i>
				</div>
				<div class="navi-text ">
					<div class="font-weight-bold">
						<?= $values['title'] ?>
					</div>
					<div class="text-muted">
						<?= $values['sub-title'] ?>
					</div>
				</div>
			</div>
		</a>

	<?php endforeach; ?>

	<!--begin::Footer-->
	<div class="navi-separator mt-3"></div>
	<div class="navi-footer px-8 py-5">
		<a onclick="Main.doLogout({},logoutCallback);" class="btn btn-light-primary font-weight-bold"><?= $this->write('Sign Out', 'admin')?></a>
		<!-- <a href="custom/user/login-v2.html" target="_blank" class="btn btn-clean font-weight-bold">Upgrade Plan</a> -->
	</div>

	<!--end::Footer-->
</div>
<!--end::Nav-->

<script>
	function logoutCallback (content)
	{
		if(content.status == 200)
			return window.location.href = '<?= $this->getRouteURL('cms-login'); ?>';

		//document.getElementById('logout-button').disabled = false;
	}
</script>