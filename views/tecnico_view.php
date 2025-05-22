<?php
require_once __DIR__ . '/../auth/auth.php'; // Asegúrate de que el usuario esté autenticado

require_once __DIR__ . '/../auth/conexion.php'; // Ajusta ruta si es necesario

// Obtener datos para selects
$carreras = [];
$res = $conn->query("SELECT id_carrera, nombre FROM carreras ORDER BY nombre");
while ($row = $res->fetch_assoc()) {
    $carreras[] = $row;
}

$generos = [];
$res = $conn->query("SELECT id_genero, descripcion FROM generos ORDER BY descripcion");
while ($row = $res->fetch_assoc()) {
    $generos[] = $row;
}

$equipos = [];
$res = $conn->query("SELECT id_equipo, descripcion FROM equipos ORDER BY descripcion");
while ($row = $res->fetch_assoc()) {
    $equipos[] = $row;
}

$servicios = [];
$res = $conn->query("SELECT id_servicio, descripcion FROM servicios ORDER BY descripcion");
while ($row = $res->fetch_assoc()) {
    $servicios[] = $row;
}

function generarNumeroControl($conn) {
    $res = $conn->query("SELECT MAX(no_control) AS max_no FROM reportes");
    $row = $res->fetch_assoc();
    $max_no = $row['max_no'] ?? 0;
    return intval($max_no) + 1;
}

$numero_control = generarNumeroControl($conn);

// Obtener todos los reportes para la tabla (sin nombre_completo ni numero_celular)
$reportes = [];
$res = $conn->query("SELECT r.no_control, r.matricula_cliente,
    c.nombre AS carrera, r.semestre, g.descripcion AS genero, e.descripcion AS equipo,
    s.descripcion AS servicio, ss.descripcion AS subservicio
    FROM reportes r
    LEFT JOIN carreras c ON r.id_carrera = c.id_carrera
    LEFT JOIN generos g ON r.id_genero = g.id_genero
    LEFT JOIN equipos e ON r.id_equipo = e.id_equipo
    LEFT JOIN servicios s ON r.id_servicio = s.id_servicio
    LEFT JOIN subservicios ss ON r.id_subservicio = ss.id_subservicio
    ORDER BY r.no_control DESC");

while ($row = $res->fetch_assoc()) {
    $reportes[] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Reportes Técnicos</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --primary-light: #eef2ff;
            --secondary: #64748b;
            --dark: #1e293b;
            --light: #f8fafc;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            --border: #e2e8f0;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            line-height: 1.6;
            color: var(--dark);
            background-color: #f1f5f9;
            padding: 0;
            margin: 0;
        }
        
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background-color: var(--primary);
            color: white;
            padding: 20px 0;
            margin-bottom: 30px;
            box-shadow: var(--shadow);
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
        }
        
        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: var(--shadow);
            margin-bottom: 30px;
            overflow: hidden;
        }
        
        .card-header {
            background-color: var(--primary);
            color: white;
            padding: 15px 20px;
            font-size: 18px;
            font-weight: 500;
        }
        
        .card-body {
            padding: 25px;
        }
        
        .form-row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -10px;
        }
        
        .form-group {
            flex: 1;
            min-width: 250px;
            padding: 0 10px;
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
        }
        
        input, select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: 4px;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        input:focus, select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }
        
        input[readonly] {
            background-color: var(--light);
            cursor: not-allowed;
        }
        
        button {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            font-weight: 500;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        button:hover {
            background-color: var(--primary-dark);
        }
        
        hr {
            border: 0;
            height: 1px;
            background-color: var(--border);
            margin: 30px 0;
        }
        
        /* Table styles */
        .table-responsive {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: white;
            box-shadow: var(--shadow);
            border-radius: 8px;
            overflow: hidden;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }
        
        th {
            background-color: var(--primary);
            color: white;
            font-weight: 500;
            white-space: nowrap;
        }
        
        tr:nth-child(even) {
            background-color: var(--light);
        }
        
        tr:hover {
            background-color: var(--primary-light);
        }
        
        .section-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary);
        }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
        }
        
        .badge-primary {
            background-color: var(--primary-light);
            color: var(--primary);
        }
        
        .badge-success {
            background-color: #d1fae5;
            color: var(--success);
        }
        
        .form-actions {
            text-align: right;
            margin-top: 20px;
        }
        
        .form-note {
            background-color: var(--primary-light);
            border-left: 4px solid var(--primary);
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .form-note p {
            margin: 0;
            color: var(--dark);
        }
        
        @media (max-width: 768px) {
            .form-group {
                flex: 100%;
            }
            
            .header-content {
                flex-direction: column;
                text-align: center;
            }
            
            .header h1 {
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>

<div class="header">
    <div class="container">
        <div class="header-content">
            <h1>Sistema de Reportes Técnicos</h1>
        </div>
    </div>
</div>

<div class="container">
    <div class="card">
        <div class="card-header">
            Registrar reporte técnico
        </div>
        <div class="card-body">
            <div class="form-note">
                <p>Complete todos los campos requeridos para generar un nuevo reporte técnico.</p>
            </div>
            

<button onclick="toggleFormulario()" id="btnFormulario" style="margin-bottom: 1rem;">Registrar nuevo reporte</button>

<div id="contenedorFormulario" style="display: none; margin-bottom: 2rem;">
  <form action="guardar_reporte.php" method="POST" id="formReporte" enctype="multipart/form-data">
    <div class="section-title">Información Personal</div>
    
    <div class="form-row">
      <div class="form-group">
        <label for="nombre_completo">Nombre completo (solo para PDF):</label>
        <input type="text" id="nombre_completo" name="nombre_completo" required>
      </div>
      
      <div class="form-group">
        <label for="numero_celular">Número celular (solo para PDF):</label>
        <input type="text" id="numero_celular" name="numero_celular" pattern="\d{10}" title="Solo 10 dígitos numéricos" required>
      </div>
    </div>
    
    <div class="section-title">Información del Reporte</div>
    
      <div class="form-group">
        <label for="matricula_cliente">Matrícula del cliente:</label>
        <input type="text" id="matricula_cliente" name="matricula_cliente" required>
      </div>

    
    <div class="form-row">
      <div class="form-group">
        <label for="id_carrera">Carrera:</label>
        <select id="id_carrera" name="id_carrera" required>
          <option value="">Selecciona una carrera</option>
          <?php foreach($carreras as $c): ?>
            <option value="<?= htmlspecialchars($c['id_carrera']) ?>"><?= htmlspecialchars($c['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      
      <div class="form-group">
        <label for="semestre">Semestre:</label>
        <input type="number" id="semestre" name="semestre" min="1" max="12" required>
      </div>
    </div>
    
    <div class="section-title">Detalles del Servicio</div>
    
    <div class="form-row">
      <div class="form-group">
        <label for="id_genero">Género:</label>
        <select id="id_genero" name="id_genero" required>
          <option value="">Selecciona género</option>
          <?php foreach($generos as $g): ?>
            <option value="<?= htmlspecialchars($g['id_genero']) ?>"><?= htmlspecialchars($g['descripcion']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      
      <div class="form-group">
        <label for="id_equipo">Equipo:</label>
        <select id="id_equipo" name="id_equipo" required>
          <option value="">Selecciona equipo</option>
          <?php foreach($equipos as $e): ?>
            <option value="<?= htmlspecialchars($e['id_equipo']) ?>"><?= htmlspecialchars($e['descripcion']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    
    <div class="form-row">
      <div class="form-group">
        <label for="id_servicio">Servicio:</label>
        <select id="id_servicio" name="id_servicio" required>
          <option value="">Selecciona servicio</option>
          <?php foreach($servicios as $s): ?>
            <option value="<?= htmlspecialchars($s['id_servicio']) ?>"><?= htmlspecialchars($s['descripcion']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      
      <div class="form-group">
        <label for="id_subservicio">Subservicio:</label>
        <select id="id_subservicio" name="id_subservicio" required>
          <option value="">Selecciona subservicio</option>
        </select>
      </div>
    </div>

    <!-- Campo para subir evidencia -->
<div class="form-row">
  <div class="form-group">
    <label for="evidencia">Subir evidencia (imagen o PDF):</label>
    <input 
      type="file" 
      id="evidencia" 
      name="evidencia" 
      accept="image/*,application/pdf"
      class="form-control-file"
      >
  </div>
</div>

    
    <div class="form-actions">
      <button type="submit">Guardar reporte</button>
    </div>
  </form>
</div>

<script>
  function toggleFormulario() {
    const contenedor = document.getElementById("contenedorFormulario");
    const boton = document.getElementById("btnFormulario");
    if (contenedor.style.display === "block") {
      contenedor.style.display = "none";
      boton.textContent = "Registrar nuevo reporte";
    } else {
      contenedor.style.display = "block";
      boton.textContent = "Ocultar formulario";
    }
  }
</script>


    
    <div class="card">
        <div class="card-header">
            Reportes registrados
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>No. Control</th>
                            <th>Matrícula</th>
                            <th>Carrera</th>
                            <th>Semestre</th>
                            <th>Género</th>
                            <th>Equipo</th>
                            <th>Servicio</th>
                            <th>Subservicio</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($reportes) > 0): ?>
                            <?php foreach($reportes as $rep): ?>
                                <tr>
                                    <td><span class="badge badge-primary"><?= htmlspecialchars($rep['no_control']) ?></span></td>
                                    <td><?= htmlspecialchars($rep['matricula_cliente']) ?></td>
                                    <td><?= htmlspecialchars($rep['carrera']) ?></td>
                                    <td><?= htmlspecialchars($rep['semestre']) ?></td>
                                    <td><?= htmlspecialchars($rep['genero']) ?></td>
                                    <td><?= htmlspecialchars($rep['equipo']) ?></td>
                                    <td><?= htmlspecialchars($rep['servicio']) ?></td>
                                    <td><?= htmlspecialchars($rep['subservicio']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="8" style="text-align: center;">No hay reportes aún</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('id_servicio').addEventListener('change', function() {
    const servicioId = this.value;
    const subservicioSelect = document.getElementById('id_subservicio');
    subservicioSelect.innerHTML = '<option>Cargando...</option>';

    if (!servicioId) {
        subservicioSelect.innerHTML = '<option value="">Selecciona subservicio</option>';
        return;
    }

    fetch('obtener_subservicio.php?id_servicio=' + servicioId)
        .then(response => {
            if (!response.ok) throw new Error('Error en la respuesta');
            return response.json();
        })
        .then(data => {
            subservicioSelect.innerHTML = '<option value="">Selecciona subservicio</option>';
            data.forEach(function(sub) {
                const option = document.createElement('option');
                option.value = sub.id_subservicio;
                option.textContent = sub.nombre;
                subservicioSelect.appendChild(option);
            });
        })
        .catch(() => {
            subservicioSelect.innerHTML = '<option value="">Error al cargar</option>';
        });
});
</script>

</body>
</html>