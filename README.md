# Sistema de Control de Oficios

Sistema web para el registro, control y seguimiento de oficios dentro de un entorno institucional.

SCo surge de la necesidad de estructurar la **trazabilidad operativa** de los oficios. Aunque la organización ya contaba con mecanismos para registrar y consultar información básica, el seguimiento posterior dependía parcialmente de comunicación entre áreas, archivos y otros medios, dificultando conocer de forma estructurada el responsable, estado e historial de cada oficio.

El sistema centraliza esta información y representa el ciclo operativo mediante estados, movimientos, responsables y documentos asociados.

El desarrollo considera las reglas y procesos existentes de la organización, buscando adaptar la solución tecnológica al funcionamiento operativo del entorno en lugar de imponer un flujo independiente de este.

---

## Objetivo

Diseñar e implementar un sistema web que permita registrar, estructurar y dar seguimiento operativo a los oficios institucionales, proporcionando trazabilidad sobre sus responsables, movimientos y estados durante su ciclo de atención.

---

## Alcance

La primera versión contempla:

* Registro y consulta de oficios.
* Gestión diferenciada de oficios enviados y recibidos.
* Asignación y seguimiento de responsables.
* Turnados y re-turnados.
* Control de estados operativos.
* Historial de acciones y movimientos.
* Gestión de documentos asociados.
* Relaciones entre oficios.
* Gestión de usuarios, roles y permisos.
* Clasificación mediante etiquetas.
* Reserva de folios institucionales.
* Versionado de documentos PDF.

El sistema se plantea como un producto evolutivo, cuyo alcance puede ampliarse conforme el uso permita identificar necesidades operativas concretas.

---

# Problema y contexto

El seguimiento de los oficios ya contaba con mecanismos de registro y consulta, principalmente mediante archivos estructurados que concentraban información básica.

Sin embargo, estos mecanismos resultaban limitados para representar lo que ocurre después del registro.

Cuando un oficio pasa por diferentes responsables o áreas, la información disponible no necesariamente permite reconstruir de forma estructurada:

* quién tiene actualmente la responsabilidad;
* qué movimientos ha tenido;
* qué áreas o personas han intervenido;
* en qué estado operativo se encuentra;
* qué acciones se han realizado;
* cuál es el historial que explica su situación actual.

El problema identificado, por tanto, no era únicamente la digitalización de información, sino la falta de una **representación estructurada de la trazabilidad operativa**.

---

# Enfoque de diseño

El proyecto se desarrolló bajo un enfoque **Lean**, priorizando la reducción de incertidumbre operativa y evitando incorporar complejidad antes de que exista una necesidad real.

El diseño parte de los procesos y mecanismos existentes y los estructura progresivamente dentro del sistema.

Los principales criterios fueron:

* **Trazabilidad:** conservar los movimientos relevantes del oficio.
* **Claridad:** identificar responsable y estado actual.
* **Consistencia:** representar el flujo mediante reglas explícitas.
* **Adaptabilidad:** reducir la dependencia de personas o estructuras rígidas.
* **Evolución:** ampliar el sistema conforme exista evidencia de nuevas necesidades.

---

# Características principales

### Gestión de oficios

* Registro y consulta de oficios.
* Gestión diferenciada de enviados y recibidos.
* Generación automática de consecutivos para oficios enviados.
* Registro del número de origen para oficios recibidos.
* Reserva de folios institucionales.
* Clasificación mediante etiquetas.

### Seguimiento operativo

* Turnado inicial por coordinación.
* Turnados y re-turnados.
* Seguimiento mediante estados.
* Historial de acciones y movimientos.
* Identificación del responsable actual.
* Conservación de registros sin eliminación física.

### Gestión documental

* Asociación de documentos PDF.
* Versionado de documentos.
* Relaciones documentales entre oficios.
* Conservación de antecedentes y contexto documental.

### Seguridad y acceso

* Usuarios, roles y permisos.
* Middleware y policies para autorización.
* Restricción de acciones según estado y permisos.

---

# Decisiones de diseño

## Ciclo de vida

El ciclo operativo de un oficio se representa mediante estados:

```text
Registro → Turnado → Atendido → Cerrado
                    ↘
                     Cancelado
```

Las acciones disponibles dependen del estado actual del oficio y de los permisos del usuario.

---

## Oficios enviados y recibidos

Los oficios enviados y recibidos siguen reglas diferentes de identificación.

Los **oficios enviados** utilizan un consecutivo institucional generado por el sistema, mientras que los **oficios recibidos** conservan el número asignado por la institución emisora.

Esta separación permite mantener las reglas propias de cada flujo documental.

---

## Turnado por coordinación

El turnado inicial se realiza a nivel de **coordinación**, en lugar de depender directamente de una persona específica.

La coordinación determina posteriormente al responsable operativo correspondiente.

Esta decisión reduce la dependencia de usuarios individuales y permite adaptar el sistema a cambios de personal u organización.

---

## Trazabilidad y registros

Los registros no se eliminan físicamente como parte de la operación normal.

Esto permite conservar la información necesaria para reconstruir el seguimiento de un oficio y mantener su historial operativo.

---

## Relaciones entre oficios

Los oficios pueden relacionarse entre sí para conservar antecedentes y contexto documental.

Las relaciones se mantienen independientes del estado operativo de cada oficio, permitiendo consultar antecedentes sin alterar su ciclo de vida.

---

## Gestión de documentos

Los archivos PDF se almacenan en el sistema de archivos de la aplicación, mientras que la base de datos conserva las referencias necesarias para localizarlos.

Esta separación mantiene diferenciada la información estructurada del oficio respecto de su representación documental y facilita tareas de administración y respaldo.

---

# Arquitectura

La aplicación está desarrollada sobre **Laravel** siguiendo una arquitectura MVC.

La estructura del proyecto separa responsabilidades mediante:

* Models
* Controllers
* Services
* Policies
* Middleware
* Views Blade

La lógica de autorización se complementa mediante roles, permisos, middleware y policies.

La implementación actual mantiene parte de la lógica de negocio en los controladores y utiliza servicios para determinados procesos. Esta decisión corresponde al estado actual del proyecto y permite evolucionar la arquitectura conforme aumente la complejidad del sistema.

---

# Mi participación

SCo fue desarrollado íntegramente por mí, desde el análisis del proceso hasta la implementación y documentación.

Mi participación incluyó:

* Análisis del proceso existente y de las necesidades de trazabilidad.
* Consulta con **Oficialía de Partes**, particularmente con recepción y archivo, para comprender las reglas operativas.
* Traducción de las reglas de negocio definidas por las áreas involucradas a estructuras y comportamientos del sistema.
* Diseño de la base de datos a partir de la información que la organización ya generaba mediante Excel, manteniendo estructuras compatibles para facilitar la incorporación de datos históricos.
* Diseño del flujo de estados, turnados, responsables y relaciones entre oficios.
* Desarrollo completo del backend y frontend.
* Implementación de autenticación, roles, permisos y reglas de autorización.
* Implementación de gestión documental y versionado de PDFs.
* Desarrollo del modelo de relaciones entre oficios.
* Implementación del mecanismo para distribuir los oficios turnados en las bandejas correspondientes.
* Elaboración de la documentación técnica y operativa.

Las reglas de negocio fueron definidas a partir del conocimiento operativo de las áreas involucradas. Mi responsabilidad fue comprenderlas, modelarlas técnicamente y convertirlas en un sistema funcional.

La arquitectura se diseñó buscando entregar un **MVP funcional en un periodo reducido**, manteniendo suficiente flexibilidad para evolucionar posteriormente sin sobrediseñar la primera versión.

---

# Stack tecnológico

* PHP 8.3+
* Laravel 12
* MySQL
* Blade
* Bootstrap 4
* SB Admin 2
* Laravel Breeze
* Vite
* Node.js
* npm
* Composer

---

# Instalación

## Requisitos

* PHP 8.3+
* Composer
* MySQL
* Node.js LTS
* npm

## 1. Clonar repositorio

```bash
git clone <URL_DEL_REPOSITORIO>
```

## 2. Entrar al proyecto

```bash
cd sistema-control-oficios
```

## 3. Instalar dependencias

```bash
composer install
npm install
```

## 4. Configurar entorno

Copiar el archivo de configuración:

```bash
cp .env.example .env
```

Configurar en `.env` la conexión a MySQL:

```text
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

Generar la clave de aplicación:

```bash
php artisan key:generate
```

## 5. Crear base de datos

Para una instalación limpia:

```bash
php artisan migrate:fresh --seed
```

Esto crea las tablas, catálogos, coordinaciones, roles, permisos y el usuario administrador inicial definidos por los seeders.

> `migrate:fresh` elimina las tablas existentes de la base de datos. Utilizarlo únicamente cuando se requiera reconstruir la base de datos del entorno.

## 6. Compilar assets

Para desarrollo:

```bash
npm run dev
```

Para producción:

```bash
npm run build
```

## 7. Levantar el servidor

```bash
php artisan serve
```

Abrir:

```text
http://127.0.0.1:8000
```

---

# Usuario administrador inicial

El usuario administrador inicial se genera mediante el seeder correspondiente.

La configuración utilizada para su creación se encuentra en:

```text
database/seeders/UsersSeeder.php
```

Las credenciales definidas para desarrollo deben tratarse como credenciales de prueba y no utilizarse directamente en producción.

---

# Estado actual

El sistema está diseñado para trabajar con una estructura organizacional que puede evolucionar con el tiempo.

Las coordinaciones utilizadas actualmente pueden considerarse provisionales para el MVP y están sujetas a revisión durante la estabilización operativa.

La nomenclatura institucional de oficios, como:

```text
ST
CA
CVIYSC
UIG
CC-SEA
```

es independiente de las coordinaciones operativas utilizadas para la asignación de responsables.

Las decisiones de implementación corresponden al estado actual del sistema y pueden evolucionar conforme cambien las necesidades operativas o se identifiquen oportunidades de mejora.

---

# Documentación

La documentación técnica y operativa se encuentra organizada de forma independiente e incluye:

* Arquitectura y estructura del sistema.
* Módulos y funcionalidades.
* Modelo de datos.
* Roles y permisos.
* Rutas principales.
* Mantenimiento.
* Operación y soporte.
* Comandos frecuentes.
* Tareas programadas.
* Catálogo de tablas.
* Diccionario de datos.
* Solución de problemas frecuentes.
* Decisiones técnicas.

La documentación de decisiones conserva el contexto y la justificación de los principales criterios adoptados durante el desarrollo.
