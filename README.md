# 🏥 Droguería DAS - Sistema de Gestión

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)

## 📖 Sobre el Proyecto

**Plataforma de Gestión Inteligente de la Droguería Comunal** es un sistema web transaccional desarrollado para la Dirección de Administración de Salud (DAS) de Talcahuano. Centraliza el abastecimiento farmacéutico comunal, reemplazando planillas dispersas por un control estricto que incluye metodología FEFO, trazabilidad inmutable, visor 3D de la bodega y control legal de estupefacientes.

## ✨ Características Principales

- **Despacho Inteligente (FEFO):** Asignación automática server-side del lote con la fecha de vencimiento más próxima (First Expired, First Out).
- **Despachos Controlados:** Módulo segregado exclusivo para estupefacientes, validado contra solicitudes de CESFAM y registro de recetas cheque.
- **Distribución 3D:** Visor interactivo (Three.js) de pasillos, estanterías, niveles y posiciones, con cálculo de ocupación porcentual en tiempo real.
- **Trazabilidad Inmutable:** Registro detallado de todo movimiento de stock (usuario, lote, documento de respaldo, cantidad y fecha).
- **Gestión de Lotes y Códigos QR:** Generación automática de etiquetas QR en SVG para cada lote físico.
- **Roles y Permisos por Vista:** Control de acceso granular (Spatie) para Administrador, Químico Farmacéutico y Operador/Bodeguero.

## 🛠️ Stack Tecnológico

- **Backend:** Laravel 13 (PHP 8.3+)
- **Frontend:** Blade, Tailwind CSS 3.4, Alpine.js (Vite)
- **Base de Datos:** MySQL 8
- **Librerías Clave:** Three.js (3D), html5-qrcode (Escáner), SignaturePad (Firmas), DomPDF, Laravel Excel.

---

## 👨‍💻 Guía para Desarrolladores (Modo Desarrollo)

Esta sección está documentada para el próximo desarrollador que asuma el mantenimiento o evolución del sistema. 

### 1. Requisitos Previos del Entorno
- PHP >= 8.3
- Composer v2
- Node.js (v18+) y NPM
- MySQL 8 o MariaDB equivalente
- Git

### 2. Instalación Paso a Paso

```bash
# 1. Clonar el repositorio
git clone [https://github.com/Faundez21/Proyecto_Drogueria.git]
cd drogueria-das

# 2. Instalar dependencias del backend
composer install

# 3. Instalar dependencias del frontend
npm install

# 4. Configurar variables de entorno
cp .env.example .env