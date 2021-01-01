$(document).ready(function(){
    $("#datatable").DataTable(),
    $("#datatable-buttons").DataTable({
        fixedHeader: true,
        searching: true,
        paging: true,
        ordering: true,
        info: true,
        scrollX: true,
        scrollZ: true,
        pageLength: 10,
        scrollY: 400,
        lengthChange:!1,buttons:["excel","pdf"]
            }).buttons().container().appendTo("#datatable-buttons_wrapper .col-md-6:eq(0)")
        });