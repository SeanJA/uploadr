</div>
<?php if(!empty($help_file)) {
  $help_text = $this->load->view('help/'.$help_file.'.md', '', true);
  $help_text = markdown($help_text);
?>
    <div style="display:none" id="help-text"><?php echo $help_text; ?></div>
<?php } ?>
<?php

?>
</body>
</html>

<?php

/* End of file footer.php */
/* Location: ./system/application/views/footer.php */