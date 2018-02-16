/**
 * Created by artur on 22.02.17.
 */

var table;

$(document).ready(function (){
    $('input[id="daterange"]').daterangepicker(
        {
            locale: {
                format: 'YYYY-MM-DD'
            }
        }
    );

    var fromDate = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD'),
        toDate = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');

    table = $('#stats-table').DataTable({
        "ordering": false,
        'dom':'lrtip',
        "columnDefs": [
            { "visible": false, "targets": [0, 1]}
        ],
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": Routing.generate('statsData', { from: fromDate, to: toDate }),
        },
        "columns": [
            { "data": "domain" },
            { "data": "platformId" },
            /*{ "data": "name" },*/
            { "data": function ( row, type, val, meta ) {
                        return row.name + ' / ' + row.apiToken;
                    }
            },
            { "data": function ( row, type, val, meta ) {
                        return '$' + parseFloat(row.payout).toFixed(2);
                    }
            },
            { "data": "rewardsCount" },
        ],
        "drawCallback": function ( settings ) {
            var api = this.api();
            var rows = api.rows( {page:'current'} ).nodes();
            var last=null;

            api.column(0, {page:'current'} ).data().each( function ( group, i ) {
                if ( last !== group ) {
                    var platformId = api.column(1, {page:'current'} ).data()[i];

                    $(rows).eq( i ).before(
                        '<tr class="group"><td align="center"><b>' + group + '</b></td><td><b>$'+ parseFloat(getTotalByPlatform('Payout', platformId, fromDate, toDate)).toFixed(2) +'</b></td><td><b>'+ getTotalByPlatform('Reward', platformId, fromDate, toDate) +'</b></td></tr>'
                    );

                    last = group;
                }
            } );
            // Totals
            $( api.column( 3 ).footer() ).html(
                '$' + parseFloat(getTotalByPlatform('Payout', 0, fromDate, toDate)).toFixed(2)
            );

            $( api.column( 4 ).footer() ).html(
                getTotalByPlatform('Reward', 0, fromDate, toDate)
            );
        },
    } );
});

function getTotalByPlatform(attribute, id, fromDate, toDate) {
    var total = 'processing...';

    $.ajax({
            'type': 'get',
            'async': false,
            'url': Routing.generate('platform'+ attribute +'Total', { id: id, from: fromDate, to: toDate }),
            'dataType': 'json',
        }).success(function(json) {
            return total =  parseFloat(json);
        });

    return total;
}

$('#daterange').on('apply.daterangepicker', function(ev, picker) {
    filter();
});

function filter()
{
    var fromDate = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD'),
        toDate = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');

    window.location.href = Routing.generate('stats', { from: fromDate, to: toDate });
}
