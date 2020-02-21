<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= $page_title.' | '.$Settings->site_name; ?></title>
    <link rel="shortcut icon" href="<?= $assets ?>img/icon.png"/>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link href="<?= $assets ?>bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= $assets ?>plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css" />
    <link href="<?= $assets ?>dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    <style type="text/css">
        body { background-color: #ecf0f5; }
        .table th { text-align: center; }
        .table-bordered>thead>tr>th, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>tbody>tr>td, .table-bordered>tfoot>tr>td {
            border: #eae9e9;
        }
        .table>tbody>tr.active>td, .table>tbody>tr.active>th, .table>tbody>tr>td.active, .table>tbody>tr>th.active, .table>tfoot>tr.active>td, .table>tfoot>tr.active>th, .table>tfoot>tr>td.active, .table>tfoot>tr>th.active, .table>thead>tr.active>td, .table>thead>tr.active>th, .table>thead>tr>td.active, .table>thead>tr>th.active {
            background-color: #eae9e9;
        }
        .table {
            margin-bottom: 10px;
    </style>
</head>
<body>
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <h1><?= $Settings->site_name == 'SimplePOS' ? 'Simple<b>POS</b>' : '<img src="'.base_url('uploads/'.$Settings->logo).'" alt="'.$Settings->site_name.'" />'; ?></h1>
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title"><?= lang('purchase').' # '.$purchase->id; ?></h3>
                    </div>
                    <div class="box-body">
                        <div class="col-lg-12">

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <tbody>
                                                <tr>
                                                    <td class="col-xs-2"><?= lang('supplier'); ?></td>
                                                    <td class="col-xs-10"><?= $supplier->name; ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="col-xs-2"><?= lang('reference'); ?></td>
                                                    <td class="col-xs-10"><?= $purchase->reference; ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="col-xs-2"><?= lang('date'); ?></td>
                                                    <td class="col-xs-10"><?= $this->tec->hrld($purchase->date); ?></td>
                                                </tr>
                                                <?php
                                                if($purchase->attachment) {
                                                    ?>
                                                    <tr>
                                                        <td class="col-xs-2"><?= lang('attachment'); ?></td>
                                                        <td class="col-xs-10"><a href="<?=base_url('uploads/'.$purchase->attachment);?>"><?= $purchase->attachment; ?></a></td>
                                                    </tr>
                                                    <?php
                                                }
                                                if($purchase->note) {
                                                    ?>
                                                    <tr>
                                                        <td class="col-xs-2"><?= lang('note'); ?></td>
                                                        <td class="col-xs-10"><?= $purchase->note; ?></td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                    <tr class="active">
                                                        <th><?= lang('product'); ?></th>
                                                        <th class="col-xs-2"><?= lang('quantity'); ?></th>
                                                        <th class="col-xs-2"><?= lang('unit_cost'); ?></th>
                                                        <th class="col-xs-2"><?= lang('subtotal'); ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    <?php
                                                    if($items) {
                                                        foreach ($items as $item) {
                                                            echo '<tr>';
                                                            echo '<td>'.$item->product_name.' ('.$item->product_code.')</td>';
                                                            echo '<td class="text-center">'.$item->quantity.'</td>';
                                                            echo '<td class="text-right">'.$item->cost.'</td>';
                                                            echo '<td class="text-right">'.$item->subtotal.'</td>'; /*$item->quantity*$item->cost*/
                                                            echo '</tr>';
                                                        }
                                                    }
                                                    ?>

                                                </tbody>
                                                <thead>
                                                    <tr class="active">
                                                        <td><?= lang('total'); ?></td>
                                                        <td class="col-xs-2"></td>
                                                        <td class="col-xs-2"></td>
                                                        <td class="col-xs-2 text-right"><?=$purchase->grand_total;?></td>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>

                                        <div class="text-right" style="margin-bottom: 20px">
                                            <table class="table table-condensed">
                                                <tbody>
                                                    <tr>
                                                        <td style="border: none"><?= strtoupper(lang('opgrav').':'); ?></td>
                                                        <td style="border: none; padding-right:10px" class="col-xs-2"><?= $purchase->affected ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="border: none"><?= strtoupper(lang('opexon').':'); ?></td>
                                                        <td style="border: none; padding-right:10px" class="col-xs-2"><?= $purchase->exonerated ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="border: none"><?= strtoupper(lang('product_tax'). " - " . $Settings->default_tax_rate . '%:'); ?></td>
                                                        <td style="border: none; padding-right:10px" class="col-xs-2"><?= $purchase->tax ?></td>
                                                    </tr>
                                                    <tr class="active">
                                                        <td style="border: none"><strong><?= strtoupper(lang('importe') . " " . lang('total').':'); ?></strong></td>
                                                        <td style="border: none; padding-right:10px" class="col-xs-2"><?= $purchase->grand_total ?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                </div>

                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div
    </section>
</body>
</html>
