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

                        <?php echo form_open_multipart("categories/edit/".$category->id);?>
                        <div class="row">
                            <div class="col-md-6">

                                <div class="form-group">
                                    <?= lang('code', 'code'); ?>
                                    <?= form_input('code', $category->code, 'class="form-control tip" id="code"  required="required"'); ?>
                                </div>
                                <div class="form-group">
                                    <?= lang('parent_category', 'parent_category'); ?>
                                    <?php                                    

                                    $cadd= '<select name="parent_category" class="form-control select2 tip" id="parent_category" style="width:100%;">';
                                    $cadd.= '<option value="0" >'.lang("select")." ".lang("parent_category").'</option>';
                                    foreach($categoriesOrdered as $categoryL) {
                                        $sel1 = ""; $disab1 = "";
                                        $sel2 = ""; $disab2 = "";
                                        $sel3 = ""; $disab3 = "";
                                        $sel4 = "";
                                        
                                        if($categoryL->id == $category->parent_category_id){$sel1 = " selected "; }
                                        if($categoryL->name == $category->name ){$disab1 = " disabled "; }

                                        $cadd.= "<option $sel1 value='".$categoryL->id."' $disab1 >".$categoryL->name."</option>";
                                        if($categoryL->hijos){
                                            foreach($categoryL->hijos as $hijos1) {
                                                if($hijos1->id == $category->parent_category_id){$sel2 = " selected "; }
                                                if($hijos1->name == $category->name ){$disab2 = " disabled "; }
                                                $cadd.= "<option $sel2 value='".$hijos1->id."' $disab2>&nbsp;&nbsp;&nbsp;&nbsp;".$hijos1->name."</option>";
                                                $sel2 = "";$disab2 = "";
                                                if($hijos1->hijos){
                                                    foreach($hijos1->hijos as $hijos2) {
                                                        if($hijos2->id == $category->parent_category_id){$sel3 = " selected "; }
                                                        if($hijos2->name == $category->name ){$disab3 = " disabled "; }
                                                        $cadd.= "<option $disab3 $sel3 value='".$hijos2->id."'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$hijos2->name."</option>";
                                                        $sel3 = "";$disab3 = "";
                                                        if($hijos2->hijos){
                                                            foreach($hijos2->hijos as $hijos3) {
                                                                if($hijos3->id == $category->parent_category_id){ $sel4 = " selected "; }
                                                                $cadd.= "<option $sel4 disabled value='".$hijos3->id."'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$hijos3->name."</option>";
                                                                //$sel4 = "";
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    $cadd.'</select>';                                    
                                    ?>
                                    <?= $cadd;//exit;?>
                                    <input type="hidden" name="temp" style="display: none;">
                                </div>
                                <div class="form-group">
                                    <?= lang('name', 'name'); ?>
                                    <?= form_input('name', $category->name, 'class="form-control tip" id="name"  required="required"'); ?>
                                </div>
                                <div class="form-group">
                                    <?= lang('image', 'image'); ?>
                                    <input type="file" name="userfile" id="image">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <?= form_submit('edit_category', lang('edit_category'), 'class="btn btn-primary"'); ?>
                        </div>

                        <?php echo form_close();?>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</section>
