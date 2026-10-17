let tblRoles;
document.addEventListener("DOMContentLoaded", function () {
    //roles
    tblRoles = $('#tblRoles').DataTable({
        "aPreocesing": true,
        "aServerSide": true,
        "ajax": {
            "url": "" + base_url + "/roles/listar",
            "dataSrc": ""
        },
        "columns": [{
            "data": "id"
        },
        {
            "data": "nombre"
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
});

function eliminarRol(id) {
    Swal.fire({
        title: 'Esta seguro de eliminar?',
        text: "El rol no se eliminará de forma permanente, solo cambiará el estado a inactivo!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si!',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.isConfirmed) {
            const url = base_url + "/roles/eliminar/" + id;
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
                    tblRoles.ajax.reload();
                }
            }

        }
    })
}