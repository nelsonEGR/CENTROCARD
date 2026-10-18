const dom = "<'row align-items-center'<'col-sm-6'l><'col-sm-6 text-end'f>>" +
    "<'row dt-buttons-row'<'col-12 text-center'B>>" +
    "<'row mt-2'<'col-12'tr>>" +
    "<'row align-items-center mt-2'<'col-sm-5'i><'col-sm-7'p>>";
const buttons = [{
                //Botón para Excel
                extend: 'excelHtml5',
                footer: true, 
                //Aquí es donde generas el botón personalizado
                text: '<span class="badge bg-success"><i class="fas fa-file-excel"></i></span>'
            },
            //Botón para PDF
            {
                extend: 'pdfHtml5',
                download: 'open',
                footer: true,
                text: '<span class="badge bg-danger"><i class="fas fa-file-pdf"></i></span>',
                exportOptions: {
                    columns: [0, ':visible']
                },
                customize: function (doc) {
                    // Configurar el ancho total
                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                }
            },
            //Botón para copiar
            {
                extend: 'copyHtml5',
                footer: true,
                text: '<span class="badge bg-primary"><i class="fas fa-copy"></i></span>',
                exportOptions: {
                    columns: [0, ':visible']
                }
            },
            //Botón para print
            {
                extend: 'print',
                footer: true,
                text: '<span class="badge bg-dark"><i class="fas fa-print"></i></span>'
            },
            //Botón para cvs
            {
                extend: 'csvHtml5',
                footer: true,
                text: '<span class="badge bg-success"><i class="fas fa-file-csv"></i></span>'
            }
            //Deshabilitado por ahora: botón de visibilidad de columnas (colvis).
            //Para reactivarlo, quitar los marcadores /* y */ de las siguientes dos líneas.
            /*,
            {
                extend: 'colvis',
                text: '<span class="badge bg-info"><i class="fas fa-columns"></i></span>',
                postfixButtons: ['colvisRestore']
            }*/
        ]
