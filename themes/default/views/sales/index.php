<script>
	$(document).ready(function () {

        function method(n) {
            return (n == "PEN") ? '<center><span class="label label-primary">PEN</span></center>' : '<center><span class="label label-success">USD</span></center>';
        }

		function dibujarDt(idEstado){
			$('#SLData').dataTable({
//****************TRJ006 - ALEXANDER ROCA - 03/05/2019*****************
				/*dom: '<"row"<"form-inline" <"col-md-1" B>>>lfrtip',
			buttons: [
				//'excelHtml5'
				{
					extend: 'excelHtml5',
					text: 'Exportar en Excel',//'<span class = "glyphicon glyphicon-download-alt"></span>',//'<i class="fa fa-file-excel-o"></i>',
					titleAttr: 'Exporta lo visualizado',
					className: 'btnDataTableExcel'
				}
			],
			language: {
			"decimal": "",
			"emptyTable": "No hay información",
			"info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
			"infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
			"infoFiltered": "(Filtrado de _MAX_ total entradas)",
			"infoPostFix": "",
			"thousands": ",",
			"lengthMenu": "Mostrar _MENU_ Entradas",
			"loadingRecords": "Cargando...",
			"processing": "Procesando...",
			"search": "Buscar:",
			"zeroRecords": "Sin resultados encontrados",
			"paginate": {
				"first": "Primero",
				"last": "Ultimo",
				"next": "Siguiente",
				"previous": "Anterior"
			}
		},*/
//****************TRJ006 - ALEXANDER ROCA - 03/05/2019*****************
				"aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, '<?= lang('all'); ?>']],
	            "aaSorting": [[ 0, "desc" ]], "iDisplayLength": <?= $Settings->rows_per_page ?>,
				'bProcessing': true, 'bServerSide': true, "bDestroy": true,
	            'sAjaxSource': $("#vurl").val() + '/' + idEstado ,
	            'fnServerData': function (sSource, aoData, fnCallback) {
	                aoData.push({
	                    "name": "<?= $this->security->get_csrf_token_name() ?>",
	                    "value": "<?= $this->security->get_csrf_hash() ?>"
	                });
	                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
	                console.log($("#vurl").val() + '/' + idEstado)
	            },
	            "aoColumns": 
	            [{"mRender":hrld, "bSearchable": false},
	             null, 
	             {"mRender":method},
	              {"mRender":currencyFormat}, 
	              {"mRender":currencyFormat}, 
	              null, null,null,
                    {"mRender":function(data, type, row){
                    	if(data==1){
                    		return '<td><strong style="color:#13AD78">Activa</strong></td>';
                    	}else{
                    		var anulada =  '<td><strong style="color:#9E2020;">Anulada</strong></td>';
                    		return anulada;
                    	}
                    	
                    	 

                    }}, 

	              null, null, null, 
	              {"bSortable":false, "bSearchable": false}]
			});
			// Se quitó total, total_tax, total_discount | Diego
			// {"mRender":currencyFormat}, {"mRender":currencyFormat}, {"mRender":currencyFormat}
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

		$("#btnSearchf").click(
		            function(){
		                
		            }
		        );

        $(document).on('click', '#btnSearchf', function(event) {
        	event.preventDefault();
             var statusF=document.getElementById('estadof').value;
           
             	 dibujarDt(statusF);
             	 console.log(document.getElementById('estadof'))
             

        });

	});

</script>

	
<input type="hidden" id="vurl" value="<?= site_url('sales/get_sales/') ?>" >
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

                            <div class="col-md-3">
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <br>
                                    <button type="button" id="btnSearch" class="btn btn-success"><?= lang("search2")?></button>
                                    <button type="button" id="btnAll" class="btn btn-primary"><?= lang("see"). " " . lang("all");?></button>
                                </div>
                            </div>

							<!-- estado de factura -->
                             <div class="col-md-3">
                                <div class="form-group">
                                    <?= lang('status', 'status'); ?>

                                        <?php
                                        $see[''];
                                        $see[lang('1')] = lang('active');
                                        $see[lang('3')] = lang('inactive');
                                        $see[lang('2')] = lang('all');

                                        ?>
                                        <?= form_dropdown('estadof', $see, "", 'class="form-control select2 estado" id="estadof"  required="required" style="width:100%;"'); ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <br>
                                    <button type="button" id="btnSearchf" class="btn btn-success"><?= lang("search2")?></button>
                                  
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
									<th><?php echo $this->lang->line("customer"); ?></th>
									<th style="text-align:center; width: 70px;"><?php echo $this->lang->line("currency_name"); ?></th>
									<!-- Se quitó total, total_tax, total_discount | Diego -->
									<!-- <th class="col-xs-1"><?php echo $this->lang->line("total"); ?></th> -->
									<!-- <th class="col-xs-1"><?php echo $this->lang->line("tax"); ?></th> -->
									<!-- <th class="col-xs-1"><?php echo $this->lang->line("discount"); ?></th> -->
									<th class="col-xs-1"><?php echo $this->lang->line("total_sale"); ?></th>
                                    <th class="col-xs-1"><?php echo $this->lang->line("paid"); ?></th>
									<th class="col-xs-1"><?php echo $this->lang->line("status"); ?></th>

									<th class="col-xs-1"><?php echo $this->lang->line("proof_type"); ?></th>
									<th class="col-xs-1">N° <?php echo $this->lang->line("proof"); ?></th>
									<th class="col-xs-1"><?php echo $this->lang->line("invoice_status"); ?></th>
									<th class="col-xs-1"><?php echo $this->lang->line("canal")." ". $this->lang->line("bill"); ?></th>
									<th class="col-xs-1"><?php echo $this->lang->line("send_status"); ?></th>

									<th class="col-xs-1"><?php echo $this->lang->line("local"); ?></th>


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
				filename: "Sistema TPV - Reporte de Ventas",
			});
		}
	//****************TRJ006 - ALEXANDER ROCA - 10/05/2019*****************
</script>
</section>
