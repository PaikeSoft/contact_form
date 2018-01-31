<div class="pcf-wrap">
	<div class="pcf__message">
		<p><?php echo $arr_settings['success_message']; ?></p>
		<a href="<?php echo $_SERVER['REDIRECT_URL']; ?>">Send again</a>
	</div>
	<div class="pcf__error">
		<p><?php echo __('Can not send mail. Please try again.', 'paike_contactform'); ?></p>
		<a href="<?php echo $_SERVER['REDIRECT_URL']; ?>">Send again</a>
	</div>
	
	<form name="paike-contactform" method="post">
		<?php foreach ($arr_field as $k => $v) { ?> 
			<div class="pcf__input">
				<div class="pcf__input-title"><?php echo $v->placeholder ?></div>
				<input type="<?php echo $v->type; ?>" name="pcf_<?php echo $v->name ?>" <?php if ($v->required == 1) echo 'required'; ?> />

				<?php if ($v->required == 1) { ?>
				<div class="pcf__input-errortxt"><?php echo __( 'Please fill input field', 'paike_contactform' ); ?></div>
				<?php } ?>

				<?php if ($v->type == 'email') { ?>
				<div class="pcf__input-errortxtem"><?php echo __( 'Please check input field', 'paike_contactform' ); ?></div>
				<?php } ?>
			</div>
		<?php } ?>
		<div class="pcf__input">
			<div class="pcf__input-title"><?php echo $arr_settings['message_txt'] ?></div>
			<textarea name="pcf_message" required></textarea>
			<div class="pcf__input-errortxt"><?php echo __( 'Please enter message', 'paike_contactform' ); ?></div>
		</div>
		<div class="pcf__input">
			<button class="pcf__button"><?php echo $arr_settings['button_text'] ?></button>
		</div>
		<input type="hidden" class="post-php" name="pcf_php" value="<?php echo plugins_url('js/post.php', __DIR__); ?>" />
	</form>
</div>