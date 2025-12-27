<?php
/**
 * ============================================
 * ARCHIVO: funciones.php
 * Funciones principales del sistema
 * ============================================
 */

require_once 'config.php';

// ============================================
// FUNCIONES DE AUTENTICACIÓN
// ============================================

/**
 * Autenticar usuario
 * @param string $username
 * @param string $password
 * @return array|false Datos del usuario o false
 */
function autenticar($username, $password) {
    try {
        $pdo = conectarDB();
        $sql = "SELECT id, username, password, role FROM usuarios WHERE username = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username]);
        $usuario = $stmt->fetch();
        
        // Verificar si existe y si la contraseña es correcta
        if ($usuario && password_verify($password, $usuario['password'])) {
            return $usuario;
        }
        
        return false;
    } catch (PDOException $e) {
        error_log("Error en autenticación: " . $e->getMessage());
        return false;
    }
}

// ============================================
// FUNCIONES DE GESTIÓN DE USUARIOS (ADMIN)
// ============================================

/**
 * Obtener todos los clientes
 * @return array
 */
function obtenerClientes() {
    try {
        $pdo = conectarDB();
        $sql = "SELECT id, username, password_plain, created_at FROM usuarios 
                WHERE role = 'cliente' ORDER BY created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error al obtener clientes: " . $e->getMessage());
        return [];
    }
}

/**
 * Agregar nuevo cliente
 * @param string $username
 * @param string $password
 * @return array [success, message]
 */
function agregarCliente($username, $password) {
    try {
        $pdo = conectarDB();
        
        // Validar que el username no esté vacío y tenga mínimo 3 caracteres
        if (strlen($username) < 3) {
            return ['success' => false, 'message' => 'El usuario debe tener mínimo 3 caracteres'];
        }
        
        // Validar contraseña mínimo 6 caracteres
        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'La contraseña debe tener mínimo 6 caracteres'];
        }
        
        // Verificar si ya existe
        $sql = "SELECT id FROM usuarios WHERE username = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username]);
        
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Este usuario ya existe'];
        }
        
        // Insertar nuevo cliente
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (username, password, password_plain, role) VALUES (?, ?, ?, 'cliente')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username, $password_hash, $password]);
        
        // Registrar en logs
        if (isset($_SESSION['user_id'])) {
            registrarLog($_SESSION['user_id'], 'agregar_cliente', "Usuario: $username");
        }
        
        return ['success' => true, 'message' => 'Cliente agregado correctamente'];
        
    } catch (PDOException $e) {
        error_log("Error al agregar cliente: " . $e->getMessage());
        return ['success' => false, 'message' => 'Error al agregar cliente'];
    }
}

/**
 * Actualizar contraseña de un cliente
 * @param int $id
 * @param string $nueva_password
 * @return array [success, message]
 */
function actualizarPassword($id, $nueva_password) {
    try {
        $pdo = conectarDB();
        
        // Validar contraseña
        if (strlen($nueva_password) < 6) {
            return ['success' => false, 'message' => 'La contraseña debe tener mínimo 6 caracteres'];
        }
        
        // Verificar que sea un cliente (no admin)
        $sql = "SELECT role, username FROM usuarios WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $usuario = $stmt->fetch();
        
        if (!$usuario) {
            return ['success' => false, 'message' => 'Usuario no encontrado'];
        }
        
        if ($usuario['role'] === 'admin') {
            return ['success' => false, 'message' => 'No se puede modificar un administrador'];
        }
        
        // Actualizar contraseña
        $password_hash = password_hash($nueva_password, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET password = ?, password_plain = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$password_hash, $nueva_password, $id]);
        
        // Registrar en logs
        if (isset($_SESSION['user_id'])) {
            registrarLog($_SESSION['user_id'], 'actualizar_password', "Usuario ID: $id");
        }
        
        return ['success' => true, 'message' => 'Contraseña actualizada correctamente'];
        
    } catch (PDOException $e) {
        error_log("Error al actualizar contraseña: " . $e->getMessage());
        return ['success' => false, 'message' => 'Error al actualizar contraseña'];
    }
}

/**
 * Eliminar un cliente
 * @param int $id
 * @return array [success, message]
 */
function eliminarCliente($id) {
    try {
        $pdo = conectarDB();
        
        // Verificar que sea un cliente (no admin)
        $sql = "SELECT role, username FROM usuarios WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $usuario = $stmt->fetch();
        
        if (!$usuario) {
            return ['success' => false, 'message' => 'Usuario no encontrado'];
        }
        
        if ($usuario['role'] === 'admin') {
            return ['success' => false, 'message' => 'No se puede eliminar un administrador'];
        }
        
        // Eliminar cliente
        $sql = "DELETE FROM usuarios WHERE id = ? AND role = 'cliente'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        
        // Registrar en logs
        if (isset($_SESSION['user_id'])) {
            registrarLog($_SESSION['user_id'], 'eliminar_cliente', "Usuario: {$usuario['username']}");
        }
        
        return ['success' => true, 'message' => 'Cliente eliminado correctamente'];
        
    } catch (PDOException $e) {
        error_log("Error al eliminar cliente: " . $e->getMessage());
        return ['success' => false, 'message' => 'Error al eliminar cliente'];
    }
}

// ============================================
// FUNCIONES DE CORREO (IMAP)
// ============================================

/**
 * Obtener correos recientes para un destinatario
 * @param string $email_destinatario
 * @return string HTML con tabla de correos
 */
function obtenerCorreos($email_destinatario) {
    try {
        // Validar email
        $email_destinatario = filter_var($email_destinatario, FILTER_SANITIZE_EMAIL);
        if (!filter_var($email_destinatario, FILTER_VALIDATE_EMAIL)) {
            return '<div class="alerta alerta-error">Email inválido</div>';
        }
        
        // Conectar a IMAP
        $inbox = @imap_open(IMAP_HOST, IMAP_USER, IMAP_PASS, OP_READONLY);
        
        if (!$inbox) {
            return '<div class="alerta alerta-error">Error al conectar con el servidor de correo</div>';
        }
        
        // Buscar correos del día de hoy para este destinatario
        $fecha_hoy = date('d-M-Y');
        $busqueda = 'TO "' . $email_destinatario . '" SINCE "' . $fecha_hoy . '"';
        $emails_ids = @imap_search($inbox, $busqueda, SE_UID);
        
        if (!$emails_ids) {
            @imap_close($inbox);
            return '<div class="alerta alerta-info">No se encontraron correos recientes para este email</div>';
        }
        
        // Ordenar del más reciente al más antiguo
        rsort($emails_ids);
        
        // Limitar a los últimos 2 correos
        $emails_ids = array_slice($emails_ids, 0, 2);
        
        // Obtener información de cada correo
        $correos = [];
        foreach ($emails_ids as $uid) {
            $info = @imap_fetch_overview($inbox, $uid, FT_UID);
            if ($info && isset($info[0])) {
                $correo = $info[0];
                
                // Decodificar asunto
                $asunto_partes = @imap_mime_header_decode($correo->subject ?? 'Sin asunto');
                $asunto = '';
                foreach ($asunto_partes as $parte) {
                    $asunto .= $parte->text;
                }
                
                // Extraer nombre del remitente
                $remitente = preg_replace('/<.*?>/', '', $correo->from ?? 'Desconocido');
                
                // Formatear fecha
                $fecha = date('d/m/Y H:i', strtotime($correo->date ?? 'now'));
                
                $correos[] = [
                    'uid' => $uid,
                    'remitente' => htmlspecialchars($remitente, ENT_QUOTES, 'UTF-8'),
                    'asunto' => htmlspecialchars($asunto, ENT_QUOTES, 'UTF-8'),
                    'fecha' => $fecha
                ];
            }
        }
        
        @imap_close($inbox);
        
        // Registrar consulta
        if (isset($_SESSION['user_id'])) {
            registrarLog($_SESSION['user_id'], 'consultar_correos', "Email: $email_destinatario");
        }
        
        // Generar HTML
        return generarTablaCorreos($correos, $email_destinatario);
        
    } catch (Exception $e) {
        error_log("Error al obtener correos: " . $e->getMessage());
        return '<div class="alerta alerta-error">Error al buscar correos</div>';
    }
}

/**
 * Generar HTML de tabla de correos
 * @param array $correos
 * @param string $email
 * @return string
 */
function generarTablaCorreos($correos, $email) {
    if (empty($correos)) {
        return '<div class="alerta alerta-info">No se encontraron correos</div>';
    }
    
    $html = '<div class="resultado-correos">';
    $html .= '<p class="contador-resultados">📧 Se encontraron ' . count($correos) . ' correo(s)</p>';
    $html .= '<table class="tabla-correos">';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th>Remitente</th>';
    $html .= '<th>Asunto</th>';
    $html .= '<th>Fecha</th>';
    $html .= '<th>Ver</th>';
    $html .= '</tr>';
    $html .= '</thead>';
    $html .= '<tbody>';
    
    foreach ($correos as $correo) {
        $html .= '<tr>';
        $html .= '<td>' . $correo['remitente'] . '</td>';
        $html .= '<td>' . $correo['asunto'] . '</td>';
        $html .= '<td>' . $correo['fecha'] . '</td>';
        $html .= '<td>';
        $html .= '<a href="ver_correo.php?uid=' . $correo['uid'] . '&email=' . urlencode($email) . '" ';
        $html .= 'target="_blank" class="btn-ver">Ver</a>';
        $html .= '</td>';
        $html .= '</tr>';
    }
    
    $html .= '</tbody>';
    $html .= '</table>';
    $html .= '</div>';
    
    return $html;
}