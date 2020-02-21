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
                        echo form_open("summaries/add", $attrib); ?>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <?= lang("reference_date", "reference_date"); ?>
                                    <?php echo form_input('reference_date', '', 'class="form-control datetimepicker" id="reference_date"'); ?>
                                </div>
                                <!-- <div class="form-group">
                                    <?= form_submit('add_summary', lang('add_summary'), 'class="btn btn-primary"'); ?>
                                </div> -->
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <?= form_submit('add_summary', lang('add_summary'), 'style="margin-top: 25px;" class="btn btn-primary"'); ?>
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
<script src="<?= $assets ?>plugins/bootstrap-datetimepicker/js/moment.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script type="text/javascript">
    $(function () {
        $('.datetimepicker').datetimepicker({
            format: 'YYYY-MM-DD'
        });
    });
</script>