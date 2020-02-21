<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?= $product->name; ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-xs-4">
                    <img id="pr-image" src="<?= base_url() ?>uploads/<?= $product->image ?>"
                    alt="<?= $product->name ?>" class="img-responsive img-thumbnail"/>
                </div>
                <div class="col-xs-8">
                    <div class="table-responsive">
                        <table class="table table-borderless table-striped dfTable table-right-left">
                            <tbody>
                                <tr>
                                    <td class="col-xs-5"><?= lang("product_type"); ?></td>
                                    <td class="col-xs-7"><?= lang($product->type); ?></td>
                                </tr>
                                <tr>
                                    <td><?= lang("product_name"); ?></td>
                                    <td><?= $product->name; ?></td>
                                </tr>
                                <tr>
                                    <td><?= lang("product_code"); ?></td>
                                    <td><?= $product->code; ?></td>
                                </tr>
                                <tr>
                                    <td><?= lang("category"); ?></td>
                                    <td><?= $category->name.' ('.$category->code.')'; ?></td>
                                </tr>

                                <?php switch($this->session->userdata('negocio')){
                                    case 0: ?>
                                        <tr>
                                            <td><?= lang("maker"); ?></td>
                                            <td><?= $maker; ?></td>
                                        </tr>
                                        <?php break;
                                    case 1: ?>
                                        <tr>
                                            <td><?= lang("laboratory"); ?></td>
                                            <td><?= $maker; ?></td>
                                        </tr>
                                        <tr>
                                            <td><?= lang("active_principle"); ?></td>
                                            <td><?= $product->custom_field_1; ?></td>
                                        </tr>
                                        <tr>
                                            <td><?= lang("pharmacological_action"); ?></td>
                                            <td><?= $product->custom_field_2; ?></td>
                                        </tr>
                                        <?php break;
                                } ?>

                                <tr>
                                    <td><?= lang("presentation"); ?></td>
                                    <td><?= $product->presentation; ?></td>
                                </tr>

                                <?php if ($product->type == 'standard') { ?>
                                    <tr>
                                        <td><?= lang("quantity"); ?></td>
                                        <td><?= $this->tec->formatNumber($product->quantity); ?></td>
                                    </tr>
                                <?php } ?>

                            </tbody>
                        </table>
                    </div>

                    <?php if ($product->type == 'combo') { ?>
                    <h4 class="bold"><?= lang('combo_items') ?></h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-condensed dfTable two-columns">
                            <thead>
                                <tr>
                                    <th><?= lang('product_name') ?></th>
                                    <th><?= lang('quantity') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($combo_items as $combo_item) {
                                    echo '<tr><td>' . $combo_item->name . ' (' . $combo_item->code . ') </td><td>' . $this->tec->formatNumber($combo_item->qty) . '</td></tr>';
                                } ?>
                            </tbody>
                        </table>
                </div>
                <?php } ?>
            </div>

            <div class="col-xs-12">
                <div class="table-responsive">
                    <table class="table table-borderless table-striped table-bordered">
                        <tbody>
                            <tr>
                                <td style="padding-left:15px;"><strong><?= lang("currency_name") . ":"; ?></strong></td>
                                <td><?= lang("currency_" . strtolower($product->currency)); ?></td>
                                <?php if ($Admin) {
                                        echo '<td><strong>' . lang("cost") . ":" . '</strong></td>';
                                        echo '<td>' . $this->tec->formatMoney($product->cost) . '</td>';
                                    } ?>
                                <td><strong><?= lang("price") . ":"; ?></strong></td>
                                <td><?= $this->tec->formatMoney($product->price) ?></td>
                                <td><strong><?= lang("tax_method") . ":"; ?></strong></td>
                                <td><?= $product->tax_method == 0 ? lang('inclusive') : $product->tax_method == 1 ? lang('exclusive') : lang('exonerated'); ?></td>
                            </tr>

                            <!-- <tr>
                                <td><?= lang("price"); ?></td>
                                <td><?= $this->tec->formatMoney($product->price) ?></td>
                            </tr>
                            <tr>
                                <td><?= lang("tax_rate"); ?></td>
                                <td><?= $product->tax; ?></td>
                            </tr>
                            <tr>
                                <td><?= lang("tax_method"); ?></td>
                                <td><?= $product->tax_method == 0 ? lang('inclusive') : $product->tax_method == 1 ? lang('exclusive') : lang('exonerated'); ?></td>
                            </tr> -->

                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-xs-12">
                <?= $product->details ? '<div class="panel panel-primary"><div class="panel-heading">' . lang('product_details') . '</div><div class="panel-body">' . $product->details . '</div></div>' : ''; ?>
            </div>
        </div>

    </div>
</div>
</div>