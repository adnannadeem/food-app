/**
 *
 * You can write your JS code here, DO NOT touch the default style file
 * because it will make it harder for you to update.
 *
 */

"use strict";
load_data();

var today = new Date();

$('.datepicker').datepicker({
  todayBtn : 'linked',
  format : 'dd-mm-yyyy',
  autoclose : true,
  endDate: "today",
  maxDate: today
});

$('#get-search').on('click', function () {
    let user_id    = $('#user_id').val();
    let form_date = $('#form_date').val();
    let to_date   = $('#to_date').val();

    $('#maintable').DataTable().destroy();
    load_data(user_id, form_date, to_date);
});

$('#refresh').on('click', function () {
    $('#maintable').DataTable().destroy();
    load_data();
});

function load_data(user_id, form_date, to_date) {
    $('#maintable').DataTable({
        processing : true,
        serverSide : true,
        ajax : {
            url : $('#maintable').attr('data-url'),
            data : {user_id : user_id, form_date : form_date, to_date : to_date}
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'user', name: 'user' },
            { data: 'date', name: 'date' },
            { data: 'amount', name: 'amount' },
        ],
        "ordering" : false
    });
}