<?php
$class = REPLACE_DOMAIN_CLASS_NAME;
if (strtolower($_SERVER['REQUEST_METHOD']) === 'post' && count($_POST) > 0) {
	$message = $class::do_replace_domain_func();
}
?>

<h1>Replace Domain</h1>

<?php if (isset($message) && $message) { ?>
<div class="updated notice is-dismissible">
	<p>
		<strong><?php echo $message ?></strong>
	</p>
	<button type="button" class="notice-dismiss">
		<span class="screen-reader-text">Dismiss this notice.</span>
	</button>
</div>
<?php } ?>

<p>This is a simple plugin for replace domain that widgets vanished when migratin wordpress domains, want more information, click <a href="https://hughshen.github.io/#/detail/1476194278478-WordPress%E6%9B%B4%E6%8D%A2%E5%9F%9F%E5%90%8D%E6%97%B6%E4%B8%A2%E5%A4%B1Widgets%E6%95%B0%E6%8D%AE.md" target="_blank">here</a></p>

<form action="" method="post">
	<h2 class="title">Setting</h2>
	<p>You alse can replace any string</p>
	<table class="form-table">
		<tbody>
			<tr>
				<th><label for="old-url">Old url</label></th>
				<td><input type="text" name="old_url" id="old-url" class="regular-text code" value=""></td>
			</tr>
			<tr>
				<th><label for="new-url">New url</label></th>
				<td><input type="text" name="new_url" id="new-url" class="regular-text code" value=""></td>
			</tr>
			<tr>
				<th>
					<label for="replace-table">Replace table</label>
				</th>
				<td>
					<select name="replace_table" id="replace-table">
						<?php foreach ($class::$allow_tables as $table) { ?>
							<option value="<?php echo $table ?>"><?php echo ucfirst($table) ?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
			<!-- <tr>
				<th>
					<label for="replace-options-key">Replace options key</label>
				</th>
				<td>
					<input type="checkbox" name="replace_options_key" id="replace-options-key">
				</td>
			</tr> -->
		</tbody>
	</table>

	<p class="submit">
		<input type="submit" name="submit" id="submit" class="button button-primary" value="Submit">
	</p>
</form>