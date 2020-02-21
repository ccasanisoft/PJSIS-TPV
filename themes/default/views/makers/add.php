<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>

<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header">
                    <!-- <h3 class="box-title"><?= lang('enter_info'); ?></h3> -->
                </div>
                <div class="box-body">
                    <div class="col-lg-12">
                        <?php $attrib = array('class' => 'validation', 'role' => 'form');
                        echo form_open("makers/add", $attrib); ?>
                        <div class="row">
                            <div class="col-md-6">

                                <?php switch($this->session->userdata('negocio')){
                                    case 0:
                                        $l_maker = lang("maker", "maker");
                                        $l_maker_add = lang("add_maker");
                                        break;
                                    case 1:
                                        $l_maker = lang("laboratory", "laboratory");
                                        $l_maker_add = lang("add_laboratory");
                                        break;
                                } ?>

                                <div class="form-group">
                                    <?= $l_maker; ?>
                                    <?php echo form_input('value', '', 'class="form-control" id="value" required="required"'); ?>
                                </div>

                                <div class="form-group">
                                    <?= form_submit('add_laboratory', $l_maker_add, 'class="btn btn-primary"'); ?>
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
