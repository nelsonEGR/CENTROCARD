const desde = document.getElementById('desde');
const hasta = document.getElementById('hasta');
const ticketReporte = document.getElementById('ticketReporte');
const myModal = new bootstrap.Modal(document.getElementById('modalTicket'))

document.addEventListener("DOMContentLoaded", function () {
    // Inicialización de DataTable
    t_h_v = $('#t_ventas').DataTable({
        "aProcessing": true,
        "ajax": {
            "url": base_url + '/ventas/listar',
            "data": function (d) {
                // Agregar el rango de fechas como parámetros
                d.fecha_inicio = desde.value;
                d.fecha_fin = hasta.value;
            },
            "dataSrc": ""
        },
        "columns": [
            { "data": "id" },
            { "data": "total" },
            { "data": "fecha" },
            { "data": "cliente_nombre" },
            { "data": "metodo" },
            { "data": "estado" },
            { "data": "acciones" }
        ],
        "resonsieve": true,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0, "desc"]],
        dom,
        buttons,
        language: {
            "url": base_url + '/assets/js/espanol.json'
        },
        "footerCallback": function (row, data, start, end, display) {
            let api = this.api();

            // Calcular la suma de los valores visibles en la columna "total"
            let total = api
                .rows({ search: 'applied' }) // Filas filtradas
                .data()
                .reduce((sum, item) => sum + parseFloat(item.total) || 0, 0);

            // Actualizar el contenido del pie de tabla (tfoot)
            $(api.column(1).footer()).html('Total: ' + total.toFixed(2));
        }
    });


    desde.addEventListener('change', function (e) {
        e.preventDefault();
        t_h_v.ajax.reload();
    })

    hasta.addEventListener('change', function (e) {
        e.preventDefault();
        t_h_v.ajax.reload();
    })
})

function verTicket(id) {
    let ruta = base_url + '/ventas/generarTicket/' + id;
    ticketReporte.src = ruta;
    myModal.show();
}