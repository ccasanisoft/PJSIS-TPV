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
	<link href="<?= $assets ?>plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet" type="text/css" />
	<link href="<?= $assets ?>plugins/redactor/redactor.css" rel="stylesheet" type="text/css" />
	<link href="<?= $assets ?>dist/css/jquery-ui.css" rel="stylesheet" type="text/css" />
	<link href="<?= $assets ?>dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
	<link href="<?= $assets ?>dist/css/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />
	<link href="<?= $assets ?>dist/css/custom.css" rel="stylesheet" type="text/css" />
	<script src="<?= $assets ?>plugins/jQuery/jQuery-2.1.4.min.js" type="text/javascript"></script>
<!--********TRJ073 - KENY PONTE - 20/08/2019*****************-->
<!--estilos para que los div se comporten como radio button-->
<style type="text/css">
  .radio-group{
    position: relative;
}

.radio{
    display:inline-block;
    width:15px;
    height: 15px;
    border-radius: 100%;
    background-color:lightblue;
    border: 5px solid lightblue;
    cursor:pointer;
    margin: 2px 0; 
}

.radio.selected{
    border-color:black;
}
</style>
<!--********TRJ073 - KENY PONTE - 20/08/2019*****************-->
</head>
<body class="skin-green sidebar-collapse sidebar-mini pos">

	<div class="wrapper">

		<header class="main-header">
			<a href="<?= site_url(); ?>" class="logo">
				<!-- <span class="logo-mini">PDV</span>
				<span class="logo-lg"><?= $Settings->site_name == 'PDV' ? '<b>PDV</b>' : '<img src="'.base_url('assets/uploads/'.$Settings->logo).'" alt="'.$Settings->site_name.'" />'; ?></span> -->
				<span class="logo-mini">TPV</span>
            	<span class="logo-lg"><?= $Settings->site_name == 'TPV' ? '<b>TPV</b>' : '<img src="'.base_url('uploads/'.$Settings->logo).'" alt="'.$Settings->site_name.'" />'; ?></span>
			</a>
			<nav class="navbar navbar-static-top" role="navigation">
				<ul class="nav navbar-nav pull-left">
				    <!-- <li class="dropdown">
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
				    </li> -->
				</ul>
				<div class="navbar-custom-menu">
					<ul class="nav navbar-nav">
						<li><a href="#" class="clock"></a></li>
						<li><a href="#"><?= lang('exchange') . ": " . $Exchange->sell; ?></a></li>
						<li><a href="<?= site_url(); ?>"><i class="fa fa-dashboard"></i></a></li>
						<?php if($Admin) { ?>
						<li><a href="<?= site_url('settings'); ?>"><i class="fa fa-cogs"></i></a></li>
						<?php } ?>
						<!-- <li><a href="<?= site_url('pos/mesa'); ?>" data-toggle="ajax"><i class="fa fa-cutlery"></i></a></li> -->
						<li><a href="<?= site_url('pos/view_bill'); ?>" target="_blank"><i class="fa fa-file-text-o"></i></a></li>
						<li><a href="<?= site_url('pos/shortcuts'); ?>" data-toggle="ajax"><i class="fa fa-key"></i></a></li>
						<li><a href="<?= site_url('pos/register_details'); ?>" data-toggle="ajax"><?= lang('register_details'); ?></a></li>
						<?php if($Admin) { ?>
						<li><a href="<?= site_url('pos/today_sale'); ?>" data-toggle="ajax"><?= lang('today_sale'); ?></a></li>
						<?php } ?>
						<li><a href="<?= site_url('pos/close_register'); ?>" data-toggle="ajax"><?= lang('close_register'); ?></a></li>
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
						<li class="dropdown user user-menu">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								<img src="<?= base_url('uploads/avatars/thumbs/'.($this->session->userdata('avatar') ? $this->session->userdata('avatar') : $this->session->userdata('gender').'.png')) ?>" class="user-image" alt="Avatar" />
								<span><?= $this->session->userdata('first_name').' '.$this->session->userdata('last_name'); ?></span>
							</a>
							<ul class="dropdown-menu">
								<li class="user-header">
									<img src="<?= base_url('uploads/avatars/'.($this->session->userdata('avatar') ? $this->session->userdata('avatar') : $this->session->userdata('gender').'.png')) ?>" class="img-circle" alt="Avatar" />
									<p>
										<?= $this->session->userdata('email'); ?>
										<small><?= lang('member_since').' '.$this->session->userdata('created_on'); ?></small>
									</p>
								</li>
								<li class="user-footer">
									<div class="pull-left">
										<a href="<?= site_url('users/profile/'.$this->session->userdata('user_id')); ?>" class="btn btn-default btn-flat"><?= lang('profile'); ?></a>
									</div>
									<div class="pull-right">
										<a href="<?= site_url('logout'); ?>" class="btn btn-default btn-flat"><?= lang('sing_out'); ?></a>
									</div>
								</li>
							</ul>
						</li>
						<li>
							<a href="#" data-toggle="control-sidebar" class="sidebar-icon"><i class="fa fa-folder sidebar-icon"></i></a>
						</li>
					</ul>
				</div>
			</nav>
		</header>

		<aside class="main-sidebar">
			<section class="sidebar">
				<ul class="sidebar-menu">
					<li class="header"><?= lang('mian_navigation'); ?></li>

					<li id="mm_welcome"><a href="<?= site_url(); ?>"><i class="fa fa-dashboard"></i> <span><?= lang('dashboard'); ?></span></a></li>
					<li id="mm_pos"><a href="<?= site_url('pos'); ?>"><i class="fa fa-th"></i> <span><?= lang('pos'); ?></span></a></li>

					<?php if($Admin) { ?>

						<li class="treeview" id="mm_products">
							<a href="#">
								<i class="fa fa-barcode"></i>
								<span><?= lang('products'); ?></span>
								<i class="fa fa-angle-left pull-right"></i>
							</a>
							<ul class="treeview-menu">
								<li id="products_index"><a href="<?= site_url('products'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_products'); ?></a></li>
								<li id="products_add"><a href="<?= site_url('products/add'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_product'); ?></a></li>
								<li id="products_import_csv"><a href="<?= site_url('products/import'); ?>"><i class="fa fa-circle-o"></i> <?= lang('import_products'); ?></a></li>
								<li id="products_print_barcodes"><a onclick="window.open('<?= site_url('products/print_barcodes'); ?>', 'pos_popup', 'width=900,height=600,menubar=yes,scrollbars=yes,status=no,resizable=yes,screenx=0,screeny=0'); return false;"
									href="#"><i class="fa fa-circle-o"></i> <?= lang('print_barcodes'); ?></a></li>
								<li id="products_print_labels"><a onclick="window.open('<?= site_url('products/print_labels'); ?>', 'pos_popup', 'width=900,height=600,menubar=yes,scrollbars=yes,status=no,resizable=yes,screenx=0,screeny=0'); return false;"
										href="#"><i class="fa fa-circle-o"></i> <?= lang('print_labels'); ?></a></li>
								<li id="products_movements"><a href="<?= site_url('products/movements'); ?>"><i class="fa fa-circle-o"></i> <?= lang('movements_search'); ?></a></li>

								<li class="divider"></li>
								<li id="warehouses_index"><a href="<?= site_url('warehouses'); ?>"><i class="fa fa-circle-o"></i> <?= lang('warehouse'); ?></a></li>
								<li id="warehouses_add"><a href="<?= site_url('warehouses/add'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_warehouse'); ?></a></li>
								<li id="warehouses_transfers"><a href="<?= site_url('warehouses/transfers'); ?>"><i class="fa fa-circle-o"></i> <?= lang('transfer_s'); ?></a></li>

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

						<li class="treeview" id="mm_categories">
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

						<li class="treeview" id="mm_sales">
							<a href="#">
								<i class="fa fa-shopping-cart"></i>
								<span><?= lang('sales'); ?></span>
								<i class="fa fa-angle-left pull-right"></i>
							</a>
							<ul class="treeview-menu">
								<li id="sales_index"><a href="<?= site_url('sales'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_sales'); ?></a></li>
								<li id="sales_add"><a href="<?= site_url('sales/add'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_sale'); ?></a></li>
								<!--<li id="sales_opened"><a href="<?//= site_url('sales/opened'); ?>"><i class="fa fa-circle-o"></i> <?//= lang('list_opened_bills'); ?></a></li>-->
								<!--**************TRJ052 - ALEXANDER ROCA - 13/06/2019***********-->
								<li class="divider"></li>
								<li id="makers_index"><a href="<?= site_url('Referral_guide'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_referral_guide'); ?></a></li>
								<li id="makers_index"><a href="<?= site_url('Referral_guide/add'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_referral_guide'); ?></a></li>
								<li class="divider"></li>
								<li id="credit_note_index"><a href="<?= site_url('credit_note'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_credit_note'); ?></a></li>
								<li id="credit_note_add"><a href="<?= site_url('credit_note/add'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_credit_note'); ?></a></li>

								<!--**************TRJ052 - ALEXANDER ROCA - 13/06/2019***********-->
								<!--**************TRJ132 -KENY PONTE - 06/11/2019 ***************-->
								<!--<li class="divider"></li>
								<li id="referral_guide_index"><a href="<?= site_url('referral_guide'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_referral_guide'); ?></a></li>
								<li id="referral_guide_add"><a href="<?= site_url('referral_guide/add'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_referral_guide'); ?></a></li>-->
								<!--**************TRJ132 -KENY PONTE - 06/11/2019 ***************-->

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

						<li class="treeview" id="mm_gift_cards">
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

						<li class="treeview mm_auth mm_customers mm_suppliers">
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

					

						<li class="treeview" id="mm_reports">
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
							<li class="treeview" id="mm_settings">
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

						<li id="mm_products"><a href="<?= site_url('products'); ?>"><i class="fa fa-barcode"></i> <span><?= lang('products'); ?></span></a></li>
						<li id="mm_categories"><a href="<?= site_url('categories'); ?>"><i class="fa fa-folder-open"></i> <span><?= lang('categories'); ?></span></a></li>

						<li class="treeview" id="mm_sales">
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

						<li class="treeview" id="mm_gift_cards">
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

						<li class="treeview" id="mm_customers">
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

			<div class="col-lg-12 alerts">
				<?php if($error)  { ?>
				<div class="alert alert-danger alert-dismissable">
					<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
					<h4><i class="icon fa fa-ban"></i> <?= lang('error'); ?></h4>
					<?= $error; ?>
				</div>
				<?php } if($message) { ?>
				<div class="alert alert-success alert-dismissable">
					<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
					<h4><i class="icon fa fa-check"></i> <?= lang('Success'); ?></h4>
					<?= $message; ?>
				</div>
				<?php } ?>
			</div>

			<table style="width:100%;" class="layout-table">
				<tr>
					<td style="width: 460px;">

						<div id="pos">
							<?= form_open('pos', 'id="pos-sale-form"'); ?>
							<div class="well well-sm" id="leftdiv">
								<div id="lefttop" style="margin-bottom:5px;">
									<!-- <div class="form-group" style="margin-bottom:5px;">
										<div class="input-group">
											<?php
											$can[0] = lang("select") . ' ' . lang("canal");
											foreach($canals as $canal){ $can[$canal->id] = $canal->canal; } ?>
											<?= form_dropdown('canal', $can, set_value('canal_id', $canal1), 'id="canal_id" data-placeholder="' . lang("select") . ' ' . lang("canal") . '" required="required" class="form-control select2" style="width:100%;"'); ?>

										</div>
										<div style="clear:both;"></div>
									</div> -->

									<div class="form-group" style="margin-bottom:5px;">
										<div class="input-group">
											<?php foreach($customers as $customer){ $cus[$customer->id] = $customer->name; } ?>
											<?= form_dropdown('customer_id', $cus, set_value('customer_id', $Settings->default_customer), 'id="spos_customer" data-placeholder="' . lang("select") . ' ' . lang("customer") . '" required="required" class="form-control select2" style="width:100%;"'); ?>
											<div class="input-group-addon no-print" style="padding: 2px 5px;">
												<a href="#" id="edit-customer" class="external" data-toggle="modal" data-target="#myModal"><i class="fa fa-2x fa-edit" id="editIcon"></i></a>
												<a href="#" id="add-customer" class="external" data-toggle="modal" data-target="#myModal"><i class="fa fa-2x fa-plus-circle" id="addIcon"></i></a>
											</div>
										</div>
										<div style="clear:both;"></div>
									</div>
									<div class="form-group" style="margin-bottom:5px;">
										<div class="input-group">
											<input type="text" name="code" id="add_item" class="form-control" placeholder="<?=lang('search__scan')?>" />
											<div class="input-group-addon no-print" style="padding: 2px 5px;">
												<a href="#" id="search-customer" class="external" data-toggle="modal" data-target="#myModal"><i class="fa fa-2x fa-search" id="addIcon"></i></a>
											</div>
										</div>
									</div>
								</div>
								<div id="printhead" class="print">
									<?= $Settings->header; ?>
									<p>Date: <?=date($Settings->dateformat)?></p>
								</div>


								<div id="print"><!-- tabla items productos  -->
									<div id="list-table-div">
										<table id="posTable" class="table table-striped table-condensed table-hover list-table" style="margin:0;border-collapse: collapse;border: 1px" border="0" >
											<thead>
												<tr class="" style="background: #008D4C;color: #FFF">
													<th><?=lang('product')?></th>
													
													<th style="width: 15%;text-align:center;"><?=lang('price')?></th>
													<th style="width: 15%;text-align:center;"><?=lang('qty')?></th>
													<?php if($plastic_bags > 0){?>
													<th style="width: 20%;text-align:center;"><?=lang('tax_ICBPER')?></th>
													<?php }?>
													<th style="width: 20%;text-align:center;"><?=lang('subtotal')?></th>
													<th style="width: 20px;" class="satu"><i class="fa fa-trash-o"></i></th>
												</tr>
											</thead>
											<tbody></tbody>
										</table>
									</div>
									<div style="clear:both;"></div>
									<div id="totaldiv">
										<table id="totaltbl" class="table table-condensed totals" style="margin-bottom:10px;">
											<tbody>
												<tr class="" style="background: #bbded6">
													<td width="25%"><?=lang('total_items')?></td>
													<td class="text-right" style="padding-right:10px;"><span id="count">0</span></td>
													<td width="25%"><?=lang('total')?></td>
													<td class="text-right" colspan="2"><span id="total">0</span></td>
												</tr>
												<tr class="" style="background: #bbded6">
													<td colspan="2" width="25%"><a href="#" id="add_discount"><?=lang('discount')?></a></td>
													<td colspan="2" class="text-right" style="padding-right:10px;"><span id="ds_con">0</span></td>
													<!-- <td width="25%"><a href="#" id="add_tax"><?=lang('order_tax')?></a></td>
													<td class="text-right"><span id="ts_con">0</span></td> -->
												</tr>
												<tr style="background: #7fa998">
													<td colspan="2" style="font-weight:bold;"><?=lang('total_payable')?></td>
													<td class="text-right" colspan="2" style="font-weight:bold;"><span id="total-payable">0</span></td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>

								<div id="botbuttons" class="col-xs-12 text-center">
									<div class="row">
										<div class="col-xs-4" style="padding: 0;">
											<div class="btn-group-vertical btn-block">
												<button type="button" class="btn btn-warning btn-block btn-flat"
												id="suspend"><?= lang('hold'); ?></button>
												<button type="button" class="btn btn-danger btn-block btn-flat"
												id="reset"><?= lang('cancel'); ?></button>
											</div>

										</div>
										<div class="col-xs-4" style="padding: 0 5px;">
											<div class="btn-group-vertical btn-block">
												<button type="button" class="btn bg-purple btn-block btn-flat" id="print_order"><?= lang('print_order'); ?></button>

												<button type="button" class="btn bg-navy btn-block btn-flat" id="print_bill"><?= lang('print_bill'); ?></button>
											</div>
										</div>
										<div class="col-xs-4" style="padding: 0;">
											<button type="button" class="btn btn-success btn-block btn-flat" id="<?= $eid ? 'submit-sale' : 'payment'; ?>" style="height:67px;"><?= $eid ? lang('submit') : lang('payment'); ?></button>
										</div>
									</div>

								</div>
								<div class="clearfix"></div>
								<span id="hidesuspend"></span>
								<input type="hidden" name="spos_note" value="" id="spos_note"/>

								<div id="payment-con">
									
									<input type="hidden" name="amount" id="amount_val" value="<?= $eid ? $sale->paid : 0; ?>"/>
									<input type="hidden" name="balance_amount" id="balance_val" value=""/>
									<input type="hidden" name="paid_by" id="paid_by_val" value="cash"/>
									<input type="hidden" name="document_type" id="document_type_val" value="1"/>
									<input type="hidden" name="canal_id" id="canal_id_val" value="<?php echo $canal1;?>"/>

									<input type="hidden" name="cc_no" id="cc_no_val" value=""/>
									<input type="hidden" name="paying_gift_card_no" id="paying_gift_card_no_val" value=""/>
									<input type="hidden" name="cc_holder" id="cc_holder_val" value=""/>
									<input type="hidden" name="cheque_no" id="cheque_no_val" value=""/>
									<input type="hidden" name="cc_month" id="cc_month_val" value=""/>
									<input type="hidden" name="cc_year" id="cc_year_val" value=""/>
									<input type="hidden" name="cc_type" id="cc_type_val" value=""/>
									<input type="hidden" name="cc_cvv2" id="cc_cvv2_val" value=""/>
									<input type="hidden" name="balance" id="balance_val" value=""/>
									<input type="hidden" name="payment_note" id="payment_note_val" value=""/>

									<input type="hidden" name="custom_field_1" id="custom_field_1_val" value=""/> <!--tec_sales-->
									<input type="hidden" name="custom_field_2" id="custom_field_2_val" value=""/> <!--tec_sales-->

									<!-- -------------------------------------------------------------- -->
									<input type="hidden" name="monto_total" id="monto_total_val" value="0"/>
									<input type="hidden" name="descuento" id="descuento_val" value="0"/>

									<!-- -------------------------------------------------------------- -->

									<!-- Tipo de Cambio | Diego -->
									<input type="hidden" id="exchange" value="<?= $Exchange->sell ?>">

								</div>
								<input type="hidden" name="customer" id="customer" value="<?=$Settings->default_customer?>" />
								<input type="hidden" name="order_tax" id="tax_val" value="" />
								<input type="hidden" name="order_discount" id="discount_val" value="" />
								<input type="hidden" name="count" id="total_item" value="" />
								<input type="hidden" name="did" id="is_delete" value="<?=$sid;?>" />
								<input type="hidden" name="eid" id="is_delete" value="<?=$eid;?>" />
								<input type="hidden" name="hold_ref" id="hold_ref" value="" />
								<input type="hidden" name="total_items" id="total_items" value="0" />
								<input type="hidden" name="total_quantity" id="total_quantity" value="0" />
								<input type="hidden" name="submit_type" id="submit_type"  />
								<input type="hidden" name="envioPos" id="envioPos" value="2" /><!-- ********* TRJ062 - KENY PONTE 20/09/2019********** -->
								<input type="submit" id="submit" value="Submit Sale" style="display: none;" />
							</div>
							<?=form_close();?>
						</div>

					</td>
					<td>

						<div class="contents" id="right-col"> <!-- list  items products -->
							<div id="item-list">
								<div class="items">
									<?php echo $products; ?>
								</div>
							</div>

							<div class="product-nav">
								<div class="btn-group btn-group-justified">
									<div class="btn-group">
										<button style="z-index:10002;" class="btn btn-warning pos-tip btn-flat" type="button" id="previous"><i class="fa fa-chevron-left"></i></button>
									</div>
									<div class="btn-group">
										<button style="z-index:10003;" class="btn btn-success pos-tip btn-flat" type="button" id="sellGiftCard"><i class="fa fa-credit-card" id="addIcon"></i> <?= lang('sell_gift_card') ?></button>
									</div>
									<div class="btn-group">
										<button style="z-index:10004;" class="btn btn-warning pos-tip btn-flat" type="button" id="next"><i class="fa fa-chevron-right"></i></button>
									</div>
								</div>
							</div>

						</div>

					</td>
				</tr>
			</table>

		</div>

	</div>



			<aside class="control-sidebar control-sidebar-dark" id="categories-list">

				<div class="tab-content">
					<div class="tab-pane active" id="control-sidebar-home-tab">
						<ul class="control-sidebar-menu">
							
							<?php
							foreach($categories as $category) {
								echo '<li><a href="#" class="category'.($category->id == $Settings->default_category ? ' active' : '').'" id="'.$category->id.'">';
								if($category->image) {
									echo '<div class="menu-icon"><img src="'.base_url('uploads/thumbs/'.$category->image).'" alt="" class="img-thumbnail img-circle img-responsive"></div>';
								} else {
									echo '<i class="menu-icon fa fa-folder-open bg-red"></i>';
								}
								echo '<div class="menu-info"><h4 class="control-sidebar-subheading">'.$category->code.'</h4><p>'.$category->name.'</p></div>
							</a></li>';
						}
						?>
					</ul>
				</div>
			</div>
			</aside>

		<div class="control-sidebar-bg"></div>
<!--	</div>
</div>-->
<div id="order_tbl" style="display:none;"><span id="order_span"></span>
	<table id="order-table" class="prT table table-striped table-condensed" style="width:100%;margin-bottom:0;"></table>
</div>
<div id="bill_tbl" style="display:none;"><span id="bill_span"></span>
	<table id="bill-table" width="100%" class="prT table table-striped table-condensed" style="width:100%;margin-bottom:0;"></table>
	<table id="bill-total-table" width="100%" class="prT table table-striped table-condensed" style="width:100%;margin-bottom:0;"></table>
</div>

<div class="modal" data-easein="flipYIn" id="posModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>
<div class="modal" data-easein="flipYIn" id="posModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true"></div>
<div id="ajaxCall"><i class="fa fa-spinner fa-pulse"></i></div>

<div class="modal" data-easein="flipYIn" id="gcModal" tabindex="-1" role="dialog" aria-labelledby="mModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title" id="myModalLabel"><?= lang('sell_gift_card'); ?></h4>
			</div>
			<div class="modal-body">
				<!-- <p><?= lang('enter_info'); ?></p> -->

				<div class="alert alert-danger gcerror-con" style="display: none;">
					<button data-dismiss="alert" class="close" type="button">×</button>
					<span id="gcerror"></span>
				</div>
				<div class="form-group">
					<?= lang("card_no", "gccard_no"); ?> *
					<div class="input-group">
						<?php echo form_input('gccard_no', '', 'class="form-control" id="gccard_no"'); ?>
						<div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;"><a href="#" id="genNo"><i class="fa fa-cogs"></i></a></div>
					</div>
				</div>
				<input type="hidden" name="gcname" value="<?= lang('gift_card') ?>" id="gcname"/>
				<div class="form-group">
					<?= lang("value", "gcvalue"); ?> *
					<?php echo form_input('gcvalue', '', 'class="form-control" id="gcvalue"'); ?>
				</div>
				<div class="form-group">
					<?= lang("price", "gcprice"); ?> *
					<?php echo form_input('gcprice', '', 'class="form-control" id="gcprice"'); ?>
				</div>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?=lang('close')?></button>
				<button type="button" id="addGiftCard" class="btn btn-primary"><?= lang('sell_gift_card') ?></button>
			</div>
		</div>
	</div>
</div>

<div class="modal" data-easein="flipYIn" id="dsModal" tabindex="-1" role="dialog" aria-labelledby="dsModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title" id="dsModalLabel"><?= lang('discount_title'); ?></h4>
			</div>
			<div class="modal-body">
				<input type='text' class='form-control input-sm kb-pad' id='get_ds' onClick='this.select();' value=''>

				<label class="checkbox" for="apply_to_order">
					<input type="radio" name="apply_to" value="order" id="apply_to_order" checked="checked"/>
					<?= lang('apply_to_order') ?>
				</label>
				<!-- <label class="checkbox" for="apply_to_products">
					<input type="radio" name="apply_to" value="products" id="apply_to_products"/>
					<?= lang('apply_to_products') ?>
				</label> -->
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default btn-sm pull-left" data-dismiss="modal"><?=lang('close')?></button>
				<button type="button" id="updateDiscount" class="btn btn-primary btn-sm"><?= lang('update') ?></button>
			</div>
		</div>
	</div>
</div>

<div class="modal" data-easein="flipYIn" id="tsModal" tabindex="-1" role="dialog" aria-labelledby="tsModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title" id="tsModalLabel"><?= lang('tax_title'); ?></h4>
			</div>
			<div class="modal-body">
				<input type='text' class='form-control input-sm kb-pad' id='get_ts' onClick='this.select();' value=''>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default btn-sm pull-left" data-dismiss="modal"><?=lang('close')?></button>
				<button type="button" id="updateTax" class="btn btn-primary btn-sm"><?= lang('update') ?></button>
			</div>
		</div>
	</div>
</div>

<div class="modal" data-easein="flipYIn" id="proModal" tabindex="-1" role="dialog" aria-labelledby="proModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-primary">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title" id="proModalLabel">
					<?=lang('payment')?>
				</h4>
			</div>
			<div class="modal-body">
				<table class="table table-bordered table-striped">
					<tr>
						<th style="width:25%;"><?= lang('net_price'); ?></th>
						<th style="width:25%;"><span id="net_price"></span></th>
						<th style="width:25%;"><?= lang('product_tax'); ?></th>
						<th style="width:25%;"><span id="pro_tax"></span> <span id="pro_tax_method"></span></th>
					</tr>
				</table>
				<input type="hidden" id="row_id" />
				<input type="hidden" id="item_id" />
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<?=lang('unit_price', 'nPrice')?>
							<input type="text" class="form-control input-sm kb-pad" id="nPrice" onClick="this.select();" placeholder="<?=lang('new_price')?>">
						</div>
						<div class="form-group">
							<?=lang('discount', 'nDiscount')?>
							<input type="text" class="form-control input-sm kb-pad" id="nDiscount" onClick="this.select();" placeholder="<?=lang('discount')?>">
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<?=lang('quantity', 'nQuantity')?>
							<input type="text" class="form-control input-sm kb-pad" id="nQuantity" onClick="this.select();" placeholder="<?=lang('current_quantity')?>">
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?=lang('close')?></button>
				<button class="btn btn-success" id="editItem"><?=lang('update')?></button>
			</div>
		</div>
	</div>
</div>
		
<div class="modal" data-easein="flipYIn" id="susModal" tabindex="-1" role="dialog" aria-labelledby="susModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title" id="susModalLabel"><?= lang('suspend_sale'); ?></h4>
			</div>
			<div class="modal-body">
				<p><?= lang('type_reference_note'); ?></p>

				<div class="form-group">
					<?= lang("reference_note", "reference_note"); ?>
					<?php echo form_input('reference_note', $reference_note, 'class="form-control kb-text" id="reference_note"'); ?>
				</div>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal"> <?=lang('close')?> </button>
				<button type="button" id="suspend_sale" class="btn btn-primary"><?= lang('submit') ?></button>
			</div>
		</div>
	</div>
</div>



<div class="modal" data-easein="flipYIn" id="saleModal" tabindex="-1" role="dialog" aria-labelledby="saleModalLabel" aria-hidden="true"></div>
<div class="modal" data-easein="flipYIn" id="opModal" tabindex="-1" role="dialog" aria-labelledby="opModalLabel" aria-hidden="true"></div>

<div class="modal" data-easein="flipYIn" id="payModal" tabindex="-1" role="dialog" aria-labelledby="payModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-success">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title" id="payModalLabel">
					<?=lang('payment')?>
				</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-9">
						<div class="font16">
							<table class="table table-bordered table-condensed" style="margin-bottom: 0;">
								<tbody>
									<tr>
										<td width="25%" style="border-right-color: #FFF !important;"><?= lang("total_items"); ?></td>
										<td width="25%" class="text-right"><span id="item_count">0.00</span></td>
										<td width="25%" style="border-right-color: #FFF !important;"><?= lang("total_payable"); ?></td>
										<td width="25%" class="text-right"><span id="twt">0.00</span></td>
									</tr>
									<tr>
										<td style="border-right-color: #FFF !important;"><?= lang("total_paying"); ?></td>
										<td class="text-right"><span id="total_paying">0.00</span></td>
										<td style="border-right-color: #FFF !important;"><?= lang("balance"); ?></td>
										<td class="text-right"><span id="balance">0.00</span></td>
									</tr>
								</tbody>
							</table>
							<div class="clearfix"></div>
						</div>
						<div class="row">
							<div class="col-xs-12">
								<div class="form-group">
									<?= lang('note', 'note'); ?>
									<textarea name="note" id="note" class="pa form-control kb-text"></textarea>
								</div>
							</div>
						</div>

						<?php $negocio = $this->session->userdata('negocio');?>

						<?php switch($negocio){
							case 0: ?>
								<?php break;
							case 1: ?>
								<div class="row">
								<div class="col-xs-6">
									<div class="form-group">
										<?= lang('cmp','cmp'); ?>
										<input type="text" id="custom_field_1" maxlength="6" class="form-control custom_field_1 kb-pad"></> <!--tec_sales-->
									</div>
								</div>
								<div class="col-xs-6">
									<div class="form-group">
										<?= lang('doctor','doctor'); ?>
										<input type="text" id="custom_field_2" readonly class="form-control custom_field_2 kb-text"></> <!--tec_sales-->
									</div>
								</div>
							</div>
								<?php break;
						} ?>

						<div class="row">
							<div class="col-xs-6">
								<div class="form-group">
									<?= lang("document_type", "document_type"); ?>
									<select id="document_type" class="form-control document_type select2" style="width:100%;">
										<option value="0"><?= lang("select"); ?></option>
										<option value="1" selected><?= lang("bill_type"); ?></option>
										<option value="2"><?= lang("invoice"); ?></option>
										<option value="3"><?= lang("nventa"); ?></option> <!--//*****Renato TRJ023 25/04/2019   ********-->
									</select>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-6">
								<div class="form-group">
									<?= lang("amount", "amount"); ?>
									<input name="amount[]" type="text" id="amount"
									class="pa form-control kb-pad amount"/>
								</div>
							</div>
							<div class="col-xs-6">
								<div class="form-group">
									<?= lang("paying_by", "paid_by"); ?>
									<select id="paid_by" class="form-control paid_by select2" style="width:100%;">
										<option value="cash"><?= lang("cash"); ?></option>
										<option value="CC"><?= lang("cc"); ?></option>
										<option value="Cheque"><?= lang("cheque"); ?></option>
										<option value="gift_card"><?= lang("gift_card"); ?></option>
										<?= isset($Settings->stripe) ? '<option value="stripe">' . lang("stripe") . '</option>' : ''; ?>
									</select>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12">
								<div class="form-group gc" style="display: none;">
									<?= lang("gift_card_no", "gift_card_no"); ?>
									<input type="text" id="gift_card_no"
									class="pa form-control kb-pad gift_card_no gift_card_input"/>

									<div id="gc_details"></div>
								</div>

								<!-- <div class="pcc" style="display:none;">
									<div class="form-group">
										<input type="text" id="swipe" class="form-control swipe swipe_input"
										placeholder="<?= lang('focus_swipe_here') ?>"/>
									</div>
									<div class="row">
										<div class="col-xs-6">
											<div class="form-group">
												<input type="text" id="pcc_no"
												class="form-control kb-pad"
												placeholder="<?= lang('cc_no') ?>"/>
											</div>
										</div>
										<div class="col-xs-6">
											<div class="form-group">

												<input type="text" id="pcc_holder"
												class="form-control kb-text"
												placeholder="<?= lang('cc_holder') ?>"/>
											</div>
										</div>
										<div class="col-xs-3">
											<div class="form-group">
												<select id="pcc_type"
												class="form-control pcc_type select2"
												placeholder="<?= lang('card_type') ?>">
												<option value="Visa"><?= lang("Visa"); ?></option>
												<option
												value="MasterCard"><?= lang("MasterCard"); ?></option>
												<option value="Amex"><?= lang("Amex"); ?></option>
												<option
												value="Discover"><?= lang("Discover"); ?></option>
											</select>
										</div>
									</div>
									<div class="col-xs-3">
										<div class="form-group">
											<input type="text" id="pcc_month"
											class="form-control kb-pad"
											placeholder="<?= lang('month') ?>"/>
										</div>
									</div>
									<div class="col-xs-3">
										<div class="form-group">

											<input type="text" id="pcc_year"
											class="form-control kb-pad"
											placeholder="<?= lang('year') ?>"/>
										</div>
									</div>
									<div class="col-xs-3">
										<div class="form-group">

											<input type="text" id="pcc_cvv2"
											class="form-control kb-pad"
											placeholder="<?= lang('cvv2') ?>"/>
										</div>
									</div>
								</div>
							</div> -->

							<div class="pcheque" style="display:none;">
								<div class="form-group"><?= lang("cheque_no", "cheque_no"); ?>
									<input type="text" id="cheque_no"
									class="form-control cheque_no  kb-text"/>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-3 text-center">
					<!-- <span style="font-size: 1.2em; font-weight: bold;"><?= lang('quick_cash'); ?></span> -->

					<div class="btn-group btn-group-vertical" style="width:100%;">
						<button type="button" class="btn btn-info btn-block quick-cash" id="quick-payable">0.00
						</button>
						<?php
						foreach (lang('quick_cash_notes') as $cash_note_amount) {
							echo '<button type="button" class="btn btn-block btn-warning quick-cash">' . $cash_note_amount . '</button>';
						}
						?>
						<button type="button" class="btn btn-block btn-danger"
						id="clear-cash-notes"><?= lang('clear'); ?></button>
					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default pull-left" data-dismiss="modal"> <?=lang('close')?> </button>

			<?php $pago = $this->session->userdata('habilita_btn_pago');?>
			<?php $caja = $this->session->userdata('habilita_btn_caja');?>

			<?php if($pago){ ?>
				<button class="btn btn-primary" id="<?= $eid ? '' : 'submit-sale'; ?>"><?=lang('pay')?> </button>
			<?php } ?>

			<?php if($caja){ ?>
				<button class="btn btn-primary" onclick = "this.disabled = true" id="<?= $eid ? '' : 'submit-sale'; ?>_caja"><?=lang('pay')?> <?=lang('in')?> <?=lang('register')?></button>

			<?php } ?>

		</div>
	</div>
</div>
</div>
<!--****************************************Modal para agregar cliente*************************************************-->
<div class="modal" data-easein="flipYIn" id="customerModal" tabindex="-1" role="dialog" aria-labelledby="cModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-primary">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-remove"></i></button>
				<h4 class="modal-title" id="cModalLabel">
					<?=lang('add_customer')?>
				</h4>
			</div>
			<?= form_open('pos/add_customer', 'id="customer-form"'); ?>
			<div class="modal-body">
				<div id="c-alert" class="alert alert-danger" style="display:none;"></div>
				<div id="rucalert" class="alert alert-danger" style="display:none;">El Ruc Ingresado No Ha Sido Encontrado</div>
				<div id="rucalert2" class="alert alert-danger" style="display:none;">Ruc cuenta con 11 digitos</div>
	<!--*********************TRJ073 - KENY PONTE - 20/08/2019**************************************-->			<!--------------------------------------------------------------------------------------------------------->
				<div class="form-group">
					<label class="control-label" for="type_persona"><?= $this->lang->line("type_person"); ?></label>

					<div class="radio-group">
     					 <div class='radio'name="persona" style="border: 5px solid #000" id="persona_new" data-value="<?= $this->lang->line("person1");?>" onclick="javascript:cambiarDatos(this.form,this);"></div>&nbsp;&nbsp;&nbsp;&nbsp;<label><?= $this->lang->line("person1"); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
      					 <div class='radio' name="persona" id="persona_new_2" data-value="<?= $this->lang->line("person2");?>" onclick="javascript:cambiarDatos1(this.form,this);"></div><label>&nbsp;&nbsp;&nbsp;&nbsp;<?= $this->lang->line("person2"); ?></label>
     							 
     				</div>
					<div class="custom-control custom-radio custom-control-inline">	
						<label class="control-label" for="type_person">
					</div>
					<div class="custom-control custom-radio custom-control-inline">
						<input type="radio" name="person" id="person1" value="1">
						<input type="radio" name="person" id="person2" value="2">
					</div>
				</div>
				<div class="form-group">
                    <?= lang('document_type', 'document_type'); ?>
                    <?php
                        $ctv1[null] = lang("select")." ".lang("document_type");
                        foreach($document_type as $doc_type) {
                                $ctv1[$doc_type->id] = $doc_type->document_type;
                        }
                    ?>
              		 <?= form_dropdown('document_type', $ctv1, set_value("type",$customer->document_type), 'class="form-control tip select2" id="document_type1"  required="required" style="width:100%;"'); ?>
                </div>
				<!--*********************TRJ073 - KENY PONTE - 20/08/2019**************************************-->


				<div class="row">
					<div class="col-xs-12">
						<div class="form-group">
							<label class="control-label" id="nombre-persona" for="code">
								<?= lang("name"); ?>
							</label>
							<?= form_input('name', '', 'class="form-control input-sm kb-text" id="cname"'); ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-6">
						<div class="form-group">
							<label class="control-label" for="cemail">
								<?= lang("email_address"); ?>
							</label>
							<?= form_input('email', '', 'class="form-control input-sm kb-text" id="cemail"'); ?>
						</div>
					</div>
					<div class="col-xs-6">
						<div class="form-group">
							<label class="control-label" for="phone">
								<?= lang("phone"); ?>
							</label>
							<?= form_input('phone', '', 'class="form-control input-sm kb-pad" id="cphone"');?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-6">
						<div class="form-group">
							<label class="control-label" for="cf1">
								<?= lang("cf2"); ?>
							</label>
							<?= form_input('cf1', '', 'class="form-control input-sm kb-text" id="cf1"'); ?>
						</div>
					</div>
					
					<div class="col-xs-5">
					
						<div class="form-group">
							<label class="control-label" for="cf2">
								<?= lang("cf1"); ?>
							</label>
							
							<?= form_input('cf2', '', 'class="form-control input-sm kb-text" id="cf2" maxlength="11"');?>
	
						</div>
					</div>
					<div class="col-xs-1" style="margin-top:4%;margin-left:-4%">
					<div type="conruc" onclick="obtenerDatos2()" class="btn btn-info btn-sm glyphicon glyphicon-ok" tabindex="0" data-toggle="tooltip" title="Consulta Sunat"></div>
					<div id="cargando" class="" style="display:none;"><img style="width: 30px;height:auto;" src="https://pa1.narvii.com/6558/d6738388bae69543478eb78d0545bf0475ef05a6_hq.gif"></div>
				</div>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<div class="form-group">
							<label class="control-label" for="code">
								<?= lang("address"); ?>
							</label>
							<?= form_input('direccion', '', 'class="form-control input-sm kb-text" id="cdireccion"'); ?>
						</div>
					</div>
				</div>
				  <div class="form-group"> 
             			  <?= lang('customers_type', 'customers_type'); ?>
               			 <?php
               				 $ctv[] = lang("select")." ".lang("customers_type");

               				 foreach($customer_type as $cust_type) {
                  				  $ctv[$cust_type->id] = $cust_type->customers_type;
               				 }
              			  ?>
              			
							<!--********** TRJ124***** -->
							<!-- AMADOR -->

              			 <?= form_dropdown('customer_type',$ctv, set_value("type",$customer->customers_type_id),'class="form-control " id="customer_type"  required="required" style="width:100%;"');
              			 ?>
              				 <!-- END -->
            		</div>	

			</div>
			<div class="modal-footer" style="margin-top:0;">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal"> <?=lang('close')?> </button>
				<button type="submit" class="btn btn-primary" id="add_customer"> <?=lang('add_customer')?> </button>
			</div>
			<?= form_close(); ?>
		</div>
	</div>
</div>

<!--********************************************modal para editar clietne*********************************-->

<div class="modal" data-easein="flipYIn" id="customerModalEdit" tabindex="-1" role="dialog" aria-labelledby="cModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-primary">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-remove"></i></button>
				<h4 class="modal-title" id="cModalLabel">
					<?=lang('edit_customer')?>
				</h4>
			</div>
			<?= form_open('pos/edit_customer', 'id="customer-edit-form"'); ?>
			<div class="modal-body">
				<div id="c-alert2" class="alert alert-danger" style="display:none;"></div>
				<div id="rucalert3" class="alert alert-danger" style="display:none;">El Ruc Ingresado No Ha Sido Encontrado</div>
				<div id="rucalert4" class="alert alert-danger" style="display:none;">Ruc cuenta con 11 digitos</div>
<!--*********************TRJ073 - KENY PONTE - 20/08/2019**************************************-->
				<div class="form-group">
							<label class="control-label" for="type_persona"><?= $this->lang->line("type_person"); ?></label>

							<div class="radio-group">
     							 <div class='radio' id="divpersona1" name="persona" data-value="<?= $this->lang->line("person1");?>" onclick="javascript:cambiarDatos2(this.form,this);"></div>&nbsp;&nbsp;&nbsp;&nbsp;<label><?= $this->lang->line("person1"); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
      							 <div class='radio' id="divpersona2" name="persona" data-value="<?= $this->lang->line("person2");?>" onclick="javascript:cambiarDatos3(this.form,this);"></div><label>&nbsp;&nbsp;&nbsp;&nbsp;<?= $this->lang->line("person2"); ?></label>
     							 
     					    </div>
							<div class="custom-control custom-radio custom-control-inline">
								<!--<?= form_radio('person', set_value('person1'),'id="person1"');?>-->
								<label class="control-label" for="type_person">
							</div>
								 <div class="custom-control custom-radio custom-control-inline">
								 	 <input type="radio" name="person" id="person3" value="1"/>
                 					 <input type="radio" name="person" id="person4" value="2"/>
										
								</div>
						</div>

						 <div class="form-group">
                            <?= lang('document_type', 'document_type');?>
                            <?php
                           		foreach($document_type as $doc_type) {
                                $ctv1[$doc_type->id] = $doc_type->document_type;
                            	}
                            ?>
              			 <?= form_dropdown('document_type', $ctv1, set_value("document_type",$customer->document_type), 'class="form-control tip select2" id="document_type2"  required="required" style="width:100%;"'); ?>
                        </div>
<!--*********************TRJ073 - KENY PONTE - 20/08/2019**************************************-->
				<div class="row">
					<div class="col-xs-12">
						<div class="form-group">
							<label class="control-label" id="nombre-persona1" for="code">
								<?= lang("name"); ?>
							</label>
							<?= form_input('name', '', 'class="form-control input-sm kb-text" id="cname2"'); ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-6">
						<div class="form-group">
							<label class="control-label" for="cemail">
								<?= lang("email_address"); ?>
							</label>
							<?= form_input('email', '', 'class="form-control input-sm kb-text" id="cemail2"'); ?>
						</div>
					</div>
					<div class="col-xs-6">
						<div class="form-group">
							<label class="control-label" for="phone">
								<?= lang("phone"); ?>
							</label>
							<?= form_input('phone', '', 'class="form-control input-sm kb-pad" id="cphone2"');?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-6">
						<div class="form-group">
							<label class="control-label"  for="cf1">
								<?= lang("document_number"); ?>
							</label>
							<?= form_input('cf1', '', 'class="form-control input-sm kb-text" id="cf12"'); ?>
						</div>
					</div>
					<div class="col-xs-5">
						<div class="form-group">
							<label class="control-label" for="cf2">
								<?= lang("cf1"); ?>
							</label>
							<?= form_input('cf2', '', 'class="form-control input-sm kb-text" id="cf22" maxlength="11"');?>
						</div>
					</div>
					<div class="col-xs-1" style="margin-top:4%;margin-left:-4%">
					<div type="conruc" onclick="obtenerDatos()" class="btn btn-info btn-sm glyphicon glyphicon-ok" tabindex="0" data-toggle="tooltip" title="Consulta Sunat"></div>
					<div id="cargando1" class="" style="display:none;"><img style="width: 30px;height:auto;" src="https://pa1.narvii.com/6558/d6738388bae69543478eb78d0545bf0475ef05a6_hq.gif"></div>
				</div>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<div class="form-group">
							<label class="control-label" for="code">
								<?= lang("address"); ?>
							</label>
							<?= form_input('direccion', '', 'class="form-control input-sm kb-text" id="cdireccion2"'); ?>
						</div>
					</div>
				</div>
				  <div class="form-group"> 
             			  <?= lang('customers_type', 'customers_type'); ?>
               			 <?php
               				 foreach($customer_type as $cust_type) {
                  				  $ctv[$cust_type->id] = $cust_type->customers_type;
               				 }
              			  ?>
              			<!--********** TRJ124***** -->
							<!-- AMADOR -->
						  <?= form_dropdown('customer_type', $ctv, set_value("customers_type",$customer->customers_type_id), 'class="form-control" id="customer_type_edit"  required="required" style="width:100%;"'); ?>
              			  <!-- END -->
            		</div>	

			</div>
			<div class="modal-footer" style="margin-top:0;">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal"> <?=lang('close')?> </button>
				<button type="submit" class="btn btn-primary" id="add_customer"> <?=lang('edit_customer')?> </button>
			</div>
			<?= form_close(); ?>
		</div>
	</div>
</div>



<div class="modal" data-easein="flipYIn" id="searchProducts" tabindex="-1" role="dialog" aria-labelledby="cModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="min-width: 85%" >
		<div class="modal-content" >
			<div class="modal-header modal-primary">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-remove"></i></button>
				<h4 class="modal-title" id="cModalLabel">
					<?=lang('product_search') ?>
				</h4>
			</div>
			<input type="hidden" name="___token" id="___token" value="<?= $this->security->get_csrf_token_name() ?>">
			<input type="hidden" name="___hash" id="___hash" value="<?= $this->security->get_csrf_hash() ?>">
			<div class="modal-body">
				<div id="c-alert3" class="alert alert-danger" style="display:none;"></div>
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label" for="product_search">
								<?= lang("product"); ?>
							</label>
							<?= form_input('product_search', '', 'class="form-control input-sm kb-text" id="product_search"'); ?>
						</div>
					</div>

					<?php switch($negocio){
						case 0: ?>
							<?= form_input('negocio', $negocio, 'style="display:none" id="negocio"'); ?>
							<?php break;
						case 1: ?>
							<?= form_input('negocio', $negocio, 'style="display:none" id="negocio"'); ?>
							<div class="col-md-3">
								<div class="form-group">
									<label class="control-label" for="prin_activ_search">
										<?= lang("active_principle"); ?>
									</label>
									<?= form_input('prin_activ_search', '', 'class="form-control input-sm kb-text" id="prin_activ_search"');?>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label class="control-label" for="acc_farm_search">
										<?= lang("pharmacological_action"); ?>
									</label>
									<?= form_input('acc_farm_search', '', 'class="form-control input-sm kb-text" id="acc_farm_search"');?>
								</div>
							</div>
							<?php break;
					} ?>

					<div class="col-md-2">
						<button style="margin-bottom: -50px;" type="button" class="btn btn-success" id="btn_search_product"> <?=lang('search2')?> </button>

					</div>
				</div>

				<div class="row">
					<div class="box-body">
					        <div class="table-responsive">
					        <table id="searchPresults" class="table table-striped table-bordered table-hover" style="margin-bottom:5px;">
					            <thead>
					            <tr class="active">

					                <th class="col-xs-1"><?= lang("code"); ?></th>
					                <th class="col-xs-4" ><?= lang("name"); ?></th>
									<?php switch($negocio){
										case 0: ?>
											<th><?= lang("category"); ?></th>
											<th><?= lang("maker"); ?></th>
											<?php break;
										case 1: ?>
											<th ><?= lang("active_principle"); ?></th>
											<th ><?= lang("pharmacological_action"); ?></th>
											<th><?= lang("category"); ?></th>
											<th><?= lang("laboratory"); ?></th>
											<?php break;
									} ?>
					                <th class="col-xs-1"><?= lang("price"); ?></th>
					                <th class="col-xs-1"><?= lang("stock"); ?></th>
										<th class="col-xs-1"><?= lang("range"); ?></th>
					                <th ><?= lang("actions"); ?></th>
					            </tr>
					            </thead>
					            <tbody>
					            <tr>
					                <td colspan="8" class="dataTables_empty"></td>
					            </tr>
					            </tbody>
					        </table>
					        </div>


					    <div class="clearfix"></div>
					</div>
				</div>

			</div>
			<div class="modal-footer" style="margin-top:0;">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal"> <?=lang('close')?> </button>
			</div>

		</div>
	</div>
</div>
<!--************************TRJ073 - KENY PONTE ******************************-->
 
<script src="<?= $assets ?>bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/perfect-scrollbar/js/perfect-scrollbar.jquery.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/fastclick/fastclick.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/redactor/redactor.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/iCheck/icheck.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/select2/select2.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/formvalidation/js/formValidation.popular.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/formvalidation/js/framework/bootstrap.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>dist/js/common-libs.js" type="text/javascript"></script>
<script src="<?= $assets ?>dist/js/jquery-ui.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>dist/js/app.js" type="text/javascript"></script>
<script src="<?= $assets ?>dist/js/pages/all.js" type="text/javascript"></script>
<script src="<?= $assets ?>dist/js/custom.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/input-mask/jquery.inputmask.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/input-mask/jquery.inputmask.date.extensions.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/velocity/velocity.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/velocity/velocity.ui.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>dist/js/parse-track-data.js" type="text/javascript"></script>



<!-- <?php if($Settings->java_applet) { ?>
<script type="text/javascript" src="<?= $assets ?>plugins/qz/js/deployJava.js"></script>
<script type="text/javascript" src="<?= $assets ?>plugins/qz/qz-functions.js"></script>
<script type="text/javascript">
    deployQZ('themes/<?=$Settings->theme?>/assets/plugins/qz/qz-print.jar', '<?= $assets ?>plugins/qz/qz-print_jnlp.jnlp');
    function printBill(bill) {
        usePrinter("<?= $Settings->receipt_printer; ?>");
        printData(bill);
    }
    <?php
    $printers = json_encode(explode('|', $Settings->pos_printers));
    echo 'var printer = '.$printers.';
    ';
    ?>
    function printOrder(order) {
        for (index = 0; index < printers.length; index++) {
            usePrinter(printers[index]);
            printData(order);
        }
    }
</script>
<?php } ?> -->


<script src="<?= $assets ?>dist/js/pos.js" type="text/javascript"></script>
<script type="text/javascript">
	var base_url = '<?=base_url();?>', assets = '<?= $assets ?>';
	var plastic_bags='<?=$plastic_bags;?>';

	var dateformat = '<?=$Settings->dateformat;?>', timeformat = '<?= $Settings->timeformat ?>';
	<?php unset($Settings->protocol, $Settings->smtp_host, $Settings->smtp_user, $Settings->smtp_pass, $Settings->smtp_port, $Settings->smtp_crypto, $Settings->mailpath, $Settings->timezone, $Settings->setting_id, $Settings->default_email, $Settings->version, $Settings->stripe, $Settings->stripe_secret_key, $Settings->stripe_publishable_key); ?>
	var Settings = <?= json_encode($Settings); ?>;
	var sid = false, username = '<?=$this->session->userdata('username');?>', spositems = {};
	$(window).load(function () {
		$('#mm_<?=$m?>').addClass('active');
		$('#<?=$m?>_<?=$v?>').addClass('active');
	});
	var pro_limit = <?=$Settings->pro_limit?>, java_applet = 0, count = 1, total = 0, an = 1, p_page = 0, page = 0, cat_id = <?=$Settings->default_category?>, tcp = <?=$tcp?>;
	var gtotal = 0, order_discount = 0, order_tax = 0, protect_delete = <?= ($Admin) ? 0 : ($Settings->pin_code ? 1 : 0); ?>;
	var order_data = '', bill_data = '';
	var lang = new Array();
	lang['code_error'] = '<?= lang('code_error'); ?>';
	lang['r_u_sure'] = '<?= lang('r_u_sure'); ?>';
	lang['please_add_product'] = '<?= lang('please_add_product'); ?>';
	lang['paid_less_than_amount'] = '<?= lang('paid_less_than_amount'); ?>';
	lang['x_suspend'] = '<?= lang('x_suspend'); ?>';
	lang['discount_title'] = '<?= lang('discount_title'); ?>';
	lang['update'] = '<?= lang('update'); ?>';
	lang['tax_title'] = '<?= lang('tax_title'); ?>';
	lang['leave_alert'] = '<?= lang('leave_alert'); ?>';
	lang['close'] = '<?= lang('close'); ?>';
	lang['delete'] = '<?= lang('delete'); ?>';
	lang['no_match_found'] = '<?= lang('no_match_found'); ?>';
	lang['wrong_pin'] = '<?= lang('wrong_pin'); ?>';
	lang['file_required_fields'] = '<?= lang('file_required_fields'); ?>';
	lang['enter_pin_code'] = '<?= lang('enter_pin_code'); ?>';
	lang['edit'] = '<?= lang('edit'); ?>';
	lang['incorrect_gift_card'] = '<?= lang('incorrect_gift_card'); ?>';
	lang['card_no'] = '<?= lang('card_no'); ?>';
	lang['value'] = '<?= lang('value'); ?>';
	lang['balance'] = '<?= lang('balance'); ?>';
	lang['unexpected_value'] = '<?= lang('unexpected_value'); ?>';
	lang['inclusive'] = '<?= lang('inclusive'); ?>';
	lang['exclusive'] = '<?= lang('exclusive'); ?>';
	lang['exonerated'] = '<?= lang('exonerated'); ?>';
	lang['total'] = '<?= lang('total'); ?>';
	lang['total_items'] = '<?= lang('total_items'); ?>';
	lang['order_tax'] = '<?= lang('order_tax'); ?>';
	lang['order_discount'] = '<?= lang('order_discount'); ?>';
	lang['total_payable'] = '<?= lang('total_payable'); ?>';
	lang['rounding'] = '<?= lang('rounding'); ?>';
	lang['grand_total'] = '<?= lang('grand_total'); ?>';
	lang['all'] = '<?= lang('all'); ?>';
	lang['aviso_tax_ICBPER'] = '<?= lang('aviso_tax_ICBPER'); ?>';


	$(document).ready(function() {
		posScreen();
		<?php if($this->session->userdata('rmspos')) { ?>
		if (get('spositems')) { remove('spositems'); }
		if (get('spos_discount')) { remove('spos_discount'); }
		// if (get('spos_tax')) { remove('spos_tax'); }
		if (get('spos_note')) {
			remove('spos_note');
			$('#spos_note').val("");
			$('#note').val("");
		}
		if (get('custom_field_1')) {
			remove('custom_field_1');
			$('#custom_field_1').val("");
			$('#custom_field_1_val').val("");
		}
		if (get('custom_field_2')) {
			remove('custom_field_2');
			$('#custom_field_2').val("");
			$('#custom_field_2_val').val("");
		}
		if (get('spos_customer')) { remove('spos_customer'); }
		if (get('amount')) { remove('amount'); }
		<?php $this->tec->unset_data('rmspos'); } ?>

		if(get('rmspos')) {
			if (get('spositems')) { remove('spositems'); }
			if (get('spos_discount')) { remove('spos_discount'); }
			// if (get('spos_tax')) { remove('spos_tax'); }
			if (get('spos_note')) { remove('spos_note'); }
			if (get('spos_customer')) { remove('spos_customer'); }
			if (get('amount')) { remove('amount'); }
			remove('rmspos');
		}
		<?php if($sid) { ?>

			store('spositems', JSON.stringify(<?=$items;?>));
			store('spos_discount', '<?=$suspend_sale->order_discount_id;?>');
			// store('spos_tax', '<?=$suspend_sale->order_tax_id;?>');
			store('canal_id', '<?=$suspend_sale->canal_id;?>');
			$('#canal_id').select2('val', '<?=$suspend_sale->canal_id;?>');
			store('spos_customer', '<?=$suspend_sale->customer_id;?>');
			$('#spos_customer').select2('val', '<?=$suspend_sale->customer_id;?>');
			store('rmspos', '1');
			$('#tax_val').val('<?=$suspend_sale->order_tax_id;?>');
			$('#discount_val').val('<?=$suspend_sale->order_discount_id;?>');
		<?php } elseif($eid) { ?>

			store('spositems', JSON.stringify(<?=$items;?>));
			store('spos_discount', '<?=$sale->order_discount_id;?>');
			// store('spos_tax', '<?=$sale->order_tax_id;?>');

			store('canal_id', '<?=$sale->canal_id;?>');
			$('#canal_id').select2('val', '<?=$sale->canal_id;?>');

			store('spos_customer', '<?=$sale->customer_id;?>');
			$('#spos_customer').select2('val', '<?=$sale->customer_id;?>');
			store('rmspos', '1');
			$('#tax_val').val('<?=$sale->order_tax_id;?>');
			$('#discount_val').val('<?=$sale->order_discount_id;?>');
		<?php } else { ?>
			if(! get('spos_discount')) {
				store('spos_discount', '<?=$Settings->default_discount;?>');
				$('#discount_val').val('<?=$Settings->default_discount;?>');
			}
			// if(! get('spos_tax')) {
			// 	store('spos_tax', '<?=$Settings->default_tax_rate;?>');
			// 	$('#tax_val').val('<?=$Settings->default_tax_rate;?>');
			// }
		<?php } ?>

		// if (ots = get('spos_tax')) {
		//     $('#tax_val').val(ots);
		// }
		if (ods = get('spos_discount')) {
		    $('#discount_val').val(ods);
		}
		if(Settings.display_kb == 1) { display_keyboards(); }
		nav_pointer();
		loadItems();
		read_card();
		bootbox.addLocale('bl',{OK:'<?= lang('ok'); ?>',CANCEL:'<?= lang('no'); ?>',CONFIRM:'<?= lang('yes'); ?>'});
		bootbox.setDefaults({closeButton:false,locale:"bl"});
		<?php if($eid) { ?>
			$('#suspend').attr('disabled', true);
			$('#print_order').attr('disabled', true);
			$('#print_bill').attr('disabled', true);
		<?php } ?>

		$("#custom_field_1").keyup(function() {  //tec_sales
			$.ajax({
			    url: base_url+'Pos/search_cmp',
			    type:'get',
			    data: {custom_field_1 : $("#custom_field_1").val()},
			    dataType: 'json',
			    success: function(response) {
			    	if(response.name!=""){
				      $("#custom_field_2").val(response.name); //tec_sales
					  $('#custom_field_2_val').val(response.name);
					  store('custom_field_2', response.name);
			    	}else{
				      $("#custom_field_2").val(""); //tec_sales
				      $('#custom_field_2_val').val("");
			    	}
			    },
			    error: function(xhr) {
			    //Do Something to handle error
			    }
			});
		});

	});
</script>

<!--*********************TRJ073 - KENY PONTE - 20/08/2019**************************************-->
<script>
function obtenerDatos2(){
	 var ruc=document.getElementById('cf2').value;
  if (ruc>9999999999) {
    $('#rucalert2').hide('fad');
      $.ajax({
        type:'get',
          url: 'https://cors-anywhere.herokuapp.com/http://api.ateneaperu.com/api/Sunat/Ruc',
          data:{'sNroDocumento':ruc},
        success: function(datos) {
        $('#cargando').hide('fade');
           if (datos.success===true) {
              $('#rucalert').hide('fade');
              document.getElementById('cname').value = datos.nombre_o_razon_social; 
              document.getElementById('cdireccion').value = datos.direccion_completa;            
           }else{
              $('#rucalert').show('fade');
            }
        },
        error: function() {
          console.log("no se encontro ningun registro");
        }, 
        beforeSend: function(){
        $('#cargando').show('fade');         
        },
    });
  }else{
    $('#rucalert2').show('fad');
  }
}

</script>
<!--script para consumir api en el modad edit cliente-->
<script>
function obtenerDatos(){

	 var ruc=document.getElementById('cf22').value;
  if (ruc>9999999999) {
    $('#rucalert2').hide('fad');
      $.ajax({
        type:'get',
          url: 'https://cors-anywhere.herokuapp.com/http://api.ateneaperu.com/api/Sunat/Ruc',
          data:{'sNroDocumento':ruc},
        success: function(datos) {
        $('#cargando1').hide('fade');
           if (datos.success===true) {
              $('#rucalert').hide('fade');
              document.getElementById('cname2').value = datos.nombre_o_razon_social; 
              document.getElementById('cdireccion2').value = datos.direccion_completa;            
           }else{
              $('#rucalert').show('fade');
            }
        },
        error: function() {
          console.log("no se encontro ningun registro");
        }, 
        beforeSend: function(){
        $('#cargando1').show('fade');         
        },
    });
  }else{
    $('#rucalert2').show('fad');
  }
}
</script>
<!--*********************TRJ073 - KENY PONTE - 20/08/2019**************************************-->

<!--******TRJ035 - ALEXANDER ROCA - 13/05/2019********-->
<script language="javascript">
$(document).ready(function() {

  $('form').keypress(function(e){   
    if(e == 13){
      return false;
    }
  });

  $('input').keypress(function(e){
    if(e.which == 13){
      return false;
    }
  });

/*<!--********** TRJ124***** -->
	<!-- AMADOR -->*/
  document.getElementById('customer_type').value=0;
  /*END*/

});
</script>

<!--******TRJ035 - ALEXANDER ROCA - 13/05/2019********-->
<!--*********************TRJ073 - KENY PONTE - 20/08/2019**************************************-->
<script type="text/javascript">
	$('.radio-group .radio').click(function(){
    $(this).parent().find('.radio').removeClass('selected');
    $(this).addClass('selected');
    var val = $(this).attr('data-value');
    //alert(val);
    $(this).parent().find('input').val(val);
});
	  document.getElementById('person1').checked=true;
function cambiarDatos(form,radio){
	var vis=(radio.checked) ? false : true;
	document.querySelector('#nombre-persona').innerText = 'Nombre';
	document.getElementById('document_type1').disabled=false;
	document.getElementById('cf1').readOnly=false;
    document.getElementById('person1').checked=true;
   
   /*<!--********** TRJ124***** -->
			<!-- AMADOR -->*/
	document.getElementById('persona_new_2').style.border="thick  solid lightblue";
  	document.getElementById('persona_new').style.border="thick  solid black";
  	document.getElementById('select2-document_type1-container').textContent='Seleccionar Tipo de Documento';

  	/*END*/
}


function cambiarDatos1(form,radio){

	document.querySelector('#nombre-persona').innerText = 'Razon Social';
	document.getElementById('document_type1').disabled=true;
	document.getElementById('cf1').readOnly=true;
	document.getElementById('cf1').value="";
	document.getElementById('person2').checked=true;
	document.getElementById('select2-document_type1-container').textContent='Seleccionar Tipo de Documento';

/*<!--********** TRJ124***** -->
				<!-- AMADOR -->
*/
	document.getElementById('persona_new').style.border="thick  solid lightblue";
  	document.getElementById('persona_new_2').style.border="thick  solid black";
  	/*END*/
	
}
</script>

<!--------------------Script par a el modal de editar------------->
<script type="text/javascript">
	$('.radio-group .radio').click(function(){
    $(this).parent().find('.radio').removeClass('selected');
    $(this).addClass('selected');
    var val = $(this).attr('data-value');
    //alert(val);
    $(this).parent().find('input').val(val);
});
function cambiarDatos2(form,radio){
	var vis=(radio.checked) ? false : true;
	document.querySelector('#nombre-persona1').innerText = 'Nombre';
	document.getElementById('document_type2').disabled=false;
	document.getElementById('cf12').readOnly=false;
    document.getElementById('person3').checked=true;
    document.getElementById('divpersona2').style.border="thick  solid lightblue";
  	document.getElementById('divpersona1').style.border="thick  solid black";
	
}
function cambiarDatos3(form,radio){

	document.querySelector('#nombre-persona1').innerText = 'Razon Social';
	document.getElementById('document_type2').disabled=true;
	document.getElementById('cf12').readOnly=true;
	document.getElementById('cf12').value="";
	document.getElementById('person4').checked=true;
	  document.getElementById('divpersona1').style.border="thick  solid lightblue";
  document.getElementById('divpersona2').style.border="thick  solid black";

	
}
</script>
<!--*********************TRJ073 - KENY PONTE - 20/08/2019**************************************-->
</body>
</html>
