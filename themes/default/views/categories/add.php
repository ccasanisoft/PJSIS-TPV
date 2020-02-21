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

                        <?php echo form_open_multipart("categories/add", 'class="validation"'); ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= lang('code', 'code'); ?>
                                    <?= form_input('code', set_value('code'), 'class="form-control tip" id="code"  required="required"'); ?>
                                </div>
                                <div class="form-group">
                                    <?= lang('parent_category', 'parent_category'); ?>
                                    <?php

                                    $cadd= '<select name="parent_category" class="form-control select2 tip" id="parent_category" style="width:100%;">\n';
                                    $cadd.= '<option value="0" selected="selected">'.lang("select")." ".lang("parent_category").'</option>\n';
                                    foreach($categoriesOrdered as $category) {

                                        $cadd.= "<option value='".$category->id."'>".$category->name."</option>\n";
                                        if(isset($category->hijos)){
                                            foreach($category->hijos as $hijos1) {
                                                $cadd.= "<option value='".$hijos1->id."'>&nbsp;&nbsp;&nbsp;&nbsp;".$hijos1->name."</option>\n";
                                                if(isset($hijos1->hijos)){
                                                    foreach($hijos1->hijos as $hijos2) {
                                                        $cadd.= "<option value='".$hijos2->id."'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$hijos2->name."</option>\n";
                                                        if(isset($hijos2->hijos)){
                                                            foreach($hijos2->hijos as $hijos3) {
                                                                $cadd.= "<option disabled value='".$hijos3->id."'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$hijos3->name."</option>\n";
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    $cadd.'</select>';
                                    ?>
                                    <?= $cadd;?>
                                    <input type="hidden" name="temp" style="display: none;">
                                </div>
                                <div class="form-group">
                                    <?= lang('name', 'name'); ?>
                                    <?= form_input('name', set_value('name'), 'class="form-control tip" id="name"  required="required"'); ?>
                                </div>
                                <div class="form-group">
                                    <?= lang('image', 'image'); ?>
                                    <input type="file" name="userfile" id="image">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <?= form_submit('add_category', lang('add_category'), 'class="btn btn-primary"'); ?>
                        </div>

                        <?php echo form_close();?>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</section>
