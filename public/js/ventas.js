const inputBuscarCodigo = document.querySelector('#buscarProductoCodigo');
const inputBuscarNombre = document.querySelector('#buscarProductoNombre');
const barcode = document.querySelector('#barcode');
const nombre = document.querySelector('#nombre');
const containerCodigo = document.querySelector('#containerCodigo');
const containerNombre = document.querySelector('#containerNombre');

const id_cli = document.getElementById('id_cli');
const pago_con = document.getElementById('pago_con');
const cambio = document.getElementById('cambio');
const metodo = document.getElementById('metodo');
const btnVaciar = document.getElementById('btnVaciar');

const errorBusqueda = document.querySelector('#errorBusqueda');
const ticketReporte = document.getElementById('ticketReporte');
const myModal = new bootstrap.Modal(document.getElementById('modalTicket'))

document.addEventListener('DOMContentLoaded', function () {
    cargarDetalle();
    //mostrar input para la busqueda por nombre
    nombre.addEventListener('click', function () {
        containerCodigo.classList.add('d-none');
        containerNombre.classList.remove('d-none');
        inputBuscarNombre.value = '';
        errorBusqueda.textContent = '';
        inputBuscarNombre.focus();
    })
    //mostrar input para la busqueda por codigo
    barcode.addEventListener('click', function () {
        containerNombre.classList.add('d-none');
        containerCodigo.classList.remove('d-none');
        inputBuscarCodigo.value = '';
        errorBusqueda.textContent = '';
        inputBuscarCodigo.focus();
    })

    inputBuscarCodigo.addEventListener('keyup', function (e) {
        if (e.keyCode === 13) {
            buscarProducto(e.target.value);
        }
        return;
    })
    //autocomplete productos
    $("#buscarProductoNombre").autocomplete({
        source: function (request, response) {
            $.ajax({
                url: base_url + '/ventas/buscarNombre',
                dataType: "json",
                data: {
                    term: request.term
                },
                success: function (data) {
                    response(data);
                    if (data.length > 0) {
                        errorBusqueda.textContent = '';
                    } else {
                        errorBusqueda.textContent = 'NO HAY PRODUCTO CON ESE NOMBRE';
                    }
                }
            });
        },
        minLength: 2,
        select: function (event, ui) {
            agregarProducto(ui.item.id);
            inputBuscarNombre.value = '';
            inputBuscarNombre.focus();
            return false;
        }
    });
    
    $("#nom_cli").autocomplete({
        minLength: 3,
        source: function (request, response) {
            $.ajax({
                url: base_url + '/ventas/buscar',
                dataType: "json",
                data: {
                    cli: request.term
                },
                success: function (data) {
                    response(data);
                }
            });
        },
        select: function (event, ui) {
            document.getElementById('id_cli').value = ui.item.id;
            document.getElementById('nom_cli').value = ui.item.nombre;
            document.getElementById('dir_cli').value = ui.item.direccion;
        }
    })

    btnVaciar.addEventListener('click', function (e) {
        const url = base_url + '/ventas/clearCart';
        //hacer una instancia del objeto XMLHttpRequest 
        const http = new XMLHttpRequest();
        //Abrir una Conexion - POST - GET
        http.open('GET', url, true);
        //Enviar Datos
        http.send();
        //verificar estados
        http.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                const res = JSON.parse(this.responseText);
                alertas(res.message, res.status);
                cargarDetalle();
            }
        }
    })

    pago_con.addEventListener('keyup', function (e) {
        let total = document.querySelector('#total');
        if (total.textContent > 0) {
            console.log(e.target.value);
            if (e.target.value != '') {
                let totalcambio = parseFloat(e.target.value) - parseFloat(total.textContent);
                cambio.value = totalcambio.toFixed(2);
            }
        } else {
            cambio.value = '0';
        }
    })

    document.addEventListener('keydown', function (event) {
        // Comprobar si la tecla presionada es la tecla F1
        if (event.key === 'F12') {
            // Prevenir el comportamiento por defecto de la tecla F1
            event.preventDefault();

            // Llamar a la función para guardar el registro
            procesarVenta();
        }
    });
});

function buscarProducto(valor) {
    const url = base_url + '/ventas/buscarPorCodigo/' + valor;
    //hacer una instancia del objeto XMLHttpRequest 
    const http = new XMLHttpRequest();
    //Abrir una Conexion - POST - GET
    http.open('GET', url, true);
    //Enviar Datos
    http.send();
    //verificar estados
    http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            errorBusqueda.textContent = '';
            if (res.estado) {
                agregarProducto(res.datos.id);
            } else {
                errorBusqueda.textContent = 'CODIGO NO EXISTE';
            }
            inputBuscarCodigo.value = '';
            inputBuscarCodigo.focus();
        }
    }
}

function agregarProducto(idProducto) {
    const url = base_url + "/ventas/addCarrito/" + idProducto;
    const http = new XMLHttpRequest();
    http.open("GET", url, true);
    http.send();
    http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            alertas(res.message, res.status);
            cargarDetalle();
        }
    }
}

function cargarDetalle() {
    let url = base_url + "/ventas/detalle";
    const http = new XMLHttpRequest();
    http.open("GET", url, true);
    http.send();
    http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            let html = '';
            if (res.productos != null) {
                res.productos.forEach(row => {
                    html += `<tr>
                   <td>${row['name']}</td>
                   <td width="150">
                   <input type="number" class="form-control" id="pro_${row['id']}" min="1" value="${row['quantity'] == 0 ? '' : row['quantity']}" onchange="cantidadVenta(event, ${row['id']})">
                   </td>
                   <td width="250">
                   <input type="number" class="form-control" min="0.01" step="0.01" value="${row['price']}" onchange="precioVenta(event, ${row['id']})"></td>
                   <td>${row['quantity'] * row['price']}</td>
                   <td>
                   <button class="btn btn-outline-danger" type="button" onclick="deleteProducto(${row['id']})">
                   <i class="fas fa-trash-alt"></i></button>
                   </td>
                   </tr>`;
                });
            } else {
                html = `<tr>
                <td colspan="5" class="text-center">CARRITO VACIO</td>
                </tr>`;
            }
            document.getElementById("total").textContent = res.total;
            document.getElementById("tblDetalleVenta").innerHTML = html;
        }
    }
}

function deleteProducto(id) {
    const url = base_url + '/ventas/deleteCarrito/' + id;
    const http = new XMLHttpRequest();
    http.open("GET", url, true);
    http.send();
    http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            alertas(res.message, res.status);
            cargarDetalle();
        }
    }
}

function cantidadVenta(e, idProducto) {
    let cantidad = e.target.value == '' ? 0 : e.target.value;
    const url = base_url + "/ventas/cantidadVenta/" + idProducto + '/' + cantidad;
    const http = new XMLHttpRequest();
    http.open("GET", url, true);
    http.send();
    http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            if (res.status != 'success') {
                alertas(res.message, res.status);
            }
            cargarDetalle();
            if (barcode.checked) {
                $('#buscarProductoCodigo').focus();
            } else {
                $('#buscarProductoNombre').focus();
            }
        }
    }
}

function precioVenta(e, idProducto) {
    const url = base_url + "/ventas/precioVenta/" + idProducto + '/' + e.target.value;
    const http = new XMLHttpRequest();
    http.open("GET", url, true);
    http.send();
    http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            if (res.status != 'success') {
                alertas(res.message, res.status);
            }
            cargarDetalle();
        }
    }
}

function generar() {
    Swal.fire({
        title: 'Esta seguro de procesar?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si!',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.isConfirmed) {
            procesarVenta();
        }
    })
}

function procesarVenta() {
    const fila = document.querySelectorAll("#table tr").length;
    if (fila < 2) {
        alertas('No hay productos en la tabla', 'warning');
    } else if (pago_con.value == '') {
        alertas('Ingresa el pago', 'warning');
    } else {
        let data = new FormData();
        data.append('id_cliente', id_cli.value);
        data.append('pago_con', pago_con.value);
        data.append('metodo', metodo.value);
        url = base_url + "/ventas/generar";
        const http = new XMLHttpRequest();
        http.open("POST", url, true);
        http.send(data);
        http.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                const res = JSON.parse(this.responseText);
                alertas(res.msg, res.icono);
                if (res.estado) {
                    setTimeout(() => {
                        cargarDetalle();
                        id_cli.value = '';
                        document.getElementById('nom_cli').value = '';
                        pago_con.value = '';
                        cambio.value = '';
                        ticketReporte.src = base_url + '/ventas/generarTicket/' + res.id;
                        myModal.show();
                    }, 1500);
                }
            }
        }
    }
}