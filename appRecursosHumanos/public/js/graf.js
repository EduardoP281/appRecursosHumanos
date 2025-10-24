let board; // Variable global para el dashboard

Highcharts.setOptions({
    chart: { styledMode: true }
});

/**
 * ✅ Esta es la nueva función "nuclear".
 * Destruye el dashboard viejo y crea uno nuevo con los datos frescos.
 * Es la forma más segura de evitar problemas de caché o de estado.
 */
function createOrUpdateDashboard(csvData, chartTitle) {
    // Si el dashboard ya existe, lo destruimos
    if (board) {
        board.destroy();
    }

    // Creamos (o recreamos) el dashboard con los nuevos datos
    board = Dashboards.board('container', {
        dataPool: {
            connectors: [{
                id: 'VegeTable',
                type: 'CSV',
                options: { csv: csvData } // Usar los nuevos datos CSV
            }]
        },
        gui: {
            layouts: [{
                rows: [{
                    cells: [{ id: 'cell-bar' }, { id: 'cell-pie' }]
                }, {
                    cells: [{ id: 'cell-scatter' }]
                }]
            }]
        },
        components: [
            {
                id: 'chart-bar',
                renderTo: 'cell-bar',
                type: 'Highcharts',
                connector: { id: 'VegeTable' },
                sync: { highlight: true },
                chartOptions: {
                    chart: { type: 'bar' },
                    title: { text: chartTitle }, // Usar el nuevo título
                    credits: { enabled: false },
                    xAxis: { type: 'category' },
                    yAxis: { title: { text: 'Cantidad' } },
                    plotOptions: { series: { colorByPoint: true, dataLabels: { enabled: true } } }
                }
            },
            {
                id: 'chart-pie',
                renderTo: 'cell-pie',
                type: 'Highcharts',
                connector: { id: 'VegeTable' },
                sync: { highlight: true },
                chartOptions: {
                    chart: { type: 'pie' },
                    title: { text: 'Distribución: ' + chartTitle },
                    credits: { enabled: false },
                    plotOptions: {
                        pie: {
                            innerSize: '60%',
                            dataLabels: { enabled: true, format: '<b>{point.name}</b>: {point.y}' }
                        }
                    }
                }
            },
            {
                id: 'chart-scatter',
                renderTo: 'cell-scatter',
                type: 'Highcharts',
                connector: { id: 'VegeTable' },
                sync: { highlight: true },
                chartOptions: {
                    chart: { type: 'scatter', zoomType: 'xy' },
                    title: { text: 'Dispersión: ' + chartTitle },
                    credits: { enabled: false },
                    xAxis: { type: 'category', title: { text: 'Categoría' } },
                    yAxis: { title: { text: 'Cantidad' } },
                    plotOptions: {
                        scatter: {
                            marker: { radius: 5 },
                            tooltip: { headerFormat: '<b>{series.name}</b><br>', pointFormat: '{point.name}, {point.y}' }
                        }
                    }
                }
            }
        ]
    });
    console.log("Dashboard recreado con nuevos datos.");
}

// --- Lógica de Filtros ---
const form = document.getElementById('filtroForm');

function actualizarGrafico() {
    const formData = new FormData(form);
    console.log("Enviando filtros:", Object.fromEntries(formData)); // Para depurar (F12)

    // ✅ Volvemos a usar 'POST'
    fetch('datos_departamento.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json()) 
    .then(data => { // data = {csv: "...", titulo: "..."}
        console.log("Datos JSON recibidos:", data); 

        // Llamamos a la función que destruye y reconstruye
        createOrUpdateDashboard(data.csv, data.titulo);
    })
    .catch(error => console.error("Error en AJAX:", error));
}

function cargarDepartamentos() {
    // Usamos POST por consistencia
    fetch('get_departamentos.php', { method: 'POST' })
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById('departamento');
            select.innerHTML = '<option value="">Todos</option>';
            data.forEach(dep => {
                const option = document.createElement('option');
                option.value = dep;
                option.textContent = dep;
                select.appendChild(option);
            });
        })
        .catch(err => console.error("Error cargando deptos:", err));
}

// --- Event Listeners ---
document.addEventListener('DOMContentLoaded', function() {
    const initialCSV = document.querySelector('#csv').innerHTML;
    // Creación inicial del dashboard
    createOrUpdateDashboard(initialCSV, 'Personal por Departamento'); 
    
    // Carga de filtros
    cargarDepartamentos();
    
    // Asignación del evento al botón
    form.addEventListener('submit', function(e) {
        e.preventDefault(); // Evitar recarga de página
        actualizarGrafico(); 
    });
});