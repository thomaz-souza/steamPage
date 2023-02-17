
	<div class="card card-custom card-stretch gutter-b">
		<div class="card-header align-items-center border-0 mt-4">
			<h3 class="card-title align-items-start flex-column">
				<span class="font-weight-bolder text-dark"><su: write="Notifications" scope="admin"/></span>
			</h3>						
		</div>

		<div class="card-body pt-4">
			<div class="scroll" data-scroll="true">
				<?php $events = 0; //Para cada Evento Novo
				foreach ($this->Main->Interface->getEvent('new') as $id => $values):
					?>
					<?php if($values['visible'] !== true) continue; ?>
					<?php $events++; ?>

					<div class="d-flex align-items-center mb-10">
						<div class="symbol symbol-40 symbol-light-<?= $values['color'] ?> mr-5">
							<span class="symbol-label">
								<span class="svg-icon svg-icon-lg icon-<?= $values['color'] ?>">
									<i class="<?= $values['icon'] ?> text-<?= $values['color'] ?>"></i>
								</span>
							</span>
						</div>
						<div class="d-flex flex-column font-weight-bold">
							<a href="<?= $this->getRouteURL('', $values['action']); ?>" class="text-dark text-hover-<?= $values['color'] ?> mb-1 font-size-lg"><?= $values['title'] ?></a>
							<span class="text-muted"><?= $values['time'] ?></span>
						</div>
					</div>
				<?php endforeach; ?>
				<?php
					if($events == 0)
						echo $this->write("There are no new notifications", 'admin');
				?>
			</div>
		</div>
	</div>