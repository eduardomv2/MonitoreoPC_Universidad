
<?php
require_once __DIR__ . '/../auth/conexion.php';

// Mensaje de error opcional (si lo recibes vía GET)
if (isset($_GET['error'])) {
    $error = $_GET['error'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Maestros - Sistema de Reportes</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <!-- Estilos personalizados -->
  <link rel="stylesheet" href="../public/css/login_maestro.css">
</head>
<body>
  <div class="container">
    <div class="card w-100">
      <div class="card-header text-center">
        <h3 class="mb-0"><i class="bi bi-laptop card-header-icon"></i> Sistema de Reportes de Computadoras - Maestros</h3>
      </div>
      <div class="card-body">

        <?php if (isset($error)): ?>
          <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

            <form action="../auth/login_maestro.php" method="POST">
            <div class="form-group mb-3">
                <label for="usuario" class="form-label">Usuario</label>
                <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <input type="text" class="form-control" id="usuario" name="usuario" placeholder="Ingresa tu usuario" required>
                </div>
            </div>


          <div class="form-group mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-lock"></i></span>
              <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
            </div>
          </div>

      
          <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
            </button>
          </div>
        </form>

      </div>
    </div>
  </div>

  <!-- Bootstrap JS Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>