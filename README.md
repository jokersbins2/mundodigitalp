# 🎬 MUNDO DIGITAL PREMIUM - Sistema de Códigos

Sistema simple y funcional para consultar correos electrónicos y gestionar usuarios.

## 📋 ¿Qué hace este sistema?

### Para ADMINISTRADORES:
- ✅ Consultar correos/códigos por email del cliente
- ✅ Agregar nuevos clientes
- ✅ Cambiar contraseñas de clientes
- ✅ Eliminar clientes

### Para CLIENTES:
- ✅ Consultar correos/códigos por email
- ✅ Ver contenido completo de los correos

## 🚀 Instalación Rápida

### Paso 1: Base de Datos
1. Abre **phpMyAdmin**
2. Click en "**SQL**"
3. Copia y pega todo el contenido de `database.sql`
4. Click en "**Continuar**"

### Paso 2: Configurar Credenciales
Abre el archivo `config.php` y ajusta:

```php
// Líneas 21-24: Base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'mundo_digital_premium');
define('DB_USER', 'root');        // Tu usuario MySQL
define('DB_PASS', 'root');        // Tu contraseña MySQL

// Líneas 29-31: Email IMAP (Gmail)
define('IMAP_USER', 'tu_email@gmail.com');
define('IMAP_PASS', 'tu_app_password');  // App Password de Gmail
```

#### ¿Cómo obtener App Password de Gmail?
1. Ve a https://myaccount.google.com/security
2. Activa "Verificación en 2 pasos"
3. Busca "Contraseñas de aplicaciones"
4. Genera una nueva contraseña para "Correo"
5. Copia la contraseña de 16 caracteres
6. Pégala en `IMAP_PASS`

### Paso 3: Subir Archivos
Sube TODOS estos archivos a tu servidor:
- `config.php`
- `funciones.php`
- `index.php`
- `procesar_usuarios.php`
- `ver_correo.php`
- `styles.css`

### Paso 4: Probar
1. Abre tu sitio en el navegador
2. Usuario: `admin`
3. Contraseña: `admin123`

## 📁 Archivos del Sistema

```
sistema/
├── config.php              ← Configuración general
├── funciones.php           ← Todas las funciones
├── index.php               ← Página principal
├── procesar_usuarios.php   ← Procesa agregar/editar/eliminar
├── ver_correo.php          ← Ver contenido del correo
├── styles.css              ← Estilos
└── database.sql            ← Base de datos
```

## 🔐 Usuarios de Prueba

**Administrador:**
- Usuario: `admin`
- Contraseña: `admin123`

**Clientes de ejemplo:**
- Usuario: `cliente1@streamingplus.ef`
- Usuario: `cliente2@streamingplus.ef`
- Contraseña: `admin123` (ambos)

**⚠️ IMPORTANTE:** Cambia la contraseña del admin después de instalar

## 📖 Cómo Usar el Sistema

### Como ADMIN:

#### 1. Consultar Correos
- Ingresa el email del cliente
- Click en "Buscar"
- Se mostrarán los correos del día de hoy
- Click en "Ver" para ver el contenido completo

#### 2. Agregar Cliente
- Ve a tab "Gestionar Usuarios"
- Completa usuario y contraseña
- Click en "Agregar"

#### 3. Cambiar Contraseña
- En la tabla de usuarios
- Click en "🔑 Cambiar"
- Ingresa nueva contraseña
- Click en "Actualizar Contraseña"

#### 4. Eliminar Cliente
- En la tabla de usuarios
- Click en "🗑️ Eliminar"
- Confirma la eliminación

### Como CLIENTE:

#### 1. Consultar Códigos
- Ingresa el email
- Click en "Buscar"
- Click en "Ver" para ver el correo completo

## ⚙️ Configuración Avanzada

### Cambiar Tiempo de Sesión
En `config.php` línea 37:
```php
define('SESSION_TIMEOUT', 18000); // 5 horas en segundos
```

### Cambiar Cantidad de Correos
En `funciones.php` línea 152:
```php
// Cambiar el 10 por la cantidad que desees
$emails_ids = array_slice($emails_ids, 0, 10);
```

### Buscar Correos de Más Días
En `funciones.php` línea 143, cambiar la búsqueda:
```php
// Para buscar de hace 7 días
$fecha = date('d-M-Y', strtotime('-7 days'));
$busqueda = 'TO "' . $email_destinatario . '" SINCE "' . $fecha . '"';
```

## 🔧 Solución de Problemas

### Error: "Error de conexión a la base de datos"
✅ Verifica credenciales en `config.php` líneas 21-24
✅ Asegúrate de haber ejecutado `database.sql`

### Error: "Error al conectar con el servidor de correo"
✅ Verifica credenciales IMAP en `config.php` líneas 29-31
✅ Asegúrate de usar "App Password" de Gmail, no tu contraseña normal
✅ Verifica que la extensión PHP IMAP esté instalada

### No se muestran correos
✅ Verifica que el email del cliente sea correcto
✅ Verifica que haya correos enviados HOY a ese email
✅ Si quieres buscar de más días atrás, modifica la función (ver arriba)

### La sesión expira muy rápido
✅ Aumenta el valor de `SESSION_TIMEOUT` en `config.php`

## 📝 Notas Importantes

1. **Extensión IMAP**: Este sistema requiere la extensión PHP IMAP
   - En cPanel/Hosting: Generalmente ya está instalada
   - En XAMPP/Local: Descomentar `extension=imap` en php.ini

2. **Gmail y Apps Password**: 
   - NO uses tu contraseña normal de Gmail
   - Debes generar una "App Password"
   - Requiere tener verificación en 2 pasos activada

3. **Seguridad**:
   - Cambia las contraseñas por defecto
   - Usa HTTPS en producción
   - No compartas las credenciales de base de datos

4. **Búsqueda de Correos**:
   - Solo busca correos del DÍA ACTUAL por defecto
   - Puedes modificar esto en `funciones.php`

## 🎯 Estructura del Sistema

### config.php
Contiene toda la configuración:
- Conexión a base de datos
- Credenciales IMAP
- Funciones de sesión
- Funciones auxiliares

### funciones.php
Todas las funciones principales:
- `autenticar()` - Login de usuarios
- `obtenerClientes()` - Lista de clientes
- `agregarCliente()` - Agregar nuevo cliente
- `actualizarPassword()` - Cambiar contraseña
- `eliminarCliente()` - Eliminar cliente
- `obtenerCorreos()` - Buscar correos por email

### index.php
Página principal que muestra:
- Formulario de login
- Dashboard con tabs (admin)
- Búsqueda de correos
- Gestión de usuarios

### procesar_usuarios.php
Procesa las acciones de:
- Agregar cliente
- Editar contraseña
- Eliminar cliente

### ver_correo.php
Muestra el contenido completo de un correo específico

## 💡 Tips

1. **Usuarios**: Pueden ser emails o nombres de usuario
2. **Contraseñas**: Mínimo 6 caracteres
3. **Búsqueda**: Solo muestra correos del día actual
4. **Sesión**: Expira después de 5 horas de inactividad

## ❓ Preguntas Frecuentes

**P: ¿Puedo buscar correos de días anteriores?**
R: Sí, modifica la búsqueda en `funciones.php` línea 143

**P: ¿Cuántos correos muestra como máximo?**
R: 10 correos, puedes cambiar esto en `funciones.php` línea 152

**P: ¿Funciona con otros servicios además de Gmail?**
R: Sí, solo cambia las credenciales IMAP en `config.php`

**P: ¿Se pueden agregar más administradores?**
R: Sí, directamente desde phpMyAdmin en la tabla `usuarios` con role='admin'

## 📞 Soporte

Si tienes problemas:
1. Revisa esta documentación
2. Verifica los archivos de configuración
3. Revisa la consola del navegador (F12) para errores

---

**Desarrollado para Mundo Digital Premium**  
**Versión:** 1.0  
**Fecha:** Diciembre 2025