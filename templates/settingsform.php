<div class="wcb_page">
	<h2><?php echo __('Contact form', 'paike_contactform'); ?></h2>

	<form action="" name="paike_contactform" method="post">
		<h3><?php echo __('Form fields', 'paike_contactform'); ?></h3>
		<table class="wp-list-table widefat">
			<thead>
				<tr>
					<td width="140"><b><?php echo __( 'Field Name', 'paike_contactform' ); ?></b></td>
					<td><b><?php echo __( 'Placeholder', 'paike_contactform' ); ?></b></td>
					<td width="70"><b><?php echo __( 'Show', 'paike_contactform' ); ?></b></td>
					<td width="70"><b><?php echo __( 'Required', 'paike_contactform' ); ?></b></td>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($arr_field as $k => $v) { ?>
					<tr>
						<td><b><?php echo $v->text ?></b></td>
						<td><input type="text" name="field[<?php echo $v->name ?>][placeholder]" value="<?php echo $v->placeholder ?>" /></td>
						<td><?php html_chekbox('field['.$v->name.'][show]', $v->show ); ?></td>
						<td><?php html_chekbox('field['.$v->name.'][required]', $v->required ); ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	
		<h3><?php echo __('Settings', 'paike_contactform'); ?></h3>
		<table class="wp-list-table widefat">
			<thead>
				<tr>
					<td width="140"><b><?php echo __( 'Parameter', 'paike_contactform' ); ?></b></td>
					<td><b><?php echo __( 'Value', 'paike_contactform' ); ?></b></td>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($arr_settings as $k => $v) { ?>
					<tr>
						<td><b><?php echo $v->text ?></b></td>
						<td>
							<?php if ($v->type == 'radio') { html_chekbox('setting['.$v->name.']', $v->value ); ?>
							<?php } else { ?>
							<input type="<?php echo $v->type ?>" name="setting[<?php echo $v->name ?>]" value="<?php echo $v->value ?>" />
							<?php } ?>
						</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>

		<input name="action" type="hidden" value="edit">
 		<input type="submit" name="submit" value="<?php echo __( 'Save', 'save' ); ?>">
	</form>
</div>