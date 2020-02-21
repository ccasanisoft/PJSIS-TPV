<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>

<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title"><?= lang('update_info'); ?></h3>
                </div>
                <div class="box-body">
                    <div class="col-lg-12">
                        <?php $attrib = array('class' => 'validation', 'role' => 'form');
                        echo form_open("customers_type/edit/".$customers_type->id, $attrib); ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                   <div class="form-group">
                                    <?= lang("type", "type"); ?>
                                    <?php echo form_input('value', $customers_type->customers_type, 'class="form-control" id="value" required="required"'); ?>
                                </div>
                                
                                <div class="form-group">
                                    <?= form_submit('update', lang('update'), 'class="btn btn-primary"'); ?>
                                </div>
                            </div>
                        </div>
                        <?php echo form_close(); ?>

                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="<?= $assets ?>plugins/input-mask/jquery.inputmask.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/input-mask/jquery.inputmask.date.extensions.js" type="text/javascript"></script>