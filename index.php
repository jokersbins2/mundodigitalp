<?php
/**
 * ============================================
 * ARCHIVO: index.php
 * Página principal - Login y Dashboard
 * ============================================
 */

require_once 'config.php';
require_once 'funciones.php';

$error = '';
$mensaje = '';

// ============================================
// PROCESAR LOGIN
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    $usuario = autenticar($username, $password);
    
    if ($usuario) {
        // Guardar datos en sesión
        $_SESSION['user_id'] = $usuario['id'];
        $_SESSION['username'] = $usuario['username'];
        $_SESSION['role'] = $usuario['role'];
        $_SESSION['ultimo_acceso'] = time();
        
        // Registrar login
        registrarLog($usuario['id'], 'login', 'Inicio de sesión');
        
        // Redirigir para evitar reenvío de formulario
        header("Location: index.php");
        exit();
    } else {
        $error = 'Usuario o contraseña incorrectos';
    }
}

// ============================================
// PROCESAR LOGOUT
// ============================================
if (isset($_GET['logout'])) {
    if (isset($_SESSION['user_id'])) {
        registrarLog($_SESSION['user_id'], 'logout', 'Cierre de sesión');
    }
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

// Verificar timeout si está logueado
if (estaLogueado()) {
    verificarTimeout();
}

// Capturar mensajes de otras páginas
if (isset($_GET['msg'])) {
    $mensaje = limpiar($_GET['msg']);
}
if (isset($_GET['error'])) {
    $error = limpiar($_GET['error']);
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mundo Digital Premium - Sistema de Códigos</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php if (!estaLogueado()): ?>
    <!-- ========================================== -->
    <!-- PANTALLA DE LOGIN -->
    <!-- ========================================== -->
    <div class="contenedor-login">
        <div class="tarjeta-login">
            <!-- Logo -->
            <div class="login-logo">
                <img src="imagenes/logo/logo.webp" alt="Mundo Digital Premium">
            </div>
            
            <h1>MUNDO DIGITAL</h1>
            <p class="subtitulo">Sistema de Códigos Premium</p>
            
            <?php if ($error): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="grupo-input">
                    <label>👤 Usuario</label>
                    <input 
                        type="text" 
                        name="username" 
                        placeholder="Ingrese su usuario"
                        required 
                        autofocus
                    >
                </div>
                
                <div class="grupo-input">
                    <label>🔒 Contraseña</label>
                    <input 
                        type="password" 
                        name="password" 
                        placeholder="Ingrese su contraseña"
                        required
                    >
                </div>
                
                <button type="submit" name="login" class="btn-primary">
                    Iniciar Sesión
                </button>
            </form>
            
            <p class="texto-footer">
                Sistema de gestión de códigos<br>
                Mundo Digital Premium
            </p>
        </div>
    </div>

<?php else: ?>
    <!-- ========================================== -->
    <!-- DASHBOARD (Usuario logueado) -->
    <!-- ========================================== -->
    <div class="contenedor-dashboard">
        <!-- Header -->
        <div class="header">
            <h1>🎬 MUNDO DIGITAL PREMIUM</h1>
            <div class="info-usuario">
                <span>Hola, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></span>
                <span class="badge <?= esAdmin() ? 'badge-admin' : 'badge-cliente' ?>">
                    <?= esAdmin() ? 'Admin' : 'Cliente' ?>
                </span>
                <a href="?logout=1" class="btn btn-salir">Cerrar Sesión</a>
            </div>
        </div>
        
        <!-- Mensajes -->
        <?php if ($mensaje): ?>
            <div class="alerta alerta-success"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alerta alerta-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <!-- Navegación Admin -->
        <?php if (esAdmin()): ?>
            <div class="tabs">
                <button class="tab-btn activo" onclick="mostrarTab('consultar')">
                    📧 Consultar Correos
                </button>
                <button class="tab-btn" onclick="mostrarTab('usuarios')">
                    👥 Gestionar Usuarios
                </button>
            </div>
        <?php endif; ?>
        
        <!-- ========================================== -->
        <!-- TAB: CONSULTAR CORREOS -->
        <!-- ========================================== -->
        <div id="tab-consultar" class="tab-contenido activo">
            <div class="seccion">
                <h2>🔍 Consultar Códigos Premium</h2>
                <p class="descripcion">Ingresa el email del cliente para buscar sus códigos recientes</p>
                
                <form method="GET" action="" class="formulario-busqueda">
                    <div class="grupo-input-busqueda">
                        <input 
                            type="email" 
                            name="buscar_email" 
                            placeholder="ejemplo@correo.com"
                            value="<?= isset($_GET['buscar_email']) ? htmlspecialchars($_GET['buscar_email']) : '' ?>"
                            required
                            class="input-busqueda"
                        >
                        <button type="submit" class="btn-buscar">🔍 Buscar</button>
                    </div>
                </form>
                
                <!-- Resultados de la búsqueda -->
                <div id="resultados">
                    <?php
                    if (isset($_GET['buscar_email'])) {
                        $email = trim($_GET['buscar_email']);
                        echo obtenerCorreos($email);
                    }
                    ?>
                </div>
            </div>
        </div>
        
        <?php if (esAdmin()): ?>
        <!-- ========================================== -->
        <!-- TAB: GESTIONAR USUARIOS (Solo Admin) -->
        <!-- ========================================== -->
        <div id="tab-usuarios" class="tab-contenido">
            <div class="seccion">
                <h2>👥 Gestión de Usuarios</h2>
                
                <!-- Formulario agregar usuario -->
                <div class="formulario-agregar">
                    <h3>➕ Agregar Nuevo Cliente</h3>
                    <form method="POST" action="procesar_usuarios.php">
                        <div class="grupo-inputs-horizontal">
                            <div class="grupo-input">
                                <label>Usuario</label>
                                <input 
                                    type="text" 
                                    name="nuevo_username" 
                                    placeholder="cliente@email.com o usuario123"
                                    required
                                    minlength="3"
                                >
                            </div>
                            <div class="grupo-input">
                                <label>Contraseña</label>
                                <input 
                                    type="text" 
                                    name="nuevo_password" 
                                    placeholder="Mínimo 6 caracteres"
                                    required
                                    minlength="6"
                                >
                            </div>
                        </div>
                        <button type="submit" name="accion" value="agregar" class="btn-add">
                            ➕ Agregar Cliente
                        </button>
                    </form>
                </div>
                
                <!-- Lista de clientes -->
                <div class="lista-usuarios">
                    <h3>📋 Clientes Registrados</h3>
                    <table class="tabla-usuarios">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Contraseña</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $clientes = obtenerClientes();
                            if (empty($clientes)):
                            ?>
                                <tr>
                                    <td colspan="3" style="text-align: center;">No hay clientes registrados</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($clientes as $cliente): ?>
                                <tr>
                                    <td><?= htmlspecialchars($cliente['username']) ?></td>
                                    <td>
                                        <span class="password-text" id="pass-<?= $cliente['id'] ?>">
                                            <?= htmlspecialchars($cliente['password_plain'] ?? '••••••••') ?>
                                        </span>
                                    </td>
                                    <td class="acciones-td">
                                        <!-- Botón copiar -->
                                        <button 
                                            onclick="copiarDatos('<?= htmlspecialchars($cliente['username']) ?>', '<?= htmlspecialchars($cliente['password_plain'] ?? '') ?>')"
                                            class="btn-copiar"
                                            title="Copiar usuario y contraseña">
                                            📋 Copiar
                                        </button>
                                        
                                        <!-- Botón editar contraseña -->
                                        <button 
                                            onclick="editarPassword(<?= $cliente['id'] ?>, '<?= htmlspecialchars($cliente['username']) ?>')"
                                            class="btn-editar">
                                            🔑 Cambiar
                                        </button>
                                        
                                        <!-- Formulario eliminar -->
                                        <form method="POST" action="procesar_usuarios.php" style="display: inline;">
                                            <input type="hidden" name="id_cliente" value="<?= $cliente['id'] ?>">
                                            <button 
                                                type="submit" 
                                                name="accion" 
                                                value="eliminar"
                                                class="btn-eliminar"
                                                onclick="return confirm('¿Eliminar a <?= htmlspecialchars($cliente['username']) ?>?')">
                                                🗑️ Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Modal para cambiar contraseña (SOLO ADMIN) -->
            <div id="modalPassword" class="modal">
                <div class="modal-contenido">
                    <span class="modal-cerrar" onclick="cerrarModal()">&times;</span>
                    <h3>🔑 Cambiar Contraseña</h3>
                    <form method="POST" action="procesar_usuarios.php">
                        <input type="hidden" name="id_cliente" id="modal_id">
                        <p style="color: #fff; margin-bottom: 20px;">
                            Usuario: <strong style="color: #FFC107;" id="modal_username"></strong>
                        </p>
                        <div class="grupo-input">
                            <label>Nueva Contraseña</label>
                            <input 
                                type="text" 
                                name="nueva_password" 
                                placeholder="Mínimo 6 caracteres"
                                required
                                minlength="6"
                            >
                        </div>
                        <button type="submit" name="accion" value="editar_password" class="btn-primary">
                            Actualizar Contraseña
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

<?php endif; ?>

<script>
// Función para cambiar entre tabs (admin)
function mostrarTab(nombreTab) {
    // Ocultar todos los contenidos
    document.querySelectorAll('.tab-contenido').forEach(tab => {
        tab.classList.remove('activo');
    });
    
    // Desactivar todos los botones
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('activo');
    });
    
    // Activar el tab seleccionado
    document.getElementById('tab-' + nombreTab).classList.add('activo');
    event.target.classList.add('activo');
}

// Función para abrir modal de cambiar contraseña
function editarPassword(id, username) {
    document.getElementById('modal_id').value = id;
    document.getElementById('modal_username').textContent = username;
    document.getElementById('modalPassword').style.display = 'block';
}

// Función para cerrar modal
function cerrarModal() {
    document.getElementById('modalPassword').style.display = 'none';
}

// Función para copiar usuario y contraseña
function copiarDatos(usuario, password) {
    const texto = `Usuario: ${usuario}\nContraseña: ${password}`;
    
    // Copiar al portapapeles
    navigator.clipboard.writeText(texto).then(function() {
        // Mostrar mensaje de éxito
        alert('✅ Datos copiados al portapapeles:\n\n' + texto);
    }).catch(function(err) {
        // Fallback para navegadores antiguos
        const textArea = document.createElement('textarea');
        textArea.value = texto;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        alert('✅ Datos copiados al portapapeles:\n\n' + texto);
    });
}


// Cerrar modal si se hace clic fuera de él
window.onclick = function(event) {
    const modal = document.getElementById('modalPassword');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}
</script>

</body>
</html>