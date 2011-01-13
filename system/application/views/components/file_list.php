<?php foreach ($files as $folder => $f) { ?>
	<h2><?php echo $f['title']; ?></h2>
	<div class="files-scroll tall">
	<?php if (!empty($f['files'])) { ?>
		<table id="files">
			<?php foreach ($f['files'] as $file) { ?>
	        <tr>
				<td>
				<?php echo anchor($upload_url . $folder . '/' . $file, $file, array('class' => 'popup')); ?>
				</td>
				<td class="actions">
				<?php
				$data['data'] = array(
					'action' => 'uploadr/delete',
					'folder' => $folder,
					'file' => $file,
				);
				$this->load->view('forms/delete_file', $data);
				?>
				</td>
			</tr>
			<tr>
				<td colspan="99">
					<?php
					$id = md5($upload_url . $folder . '/' . $file);
					?>
					<label for="<?php echo $id; ?>">Link: </label>
					<input id="<?php echo $id; ?>" class="read-only" type="text" readonly="true" value="<?php echo $upload_url . $folder . '/' . $file; ?>" />
				</td>
			</tr>
			<?php } ?>
		</table>
	<?php } ?>
	</div>
<?php } ?>