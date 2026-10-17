let tblCreditos;
const id_credito = document.querySelector('#id_credito');
const monto = document.querySelector('#monto');
const btnAbonar = document.querySelector('#btnAbonar');
const myModal = new bootstrap.Modal(document.getElementById('modalAbono'))
const ticketReporte = document.getElementById('ticketReporte');
const modalTicket = new bootstrap.Modal(document.getElementById('modalTicket'))
document.addEventListener('DOMContentLoaded', function () {
    tblCreditos = $('#tblCreditos').DataTable({
        "aPreocesing": true,
        "aServerSide": true,
        "ajax": {
            "url": "" + base_url + "/credventas/listar",
            "dataSrc": ""
        },
        "columns": [{
            "data": "id"
        },
        {
            "data": "fecha"
        },
        {
            "data": "nombre"
        },
        {
            "data": "monto"
        },
        {
            "data": "abonado"
        },
        {
            "data": "restante"
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

    btnAbonar.addEventListener('click', function () {
        if (id_credito.value == '' || monto.value == '') {
            alertas('El monto es requerido', 'warning');
        } else {
            const url = base_url + '/credventas/abonar';
            let data = new FormData();
            data.append('id_credito', id_credito.value);
            data.append('monto', monto.value);
            //hacer una instancia del objeto XMLHttpRequest 
            const http = new XMLHttpRequest();
            //Abrir una Conexion - POST - GET
            http.open('POST', url, true);
            //Enviar Datos
            http.send(data);
            //verificar estados
            http.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    const res = JSON.parse(this.responseText);
                    alertas(res.msg, res.icono);
                    if (res.icono == 'success') {
                        myModal.hide();
                        tblCreditos.ajax.reload();
                        setTimeout(() => {
                            ticketReporte.src = base_url + '/credventas/' + res.id + '/generarTicket';
                            modalTicket.show();
                        }, 1500);
                    }
                }
            }
        }
    })
})

function abonoCredito(idCredito) {
    id_credito.value = idCredito;
    myModal.show();
}

function verTicket(id) {
    ticketReporte.src = base_url + '/credventas/' + id + '/generarTicket';
    modalTicket.show();
}