/**
 *
 * You can write your JS code here, DO NOT touch the default style file
 * because it will make it harder for you to update.
 *
 */

$(function() {
  "use strict";
    $('#maintable').DataTable({
        processing: true,
        serverSide: true,
        ajax: $('#maintable').attr('data-url'),
        columns: [
            { data: 'id', name: 'id' },
            { data: 'image', name: 'image' },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'phone', name: 'phone' },
            { data: 'action', name: 'action' },
        ],
        "ordering": false
    });
});

$('#maintable').on('draw.dt', function () {
    $('[data-toggle="tooltip"]').tooltip();
})