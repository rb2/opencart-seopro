<?php
/**
 * Seopro Module
 * 
 * @copyright 2015 OpenCartJazz
 * @link http//www.opencartjazz.com
 * @author Sergey Ogarkov <sogarkov@gmail.com>
 * 
 * @license GNU GPL v.3
 */
?>
<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-ocjazz-seopro" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
    <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>

  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-ocjazz-seopro" class="form-horizontal">
          <div class="form-group">
            <label class="col-sm-3 control-label"><span data-toggle="tooltip" title="<?php echo $help_hide_default; ?>"><?php echo $entry_hide_default; ?></span></label>
            <div class="col-sm-9">
              <label class="radio-inline">
                <?php if ($ocjazz_seopro_hide_default) { ?>
                <input type="radio" name="ocjazz_seopro_hide_default" value="1" checked="checked" />
                <?php echo $text_yes; ?>
                <?php } else { ?>
                <input type="radio" name="ocjazz_seopro_hide_default" value="1" />
                <?php echo $text_yes; ?>
                <?php } ?>
              </label>
              <label class="radio-inline">
                <?php if (!$ocjazz_seopro_hide_default) { ?>
                <input type="radio" name="ocjazz_seopro_hide_default" value="0" checked="checked" />
                <?php echo $text_no; ?>
                <?php } else { ?>
                <input type="radio" name="ocjazz_seopro_hide_default" value="0" />
                <?php echo $text_no; ?>
                <?php } ?>
              </label>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
  <?php echo $footer; ?>