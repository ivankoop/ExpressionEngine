<a class="has-sub" href="">
	<?=strtolower(lang($label))?>
	<?php if ($value): ?>
	<span class="faded">(<?=$value?>)</span>
	<?php endif; ?>
</a>
<div class="sub-menu">
	<?php if ($has_custom_value): ?>
	<fieldset class="filter-search">
		<input
			type="text"
			name="<?=$name?>"
			value="<?=$custom_value?>"
			placeholder="<?=$placeholder?>"
			data-threshold="<?=$threshold?>"
			data-threshold-text="<?=sprintf(lang('confirm_show_all_desc'), $threshold)?>"
		>
	</fieldset>
	<?php endif; ?>
	<ul>
	<?php foreach ($options as $url => $label): ?>
		<?php if ($url == $show_all_url && $confirm_show_all): ?>
		<li><a class="m-link" rel="modal-confirm-show-all" href="<?=$url?>"><?=$label?></a></li>
		<?php else: ?>
		<li><a href="<?=$url?>"><?=$label?></a></li>
		<?php endif; ?>
	<?php endforeach; ?>
	</ul>
</div>

<?php if ($confirm_show_all): ?>
<?php $this->startOrAppendBlock('modals'); ?>
<div class="modal-wrap modal-confirm-show-all">
	<div class="modal">
		<div class="col-group">
			<div class="col w-16">
				<a class="m-close" href="#"></a>
				<div class="box">
					<h1><?=lang('confirm_show_all')?></h1>
					<form class="settings">
						<div class="alert inline warn">
							<p><?=sprintf(lang('confirm_show_all_desc'), $threshold)?></p>
						</div>
						<fieldset class="form-ctrls">
							<a class="btn submit" href="<?=$show_all_url?>"><?=lang('confirm_show_all_btn')?></a>
						</fieldset>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<?php $this->endBlock(); ?>
<?php endif; ?>