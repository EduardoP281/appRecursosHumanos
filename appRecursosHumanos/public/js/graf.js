Highcharts.setOptions({
    chart: {
        styledMode: true
    }
});
Dashboards.board('container', {
    dataPool: {
        connectors: [{
            id: 'VegeTable',
            type: 'CSV',
            options: {
                csv: document.querySelector('#csv').innerHTML
            }
        }]
    },
    gui: {
        layouts: [{
            rows: [{
                cells: [{
                    id: 'top-left'
                }, {
                    id: 'top-right'
                }]
            }, {
                cells: [{
                    id: 'bottom'
                }]
            }]
        }]
    },
    components: [{
        renderTo: 'top-left',
        type: 'Highcharts',
        sync: {
            highlight: true
        },
        connector: {
            id: 'VegeTable'
        },
        chartOptions: {
            chart: {
                type: 'bar'
            },
            credits: {
                enabled: false
            },
            legend: {
                enabled: false
            },
            plotOptions: {
                series: {
                    colorByPoint: true
                }
            },
            title: {
                text: ''
            },
            xAxis: {
                type: 'category'
            },
            yAxis: {
                title: {
                    enabled: false
                }
            }
        }
    }, {
        renderTo: 'top-right',
        type: 'Highcharts',
        sync: {
            highlight: true
        },
        connector: {
            id: 'VegeTable'
        },
        chartOptions: {
            chart: {
                type: 'pie'
            },
            credits: {
                enabled: false
            },
            legend: {
                enabled: false
            },
            plotOptions: {
                pie: {
                    innerSize: '60%'
                },
                series: {
                    colorByPoint: true
                }
            },
            title: {
                text: ''
            },
            xAxis: {
                type: 'category'
            },
            yAxis: {
                title: {
                    enabled: false
                }
            }
        }
    }, {
        renderTo: 'bottom',
        type: 'Highcharts',
        sync: {
            highlight: true
        },
        connector: {
            id: 'VegeTable'
        },
        chartOptions: {
            chart: {
                type: 'scatter'
            },
            credits: {
                enabled: false
            },
            legend: {
                enabled: false
            },
            plotOptions: {
                series: {
                    colorByPoint: true,
                    dataSorting: {
                        enabled: true,
                        sortKey: 'y'
                    },
                    marker: {
                        radius: 8
                    }
                }
            },
            title: {
                text: ''
            },
            xAxis: {
                type: 'category'
            },
            yAxis: {
                title: {
                    enabled: false
                }
            }
        }
    }]
});

const form = document.getElementById('filtroForm');
const estadoCivil = document.getElementById('estadoCivil');
const departamento = document.getElementById('departamento');

function actualizarGrafico() {
  const formData = new FormData(form);

  fetch('../../php/datos_departamento.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.text())
  .then(csv => {
    document.querySelector('#csv').innerHTML = csv;

    if (window.dashboard) {
      window.dashboard.setDataPool({
        connectors: [{
          id: 'VegeTable',
          type: 'CSV',
          options: { csv: csv }
        }]
      });
    }
  })
  .catch(error => console.error("Error en AJAX:", error));
}


//ESTO NO SIRVE
function cargarDepartamentos() {
  fetch('../../php/get_departamentos.php')
    .then(res => {
      if (!res.ok) throw new Error("Error en la respuesta del servidor");
      return res.json();
    })
    .then(data => {
      console.log("Departamentos recibidos:", data); // Verifica en consola
      const select = document.getElementById('departamento');

      // Limpiar opciones anteriores (excepto "Todos")
      select.innerHTML = '<option value="">Todos</option>';

      data.forEach(dep => {
        const option = document.createElement('option');
        option.value = dep;
        option.textContent = dep;
        select.appendChild(option);
      });
    })
    .catch(err => console.error("Error cargando departamentos:", err));
}

document.addEventListener('DOMContentLoaded', cargarDepartamentos);

// Actualizar automáticamente al cambiar filtros
estadoCivil.addEventListener('change', actualizarGrafico);
departamento.addEventListener('change', actualizarGrafico);
