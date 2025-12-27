<?php
/**
 * ============================================
 * ARCHIVO: procesar_usuarios.php
 * Procesa acciones de gestión de usuarios
 * Solo accesible para administradores
 * ============================================
 */

require_once 'config.php';
require_once 'funciones.php';

// Verificar que esté logueado y sea admin
if (!estaLogueado() || !esAdmin()) {
    header("Location: index.php?error=Acceso denegado");
    exit();
}

verificarTimeout();

// Verificar que sea petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

$accion = $_POST['accion'] ?? '';

// ============================================
// AGREGAR NUEVO CLIENTE
// ============================================
if ($accion === 'agregar') {
    $username = trim($_POST['nuevo_username'] ?? '');
    $password = $_POST['nuevo_password'] ?? '';
    
    $resultado = agregarCliente($username, $password);
    
    if ($resultado['success']) {
        header("Location: index.php?msg=" . urlencode($resultado['message']));
    } else {
        header("Location: index.php?error=" . urlencode($resultado['message']));
    }
    exit();
}

// ============================================
// EDITAR CONTRASEÑA
// ============================================
if ($accion === 'editar_password') {
    $id = intval($_POST['id_cliente'] ?? 0);
    $nueva_password = $_POST['nueva_password'] ?? '';
    
    $resultado = actualizarPassword($id, $nueva_password);
    
    if ($resultado['success']) {
        header("Location: index.php?msg=" . urlencode($resultado['message']));
    } else {
        header("Location: index.php?error=" . urlencode($resultado['message']));
    }
    exit();
}

// ============================================
// ELIMINAR CLIENTE
// ============================================
if ($accion === 'eliminar') {
    $id = intval($_POST['id_cliente'] ?? 0);
    
    $resultado = eliminarCliente($id);
    
    if ($resultado['success']) {
        header("Location: index.php?msg=" . urlencode($resultado['message']));
    } else {
        header("Location: index.php?error=" . urlencode($resultado['message']));
    }
    exit();
}

// Si no hay acción válida, redirigir
header("Location: index.php");
exit();