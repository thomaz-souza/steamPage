
<!--begin::Aside-->
<div class="aside aside-left  aside-fixed  d-flex flex-column flex-row-auto" id="kt_aside">

	<!--begin::Brand-->
	<div class="brand flex-column-auto " id="kt_brand">

		<!--begin::Logo-->
		<a href="<?= $this->getRouteURL() ?>" class="brand-logo">
			<img class="max-h-50px max-w-150px" alt="Logo" src="~<?= $this->Main->getLogoImage(); ?>" />
		</a>

		<!--end::Logo-->

		<!--begin::Toggle-->
		<button class="brand-toggle btn btn-sm px-0" id="kt_aside_toggle">
			<span class="svg-icon svg-icon svg-icon-xl">

				<!--begin::Svg Icon | path:assets/media/svg/icons/Navigation/Angle-double-left.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
					<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
						<polygon points="0 0 24 0 24 24 0 24" />
						<path d="M5.29288961,6.70710318 C4.90236532,6.31657888 4.90236532,5.68341391 5.29288961,5.29288961 C5.68341391,4.90236532 6.31657888,4.90236532 6.70710318,5.29288961 L12.7071032,11.2928896 C13.0856821,11.6714686 13.0989277,12.281055 12.7371505,12.675721 L7.23715054,18.675721 C6.86395813,19.08284 6.23139076,19.1103429 5.82427177,18.7371505 C5.41715278,18.3639581 5.38964985,17.7313908 5.76284226,17.3242718 L10.6158586,12.0300721 L5.29288961,6.70710318 Z" fill="#000000" fill-rule="nonzero" transform="translate(8.999997, 11.999999) scale(-1, 1) translate(-8.999997, -11.999999) " />
						<path d="M10.7071009,15.7071068 C10.3165766,16.0976311 9.68341162,16.0976311 9.29288733,15.7071068 C8.90236304,15.3165825 8.90236304,14.6834175 9.29288733,14.2928932 L15.2928873,8.29289322 C15.6714663,7.91431428 16.2810527,7.90106866 16.6757187,8.26284586 L22.6757187,13.7628459 C23.0828377,14.1360383 23.1103407,14.7686056 22.7371482,15.1757246 C22.3639558,15.5828436 21.7313885,15.6103465 21.3242695,15.2371541 L16.0300699,10.3841378 L10.7071009,15.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(15.999997, 11.999999) scale(-1, 1) rotate(-270.000000) translate(-15.999997, -11.999999) " />
					</g>
				</svg>

				<!--end::Svg Icon--></span> </button>

		<!--end::Toolbar-->
	</div>

	<!--end::Brand-->

	<!--begin::Aside Menu-->
	<div class="aside-menu-wrapper flex-column-fluid" id="kt_aside_menu_wrapper">

		<!--begin::Menu Container-->
		<div id="kt_aside_menu" class="aside-menu my-4 " data-menu-vertical="1" data-menu-scroll="1" data-menu-dropdown-timeout="500">

			<!--begin::Menu Nav-->
			<ul class="menu-nav ">

				<?php
									
					$modules = $this->Main->Interface->getSideMenu();

					//Para cada módulo
					foreach ($modules as $id => $values):

					if(!empty($values['parent'])) continue;

				?>

				<li data-id="<?= $id ?>" class="menu-item menu-item-submenu" aria-haspopup="true">

				<?php if(!empty($values['child'])): ?>

					<a class="menu-link menu-toggle">
						<?php if(!empty($values['icon'])): ?>
						<span class="svg-icon menu-icon"><i class="<?= $values['icon'] ?>"></i></span>
						<?php endif; ?>
						<span class="menu-text"><?= $values['title'] ?></span><i class="menu-arrow"></i>
					
				<?php else:

					$url = $values['url'] ? $values['url'] : $this->getRouteURL('cms', $values['action']);

				?>					
					<a href="<?= $url ?>" class="menu-link">
						<?php if(!empty($values['icon'])): ?>
						<span class="svg-icon menu-icon"><i class="<?= $values['icon'] ?>"></i></span>
						<?php endif; ?>
						<span class="menu-text"><?= $values['title'] ?></span>

				<?php endif; ?>

				<?php if($values['counter'] != ''): ?>

						<span class="menu-label"><span class="label label-rounded label-primary"><?= $values['counter'] ?></span></span>

				<?php endif; ?>

				</a>

				<?php if(!empty($values['child'])): ?>

					<div class="menu-submenu "><i class="menu-arrow"></i>
						<ul class="menu-subnav">

						<?php foreach ($values['child'] as $childId => $childValues):

							//Ignorar se módulo não estiver habilitado
							if(!isset($modules[$childId])) continue;

							$url = $childValues['url'] ? $childValues['url'] : $this->getRouteURL('cms', $childValues['action']);

						?>
							<li data-id="<?= $childId ?>" class="menu-item menu-item-submenu" aria-haspopup="true">

								<a href="<?= $url ?>" class="menu-link">
									<?php if(!empty($childValues['icon'])): ?>
									<span class="svg-icon menu-icon"><i class="<?= $childValues['icon'] ?>"></i></span>
									<?php endif; ?>
									<span class="menu-text"><?= $childValues['title'] ?></span>

									<?php if($childValues['counter']): ?>
										
										<span class="menu-label"><span class="label label-rounded label-primary"><?= $childValues['counter'] ?></span></span>

									<?php endif; ?>
								</a>
							</li>
						<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>
				</li>
			<?php endforeach; ?>

			</ul>

			<!--end::Menu Nav-->
		</div>

		<!--end::Menu Container-->
	</div>

	<!--end::Aside Menu-->
</div>

<!--end::Aside-->