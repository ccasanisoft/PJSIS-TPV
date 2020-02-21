<script>
	$(document).ready(function () {

		function dibujarDt(idEstado){
			$('#SLData').dataTable({

				"aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, '<?= lang('all'); ?>']],
	            "aaSorting": [[ 0, "desc" ]], "iDisplayLength": <?= $Settings->rows_per_page ?>,
				'bProcessing': true, 'bServerSide': true, "bDestroy": true,
	            'sAjaxSource': $("#vurl").val(),
	            'fnServerData': function (sSource, aoData, fnCallback) {
	                aoData.push({
	                    "name": "<?= $this->security->get_csrf_token_name() ?>",
	                    "value": "<?= $this->security->get_csrf_hash() ?>"
	                });
	                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
	            },
	            "aoColumns": [{"mRender":hrld, "bSearchable": false}, null, null, null, null, null, null, null, null]
			});

		}
		dibujarDt("");

        $("#btnAll").click(
            function(){
                $("#estado").val("");
                dibujarDt("");
            }
        );
        $("#btnSearch").click(
            function(){
                if( $("#estado").val()=="" ){
                    alert("<?= lang('select'); ?> <?= lang('status'); ?>");
                    return false;
                }
                dibujarDt($("#estado").val());
            }
        );

	});
</script>
<input type="hidden" id="vurl" value="<?= site_url('Referral_guide/get_Referral_guide/') ?>" >
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title"><?= lang('search_by'); ?></h3>
                </div>
                <div class="box-body">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang('status', 'status'); ?>

                                        <?php
                                        $sw[''] = lang("select")." ".lang("status");
                                        $sw[lang('st_due')] = lang('st_due');
                                        $sw[lang('st_partial')] = lang('st_partial');
                                        $sw[lang('st_paid')] = lang('st_paid');

                                        ?>
                                        <?= form_dropdown('estado', $sw, "", 'class="form-control select2 estado" id="estado"  required="required" style="width:100%;"'); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <br>
                                    <button type="button" id="btnSearch" class="btn btn-success"><?= lang("search2")?></button>
                                    <button type="button" id="btnAll" class="btn btn-primary"><?= lang("see"). " " . lang("all");?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-header">
					<!--****************TRJ006 - ALEXANDER ROCA - 03/05/2019*****************-->
					<!--<h3 class="box-title"><?= lang('list_results'); ?></h3>-->
					<!--****************TRJ006 - ALEXANDER ROCA - 03/05/2019*****************-->
					<div class="btn btn-success col-md-offset-10" onclick="exportTableToExcel()">Exportar Excel</div>
				</div>
				<div class="box-body">
					<div class="table-responsive">
						<table id="SLData" class="table table-striped table-bordered table-condensed table-hover">
							<thead>
								<tr class="active">
									<th style="width: 80px;"><?php echo $this->lang->line("date"); ?></th>
									<th><?php echo $this->lang->line("receiver"); ?></th>
									<th style="text-align:center; width: 70px;"><?php echo $this->lang->line("type_transport"); ?></th>
									<th style="text-align:center; width: 70px;">N° <?php echo $this->lang->line("Carrier_Document"); ?></th>
									<th style="text-align:center; width: 70px;">N° <?php echo $this->lang->line("proof").' de '.$this->lang->line("referral_guide"); ?></th>
									<th style="text-align:center; width: 70px;">N° <?php echo $this->lang->line("license_plate"); ?></th>
									<th style="text-align:center; width: 70px;"><?php echo $this->lang->line("motive").' de '.$this->lang->line("referral_guide"); ?></th>
									<th style="text-align:center; width: 70px;"><?php echo $this->lang->line("send_status"); ?></th>
									<th style="text-align:center; width: 100px;"><?php echo $this->lang->line("actions"); ?></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td colspan="9" class="dataTables_empty"><?= lang('loading_data_from_server'); ?></td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
	</div>
	
<script>
    //****************TRJ006 - ALEXANDER ROCA - 10/05/2019*****************
		function exportTableToExcel(){
			$(".table").table2excel({
				//exclude: "#tableID",
				name: "Excel Document Name",
				filename: "Sistema TPV - Reporte de Notas de Crédito",
			});
		}
	//****************TRJ006 - ALEXANDER ROCA - 10/05/2019*****************
</script>
<script type="text/javascript">
					function sendEmail(){
						var email = prompt("<?= lang("email_address"); ?>", "<?= $customer->email; ?>");
						$('body').on('click', 'a', function(){
						
						if (email != null) {
							var id = $(this).attr('id');
                            $.ajax({
                                type: "post",
                                url: "<?= site_url('Referral_guide/email_receipt') ?>",
                                data: {<?= $this->security->get_csrf_token_name(); ?>: "<?= $this->security->get_csrf_hash(); ?>", email: email, id: id},
								dataType: "json",
                                success: function (data) {
                                    alert(data.msg);
									//return true;
									location.reload();
                                },
                                error: function () {
                                    alert('<?= lang('ajax_request_failed'); ?>');
                                    return false;
                                }
                            });
							email = null;
                        }
						
                        return false;
						});
					}
					
                //});
            </script>
</section>
