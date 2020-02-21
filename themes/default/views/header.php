<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= $page_title.' | '.$Settings->site_name; ?></title>
    <link rel="shortcut icon" href="<?= $assets ?>images/ccasanisofticon.png"/>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link href="<?= $assets ?>bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= $assets ?>plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css" />
    <link href="<?= $assets ?>plugins/iCheck/square/yellow.css" rel="stylesheet" type="text/css" />
    <link href="<?= $assets ?>plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= $assets ?>plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
    <link href="<?= $assets ?>plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= $assets ?>plugins/redactor/redactor.css" rel="stylesheet" type="text/css" />
    <link href="<?= $assets ?>dist/css/jquery-ui.css" rel="stylesheet" type="text/css" />
    <link href="<?= $assets ?>dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= $assets ?>dist/css/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= $assets ?>dist/css/custom.css" rel="stylesheet" type="text/css" />
	
    <script src="<?= $assets ?>plugins/jQuery/jQuery-2.1.4.min.js" type="text/javascript"></script>
	
</head>
<body class="skin-purple fixed sidebar-mini">
<div class="wrapper">

    <header class="main-header">
        <a href="<?= site_url(); ?>" class="logo">
            <span class="logo-mini">TPV</span>
            <span class="logo-lg"><?= $Settings->site_name == 'TPV' ? '<b>TPV</b>' : '<img src="'.base_url('uploads/'.$Settings->logo).'" alt="'.$Settings->site_name.'" width=200 height=45 />'; ?></span>
        </a>
        <nav class="navbar navbar-static-top" role="navigation">
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Navegación</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <ul class="nav navbar-nav pull-left">
                <li class="dropdown hidden-xs">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><img src="<?= $assets; ?>images/<?= $Settings->language; ?>.png" alt="<?= $Settings->language; ?>"></a>
                    <ul class="dropdown-menu">
                        <?php $scanned_lang_dir = array_map(function ($path) {
                            return basename($path);
                        }, glob(APPPATH . 'language/*', GLOB_ONLYDIR));
                        foreach ($scanned_lang_dir as $entry) { ?>
                            <li><a href="<?= site_url('pos/language/' . $entry); ?>"><img
                                        src="<?= $assets; ?>images/<?= $entry; ?>.png"
                                        class="language-img"> &nbsp;&nbsp;<?= ucwords($entry); ?></a></li>
                        <?php } ?>
                    </ul>
                </li>
            </ul>
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <li class="hidden-xs"><a href="#"><?= $Local; ?></a></li>
                    <li class="hidden-xs hidden-sm"><a href="#" class="clock"></a></li>
                    <li class="hidden-xs"><a href="#"><?= lang('exchange') . ": " . $Exchange->sell; ?></a></li>
                    <li class="hidden-xs"><a href="<?= site_url(); ?>"><i class="fa fa-dashboard"></i></a></li>
                    <?php if($Admin) { ?>
                    <li class="hidden-xs"><a href="<?= site_url('settings'); ?>"><i class="fa fa-cogs"></i></a></li>
                    <?php } ?>
                    <li><a href="<?= site_url('pos/view_bill'); ?>" target="_blank"><i class="fa fa-file-text-o"></i></a></li>
                    <li><a href="<?= site_url('pos'); ?>"><i class="fa fa-th"></i></a></li>
                    <?php if($Admin && $qty_alert_num) { ?>
                    <li>
                        <a href="<?= site_url('reports/alerts'); ?>">
                            <i class="fa fa-bullhorn"></i>
                            <span class="label label-danger"><?= $qty_alert_num; ?></span>
                        </a>
                    </li>
                    <?php } ?>
                    <?php if($suspended_sales) { ?>
                    <li class="dropdown notifications-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-bell-o"></i>
                            <span class="label label-danger"><?=sizeof($suspended_sales);?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header"><?=lang('recent_suspended_sales');?></li>
                            <li>
                                <ul class="menu">
                                    <li>
                                    <?php
                                    foreach ($suspended_sales as $ss) {
                                        echo '<a href="'.site_url('pos/?hold='.$ss->id).'" class="load_suspended">'.$this->tec->hrld($ss->date).' ('.$ss->customer_name.')<br><strong>'.$ss->hold_ref.'</strong></a>';
                                    }
                                    ?>
                                    </li>
                                </ul>
                            </li>
                            <li class="footer"><a href="<?= site_url('sales/opened'); ?>"><?= lang('view_all'); ?></a></li>
                        </ul>
                    </li>
                    <?php } ?>
                    <li class="dropdown user user-menu" style="padding-right:5px;">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <img src="<?= base_url('uploads/avatars/thumbs/'.($this->session->userdata('avatar') ? $this->session->userdata('avatar') : $this->session->userdata('gender').'.png')) ?>" class="user-image" alt="Avatar" />
                            <span class="hidden-xs"><?= $this->session->userdata('first_name').' '.$this->session->userdata('last_name'); ?></span>
                        </a>
                        <ul class="dropdown-menu" style="padding-right:3px;">
                            <li class="user-header">
                                <img src="<?= base_url('uploads/avatars/'.($this->session->userdata('avatar') ? $this->session->userdata('avatar') : $this->session->userdata('gender').'.png')) ?>" class="img-circle" alt="Avatar" />
                                <p>
                                    <?= $this->session->userdata('email'); ?>
                                    <small><?= lang('member_since').' '.$this->session->userdata('created_on'); ?></small>
                                </p>
                            </li>
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a href="<?= site_url('users/profile/'.$this->session->userdata('user_id')); ?>" class="btn btn-default btn-flat">Perfil</a>
                                </div>
                                <div class="pull-right">
                                    <a href="<?= site_url('logout'); ?>" class="btn btn-default btn-flat">Salir</a>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <aside class="main-sidebar">
        <section class="sidebar">
            <ul class="sidebar-menu">
                <!-- <li class="header"><?= lang('mian_navigation'); ?></li> -->

                <li class="mm_welcome"><a href="<?= site_url(); ?>"><i class="fa fa-dashboard"></i> <span><?= lang('dashboard'); ?></span></a></li>
                <li class="mm_pos"><a href="<?= site_url('pos'); ?>"><i class="fa fa-th"></i> <span><?= lang('pos'); ?></span></a></li>

                <?php if($Admin) { ?>

                <li class="treeview mm_products mm_warehouses mm_makers">
                    <a href="#">
                        <i class="fa fa-barcode"></i>
                        <span><?= lang('products'); ?></span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li id="products_index"><a href="<?= site_url('products'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_products'); ?></a></li>
                        <li id="products_add"><a href="<?= site_url('products/add'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_product'); ?></a></li>
                       <!--  <li id="products_import"><a href="<?= site_url('products/import'); ?>"><i class="fa fa-circle-o"></i> <?= lang('import_products'); ?></a></li> -->
                       <!--  <li id="products_print_barcodes"><a onclick="window.open('<?= site_url('products/print_barcodes'); ?>', 'pos_popup', 'width=900,height=600,menubar=yes,scrollbars=yes,status=no,resizable=yes,screenx=0,screeny=0'); return false;" href="#"><i class="fa fa-circle-o"></i> <?= lang('print_barcodes'); ?></a></li> -->

                        <!-- <li id="products_print_labels"><a onclick="window.open('<?= site_url('products/print_labels'); ?>', 'pos_popup', 'width=900,height=600,menubar=yes,scrollbars=yes,status=no,resizable=yes,screenx=0,screeny=0'); return false;"
                                        href="#"><i class="fa fa-circle-o"></i> <?= lang('print_labels'); ?></a></li> -->

                        <li id="products_movements"><a href="<?= site_url('products/movements'); ?>"><i class="fa fa-circle-o"></i> <?= lang('movements_search'); ?></a></li>

                        <li class="divider"></li>
                        <li id="warehouses_index">
                            <a href="<?= site_url('warehouses'); ?>"><i class="fa fa-circle-o"></i> <?= lang('warehouse'); ?></a>
                        </li>
                        <li id="warehouses_add">
                            <a href="<?= site_url('warehouses/add'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_warehouse'); ?></a>
                        </li>
                        <li id="warehouses_transfers">
                            <a href="<?= site_url('warehouses/transfers'); ?>"><i class="fa fa-circle-o"></i> <?= lang('transfer_s'); ?></a></li>

                        <?php switch($this->session->userdata('negocio')){
                                case 0:
                                    $l_makers = lang('makers');
                                    $l_maker_add = lang("add_maker");
                                    break;
                                case 1:
                                    $l_makers = lang('laboratorys');
                                    $l_maker_add = lang("add_laboratory");
                                    break;
                            } ?>

                        <li class="divider"></li>
                        <li id="makers_index"><a href="<?= site_url('makers'); ?>"><i class="fa fa-circle-o"></i> <?= $l_makers; ?></a></li>
                        <li id="makers_add"><a href="<?= site_url('makers/add'); ?>"><i class="fa fa-circle-o"></i> <?= $l_maker_add; ?></a></li>

                    </ul>
                </li>




                <li class="treeview mm_categories">
                    <a href="#">
                        <i class="fa fa-folder"></i>
                        <span><?= lang('categories'); ?></span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li id="categories_index"><a href="<?= site_url('categories'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_categories'); ?></a></li>
                        <li id="categories_add"><a href="<?= site_url('categories/add'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_category'); ?></a></li>
                        <li id="categories_import"><a href="<?= site_url('categories/import'); ?>"><i class="fa fa-circle-o"></i> <?= lang('import_categories'); ?></a></li>
                    </ul>
                </li>

                <li class="treeview mm_sales">
                    <a href="#">
                        <i class="fa fa-shopping-cart"></i>
                        <span><?= lang('sales'); ?></span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li id="sales_index">
                            <a href="<?= site_url('sales'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_sales'); ?></a>
                        </li>
                        <li id="sales_add"><a href="<?= site_url('sales/add'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_sale'); ?></a></li>

                         <li class="divider"></li>
                        <li id="">
                            <a href="<?= site_url('Referral_guide'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_referral_guide'); ?>
                        </a>
                       </li>
                        <li id=""><a href="<?= site_url('Referral_guide/add'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_referral_guide'); ?></a></li>
                       
                      
						<?php if($Admin) { ?>
						<li class="divider"></li>
						<li id="credit_note_index"><a href="<?= site_url('credit_note'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_credit_note'); ?></a></li>
						<li id="credit_note_add"><a href="<?= site_url('credit_note/add'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_credit_note'); ?></a></li>
						<?php } ?>
						<!--**************TRJ052 - ALEXANDER ROCA - 13/06/2019***********-->
                    </ul>
                </li>

                <li class="treeview mm_purchases">
                    <a href="#">
                        <i class="fa fa-plus"></i>
                        <span><?= lang('purchases'); ?></span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li id="purchases_index"><a href="<?= site_url('purchases'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_purchases'); ?></a></li>
                        <li id="purchases_add"><a href="<?= site_url('purchases/add'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_purchase'); ?></a></li>
                        <li class="divider"></li>
                        <li id="purchases_expenses"><a href="<?= site_url('purchases/expenses'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_expenses'); ?></a></li>
                        <li id="purchases_add_expense"><a href="<?= site_url('purchases/add_expense'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_expense'); ?></a></li>
                    </ul>
                </li>

                <li class="treeview mm_summaries">
                    <a href="#">
                        <i class="fa fa-money"></i>
                        <span><?= lang('accounting'); ?></span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
						<!--**************TRJ041 - ALEXANDER ROCA - 29/04/2019***********-->
                        <!--<li id="summaries_invoices"><a href="<?= site_url('invoices'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_invoices'); ?></a></li>-->
						<!--**************TRJ041 - ALEXANDER ROCA - 29/04/2019***********-->
                        <li id="summaries_index"><a href="<?= site_url('summaries'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_summaries'); ?></a></li>
                        <li id="summaries_add"><a href="<?= site_url('summaries/add'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_summary'); ?></a></li>
                    </ul>
                </li>

                <li class="treeview mm_gift_cards">
                    <a href="#">
                        <i class="fa fa-credit-card"></i>
                        <span><?= lang('gift_cards'); ?></span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li id="gift_cards_index"><a href="<?= site_url('gift_cards'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_gift_cards'); ?></a></li>
                        <li id="gift_cards_add"><a href="<?= site_url('gift_cards/add'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_gift_card'); ?></a></li>
                    </ul>
                </li>

                <li class="treeview mm_auth mm_customers mm_suppliers mm_customers_type">
                    <a href="#">
                        <i class="fa fa-users"></i>
                        <span><?= lang('people'); ?></span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li id="auth_users"><a href="<?= site_url('users'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_users'); ?></a></li>
                        <li id="auth_create_user"><a href="<?= site_url('users/add'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_user'); ?></a></li>
                        <li class="divider"></li>
                        <li id="customers_index"><a href="<?= site_url('customers'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_customers'); ?></a></li>
                        <li id="customers_add"><a href="<?= site_url('customers/add'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_customer'); ?></a></li>

                        <li class="divider"></li>

                        <li id="customers_type_index"><a href="<?= site_url('customers_type'); ?>"><i class="fa fa-circle-o"></i> <?= lang('customers_type'); ?></a></li>
                        <li id="customers_type_add"><a href="<?= site_url('customers_type/add'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_customer_type'); ?></a></li>

                        <li class="divider"></li>
                        <li id="suppliers_index"><a href="<?= site_url('suppliers'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_suppliers'); ?></a></li>
                        <li id="suppliers_add"><a href="<?= site_url('suppliers/add'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_supplier'); ?></a></li>
                    </ul>
                </li>


                <li class="treeview mm_reports">
                    <a href="#">
                        <i class="fa fa-bar-chart-o"></i>
                        <span><?= lang('reports'); ?></span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li id="reports_daily_sales"><a href="<?= site_url('reports/daily_sales'); ?>"><i class="fa fa-circle-o"></i> <?= lang('daily_sales'); ?></a></li>
                        <li id="reports_monthly_sales"><a href="<?= site_url('reports/monthly_sales'); ?>"><i class="fa fa-circle-o"></i> <?= lang('monthly_sales'); ?></a></li>
                        <li id="reports_index"><a href="<?= site_url('reports'); ?>"><i class="fa fa-circle-o"></i> <?= lang('sales_report'); ?></a></li>
                        <li class="divider"></li>
                        <li id="reports_payments"><a href="<?= site_url('reports/payments'); ?>"><i class="fa fa-circle-o"></i> <?= lang('payments_report'); ?></a></li>
                        <li class="divider"></li>
                        <li id="reports_registers"><a href="<?= site_url('reports/registers'); ?>"><i class="fa fa-circle-o"></i> <?= lang('registers_report'); ?></a></li>
                        <li class="divider"></li>
                        <li id="reports_top_products"><a href="<?= site_url('reports/top_products'); ?>"><i class="fa fa-circle-o"></i> <?= lang('top_products'); ?></a></li>
                        <li id="reports_products"><a href="<?= site_url('reports/products'); ?>"><i class="fa fa-circle-o"></i> <?= lang('products_report'); ?></a></li>
                    </ul>
                </li>


                <li class="treeview mm_settings mm_locals">
                    <a href="#">
                        <i class="fa fa-cogs"></i>
                        <span><?= lang('settings'); ?></span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li id="settings_index"><a href="<?= site_url('settings'); ?>"><i class="fa fa-circle-o"></i> <?= lang('settings'); ?></a></li>
                        <li id="settings_backups"><a href="<?= site_url('settings/backups'); ?>"><i class="fa fa-circle-o"></i> <?= lang('backups'); ?></a></li>
                        <li class="divider"></li>
                        <li id="locals_index"><a href="<?= site_url('locals'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_locals'); ?></a></li>
                        <li id="locals_add"><a href="<?= site_url('locals/add'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_local'); ?></a></li>
                        <!-- <li id="settings_updates"><a href="<?= site_url('settings/updates'); ?>"><i class="fa fa-circle-o"></i> <?= lang('updates'); ?></a></li> -->
                    </ul>
                </li>

                <?php } else { ?>

                <li class="mm_products"><a href="<?= site_url('products'); ?>"><i class="fa fa-barcode"></i> <span><?= lang('products'); ?></span></a></li>
                <li class="mm_categories"><a href="<?= site_url('categories'); ?>"><i class="fa fa-folder-open"></i> <span><?= lang('categories'); ?></span></a></li>

                <li class="treeview mm_sales">
                    <a href="#">
                        <i class="fa fa-shopping-cart"></i>
                        <span><?= lang('sales'); ?></span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li id="sales_index"><a href="<?= site_url('sales'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_sales'); ?></a></li>
                        <li id="sales_opened"><a href="<?= site_url('sales/opened'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_opened_bills'); ?></a></li>
                    </ul>
                </li>

                <li class="treeview mm_purchases">
                    <a href="#">
                        <i class="fa fa-plus"></i>
                        <span><?= lang('expenses'); ?></span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li id="purchases_expenses"><a href="<?= site_url('purchases/expenses'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_expenses'); ?></a></li>
                        <li id="purchases_add_expense"><a href="<?= site_url('purchases/add_expense'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_expense'); ?></a></li>
                    </ul>
                </li>

                <li class="treeview mm_gift_cards">
                    <a href="#">
                        <i class="fa fa-credit-card"></i>
                        <span><?= lang('gift_cards'); ?></span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li id="gift_cards_index"><a href="<?= site_url('gift_cards'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_gift_cards'); ?></a></li>
                        <li id="gift_cards_add"><a href="<?= site_url('gift_cards/add'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_gift_card'); ?></a></li>
                    </ul>
                </li>

                <li class="treeview mm_customers">
                    <a href="#">
                        <i class="fa fa-users"></i>
                        <span><?= lang('customers'); ?></span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li id="customers_index"><a href="<?= site_url('customers'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_customers'); ?></a></li>
                        <li id="customers_add"><a href="<?= site_url('customers/add'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_customer'); ?></a></li>
                    </ul>
                </li>

                <li class="treeview mm_suppliers">
                    <a href="#">
                        <i class="fa fa-users"></i>
                        <span><?= lang('suppliers'); ?></span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li id="suppliers_index"><a href="<?= site_url('suppliers'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_suppliers'); ?></a></li>
                        <li id="suppliers_add"><a href="<?= site_url('suppliers/add'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_supplier'); ?></a></li>
                    </ul>
                </li>

                <?php } ?>

            </ul>
        </section>
    </aside>

    <div class="content-wrapper">
        <section class="content-header">
            <h1><?= $page_title; ?></h1>
            <ol class="breadcrumb">
                <li><a href="<?= site_url(); ?>"><i class="fa fa-dashboard"></i> <?= lang('home'); ?></a></li>
                <?php
                foreach ($bc as $b) {
                    if ($b['link'] === '#') {
                        echo '<li class="active">' . $b['page'] . '</li>';
                    } else {
                        echo '<li><a href="' . $b['link'] . '">' . $b['page'] . '</a></li>';
                    }
                }
                ?>
            </ol>
        </section>

        <div class="col-lg-12 alerts">
            <div id="custom-alerts" style="display:none;">
                <div class="alert alert-dismissable">
                    <div class="custom-msg"></div>
                </div>
            </div>
            <?php if($error)  { ?>
            <div class="alert alert-danger alert-dismissable">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                <h4><i class="icon fa fa-ban"></i> <?= lang('error'); ?></h4>

                <?php
                $error = str_replace(".", ".<br>", $error) ; ?>
                <?=$error; ?>
            </div>
            <?php } if($warning) { ?>
            <div class="alert alert-warning alert-dismissable">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                <h4><i class="icon fa fa-warning"></i> <?= lang('warning'); ?></h4>
                <?= $warning; ?>
            </div>
            <?php } if($message) { ?>
            <div class="alert alert-success alert-dismissable">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                <h4>	<i class="icon fa fa-check"></i> <?= lang('Success'); ?></h4>
                <?= $message; ?>
            </div>
            <?php } ?>
        </div>
