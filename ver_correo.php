<?php
/**
 * ============================================
 * ARCHIVO: ver_correo.php
 * Muestra el contenido completo de un correo
 * ============================================
 */

require_once 'config.php';

// Verificar que esté logueado
if (!estaLogueado()) {
    header("Location: index.php?error=Debes iniciar sesión");
    exit();
}

verificarTimeout();

// Obtener parámetros
$uid = isset($_GET['uid']) ? intval($_GET['uid']) : 0;
$email = isset($_GET['email']) ? filter_var($_GET['email'], FILTER_SANITIZE_EMAIL) : '';

if (!$uid || !$email) {
    die('<p style="color: red;">Parámetros inválidos</p>');
}

/**
 * Leer el contenido del correo
 */
function leerCorreo($uid) {
    try {
        // Conectar a IMAP
        $inbox = @imap_open(IMAP_HOST, IMAP_USER, IMAP_PASS, OP_READONLY);
        
        if (!$inbox) {
            throw new Exception('Error al conectar con el servidor de correo');
        }
        
        // Obtener información del correo
        $info = @imap_fetch_overview($inbox, $uid, FT_UID);
        if (!$info || !isset($info[0])) {
            throw new Exception('No se pudo obtener información del correo');
        }
        
        $correo = $info[0];
        
        // Decodificar asunto
        $asunto_partes = @imap_mime_header_decode($correo->subject ?? 'Sin asunto');
        $asunto = '';
        foreach ($asunto_partes as $parte) {
            $asunto .= $parte->text;
        }
        
        // Extraer remitente
        $remitente = preg_replace('/<.*?>/', '', $correo->from ?? 'Desconocido');
        
        // Formatear fecha en español: "26 dic 2025, 5:24 p.m."
        setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'spanish');
        $fecha_obj = strtotime($correo->date ?? 'now');
        $dia = date('j', $fecha_obj);
        $mes = strtolower(date('M', $fecha_obj));
        $ano = date('Y', $fecha_obj);
        $hora = date('g:i', $fecha_obj);
        $ampm = date('a', $fecha_obj) === 'am' ? 'a.m.' : 'p.m.';
        
        // Traducir mes al español
        $meses = [
            'jan' => 'ene', 'feb' => 'feb', 'mar' => 'mar', 'apr' => 'abr',
            'may' => 'may', 'jun' => 'jun', 'jul' => 'jul', 'aug' => 'ago',
            'sep' => 'sep', 'oct' => 'oct', 'nov' => 'nov', 'dec' => 'dic'
        ];
        $mes_es = $meses[$mes] ?? $mes;
        
        $fecha = "$dia $mes_es $ano, $hora $ampm";
        
        // Obtener estructura
        $estructura = @imap_fetchstructure($inbox, $uid, FT_UID);
        $contenido = '';
        
        // Si no tiene partes (correo simple)
        if (!$estructura || !isset($estructura->parts)) {
            $contenido = @imap_body($inbox, $uid, FT_UID);
            
            // Decodificar según encoding
            if ($estructura && $estructura->encoding == 3) {
                $contenido = base64_decode($contenido);
            } elseif ($estructura && $estructura->encoding == 4) {
                $contenido = quoted_printable_decode($contenido);
            }
        } else {
            // Correo con múltiples partes
            foreach ($estructura->parts as $indice => $parte) {
                // Buscar parte HTML o texto
                if (isset($parte->subtype)) {
                    if ($parte->subtype == 'HTML' || $parte->subtype == 'PLAIN') {
                        $parte_contenido = @imap_fetchbody($inbox, $uid, $indice + 1, FT_UID);
                        
                        // Decodificar
                        if ($parte->encoding == 3) {
                            $parte_contenido = base64_decode($parte_contenido);
                        } elseif ($parte->encoding == 4) {
                            $parte_contenido = quoted_printable_decode($parte_contenido);
                        }
                        
                        // Preferir HTML
                        if ($parte->subtype == 'HTML') {
                            $contenido = $parte_contenido;
                            break;
                        } elseif (empty($contenido)) {
                            $contenido = nl2br(htmlspecialchars($parte_contenido));
                        }
                    }
                }
            }
        }
        
        @imap_close($inbox);
        
        // Registrar visualización
        if (isset($_SESSION['user_id'])) {
            registrarLog($_SESSION['user_id'], 'ver_correo', "UID: $uid");
        }
        
        return [
            'asunto' => htmlspecialchars($asunto, ENT_QUOTES, 'UTF-8'),
            'remitente' => htmlspecialchars($remitente, ENT_QUOTES, 'UTF-8'),
            'fecha' => $fecha,
            'contenido' => $contenido
        ];
        
    } catch (Exception $e) {
        error_log("Error al leer correo: " . $e->getMessage());
        return [
            'error' => $e->getMessage()
        ];
    }
}

// Leer el correo
$datos = leerCorreo($uid);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contenido del Correo - Mundo Digital Premium</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="contenedor-ver-correo">
        <!-- Header con título y botón cerrar -->
        <div class="header-correo">
            <h1>📧 Contenido del Correo</h1>
            <button onclick="cerrarVentana()" class="btn-cerrar">✕ Cerrar</button>
        </div>
        
        <div class="tarjeta-correo">
            <?php if (isset($datos['error'])): ?>
                <div class="alerta alerta-error">
                    <?= htmlspecialchars($datos['error']) ?>
                </div>
            <?php else: ?>
                <!-- Información del correo -->
                <div class="info-correo-detalle">
                    <div class="info-item">
                        <span class="info-label">Para:</span>
                        <span class="info-valor"><?= strtolower(htmlspecialchars($email)) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">De:</span>
                        <span class="info-valor"><?= $datos['remitente'] ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Asunto:</span>
                        <span class="info-valor"><?= $datos['asunto'] ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Fecha:</span>
                        <span class="info-valor"><?= $datos['fecha'] ?></span>
                    </div>
                </div>
                
                <!-- Contenido del correo -->
                <div class="contenido-correo">
                    <?= $datos['contenido'] ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

<script>
// Función para cerrar la pestaña/ventana
function cerrarVentana() {
    // Intentar cerrar la pestaña
    window.close();
    
    // Si no se puede cerrar (pestaña no abierta por script), redirigir
    setTimeout(function() {
        window.location.href = 'index.php';
    }, 100);
}
</script>

</body>
</html>