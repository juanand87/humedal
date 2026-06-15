# Solución al Error de MySQL en XAMPP

## Problema
```
Host 'localhost' is not allowed to connect to this MariaDB server
```

## Soluciones (Probar en orden)

### Solución 1: Reiniciar MySQL (Más rápida)
1. Abre el **Panel de Control de XAMPP**
2. Haz clic en **Stop** en MySQL/MariaDB
3. Espera 5 segundos
4. Haz clic en **Start** en MySQL/MariaDB
5. Prueba acceder a phpMyAdmin: http://localhost/phpmyadmin

**✅ Si funciona:** ¡Listo! Ya puedes usar tu aplicación.

---

### Solución 2: Configurar permisos manualmente (Recomendada)

#### Paso 1: Detener MySQL
- En el Panel de Control de XAMPP, haz clic en **Stop** junto a MySQL

#### Paso 2: Editar archivo de configuración
1. Abre el archivo: `C:\xampp\mysql\bin\my.ini`
2. Busca la sección `[mysqld]` (suele estar al principio)
3. Añade esta línea justo debajo de `[mysqld]`:
   ```ini
   skip-grant-tables
   ```
4. **Guarda el archivo**

#### Paso 3: Iniciar MySQL en modo seguro
- En el Panel de Control de XAMPP, haz clic en **Start** junto a MySQL

#### Paso 4: Ejecutar script de reparación
1. Abre **PowerShell** o **CMD** como Administrador
2. Ejecuta este comando:
   ```powershell
   cd C:\xampp\mysql\bin
   Get-Content C:\xampp\htdocs\humedal\fix_mysql.sql | .\mysql.exe -u root
   ```
   
   O si prefieres ejecutarlo manualmente:
   ```powershell
   cd C:\xampp\mysql\bin
   .\mysql.exe -u root
   ```
   
   Luego copia y pega el contenido del archivo `fix_mysql.sql`

#### Paso 5: Remover modo seguro
1. Abre nuevamente: `C:\xampp\mysql\bin\my.ini`
2. **ELIMINA** o comenta (con #) la línea: `skip-grant-tables`
3. Guarda el archivo

#### Paso 6: Reiniciar MySQL normalmente
- En XAMPP: **Stop** y luego **Start** en MySQL

#### Paso 7: Verificar
- Accede a phpMyAdmin: http://localhost/phpmyadmin
- Deberías poder entrar sin problemas

---

### Solución 3: Reinstalar MySQL en XAMPP (Última opción)

Si nada funciona:
1. Haz backup de tu base de datos si es posible
2. En XAMPP, detén todos los servicios
3. Desinstala XAMPP
4. Descarga la última versión desde: https://www.apachefriends.org/
5. Reinstala XAMPP
6. Crea la base de datos con el script `fix_mysql.sql`

---

## Cambios realizados en tu proyecto

He actualizado estos archivos para usar `127.0.0.1` en lugar de `localhost`:
- ✅ `panel/config.php` - Cambió de 'localhost' a '127.0.0.1'
- ✅ `src/config.php` - Cambió de 'localhost' a '127.0.0.1'

Esto suele resolver el problema en el 90% de los casos.

---

## Verificar que funciona

Después de aplicar cualquier solución, verifica:

1. **phpMyAdmin:** http://localhost/phpmyadmin
2. **Tu aplicación:** http://localhost/humedal

---

## Comandos útiles

### Ver estado de MySQL en PowerShell:
```powershell
cd C:\xampp\mysql\bin
.\mysql.exe -h 127.0.0.1 -u root -e "SELECT 'Conexión exitosa' AS resultado"
```

### Crear base de datos manualmente:
```powershell
cd C:\xampp\mysql\bin
.\mysql.exe -h 127.0.0.1 -u root -e "CREATE DATABASE IF NOT EXISTS humedal"
```

---

## Notas importantes

- ⚠️ **No olvides** remover `skip-grant-tables` del archivo `my.ini` después de reparar (Paso 5)
- 🔒 Dejar `skip-grant-tables` activo es un **riesgo de seguridad**
- 📝 El cambio a `127.0.0.1` es permanente y seguro
- ✅ Después de aplicar las soluciones, todo debería funcionar normalmente

---

## ¿Necesitas más ayuda?

Si el problema persiste después de probar todas las soluciones, puede haber un problema con:
- El puerto 3306 está siendo usado por otro programa
- Firewall bloqueando la conexión
- Archivos corruptos de MySQL en XAMPP

En ese caso, revisa los logs de MySQL en: `C:\xampp\mysql\data\mysql_error.log`
