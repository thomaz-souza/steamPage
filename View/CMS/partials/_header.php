
<!--begin::Header-->
<div id="kt_header" class="header ">

	<!--begin::Container-->
	<div class=" container-fluid  d-flex align-items-stretch justify-content-between">

		<!--begin::Header Menu Wrapper-->
		<div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">

			<!--begin::Header Menu-->
			<div id="kt_header_menu" class="header-menu header-menu-mobile  header-menu-layout-default ">

				<!--begin::Header Nav-->
				<ul class="menu-nav ">

					<?php foreach ($this->Main->Interface->getTopBarMenu() as $id => $values): ?>
						
					<li class="menu-item" data-menu-toggle="click" aria-haspopup="true"><a data-id="<?= $id ?>" href="<?= isset($values['url']) && $values['url'] != "" ? $values['url'] : $this->getRouteURL('', $values['action']); ?>" class="menu-link menu-toggle"><span class="svg-icon menu-icon"><i class="<?= $values['icon'] ?>"></i></span> <span class="menu-text"><?= $values['title'] ?></span></a>
					</li>

					<?php endforeach; ?>

				</ul>

				<!--end::Header Nav-->
			</div>

			<!--end::Header Menu-->
		</div>

		<!--end::Header Menu Wrapper-->

		<!--begin::Topbar-->
		<div class="topbar">

			<!--begin::Notifications-->
			<div class="dropdown">

				<!--begin::Toggle-->
				<div class="topbar-item" data-toggle="dropdown" data-offset="10px,0px">
					<div class="btn btn-icon btn-clean btn-dropdown btn-lg mr-1 pulse pulse-primary">
						<span class="svg-icon svg-icon-xl svg-icon-primary">

							<!--begin::Svg Icon | path:assets/media/svg/icons/Code/Compiling.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
								<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
									<rect x="0" y="0" width="24" height="24" />
									<path d="M2.56066017,10.6819805 L4.68198052,8.56066017 C5.26776695,7.97487373 6.21751442,7.97487373 6.80330086,8.56066017 L8.9246212,10.6819805 C9.51040764,11.267767 9.51040764,12.2175144 8.9246212,12.8033009 L6.80330086,14.9246212 C6.21751442,15.5104076 5.26776695,15.5104076 4.68198052,14.9246212 L2.56066017,12.8033009 C1.97487373,12.2175144 1.97487373,11.267767 2.56066017,10.6819805 Z M14.5606602,10.6819805 L16.6819805,8.56066017 C17.267767,7.97487373 18.2175144,7.97487373 18.8033009,8.56066017 L20.9246212,10.6819805 C21.5104076,11.267767 21.5104076,12.2175144 20.9246212,12.8033009 L18.8033009,14.9246212 C18.2175144,15.5104076 17.267767,15.5104076 16.6819805,14.9246212 L14.5606602,12.8033009 C13.9748737,12.2175144 13.9748737,11.267767 14.5606602,10.6819805 Z" fill="#000000" opacity="0.3" />
									<path d="M8.56066017,16.6819805 L10.6819805,14.5606602 C11.267767,13.9748737 12.2175144,13.9748737 12.8033009,14.5606602 L14.9246212,16.6819805 C15.5104076,17.267767 15.5104076,18.2175144 14.9246212,18.8033009 L12.8033009,20.9246212 C12.2175144,21.5104076 11.267767,21.5104076 10.6819805,20.9246212 L8.56066017,18.8033009 C7.97487373,18.2175144 7.97487373,17.267767 8.56066017,16.6819805 Z M8.56066017,4.68198052 L10.6819805,2.56066017 C11.267767,1.97487373 12.2175144,1.97487373 12.8033009,2.56066017 L14.9246212,4.68198052 C15.5104076,5.26776695 15.5104076,6.21751442 14.9246212,6.80330086 L12.8033009,8.9246212 C12.2175144,9.51040764 11.267767,9.51040764 10.6819805,8.9246212 L8.56066017,6.80330086 C7.97487373,6.21751442 7.97487373,5.26776695 8.56066017,4.68198052 Z" fill="#000000" />
								</g>
							</svg>

							<!--end::Svg Icon--></span>
							<?php 
								$i = 0;
								foreach ($this->Main->Interface->getEvent('new') as $id => $values)
									if($values['visible'] === true) $i++;
								if($i > 0):
							?>
							<span class="pulse-ring"></span>
						<?php endif; ?>
					</div>
				</div>

				<!--end::Toggle-->

				<!--begin::Dropdown-->
				<div class="dropdown-menu p-0 m-0 dropdown-menu-right dropdown-menu-anim-up dropdown-menu-lg">
					<form>
						<?= $this->block('n-cms-partial-dropdown-notifications') ?>
					</form>
				</div>

				<!--end::Dropdown-->
			</div>

			<!--end::Notifications-->

			<!--begin::User-->
			<div class="dropdown">

				<!--begin::Toggle-->
				<div class="topbar-item" data-toggle="dropdown" data-offset="0px,0px">
					<div class="btn btn-icon w-auto btn-clean d-flex align-items-center btn-lg px-2">
						<span class="text-dark-50 font-weight-bolder font-size-base d-none d-md-inline mr-3"><?= $this->Main->getUserFirstName(); ?></span>
						<span class="symbol symbol-35 symbol-light-success">
							<span class="symbol-label font-size-h5 font-weight-bold"><?= $this->Main->getUserInitial(); ?></span>
						</span>
					</div>
				</div>

				<!--end::Toggle-->

				<!--begin::Dropdown-->
				<div class="dropdown-menu p-0 m-0 dropdown-menu-right dropdown-menu-anim-up dropdown-menu-lg p-0">
					<?= $this->block('n-cms-partial-dropdown-user') ?>
				</div>

				<!--end::Dropdown-->
			</div>

			<!--end::User-->
		</div>

		<!--end::Topbar-->
	</div>

	<!--end::Container-->
</div>

<!--end::Header-->