<?php
/**
 * ============================================
 * ARCHIVO: config.php
 * Configuración general del sistema
 * ============================================
 */

// Iniciar sesión
session_start();

// Configurar zona horaria
date_default_timezone_set('America/Bogota');

// Configurar errores (CAMBIAR EN PRODUCCIÓN)
ini_set('display_errors', 0); // Cambiar a 0 en producción
error_reporting(E_ALL);

// ============================================
// CONFIGURACIÓN DE BASE DE DATOS
// ============================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'mundo_digital_premium');
define('DB_USER', 'root');        // CAMBIAR en servidor real
define('DB_PASS', 'root');        // CAMBIAR en servidor real
define('DB_CHARSET', 'utf8mb4');

// ============================================
// CONFIGURACIÓN DE CORREO IMAP (GMAIL)
// ============================================
define('IMAP_USER', 'yobotyobot57@gmail.com');
define('IMAP_PASS', 'qzuuwoimtzndgiwe');  // App Password de Gmail
define('IMAP_HOST', '{imap.gmail.com:993/imap/ssl/novalidate-cert}INBOX');

// ============================================
// CONFIGURACIÓN DE SESIÓN
// ============================================
define('SESSION_TIMEOUT', 18000); // 5 horas en segundos

/**
 * Obtener conexión a base de datos
 * @return PDO
 */
function conectarDB() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $opciones = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $opciones);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
    
    return $pdo;
}

/**
 * Verificar si hay sesión activa
 * @return bool
 */
function estaLogueado() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

/**
 * Verificar si el usuario es admin
 * @return bool
 */
function esAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Verificar timeout de sesión
 */
function verificarTimeout() {
    if (isset($_SESSION['ultimo_acceso'])) {
        $tiempo_inactivo = time() - $_SESSION['ultimo_acceso'];
        
        if ($tiempo_inactivo > SESSION_TIMEOUT) {
            session_unset();
            session_destroy();
            header("Location: index.php?error=sesion_expirada");
            exit();
        }
    }
    
    $_SESSION['ultimo_acceso'] = time();
}

/**
 * Registrar actividad en logs
 * @param int $usuario_id
 * @param string $accion
 * @param string $detalles
 */
function registrarLog($usuario_id, $accion, $detalles = null) {
    try {
        $pdo = conectarDB();
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        
        $sql = "INSERT INTO logs_actividad (usuario_id, accion, detalles, ip_address) 
                VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$usuario_id, $accion, $detalles, $ip]);
    } catch (PDOException $e) {
        error_log("Error al registrar log: " . $e->getMessage());
    }
}

/**
 * Limpiar datos de entrada
 * @param string $dato
 * @return string
 */
function limpiar($dato) {
    $dato = trim($dato);
    $dato = stripslashes($dato);
    $dato = htmlspecialchars($dato, ENT_QUOTES, 'UTF-8');
    return $dato;
}