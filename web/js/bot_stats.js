/**
 * Created by artur on 22.02.17.
 */

var table;

$(document).ready(function (){

    table = $('#bot-stats-table').DataTable({
        "ordering": true,
        'dom':'ftr',

        "drawCallback": function () {
            var api = this.api();

            $( api.table().footer() ).html(
                "<tr>" +
                    "<th>Total:</th>" +
                    "<th>" + api.column( 1, {page:'current'} ).data().sum() + "</th>" +
                    "<th>" + api.column( 2, {page:'current'} ).data().sum() + "</th>" +
                    "<th>" + api.column( 3, {page:'current'} ).data().sum() + "</th>" +
                "</tr>"
            );
        }
    });
});
