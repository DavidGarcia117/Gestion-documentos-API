# API de Gestión y Radicación de Documentos

Esta es una solución técnica desarrollada en **Laravel 11** para automatizar el proceso de radicación, extracción de datos (OCR) y validación de facturas y contratos.

## Funcionalidades
- **Radicación Automática:** Generación de números de radicado únicos con formato `RAD-XXXXX`.
- **Extracción de Datos (OCR):** Integración con `smalot/pdfparser` para identificar NIT, números de contrato y montos totales.
- **Validación de Negocio:** Motor de reglas que verifica la integridad de la información extraída según el tipo de documento.
- **Sistema de Auditoría:** Registro detallado de cada acción y cambio de estado en la base de datos.
- **Simulación de Notificaciones:** Registro de envíos exitosos/fallidos en los logs del sistema.

## Aclaraciones de la Implementación

Para efectos de esta prueba técnica, se han tomado las siguientes decisiones de diseño:

1. **Extracción de Datos (OCR):** Se utiliza la librería `smalot/pdfparser` junto con expresiones regulares para identificar patrones de NIT, números de contrato y montos, garantizando la limpieza de caracteres especiales.
2. **Notificaciones:** Dado que es un entorno de prueba, las notificaciones se capturan mediante el driver `log`. Podrá verificar el "envío" de los correos revisando el archivo en: `storage/logs/laravel.log`.

## Requisitos Técnicos
- **PHP:** 8.2 o superior
- **Composer**
- **MySQL / MariaDB**

## Documentación de la API

### 1. Radicar Documento
Permite cargar un archivo (PDF/XML) para su procesamiento, extracción de datos y validación.

- **URL:** `/api/documents/filing`
- **Método:** `POST`
- **Tipo de contenido:** `multipart/form-data`

| Parámetro | Tipo | Obligatorio | Descripción |
| :--- | :--- | :--- | :--- |
| `file` | file | Sí | Archivo PDF o XML (Máx 10MB). |
| `document_type` | string | Sí | Valores: `contractor_invoice`, `supplier_invoice`, `general_invoice`. |
| `email` | string | Sí | Correo electrónico para notificaciones. |

#### Ejemplo de Respuesta Exitosa (201 Created)
```json
{
    "success": true,
    "filing_number": "RAD-A1B2C3D4"
}
Ejemplo de Error de Validación (422 Unprocessable Content)
{
    "success": false,
    "errors": [
        "El número de contrato es requerido.",
        "El NIT no es válido."
    ]
}
```

#### Instalación y Configuración

Siga estos pasos para ejecutar el proyecto en su entorno local:

1. Clonar el repositorio

git clone https://github.com/DavidGarcia117/Gestion-documentos-API.git
cd Gestion-documentos-API

2. Instalar dependencias de PHP

composer install

3. Configurar el archivo de entorno

cp .env.example .env

Nota: Edite el archivo .env y configure las credenciales de su base de datos local (DB_DATABASE, DB_USERNAME, DB_PASSWORD).

4. Generar la clave de aplicación y el enlace de almacenamiento

php artisan key:generate
php artisan storage:link

5. Ejecutar migraciones

php artisan migrate

6. Iniciar el servidor de desarrollo

php artisan serve