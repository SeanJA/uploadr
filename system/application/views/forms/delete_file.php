<?php

$form  = form_open($data['action'], array('class'=>'confirm-delete'));
$form .= form_hidden('file', $data['file'], array('class'=>'file'));
$form .= form_hidden('folder', $data['folder']);
$form .= form_submit('submit', 'Delete');
$form .= form_close();

echo $form;

/* End of file delete_file.php */
/* Location: ./system/application/views/forms/delete_file.php */