<?php

require_once __DIR__ . '/../auth/auth.php'; 
require_once __DIR__ . '/../auth/curl_headers.php';

$url_api = "http://localhost/MonitoreoPC_Universidad/api/tecnico/reportes/getDatosTecnicos.php";


// Verificar si el usuario está logueado
if (!isset($_SESSION['id_usuario'])) {
    // Si no está logueado, redirigir al login
    header("Location: ../views/login_view.php");
    exit();
}




$ch = curl_init($url_api);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, getCurlHeaders("")); 
$response = curl_exec($ch);
curl_close($ch);

// Decodificamos la respuesta
$estudiantes = json_decode($response, true);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estudiantes Técnicos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #1a56db;
            --secondary-blue: #3b82f6;
            --light-blue: #dbeafe;
            --dark-blue: #1e40af;
            --white: #ffffff;
            --light-gray: #f3f4f6;
            --text-dark: #1f2937;
            --text-gray: #6b7280;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-gray);
            color: var(--text-dark);
            line-height: 1.6;
        }

        .header {
            background: linear-gradient(135deg, var(--primary-blue), var(--dark-blue));
            color: var(--white);
            padding: 2rem 0;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            border-bottom: 5px solid var(--secondary-blue);
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            margin: 2rem 0;
        }

        .card {
            background-color: var(--white);
            border-radius: 12px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #e5e7eb;
            position: relative;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .card-image-container {
            height: 200px;
            overflow: hidden;
            position: relative;
        }

        .card-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .card:hover .card-image {
            transform: scale(1.05);
        }

        .card-content {
            padding: 1.5rem;
            background-color: var(--white);
        }

        .card-header {
            border-bottom: 2px solid var(--light-blue);
            padding-bottom: 0.75rem;
            margin-bottom: 1rem;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary-blue);
            margin-bottom: 0.25rem;
        }

        .card-info {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .info-item {
            display: flex;
            align-items: center;
            color: var(--text-gray);
            font-size: 0.95rem;
        }

        .info-item i {
            color: var(--secondary-blue);
            margin-right: 0.5rem;
            width: 20px;
            text-align: center;
        }

        .info-label {
            font-weight: 600;
            margin-right: 0.25rem;
            color: var(--text-dark);
        }

        .empty-message {
            text-align: center;
            background-color: var(--white);
            padding: 3rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin: 3rem auto;
            max-width: 600px;
        }

        .empty-message i {
            font-size: 3rem;
            color: var(--secondary-blue);
            margin-bottom: 1rem;
        }

        .empty-message h2 {
            color: var(--text-dark);
            margin-bottom: 1rem;
        }

        .empty-message p {
            color: var(--text-gray);
        }

        @media (max-width: 768px) {
            .cards-container {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
            
            .header h1 {
                font-size: 2rem;
            }
        }

        @media (max-width: 480px) {
            .cards-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <h1>Estudiantes Técnicos</h1>
            <p>Directorio de estudiantes técnicos disponibles</p>
        </div>
    </div>

    <div class="container">
        <?php if (is_array($estudiantes) && count($estudiantes) > 0): ?>
            <div class="cards-container">
                <?php foreach ($estudiantes as $est): ?>
                    <div class="card">


                       <div class="card-image-container">
  <img class="card-image" 
     src="<?= $est['foto_perfil'] ? '../uploads/' . urlencode(basename($est['foto_perfil'])) : 'https://via.placeholder.com/400x300/1a56db/ffffff?text=Sin+Foto' ?>" 
     alt="Foto de <?= htmlspecialchars($est['nombre_completo']) ?>">


</div>


                        <div class="card-content">
                            <div class="card-header">
                                <h3 class="card-title"><?= htmlspecialchars($est['nombre_completo']) ?></h3>
                            </div>
                            <div class="card-info">
                                <div class="info-item">
                                    <i class="fas fa-graduation-cap"></i>
                                    <span class="info-label">Carrera:</span>
                                    <?= htmlspecialchars($est['nombre_carrera']) ?>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-book"></i>
                                    <span class="info-label">Semestre:</span>
                                    <?= htmlspecialchars($est['semestre']) ?>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-phone"></i>
                                    <span class="info-label">Celular:</span>
                                    <?= $est['celular'] ? htmlspecialchars($est['celular']) : 'No disponible' ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-message">
                <i class="fas fa-user-slash"></i>
                <h2>No se encontraron estudiantes</h2>
                <p>No se encontraron estudiantes técnicos o hubo un problema al cargar los datos.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>