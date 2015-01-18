<?php
################################################################################################
# Russian Language Installer
# Module for install and configure Russian language
# Copyright (c) 2014 Sergey Ogarkov <sogarkov@gmail.com>
# GNU GPL v.3
################################################################################################
?>
<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
       <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>

  <div class="container-fluid">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
      	<?php echo $no_options;?>
      </div>
    </div>
  </div>
</div>

<?php echo $footer; ?>