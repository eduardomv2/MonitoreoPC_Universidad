<?php
 
require_once __DIR__ . '/../auth/curl_headers.php';

$url_api = "http://localhost/MonitoreoPC_Universidad/api/tecnico/reportes/getCarreras.php";

$headers = getCurlHeaders("");

$ch = curl_init($url_api);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
curl_close($ch);

$estudiantes = json_decode($response, true);
?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Gráfica de Carreras</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

  <canvas id="carrerasChart" width="400" height="200"></canvas>

  <script>
    // Mapeo de ID de carrera a nombre real
const carrerasMap = {
    "1": "Ing. Informática",
    "2": "Ing. Industrial",
    "3": "Ing. Electrónica",
    "4": "Ing. Gestión Empresarial",
    "5": "Ing. Energías Renovables",
    "6": "Ing. Mecánica"
};

fetch("http://localhost/MonitoreoPC_Universidad/api/tecnico/reportes/getCarreras.php")
    .then(response => response.json())
    .then(data => {
        // Convertimos id_carrera en nombres reales
        const labels = data.map(item => carrerasMap[item.id_carrera] || "Desconocido");
        const valores = data.map(item => parseInt(item.total_reportes));

        const ctx = document.getElementById('carrerasChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total de Reportes',
                    data: valores,
                    backgroundColor: 'rgba(26, 86, 219, 0.7)',
                    borderColor: 'rgba(26, 86, 219, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Cantidad de Reportes'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Carreras'
                        }
                    }
                }
            }
        });
    })
    .catch(error => {
        console.error("Error al obtener los datos de la API:", error);
    });

  </script>

</body>
</html>
