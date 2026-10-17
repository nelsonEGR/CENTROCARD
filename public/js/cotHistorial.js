let tblCotizaciones;
const ticketReporte = document.getElementById('ticketReporte');
const myModal = new bootstrap.Modal(document.getElementById('modalTicket'))
document.addEventListener('DOMContentLoaded', function () {
    tblCotizaciones = $('#tblCotizaciones').DataTable({
        "aPreocesing": true,
        "aServerSide": true,
        "ajax": {
            "url": "" + base_url + "/cotizaciones/listar",
            "dataSrc": ""
        },
        "columns": [{
            "data": "id"
        },
        {
            "data": "total"
        },
        {
            "data": "fecha"
        },
        {
            "data": "nombre"
        },
        {
            "data": "acciones"
        }
        ],
        "resonsieve": true,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [
            [0, "desc"]
        ],
        dom,
        buttons,
        language : {
            "url": base_url + '/assets/js/espanol.json'
        }
    });
})

function verTicket(id) {
    let ruta = base_url + '/cotizaciones/generarTicket/' + id;
    ticketReporte.src = ruta;
    myModal.show();
}