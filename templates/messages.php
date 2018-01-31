<div class="wcb_page">
	<h2><?php echo __('Messages', 'paike_contactform'); ?></h2>

	<table class="wp-list-table widefat table-messages">
		<thead>
			<tr>
				<td width="30"><b>ID</b></td>
				<td width="250"><b><?php echo __( 'Data', 'paike_contactform' ); ?></b></td>     
				<td><b><?php echo __( 'Content', 'paike_contactform' ); ?></b></td>
				<td width="70"><b><?php echo __( 'Date', 'paike_contactform' ); ?></b></td>
				<td width="70"><b><?php echo __( 'Delete', 'paike_contactform' ); ?></b></td>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($arr_messages as $k => $v) { ?>
				<tr>
					<td><?php echo $v->id ?></td>
					<td><?php echo $v->data ?></td>
					<td><?php echo $v->message ?></td>
					<td><?php echo date("m-d-Y", $v->date); ?></td>
					<td>
						<a class="pcf-delete" href="admin.php?page=cf_messages&action=delete&id=<?php echo $v->id; ?>" onclick="return confirm('<?php echo __( 'Delete message?', 'paike_contactform' ); ?>')"></a>
					</td>
				</tr>
			<?php } ?>
		</tbody>
	</table>

	<?php echo $page_nav; ?>
</div>