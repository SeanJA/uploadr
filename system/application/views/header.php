<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
  <head>
    <title>Demo Uploadr - <?php echo $title ?></title>
    <script type="text/javascript">
      //base url for javascript actions
      var base_url = "<?php echo base_url() . index_page() ?>";
    </script>
    <script type="text/javascript" src="<?php echo base_url() ?>public/js/jquery-1.4.4.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>public/js/fileuploader.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>public/js/script.js"></script>
    <link href="<?php echo base_url() ?>public/css/fileuploader.css" type="text/css" rel="stylesheet" />
	<link href="<?php echo base_url() ?>public/css/style.css" type="text/css" rel="stylesheet" />
  </head>
  <body>

    <div id="content">
      <a href="<?php echo base_url() . index_page(); ?>">
        Uploadr
      </a>
      <div id="page-content">
        <h1><?php echo $title ?></h1>
        <?php if (!empty($subtitle)) { ?>
          <h2><?php echo $subtitle ?></h2>
		<?php } ?>
		<?php if (!empty($errors)) { ?>
		<div class="error">
            <ul>
            <?php foreach ($errors as $error) { ?>
                <li>
					<?php echo $error; ?>
				</li>
			<?php } ?>
                </ul>
		</div>
		<?php } ?>

<?php
            /* End of file header.php */
            /* Location: ./system/application/views/header.php */