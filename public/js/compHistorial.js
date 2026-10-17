const ticketReporte = document.getElementById('ticketReporte');
const myModal = new bootstrap.Modal(document.getElementById('modalTicket'))
document.addEventListener("DOMContentLoaded", function () {  
    //historial compras
    t_h_c = $('#t_compras').DataTable({
        "aPreocesing": true,
        "aServerSide": true,
        "ajax": {
            "url": base_url + '/compras/listar',
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
            "data": "estado"
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
    let ruta = base_url + '/compras/generarTicket/' + id;
    ticketReporte.src = ruta;
    myModal.show();
}