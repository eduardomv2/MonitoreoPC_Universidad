<?php
require_once __DIR__ . '/../auth/conexion.php';

// Obtener géneros
$generos = $conn->query("SELECT id_genero, descripcion FROM generos");

if ($generos === false) {
    echo "Error en la consulta de géneros: " . $conn->error;
    exit;
}


// Obtener carreras
$carreras = $conn->query("SELECT id_carrera, nombre FROM carreras");


// Obtener roles
$roles = $conn->query("SELECT id, rol FROM roles");

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sistema de Reportes de Computadoras</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
     <link rel="stylesheet" href="../public/css/testlogin.css">

</head>
<body>
  <div class="container">
    <div class="card w-100">
      <div class="card-header text-center">
        <h3 class="mb-0"><i class="bi bi-laptop card-header-icon"></i>Sistema de Reportes de Computadoras</h3>
      </div>
      <div class="card-body">
       <!-- Tabs -->
<ul class="nav nav-tabs nav-fill" id="authTabs" role="tablist">
  <li class="nav-item" role="presentation">
    <button
      class="nav-link active text-center"
      id="login-tab"
      data-bs-toggle="tab"
      data-bs-target="#login-tab-pane"
      type="button"
      role="tab"
      aria-controls="login-tab-pane"
      aria-selected="true"
    >
      <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
    </button>
  </li>
  <li class="nav-item" role="presentation">
    <button
      class="nav-link text-center"
      id="register-tab"
      data-bs-toggle="tab"
      data-bs-target="#register-tab-pane"
      type="button"
      role="tab"
      aria-controls="register-tab-pane"
      aria-selected="false"
    >
      <i class="bi bi-person-plus me-2"></i>Registrarse
    </button>
  </li>
</ul>

        
        <!-- Tab Content -->
        <div class="tab-content" id="authTabsContent">
          <!-- Login Form -->
          <div class="tab-pane fade show active" id="login-tab-pane" role="tabpanel" aria-labelledby="login-tab" tabindex="0">
            
           <?php if (isset($error)): ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
            <form action="../auth/login.php" method="POST">
              <div class="form-group">
                <label for="loginEmail" class="form-label">Correo Electrónico</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                  <input type="email" class="form-control" id="loginEmail" placeholder="correo@monclova.tecnm.mx" name="correo" required>
                </div>
              </div>
              <div class="form-group">
                <label for="loginPassword" class="form-label">Contraseña</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-lock"></i></span>
                  <input type="password" class="form-control" id="loginPassword" placeholder="••••••••" name="password" required>
                </div>
              </div>
              <div class="form-group d-flex justify-content-end">
                <a href="forgot_password.php" class="text-decoration-none"><i ></i>¿Olvidaste tu contraseña?</a>
              </div>
              <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">
                  <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
                </button>
              </div>
              
              <div class="divider">
                <span class="divider-text">o</span>
              </div>
              
              <div class="text-center">
                <p>¿No tienes una cuenta? <span class="form-text" id="switch-to-register"><i class="bi bi-person-plus me-1"></i>Crear nueva cuenta</span></p>
              </div>
            </form>
          </div>
          
          <!-- Register Form -->
          <div class="tab-pane fade" id="register-tab-pane" role="tabpanel" aria-labelledby="register-tab" tabindex="0">
            
            <form action="../auth/register.php" method="POST">
              <h5 class="mb-3 text-primary"><i class="bi bi-person me-2"></i>Información Personal</h5>
              <div class="row">
                <div class="col-md-4 form-group">
                  <label for="nombres" class="form-label">Nombre(s)</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" class="form-control" id="nombre" placeholder="Nombre(s)" name="nombre" required>
                  </div>
                </div>
                <div class="col-md-4 form-group">
                  <label for="apellido_paterno" class="form-label">Apellido Paterno</label>
                  <input type="text" class="form-control" id="apellido-paterno" placeholder="Apellido Paterno" name="apellido_paterno" required>
                </div>
                <div class="col-md-4 form-group">
                  <label for="apellido_materno" class="form-label">Apellido Materno</label>
                  <input type="text" class="form-control" id="apellido_materno" placeholder="Apellido Materno" name="apellido_materno" required>
                </div>
                <div class="col-md-6 form-group">
                  <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                    <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required>
                  </div>
                </div>
                <div class="col-md-6 form-group">
    <label for="genero_id" class="form-label">Género</label>
    <select id="genero_id" name="genero_id" class="form-control" required>
        <?php while ($genero = $generos->fetch_assoc()): ?>
            <option value="<?= $genero['id_genero']; ?>" 
                <?= isset($genero_id) && $genero_id == $genero['id_genero'] ? 'selected' : ''; ?>>
                <?= htmlspecialchars($genero['descripcion']); ?>
            </option>
        <?php endwhile; ?>
    </select>
</div>

<div class="mb-3">
  <h5 class="mb-3 mt-4 text-primary"><i class="bi bi-mortarboard me-2"></i>Información Académica</h5>
  <div class="row">
    <!-- Carrera -->
    <div class="col-md-4 form-group">
      <label for="carrera_id" class="form-label">Carrera</label>
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-journal-bookmark"></i></span>
        <select class="form-select" id="carrera_id" name="carrera_id" required>
          <option selected disabled>Selecciona tu carrera</option>
          <?php while ($carrera = $carreras->fetch_assoc()): ?>
            <option value="<?= $carrera['id_carrera'] ?>">
              <?= htmlspecialchars($carrera['nombre']) ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>
    </div>

    <!-- Semestre -->
    <div class="col-md-4 form-group">
      <label for="semestre" class="form-label">Semestre</label>
      <input type="number" class="form-control" id="semestre" name="semestre" min="1" max="12" required>
    </div>

<!-- Matrícula -->
<div class="col-md-4 form-group">
  <label for="matricula" class="form-label">Matrícula</label>
  <div class="input-group">
    <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
    <input type="text" class="form-control" id="matricula" name="matricula" maxlength="50" required>
  </div>
</div>


    <!-- Rol -->
    <div class="col-md-4 form-group">
      <label class="form-label">Rol</label>
      <div class="mt-2">
        <?php while ($rol = $roles->fetch_assoc()): ?>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="rol_id" id="rol<?= $rol['id'] ?>" value="<?= $rol['id'] ?>" required>
            <label class="form-check-label" for="rol<?= $rol['id'] ?>">
              <?= htmlspecialchars($rol['rol']) ?>
            </label>
          </div>
        <?php endwhile; ?>
      </div>
    </div>
  </div>
</div>

              
              <h5 class="mb-3 mt-4 text-primary"><i class="bi bi-shield-lock me-2"></i>Información de Cuenta</h5>
              <div class="row">
                <div class="col-12 form-group">
                  <label for="email" class="form-label">Correo Electrónico</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control" id="email" placeholder="correo@monclova.tecnm.mx" name="correo" required>
                  </div>
                </div>
                <div class="col-md-6 form-group">
                  <label for="password" class="form-label">Contraseña</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control" id="password" placeholder="••••••••" name="password" required minlength="8">
                  </div>
                </div>
                <div class="col-md-6 form-group">
                  <label for="confirmar_password" class="form-label">Confirmar Contraseña</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" class="form-control" id="confirmar_password" placeholder="••••••••" name="confirmar_password" required>
                  </div>
                </div>
              </div>
              <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                  <i class="bi bi-person-plus me-2"></i>Crear Cuenta
                </button>
              </div>
              
              <div class="divider">
                <span class="divider-text">o</span>
              </div>
              
              <div class="text-center">
                <p>¿Ya tienes una cuenta? <span class="form-text" id="switch-to-login"><i class="bi bi-box-arrow-in-right me-1"></i>Iniciar sesión</span></p>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Script para cambiar entre login y registro con los enlaces inferiores
    document.addEventListener('DOMContentLoaded', function() {
      const loginTab = document.getElementById('login-tab');
      const registerTab = document.getElementById('register-tab');
      const switchToRegister = document.getElementById('switch-to-register');
      const switchToLogin = document.getElementById('switch-to-login');

      switchToRegister.addEventListener('click', function() {
        const registerTabTrigger = new bootstrap.Tab(registerTab);
        registerTabTrigger.show();
      });

      switchToLogin.addEventListener('click', function() {
        const loginTabTrigger = new bootstrap.Tab(loginTab);
        loginTabTrigger.show();
      });
    });
  </script>
</body>
</html>