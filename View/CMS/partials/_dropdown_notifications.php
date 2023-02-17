
<!--begin::Header-->
<div class="d-flex flex-column pt-12 bg-dark-o-5 rounded-top">

	<!--begin::Title-->
	<h4 class="d-flex flex-center">
		<span class="text-dark"><?= $this->write("Notifications", 'admin') ?></span>
		<span class="btn btn-text btn-success btn-sm font-weight-bold btn-font-md ml-2"><?= $this->Main->Interface->getEventCount('new') ?></span>
	</h4>

	<!--end::Title-->

	<!--begin::Tabs-->
	<ul class="nav nav-bold nav-tabs nav-tabs-line nav-tabs-line-3x nav-tabs-primary mt-3 px-8" role="tablist">
		<li class="nav-item">
			<a class="nav-link active show" data-toggle="tab" href="#topbar_notifications_new"><?= $this->write("New", 'admin') ?></a>
		</li>
		<li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="#topbar_notifications_old"><?= $this->write("Old", 'admin') ?></a>
		</li>
	</ul>

	<!--end::Tabs-->
</div>

<!--end::Header-->

<!--begin::Content-->
<div class="tab-content">

	<?php foreach (['old', 'new'] as $type): $i = 0; ?>

	<!--begin::Tabpane-->
	<div class="tab-pane <?= ($type=='new') ? 'active' : ''; ?> show p-8" id="topbar_notifications_<?= $type ?>" role="tabpanel">
		<!--begin::Scroll-->
		<div class="scroll pr-7 mr-n7" data-scroll="true" data-height="300" data-mobile-height="200">
			
			<?php foreach ($this->Main->Interface->getEvent($type) as $id => $values): ?>
			<?php if($values['visible'] !== true) continue; ?>
			<?php $i++; ?>

				<!--begin::Item-->
				<div data-id="<?= $id ?>" class="d-flex align-items-center mb-6">

					<!--begin::Symbol-->
					<div class="symbol symbol-40 symbol-light-<?= $values['color'] ?> mr-5">
						<span class="symbol-label">
							<span class="svg-icon svg-icon-lg svg-icon-<?= $values['color'] ?>">
								<i class="<?= $values['icon'] ?> text-<?= $values['color'] ?>"></i>
							</span>
						</span>
					</div>
					<!--end::Symbol-->

					<!--begin::Text-->
					<div class="d-flex flex-column font-weight-bold">
						<a href="<?= isset($values['url']) && $values['url'] != "" ? $values['url'] : $this->getRouteURL('', $values['action']); ?>" class="text-dark text-hover-<?= $values['color'] ?> mb-1 font-size-lg"><?= $values['title'] ?></a>
						<span class="text-muted"><?= $values['time'] ?></span>
					</div>

					<!--end::Text-->
				</div>
				<!--end::Item-->
			<?php endforeach; ?>

			<?php if($i==0): ?>
				<div class="text-muted" su:write="There are no <?= $type ?> notifications" su:scope="admin"/></div>
			<?php endif; ?>

		</div>
		<!--end::Scroll-->
	</div>
	<!--end::Tabpane-->

	<?php endforeach; ?>
</div>

<!--end::Content-->