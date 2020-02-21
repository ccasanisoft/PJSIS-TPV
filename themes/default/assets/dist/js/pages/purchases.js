    $(document).ready(function() {

        if($("#fflag").val()==0){
            loadItems(true);
        } else{
            if (get('spoitems')) {
                remove('spoitems');
            }
        }

        $("#date").inputmask("yyyy-mm-dd hh:mm", {"placeholder": "yyyy-mm-dd hh:mm"});
        $("#expiration_date").inputmask("yyyy-mm-dd", {"placeholder": "yyyy-mm-dd"});

        $("#add_item").autocomplete({
            source: base_url+'purchases/suggestions',
            minLength: 1,
            autoFocus: false,
            delay: 200,
            response: function (event, ui) {
                if ($(this).val().length >= 16 && ui.content[0].id == 0) {
                    bootbox.alert(lang.no_match_found, function () {
                        $('#add_item').focus();
                    });
                    $(this).val('');
                }
                else if (ui.content.length == 1 && ui.content[0].id != 0) {
                    ui.item = ui.content[0];
                    $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                    $(this).autocomplete('close');
                }
                else if (ui.content.length == 1 && ui.content[0].id == 0) {
                    bootbox.alert(lang.no_match_found, function () {
                        $('#add_item').focus();
                    });
                    $(this).val('');
                }
            },
            select: function (event, ui) {
                event.preventDefault();
                if (ui.item.id !== 0) {
                    var row = add_order_item(ui.item);
                    if (row)
                        $(this).val('');
                } else {
                    bootbox.alert(lang.no_match_found);
                }
            }
        });

        $('#add_item').bind('keypress', function (e) {
            if (e.keyCode == 13) {
                e.preventDefault();
                $(this).autocomplete("search");
            }
        });

        $('#add_item').focus();
        $('#reset').click(function (e) {
            bootbox.confirm(lang.r_u_sure, function (result) {
                if (result) {
                    if (get('spoitems')) {
                        remove('spoitems');
                    }

                    window.location.reload();
                }
            });
        });

        $(document).on("change", '.rquantity', function () {
            var row = $(this).closest('tr');
            var val = 0;
            if(!$(this).val() || !isNaN($(this).val())){
                val = $(this).val();
                if( val.trim() === ""){
                    val = 0;
                }
            }
            var new_qty = parseFloat(val);
            item_id = row.attr('data-item-id');
            spoitems[item_id].row.qty = new_qty;
            store('spoitems', JSON.stringify(spoitems));
            loadItems();
        });

        $(document).on("change", '.rcost', function () {
            var row = $(this).closest('tr');
            var val = 0.00;
            if(!$(this).val() || !isNaN($(this).val())){
                val = $(this).val();
                if( val.trim() === ""){
                    val = 0.00;
                }
            }
            var new_cost = parseFloat(val).toFixed(2);
            item_id = row.attr('data-item-id');
            spoitems[item_id].row.cost = new_cost;
            spoitems[item_id].row.edit = 1;
            store('spoitems', JSON.stringify(spoitems));
            loadItems();
        });

        $('#add_purchase').click(function (e) {
            if (!get('spoitems') || $("#poTable tbody").text() == "") {
                e.preventDefault();
                e.stopImmediatePropagation();
                displayOn('warning');
                document.getElementById("warning_text").innerHTML = lang.products_not_found;
                // $('html,body').animate({ scrollTop: 0 }, 'slow');
                document.body.scrollTop = document.documentElement.scrollTop = 0;
            } else if ($("#gimptotal").val() <= 0){
                e.preventDefault();
                e.stopImmediatePropagation();
                displayOn('warning');
                document.getElementById("warning_text").innerHTML = lang.sale_not_less;
                document.body.scrollTop = document.documentElement.scrollTop = 0;
            } else{
                displayOff('warning');
            }
        });

        $('#edit_purchase').click(function (e) {
            if (!get('spoitems') || $("#poTable tbody").text() == "") {
                e.preventDefault();
                e.stopImmediatePropagation();
                displayOn('warning');
                document.getElementById("warning_text").innerHTML = lang.products_not_found;
                // $('html,body').animate({ scrollTop: 0 }, 'slow');
                document.body.scrollTop = document.documentElement.scrollTop = 0;
            } else if ($("#gimptotal").val() <= 0){
                e.preventDefault();
                e.stopImmediatePropagation();
                displayOn('warning');
                document.getElementById("warning_text").innerHTML = lang.sale_not_less;
                document.body.scrollTop = document.documentElement.scrollTop = 0;
            } else{
                displayOff('warning');
            }
        });

        function displayOn(Element) {
            var x = document.getElementById(Element);
            if (x.style.display == 'none') {
                x.style.display = '';
            }
        }

        function displayOff(Element) {
            var x = document.getElementById(Element);
            if (x.style.display == '') {
                x.style.display = 'none';
            }
        }

    });

    function loadItems(edit = false) {

        if (get('spoitems')) {
            total = 0;
            affected = 0;
            tax = 0;
            exonerated = 0;

            $("#poTable tbody").empty();

            spoitems = JSON.parse(get('spoitems'));

            $.each(spoitems, function () {

                var item = this;

                var item_id = Settings.item_addition == 1 ? item.item_id : item.id;
                spoitems[item_id] = item;

                var product_id = item.row.id, item_currency = item.row.currency, item_cost = item.row.cost, item_qty = item.row.qty, item_code = item.row.code, item_tax_method = item.row.tax_method,
                item_name = item.row.name.replace(/"/g, "&#034;").replace(/'/g, "&#039;");

                if(!edit){
                    if(item.row.edit != 1){
                        if(item_cost > 0 && item_currency != $("#currency_code").val()){
                            if(item_currency == "PEN"){
                                item_cost = parseFloat(item_cost / $("#exchange").val()).toFixed(2);
                                spoitems[item_id].row.cost = item_cost;
                            }
                            if(item_currency == "USD"){
                                item_cost = parseFloat(item_cost * $("#exchange").val()).toFixed(2);
                                spoitems[item_id].row.cost = item_cost;
                            }
                        }
                    }
                }

                var item_afec = 0, item_tax = 0, item_afec = 0;

                if(item_tax_method == 0){ //Incluido
                    item_afec = parseFloat(item_cost) / (1 + (parseFloat(Settings.default_tax_rate) / 100));
                    item_tax = parseFloat(item_cost) - item_afec;
                    item_exon = 0;
                }else if(item_tax_method == 1){ //Excluido
                    if(!edit){
                        if(item.row.edit != 1){
                            item_cost =  parseFloat( parseFloat(item_cost) * (1 + (parseFloat(Settings.default_tax_rate) / 100)) ).toFixed(2);
                            spoitems[item_id].row.cost = item_cost;
                        }
                    }
                    item_afec = parseFloat(item_cost) / (1 + (parseFloat(Settings.default_tax_rate) / 100));
                    item_tax = parseFloat(item_cost) - item_afec;
                    item_exon = 0;
                }else if(item_tax_method == 2){ //Exonerado
                    item_exon = parseFloat(item_cost);
                    item_afec = 0;
                    item_tax = 0;
                }

                spoitems[item_id].row.edit = 1;
                store('spoitems', JSON.stringify(spoitems));

                var row_no = (new Date).getTime();
                var newTr = $('<tr id="' + row_no + '" class="' + item_id + '" data-item-id="' + item_id + '"></tr>');
                tr_html = '<td style="min-width:100px;"><input name="product_id[]" type="hidden" class="rid" value="' + product_id + '"><span class="sname" id="name_' + row_no + '">' + item_name + ' (' + item_code + ')</span></td>';
                tr_html += '<td style="padding:2px;"><input class="form-control input-sm kb-pad text-center rquantity" name="quantity[]" type="text" value="' + item_qty + '" data-id="' + row_no + '" data-item="' + item_id + '" id="quantity_' + row_no + '" onClick="this.select();"></td>';
                tr_html += '<td style="padding:2px; min-width:80px;"><input class="form-control input-sm kb-pad text-center rcost" name="cost[]" type="text" value="' + item_cost + '" data-id="' + row_no + '" data-item="' + item_id + '" id="cost_' + row_no + '" onClick="this.select();"></td>';
                tr_html += '<td class="text-right"><span class="text-right ssubtotal" id="subtotal_' + row_no + '">' + formatMoney(parseFloat(item_cost) * parseFloat(item_qty)) + '</span></td>';
                tr_html += '<td class="text-center"><i class="fa fa-trash-o tip pointer spodel" id="' + row_no + '" onclick="javascript:delete_order_item(' + item.item_id + ')" title="Remove"></i></td>';
                newTr.html(tr_html);
                newTr.prependTo("#poTable");

                total += (parseFloat(item_cost) * parseFloat(item_qty));

                affected += (parseFloat(item_afec) * parseFloat(item_qty));
                tax += (parseFloat(item_tax) * parseFloat(item_qty));
                exonerated += (parseFloat(item_exon) * parseFloat(item_qty));

            });

            importe_total = affected + exonerated + tax;

            grand_affected = formatMoney(affected);
            $("#gaffected").text(grand_affected);

            grand_tax = formatMoney(tax);
            $("#gtax").text(grand_tax);

            grand_exonerated = formatMoney(exonerated);
            $("#gexonerated").text(grand_exonerated);

            imp_total = formatMoney(importe_total);
            $("#gimptotal").text(imp_total);
            $("#gimptotal").val(importe_total);

            grand_total = formatMoney(total);
            $("#gtotal").text(grand_total);
            $('#add_item').focus();
        }
}

function add_order_item(item) {
    //console.log(item);
    var item_id = Settings.item_addition == 1 ? item.item_id : item.id;
    if (spoitems[item_id]) {
        spoitems[item_id].row.qty = parseFloat(spoitems[item_id].row.qty) + 1;
    } else {
        spoitems[item_id] = item;
    }
    store('spoitems', JSON.stringify(spoitems));
    loadItems();
    return true;
}

function delete_order_item(itemb) {
    delete spoitems[itemb];
    store('spoitems', JSON.stringify(spoitems));
    loadItems();
}
