<?php
	$this->variables['pageTitle'] = $this->write("Dashboard", 'admin');
	$blockFolder = 'View/CMS/blocks/';
?>


	<!--Begin::Dashboard -->

	<div class="row">

		<?php foreach ($this->Main->Interface->getBlocks() as $id => $block): ?>

			<div class="col-lg-<?= $block['size'] ?>" id="block_<?= $id ?>" data-block="<?= $id ?>">
				<?php 

					$file = $this->getPath($blockFolder . $block['board'] . '.php');

					require(file_exists($file) ? $file : $this->getPath($blockFolder . 'empty.php'));
				?>
			</div>

		<?php endforeach; ?>

	</div>