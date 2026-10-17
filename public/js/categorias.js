document.addEventListener("DOMContentLoaded", function () {
    //categorias
    t_cat = $('#tblcat').DataTable({
        "aPreocesing": true,
        "aServerSide": true,
        "ajax": {
            "url": "" + base_url + "/categorias/listar",
            "dataSrc": ""
        },
        "columns": [{
            "data": "idcat"
        },
        {
            "data": "categoria"
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
    t_catD = $('#tblcat_r').DataTable({
        "aPreocesing": true,
        "aServerSide": true,
        "ajax": {
            "url": "" + base_url + "/categorias/vaciar",
            "dataSrc": ""
        },
        "columns": [{
            "data": "idcat"
        },
        {
            "data": "categoria"
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


//categorias
function btnEliminarCat(id) {
    Swal.fire({
        title: 'Esta seguro de eliminar?',
        text: "La categoria no se eliminará de forma permanente, solo cambiará el estado a inactivo!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si!',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.isConfirmed) {
            const url = base_url + "/categorias/eliminar/" + id;
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
                    t_cat.ajax.reload();
                }
            }

        }
    })
}

function btnreingresar_cat(id) {
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
            const url = base_url + "/categorias/restaurar/" + id;
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
                    t_catD.ajax.reload();
                }
            }

        }
    })
}