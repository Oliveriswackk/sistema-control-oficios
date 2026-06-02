# Instalación del proyecto

## 1. Clonar repositorio

```bash
git clone <URL_DEL_REPOSITORIO>
```

---

## 2. Entrar al proyecto

```bash
cd sistema-control-oficios
```

---

# Requisitos

- PHP 8.3+
- Composer
- MySQL
- Node.js LTS
- npm

Descargar Node.js:

https://nodejs.org/

---

## 3. Instalar dependencias PHP

```bash
composer install
```

---

## 4. Configurar entorno

Copiar archivo `.env`

```bash
cp .env.example .env
```

Configurar:
- conexión MySQL
- nombre de base de datos
- usuario
- contraseña

---

## 5. Generar APP_KEY

```bash
php artisan key:generate
```

---

## 6. Instalar Laravel Breeze

```bash
composer require laravel/breeze --dev
```

```bash
php artisan breeze:install
```

Seleccionar:

```text
Blade
```

Dark mode:

```text
No
```

Testing framework:

```text
PHPUnit
```

---

## 7. Instalar dependencias frontend

```bash
npm install
```

---

## 8. Compilar assets frontend

```bash
npm run dev
```

---

## 9. Ejecutar migraciones y seeders

```bash
php artisan migrate:fresh --seed
```

Esto crea:
- tablas
- catálogos
- coordinaciones
- roles
- permisos
- usuario administrador inicial

---

## 10. Levantar servidor

```bash
php artisan serve
```

Abrir:

```text
http://127.0.0.1:8000
```

---

# Usuario administrador inicial

```text
Correo:
asesor.sesea.chihuahua@gmail.com
```

La contraseña se define en:

```text
database/seeders/UsersSeeder.php
```

---

# Stack tecnológico

- Laravel 12
- PHP 8.3
- MySQL
- Bootstrap 5
- SB Admin 2
- Laravel Breeze
- Vite

---

# Notas importantes

- El sistema utiliza:
  - roles
  - permisos flexibles
  - middleware
  - policies

- Los oficios NO dependen del PDF.
- Todo cambio genera trazabilidad.
- No existe borrado físico.
- Los PDFs mantienen historial de versiones.
- El sistema soporta atención paralela mediante turnados.

```text
Las coordinaciones actuales son provisionales para el MVP.

La estructura organizacional definitiva será revisada
durante la etapa de estabilización operativa.

La nomenclatura institucional de oficios (ST, CA, CVIYSC,
UIG, CC-SEA, etc.) es independiente de las coordinaciones
operativas utilizadas para asignación de responsables.
```