<?php
/**
 * ============================================
 * ARCHIVO: test_conexion.php
 * Verifica la conexión y usuarios
 * ELIMINAR después de probar
 * ============================================
 */

echo "<h2>🔍 Diagnóstico del Sistema</h2>";
echo "<hr>";

// 1. Probar conexión a base de datos
echo "<h3>1. Probando conexión a base de datos...</h3>";
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=mundo_digital_premium;charset=utf8mb4",
        "root",
        "root"
    );
    echo "✅ Conexión exitosa a la base de datos<br><br>";
    
    // 2. Verificar tabla usuarios
    echo "<h3>2. Verificando tabla usuarios...</h3>";
    $stmt = $pdo->query("SELECT id, username, password, role FROM usuarios");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($usuarios) > 0) {
        echo "✅ Se encontraron " . count($usuarios) . " usuario(s):<br>";
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse; margin-top: 10px;'>";
        echo "<tr style='background: #f0f0f0;'>";
        echo "<th>ID</th><th>Username</th><th>Password Hash</th><th>Role</th>";
        echo "</tr>";
        
        foreach ($usuarios as $user) {
            echo "<tr>";
            echo "<td>" . $user['id'] . "</td>";
            echo "<td><strong>" . htmlspecialchars($user['username']) . "</strong></td>";
            echo "<td style='font-size: 10px;'>" . substr($user['password'], 0, 50) . "...</td>";
            echo "<td>" . $user['role'] . "</td>";
            echo "</tr>";
        }
        echo "</table><br>";
    } else {
        echo "❌ No hay usuarios en la tabla<br>";
        echo "👉 Ejecuta el archivo database.sql en phpMyAdmin<br><br>";
    }
    
    // 3. Verificar password
    echo "<h3>3. Verificando password de 'admin'...</h3>";
    $stmt = $pdo->prepare("SELECT password FROM usuarios WHERE username = ?");
    $stmt->execute(['admin']);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        $password_correcto = 'admin123';
        $hash_bd = $admin['password'];
        
        echo "Password en BD: <code>" . htmlspecialchars($hash_bd) . "</code><br>";
        echo "Probando con password: <strong>admin123</strong><br>";
        
        if (password_verify($password_correcto, $hash_bd)) {
            echo "✅ <span style='color: green; font-weight: bold;'>¡El password es CORRECTO!</span><br>";
            echo "El problema NO es el password.<br><br>";
        } else {
            echo "❌ <span style='color: red; font-weight: bold;'>El password NO coincide</span><br>";
            echo "👉 Necesitas actualizar el hash del admin.<br><br>";
            
            // Generar nuevo hash
            $nuevo_hash = password_hash('admin123', PASSWORD_DEFAULT);
            echo "<h4>Solución:</h4>";
            echo "Ejecuta este SQL en phpMyAdmin:<br>";
            echo "<textarea style='width: 100%; height: 80px; font-family: monospace;'>";
            echo "UPDATE usuarios SET password = '$nuevo_hash' WHERE username = 'admin';";
            echo "</textarea><br><br>";
        }
    } else {
        echo "❌ No se encontró el usuario 'admin'<br>";
        echo "👉 Ejecuta el archivo database.sql<br><br>";
    }
    
    // 4. Probar autenticación completa
    echo "<h3>4. Probando función de autenticación...</h3>";
    
    $username_test = 'admin';
    $password_test = 'admin123';
    
    $stmt = $pdo->prepare("SELECT id, username, password, role FROM usuarios WHERE username = ?");
    $stmt->execute([$username_test]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($usuario && password_verify($password_test, $usuario['password'])) {
        echo "✅ <span style='color: green; font-weight: bold;'>¡Autenticación EXITOSA!</span><br>";
        echo "Usuario: " . $usuario['username'] . "<br>";
        echo "Role: " . $usuario['role'] . "<br>";
        echo "<br>👉 El sistema debería funcionar correctamente.<br>";
        echo "Si sigue sin funcionar, revisa el archivo config.php<br><br>";
    } else {
        echo "❌ <span style='color: red; font-weight: bold;'>Autenticación FALLIDA</span><br><br>";
    }
    
} catch (PDOException $e) {
    echo "❌ Error de conexión: " . $e->getMessage() . "<br>";
    echo "<br><strong>Posibles soluciones:</strong><br>";
    echo "1. Verifica que MySQL esté corriendo<br>";
    echo "2. Verifica el usuario y contraseña en config.php<br>";
    echo "3. Verifica que la base de datos 'mundo_digital_premium' existe<br>";
}

echo "<hr>";
echo "<h3>📋 Configuración actual en config.php:</h3>";
echo "Host: localhost<br>";
echo "Base de datos: mundo_digital_premium<br>";
echo "Usuario: root<br>";
echo "Contraseña: root<br>";
echo "<br>";
echo "<strong style='color: red;'>⚠️ IMPORTANTE: Elimina este archivo (test_conexion.php) después de probar</strong>";
?>