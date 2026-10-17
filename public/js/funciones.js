let t_user, t_userD, t_caja, t_cajaD, t_cli, t_cliD,
    t_med, t_medD, t_cat, t_catD, t_marca, t_marcaD, t_pro,
    t_proD, neditor, t_h_c, t_h_v;
let myChart;

function alertas(msg, icono) {
    notie.alert({
        type: icono,
        text: msg.toUpperCase(),
        time: 4,
        position: 'top'
    })
}
function Anular(id, accion) {
    Swal.fire({
        title: 'Esta seguro de Anular?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si!',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.isConfirmed) {
            if (accion == 1) {
                url = base_url + "/compras/anular/" + id;
            } else {
                url = base_url + "/ventas/anular/" + id;
            }
            const http = new XMLHttpRequest();
            http.open("GET", url, true);
            http.send();
            http.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    const res = JSON.parse(this.responseText);
                    alertas(res.msg, res.icono);
                    if (accion == 1) {
                        t_h_c.ajax.reload();
                    } else {
                        t_h_v.ajax.reload();
                    }
                }
            }

        }
    })
}
if (document.getElementById('comparacion')) {
    comparacion();
    stockMinimo();
}
function calcularPrecioVenta() {
    let ganancia = parseFloat(ganaciaPorcentaje);
    // Obtener el valor del precio de compra
    let precioCompra = parseFloat(document.getElementById('precio_compra').value);

    // Validar que el precio de compra sea un número
    if (!isNaN(precioCompra) && precioCompra > 0) {
        // Calcular el 25% adicional
        let precioVenta = precioCompra + (precioCompra * ganancia / 100);

        // Establecer el precio de venta
        document.getElementById('precio_venta').value = precioVenta.toFixed(2);
    } else {
        // Si el precio de compra no es válido, limpiar el campo de precio de venta
        document.getElementById('precio_venta').value = '';
    }
}

function comparacion() {
    if (myChart) {
        myChart.destroy();
    }
    const anio = document.getElementById("year").value;
    const http = new XMLHttpRequest();
    const url = base_url + '/admin/comparacion/' + anio;
    http.open("GET", url, true);
    http.send();
    http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            var res = JSON.parse(this.responseText);
            var ctx = document.getElementById('comparacion').getContext('2d');

            var gradientStroke1 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke1.addColorStop(0, '#17ead9');
            gradientStroke1.addColorStop(1, '#6078ea');

            var gradientStroke2 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke2.addColorStop(0, '#FCFF33');
            gradientStroke2.addColorStop(1, '#C7FF33');

            myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ["Ene", "Feb", "Mar", "Abr", "May", "Jun",
                        "Jul", "Ago", "Sep", "Oct", "Nov", "Dic",
                    ],
                    datasets: [{
                        label: 'Ventas',
                        data: [res.venta.ene, res.venta.feb, res.venta.mar,
                        res.venta.abr, res.venta.may, res.venta.jun,
                        res.venta.jul, res.venta.ago, res.venta.sep,
                        res.venta.oct, res.venta.nov, res.venta.dic,
                        ],
                        pointBorderWidth: 2,
                        pointBackgroundColor: 'transparent',
                        pointHoverBackgroundColor: gradientStroke1,
                        //backgroundColor: gradientStroke1,
                        borderColor: gradientStroke1,
                        borderWidth: 2
                    }, {
                        label: 'Compras',
                        data: [res.compra.ene, res.compra.feb, res.compra.mar,
                        res.compra.abr, res.compra.may, res.compra.jun,
                        res.compra.jul, res.compra.ago, res.compra.sep,
                        res.compra.oct, res.compra.nov, res.compra.dic,
                        ],
                        pointBorderWidth: 2,
                        pointBackgroundColor: 'transparent',
                        pointHoverBackgroundColor: gradientStroke2,
                        //backgroundColor: gradientStroke2,
                        borderColor: gradientStroke2,
                        borderWidth: 2
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    legend: {
                        display: true,
                        labels: {
                            boxWidth: 40
                        }
                    },
                    tooltips: {
                        displayColors: false
                    }

                }
            });
        }
    }
}

function stockMinimo() {
    const url = base_url + '/admin/stockMinimo';
    const http = new XMLHttpRequest();
    http.open("GET", url, true);
    http.send();
    http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            let nombre = [];
            let cantidad = [];
            for (let i = 0; i < res.length; i++) {
                nombre.push(res[i].descripcion);
                cantidad.push(res[i].stock);
            }
            var ctx = document.getElementById("minimo").getContext('2d');

            var gradientStroke8 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke8.addColorStop(0, '#42e695');
            gradientStroke8.addColorStop(1, '#3bb2b8');

            var gradientStroke9 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke9.addColorStop(0, '#4776e6');
            gradientStroke9.addColorStop(1, '#8e54e9');


            var gradientStroke10 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke10.addColorStop(0, '#ee0979');
            gradientStroke10.addColorStop(1, '#ff6a00');

            var myChart = new Chart(ctx, {
                type: 'polarArea',
                data: {
                    labels: nombre,
                    datasets: [{
                        backgroundColor: [
                            gradientStroke8,
                            gradientStroke9,
                            gradientStroke10
                        ],

                        hoverBackgroundColor: [
                            gradientStroke8,
                            gradientStroke9,
                            gradientStroke10
                        ],
                        data: cantidad
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    legend: {
                        display: true
                    },
                    tooltips: {
                        displayColors: false
                    }
                }
            });
        }
    };
}