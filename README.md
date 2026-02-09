# API de Gestión y Radicación de Documentos

Esta es una solución técnica desarrollada en **Laravel 11** para automatizar el proceso de radicación, extracción de datos (OCR) y validación de facturas y contratos.

## Funcionalidades
- **Radicación Automática:** Generación de números de radicado únicos con formato `RAD-XXXXX`.
- **Extracción de Datos (OCR):** Integración con `smalot/pdfparser` para identificar NIT, números de contrato y montos totales.
- **Validación de Negocio:** Motor de reglas que verifica la integridad de la información extraída según el tipo de documento.
- **Sistema de Auditoría:** Registro detallado de cada acción y cambio de estado en la base de datos.
- **Simulación de Notificaciones:** Registro de envíos exitosos/fallidos en los logs del sistema.

## 📝 Aclaraciones de la Implementación

Para efectos de esta prueba técnica, se han tomado las siguientes decisiones de diseño:

**Notificaciones:** Dado que es un entorno de prueba, no se configuró un servidor SMTP real. Las notificaciones (confirmación de radicación o rechazo) se están capturando mediante el driver `log`. Podrá verificar el "envío" de los correos revisando el archivo en: `storage/logs/laravel.log`.

## Requisitos Técnicos
- **PHP:** 8.2 o superior
- **Composer**
- **MySQL / MariaDB**
