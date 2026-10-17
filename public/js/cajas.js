document.addEventListener("DOMContentLoaded", function () {
    $('#t_cierre').DataTable({
        dom,
        buttons,
        language: {
            "url": base_url + '/assets/js/espanol.json'
        },
        "order": [
            [0, "desc"]
        ],
    });
    //cajas
    t_caja = $('#tblcaja').DataTable({
        "aPreocesing": true,
        "aServerSide": true,
        "ajax": {
            "url": "" + base_url + "/cajas/listar",
            "dataSrc": ""
        },
        "columns": [{
            "data": "id"
        },
        {
            "data": "caja"
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
        language: {
            "url": base_url + '/assets/js/espanol.json'
        }
    });
    t_cajaD = $('#tblcaja_r').DataTable({
        "aPreocesing": true,
        "aServerSide": true,
        "ajax": {
            "url": "" + base_url + "/cajas/vaciar",
            "dataSrc": ""
        },
        "columns": [{
            "data": "id"
        },
        {
            "data": "caja"
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
        language: {
            "url": base_url + '/assets/js/espanol.json'
        }
    });
})
//cajas
function btnEliminarCaja(id) {
    Swal.fire({
        title: 'Esta seguro de eliminar?',
        text: "La caja no se eliminará de forma permanente, solo cambiará el estado a inactivo!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si!',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.isConfirmed) {
            const url = base_url + "/cajas/eliminar/" + id;
            const http = new XMLHttpRequest();
            http.open("GET", url, true);
            http.send();
            http.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    const res = JSON.parse(this.responseText);
                    Swal.fire(
                        'Mensaje',
                        res.mensaje,
                        res.icono
                    )
                    t_caja.ajax.reload();
                }
            }

        }
    })
}
function btnreingresar_caja(id) {
    Swal.fire({
        title: 'Esta seguro de reingresar?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si!',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.isConfirmed) {
            const url = base_url + "/cajas/restaurar/" + id;
            const http = new XMLHttpRequest();
            http.open("GET", url, true);
            http.send();
            http.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    const res = JSON.parse(this.responseText);
                    Swal.fire(
                        'Mensaje',
                        res.mensaje,
                        res.icono
                    )
                    t_cajaD.ajax.reload();
                }
            }

        }
    })
}