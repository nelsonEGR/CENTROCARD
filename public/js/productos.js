document.addEventListener("DOMContentLoaded", function () {
    //productos
    t_pro = $('#tblpro').DataTable({
        "aPreocesing": true,
        "aServerSide": true,
        "ajax": {
            "url": "" + base_url + "/productos/listar",
            "dataSrc": ""
        },
        "columns": [
            {
                "data": "imagen"
            },
            {
                "data": "codigo"
            },
            {
                "data": "descripcion"
            },
            {
                "data": "precio_compra"
            },
            {
                "data": "precio_venta"
            },
            {
                "data": "stock"
            },
            {
                "data": "marca"
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
    t_proD = $('#tpro_D').DataTable({
        "aPreocesing": true,
        "aServerSide": true,
        "ajax": {
            "url": "" + base_url + "/productos/vaciar",
            "dataSrc": ""
        },
        "columns": [{
            "data": "id"
        },
        {
            "data": "imagen"
        },
        {
            "data": "codigo"
        },
        {
            "data": "descripcion"
        },
        {
            "data": "precio_compra"
        },
        {
            "data": "precio_venta"
        },
        {
            "data": "stock"
        },
        {
            "data": "marca"
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
//productos
function btnEliminarPro(id) {
    Swal.fire({
        title: 'Esta seguro de eliminar?',
        text: "El producto no se eliminará de forma permanente, solo cambiará el estado a inactivo!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si!',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.isConfirmed) {
            const url = base_url + "/productos/eliminar/" + id;
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
                    t_pro.ajax.reload();
                }
            }

        }
    })
}

function ingresar_pro(id) {
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
            const url = base_url + "/productos/restaurar/" + id;
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
                    t_proD.ajax.reload();
                }
            }

        }
    })
}