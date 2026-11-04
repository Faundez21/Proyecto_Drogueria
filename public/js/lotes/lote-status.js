/**
 * ============================================================================
 *  LoteStatus — Gestión de estados de lote con SweetAlert2 + AJAX (async/await)
 * ============================================================================
 *  Escenarios:
 *    - LoteStatus.Inventario  -> Cambio de estado / reubicación desde Inventario
 *    - LoteStatus.Cuarentena  -> Bloqueo preventivo y control técnico
 *    - LoteStatus.Merma       -> Declaración formal de merma (y salida de merma)
 *
 *  Flujo común:
 *    1) Validación local          (Swal warning)
 *    2) Confirmación previa       (await Swal.fire -> isConfirmed)
 *    3) Envío AJAX con fetch      (PUT /inventory/{id}, Accept: application/json)
 *    4) Alerta de éxito           (await Swal.fire -> comprobante del movimiento)
 *    5) Refresco de datos SOLO después de que el usuario cierre la alerta
 *
 *  Incluye diseño adaptable a Modo Claro y Modo Oscuro (html.dark).
 * ============================================================================
 */
(function (window, document) {
    'use strict';

    /* ------------------------------------------------------------------ *
     *  1. INYECCIÓN DE ESTILOS ADAPTATIVOS (LIGHT MODE + DARK MODE)
     * ------------------------------------------------------------------ */
    function injectStyles() {
        if (document.getElementById('lote-status-swal-styles')) return;

        const style = document.createElement('style');
        style.id = 'lote-status-swal-styles';
        style.textContent = `
            /* Variables base (Modo Claro) */
            :root {
                --ls-popup-bg: #ffffff;
                --ls-popup-border: #e2e8f0;
                --ls-popup-shadow: 0 24px 48px -12px rgba(15, 23, 42, 0.22);
                --ls-title: #0f172a;
                --ls-text: #334155;
                --ls-muted: #64748b;
                --ls-subtle: #94a3b8;
                --ls-card-bg: #f8fafc;
                --ls-card-border: #e2e8f0;
                --ls-table-head-bg: #f1f5f9;
                --ls-table-head-text: #475569;
                --ls-table-row-bg: #ffffff;
                --ls-table-border: #e2e8f0;
                --ls-chip-bg: #f1f5f9;
                --ls-chip-text: #334155;
                --ls-chip-border: #cbd5e1;

                /* Variantes de color en Modo Claro */
                --ls-amber-bg: #fffbeb;
                --ls-amber-bg-soft: rgba(245, 158, 11, 0.10);
                --ls-amber-border: #fde68a;
                --ls-amber-text: #92400e;
                --ls-amber-accent: #d97706;
                --ls-amber-badge-bg: #fef3c7;
                --ls-amber-badge-text: #b45309;

                --ls-rose-bg: #fff1f2;
                --ls-rose-bg-soft: rgba(244, 63, 94, 0.10);
                --ls-rose-border: #fecdd3;
                --ls-rose-text: #9f1239;
                --ls-rose-accent: #e11d48;
                --ls-rose-badge-bg: #ffe4e6;
                --ls-rose-badge-text: #be123c;

                --ls-emerald-bg: #ecfdf5;
                --ls-emerald-bg-soft: rgba(16, 185, 129, 0.10);
                --ls-emerald-border: #a7f3d0;
                --ls-emerald-text: #065f46;
                --ls-emerald-accent: #059669;
                --ls-emerald-badge-bg: #d1fae5;
                --ls-emerald-badge-text: #047857;

                --ls-teal-bg: #f0fdfa;
                --ls-teal-bg-soft: rgba(20, 184, 166, 0.10);
                --ls-teal-border: #99f6e4;
                --ls-teal-text: #115e59;
                --ls-teal-accent: #0f766e;
                --ls-teal-badge-bg: #ccfbf1;
                --ls-teal-badge-text: #0f766e;
            }

            /* Variables para Modo Oscuro (html.dark) */
            html.dark {
                --ls-popup-bg: #1e293b;
                --ls-popup-border: #334155;
                --ls-popup-shadow: 0 25px 55px -12px rgba(0, 0, 0, 0.72);
                --ls-title: #f8fafc;
                --ls-text: #e2e8f0;
                --ls-muted: #94a3b8;
                --ls-subtle: #64748b;
                --ls-card-bg: #162032;
                --ls-card-border: #2c3b52;
                --ls-table-head-bg: #162032;
                --ls-table-head-text: #94a3b8;
                --ls-table-row-bg: #1e293b;
                --ls-table-border: #2c3b52;
                --ls-chip-bg: #26354d;
                --ls-chip-text: #e2e8f0;
                --ls-chip-border: #3b4f6e;

                /* Variantes de color en Modo Oscuro */
                --ls-amber-bg: rgba(245, 158, 11, 0.12);
                --ls-amber-bg-soft: rgba(245, 158, 11, 0.18);
                --ls-amber-border: rgba(245, 158, 11, 0.38);
                --ls-amber-text: #fde68a;
                --ls-amber-accent: #f59e0b;
                --ls-amber-badge-bg: rgba(245, 158, 11, 0.22);
                --ls-amber-badge-text: #fcd34d;

                --ls-rose-bg: rgba(244, 63, 94, 0.12);
                --ls-rose-bg-soft: rgba(244, 63, 94, 0.18);
                --ls-rose-border: rgba(244, 63, 94, 0.38);
                --ls-rose-text: #fecdd3;
                --ls-rose-accent: #fb7185;
                --ls-rose-badge-bg: rgba(244, 63, 94, 0.22);
                --ls-rose-badge-text: #fda4af;

                --ls-emerald-bg: rgba(16, 185, 129, 0.12);
                --ls-emerald-bg-soft: rgba(16, 185, 129, 0.18);
                --ls-emerald-border: rgba(16, 185, 129, 0.38);
                --ls-emerald-text: #a7f3d0;
                --ls-emerald-accent: #34d399;
                --ls-emerald-badge-bg: rgba(16, 185, 129, 0.22);
                --ls-emerald-badge-text: #6ee7b7;

                --ls-teal-bg: rgba(20, 184, 166, 0.12);
                --ls-teal-bg-soft: rgba(20, 184, 166, 0.18);
                --ls-teal-border: rgba(20, 184, 166, 0.38);
                --ls-teal-text: #99f6e4;
                --ls-teal-accent: #2dd4bf;
                --ls-teal-badge-bg: rgba(20, 184, 166, 0.22);
                --ls-teal-badge-text: #5eead4;
            }

            /* Contenedor general del Popup SweetAlert2 */
            .swal2-popup.ls-swal-popup {
                background: var(--ls-popup-bg) !important;
                color: var(--ls-text) !important;
                border: 1px solid var(--ls-popup-border) !important;
                border-radius: 18px !important;
                box-shadow: var(--ls-popup-shadow) !important;
                padding: 1.5rem 1.5rem 1.35rem !important;
                font-family: 'IBM Plex Sans', system-ui, -apple-system, sans-serif !important;
            }

            /* Solución al parche blanco del icono success de SweetAlert2 en Dark Mode */
            .swal2-popup.ls-swal-popup .swal2-success-circular-line-left,
            .swal2-popup.ls-swal-popup .swal2-success-circular-line-right,
            .swal2-popup.ls-swal-popup .swal2-success-fix {
                background-color: transparent !important;
            }

            .swal2-popup.ls-swal-popup .swal2-icon {
                margin: 0.25rem auto 0.65rem !important;
                transform: scale(0.92);
            }

            .swal2-popup.ls-swal-popup .swal2-title {
                color: var(--ls-title) !important;
                font-size: 1.25rem !important;
                font-weight: 800 !important;
                letter-spacing: -0.015em !important;
                padding: 0 0.5rem !important;
                margin-bottom: 0.35rem !important;
                line-height: 1.3 !important;
            }

            .swal2-popup.ls-swal-popup .swal2-html-container {
                color: var(--ls-text) !important;
                margin: 0.5rem 0 0 !important;
                padding: 0 !important;
                font-size: 0.84rem !important;
                line-height: 1.45 !important;
            }

            .swal2-popup.ls-swal-popup .swal2-actions {
                margin-top: 1.15rem !important;
                gap: 0.6rem !important;
                width: 100% !important;
                justify-content: center !important;
            }

            .swal2-popup.ls-swal-popup .swal2-styled {
                border-radius: 10px !important;
                font-weight: 700 !important;
                font-size: 0.82rem !important;
                padding: 0.62rem 1.35rem !important;
                letter-spacing: 0.01em !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                gap: 0.45rem !important;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.14) !important;
                transition: transform 0.12s ease, filter 0.15s ease !important;
            }
            .swal2-popup.ls-swal-popup .swal2-styled:hover {
                filter: brightness(1.06);
                transform: translateY(-1px);
            }
            .swal2-popup.ls-swal-popup .swal2-styled.swal2-cancel {
                background-color: var(--ls-chip-bg) !important;
                color: var(--ls-text) !important;
                border: 1px solid var(--ls-chip-border) !important;
                box-shadow: none !important;
            }

            /* Paletas temáticas aplicadas por clase (.ls-theme-amber | rose | emerald | teal) */
            .ls-theme-amber {
                --t-bg: var(--ls-amber-bg);
                --t-bg-soft: var(--ls-amber-bg-soft);
                --t-border: var(--ls-amber-border);
                --t-text: var(--ls-amber-text);
                --t-accent: var(--ls-amber-accent);
                --t-badge-bg: var(--ls-amber-badge-bg);
                --t-badge-text: var(--ls-amber-badge-text);
            }
            .ls-theme-rose {
                --t-bg: var(--ls-rose-bg);
                --t-bg-soft: var(--ls-rose-bg-soft);
                --t-border: var(--ls-rose-border);
                --t-text: var(--ls-rose-text);
                --t-accent: var(--ls-rose-accent);
                --t-badge-bg: var(--ls-rose-badge-bg);
                --t-badge-text: var(--ls-rose-badge-text);
            }
            .ls-theme-emerald {
                --t-bg: var(--ls-emerald-bg);
                --t-bg-soft: var(--ls-emerald-bg-soft);
                --t-border: var(--ls-emerald-border);
                --t-text: var(--ls-emerald-text);
                --t-accent: var(--ls-emerald-accent);
                --t-badge-bg: var(--ls-emerald-badge-bg);
                --t-badge-text: var(--ls-emerald-badge-text);
            }
            .ls-theme-teal {
                --t-bg: var(--ls-teal-bg);
                --t-bg-soft: var(--ls-teal-bg-soft);
                --t-border: var(--ls-teal-border);
                --t-text: var(--ls-teal-text);
                --t-accent: var(--ls-teal-accent);
                --t-badge-bg: var(--ls-teal-badge-bg);
                --t-badge-text: var(--ls-teal-badge-text);
            }

            /* --- Estructura del Comprobante de Éxito --- */
            .ls-receipt {
                text-align: left;
                color: var(--ls-text);
                display: flex;
                flex-direction: column;
                gap: 0.75rem;
            }

            .ls-banner {
                background: var(--t-bg);
                border: 1px solid var(--t-border);
                border-radius: 14px;
                padding: 0.9rem 1rem;
            }

            .ls-banner-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 0.5rem;
                padding-bottom: 0.65rem;
                margin-bottom: 0.65rem;
                border-bottom: 1px solid var(--t-border);
            }

            .ls-folio-wrap {
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            .ls-folio-dot {
                width: 8px;
                height: 8px;
                border-radius: 999px;
                background: var(--t-accent);
                box-shadow: 0 0 0 3px var(--t-bg-soft);
                flex-shrink: 0;
            }

            .ls-folio-title {
                font-size: 0.84rem;
                font-weight: 800;
                color: var(--ls-title);
                letter-spacing: -0.01em;
            }

            .ls-folio-code {
                font-family: 'IBM Plex Mono', monospace;
                font-weight: 700;
                color: var(--t-accent);
            }

            .ls-pill-badge {
                display: inline-flex;
                align-items: center;
                padding: 0.22rem 0.68rem;
                border-radius: 999px;
                font-size: 0.65rem;
                font-weight: 800;
                letter-spacing: 0.04em;
                text-transform: uppercase;
                background: var(--t-badge-bg);
                color: var(--t-badge-text);
                border: 1px solid var(--t-border);
            }

            .ls-meta-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 0.45rem 1rem;
                font-size: 0.78rem;
            }
            @media (max-width: 520px) {
                .ls-meta-grid { grid-template-columns: 1fr; }
            }

            .ls-meta-item {
                display: flex;
                align-items: baseline;
                gap: 0.35rem;
                min-width: 0;
            }
            .ls-meta-label {
                color: var(--ls-muted);
                font-weight: 600;
                white-space: nowrap;
            }
            .ls-meta-value {
                color: var(--ls-title);
                font-weight: 700;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .ls-meta-highlight {
                color: var(--t-accent);
                font-weight: 800;
            }

            /* Ficha / Tabla del lote actualizado */
            .ls-section-label {
                font-size: 0.68rem;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: 0.06em;
                color: var(--ls-muted);
                margin-bottom: -0.35rem;
            }

            .ls-table-wrap {
                border: 1px solid var(--ls-table-border);
                border-radius: 12px;
                overflow: hidden;
                background: var(--ls-table-row-bg);
            }

            .ls-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 0.76rem;
            }

            .ls-table thead {
                background: var(--ls-table-head-bg);
                border-bottom: 1px solid var(--ls-table-border);
            }

            .ls-table th {
                padding: 0.55rem 0.6rem;
                font-size: 0.68rem;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: 0.04em;
                color: var(--ls-table-head-text);
                text-align: center;
            }
            .ls-table th:first-child { text-align: left; }

            .ls-table td {
                padding: 0.65rem 0.6rem;
                color: var(--ls-text);
                background: var(--ls-table-row-bg);
                text-align: center;
                vertical-align: middle;
            }
            .ls-table td:first-child { text-align: left; }

            .ls-prod-name {
                font-weight: 700;
                color: var(--ls-title);
                font-size: 0.8rem;
                line-height: 1.25;
            }
            .ls-prod-cat {
                display: block;
                font-size: 0.68rem;
                font-weight: 500;
                color: var(--ls-muted);
                margin-top: 2px;
            }

            .ls-mono-chip {
                display: inline-block;
                font-family: 'IBM Plex Mono', monospace;
                font-size: 0.73rem;
                font-weight: 700;
                padding: 0.15rem 0.48rem;
                border-radius: 6px;
                background: var(--ls-chip-bg);
                color: var(--ls-chip-text);
                border: 1px solid var(--ls-chip-border);
            }

            /* Badges de Estado (Disponible, Cuarentena, Merma) */
            .ls-status-badge {
                display: inline-flex;
                align-items: center;
                gap: 0.28rem;
                padding: 0.2rem 0.55rem;
                border-radius: 6px;
                font-size: 0.68rem;
                font-weight: 800;
                letter-spacing: 0.02em;
                white-space: nowrap;
            }
            .ls-status-badge::before {
                content: '';
                width: 6px;
                height: 6px;
                border-radius: 999px;
                background: currentColor;
            }
            .ls-status-amber {
                background: var(--ls-amber-badge-bg);
                color: var(--ls-amber-badge-text);
                border: 1px solid var(--ls-amber-border);
            }
            .ls-status-rose {
                background: var(--ls-rose-badge-bg);
                color: var(--ls-rose-badge-text);
                border: 1px solid var(--ls-rose-border);
            }
            .ls-status-emerald {
                background: var(--ls-emerald-badge-bg);
                color: var(--ls-emerald-badge-text);
                border: 1px solid var(--ls-emerald-border);
            }

            .ls-loc-badge {
                display: inline-flex;
                align-items: center;
                padding: 0.2rem 0.52rem;
                border-radius: 6px;
                font-size: 0.68rem;
                font-weight: 700;
                background: var(--ls-teal-badge-bg);
                color: var(--ls-teal-badge-text);
                border: 1px solid var(--ls-teal-border);
            }
            .ls-loc-empty {
                background: var(--ls-chip-bg);
                color: var(--ls-muted);
                border: 1px solid var(--ls-chip-border);
            }

            /* Caja de Motivo / Justificación */
            .ls-reason-box {
                background: var(--ls-card-bg);
                border: 1px solid var(--ls-card-border);
                border-left: 3.5px solid var(--t-accent);
                border-radius: 10px;
                padding: 0.6rem 0.85rem;
                font-size: 0.77rem;
                color: var(--ls-text);
            }
            .ls-reason-label {
                font-weight: 800;
                color: var(--ls-title);
                margin-right: 0.3rem;
            }

            .ls-footer-note {
                text-align: center;
                font-size: 0.76rem;
                color: var(--ls-muted);
                margin: 0.1rem 0 0;
            }

            /* --- Estilos para Modales de Confirmación Previa --- */
            .ls-confirm-wrap {
                text-align: left;
                display: flex;
                flex-direction: column;
                gap: 0.7rem;
                margin-top: 0.25rem;
            }
            .ls-confirm-lead {
                text-align: center;
                color: var(--ls-text);
                font-size: 0.85rem;
                margin: 0;
                line-height: 1.45;
            }
            .ls-confirm-lead strong {
                color: var(--ls-title);
                font-weight: 700;
            }
            .ls-confirm-card {
                background: var(--t-bg);
                border: 1px solid var(--t-border);
                border-radius: 12px;
                padding: 0.7rem 0.9rem;
                font-size: 0.78rem;
                color: var(--ls-text);
                display: flex;
                flex-direction: column;
                gap: 0.35rem;
            }
            .ls-confirm-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 0.5rem;
            }
            .ls-confirm-row strong {
                color: var(--ls-muted);
                font-weight: 600;
            }
            .ls-confirm-row span {
                color: var(--ls-title);
                font-weight: 700;
            }
        `;
        document.head.appendChild(style);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', injectStyles);
    } else {
        injectStyles();
    }

    /* ------------------------------------------------------------------ *
     *  CORE: utilidades compartidas
     * ------------------------------------------------------------------ */
    const COLORS = {
        cancel: '#64748b',
        teal: '#0f766e',
        emerald: '#059669',
        amber: '#d97706',
        rose: '#e11d48',
    };

    let enProceso = false;

    const esc = (value) =>
        String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');

    const val = (id) => (document.getElementById(id)?.value ?? '').trim();
    const txt = (id) => (document.getElementById(id)?.textContent ?? '').trim();

    function getCsrfToken(form) {
        return (
            form?.querySelector('input[name="_token"]')?.value ||
            document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
            ''
        );
    }

    /** Lee el contexto del modal de edición de lote (compartido por Inventario y Cuarentena). */
    function leerContexto(form) {
        return {
            form,
            estado: val('modal_estado'),
            estadoInicial: form.dataset.initialEstado || '',
            motivo: val('modal_motivo'),
            producto: txt('modal_producto_nombre') || 'Producto',
            lote: txt('modal_lote_nombre').replace(/^LOTE:\s*/i, '').trim(),
            cantidad: txt('modal_cantidad'),
            valorTotal: txt('modal_valor_total'),
            ubicacion: {
                pasillo: val('modal_edit_pasillo'),
                estante: val('modal_edit_estante'),
                nivel: val('modal_edit_nivel'),
                posicion: val('modal_edit_posicion'),
            },
            ubicacionInicial: {
                pasillo: form.dataset.initialPasillo || '',
                estante: form.dataset.initialEstante || '',
                nivel: form.dataset.initialNivel || '',
                posicion: form.dataset.initialPosicion || '',
            },
        };
    }

    function ubicacionTexto(u) {
        const parts = [];
        if (u.pasillo) parts.push('Pasillo ' + u.pasillo);
        if (u.estante) parts.push('Estante ' + u.estante);
        if (u.nivel) parts.push('Nivel ' + u.nivel);
        if (u.posicion) parts.push('Posición ' + u.posicion);
        return parts.length ? parts.join(' · ') : 'Sin asignar (P.A)';
    }

    function huboCambioUbicacion(ctx) {
        const a = ctx.ubicacion;
        const b = ctx.ubicacionInicial;
        return a.pasillo !== b.pasillo || a.estante !== b.estante || a.nivel !== b.nivel || a.posicion !== b.posicion;
    }

    /** Valida que exista motivo cuando corresponde. Devuelve true si es válido. */
    async function validarMotivo(ctx, requerido, mensaje) {
        if (!requerido || ctx.motivo) return true;
        injectStyles();
        await Swal.fire({
            icon: 'warning',
            title: 'Motivo requerido',
            text: mensaje || `Debes indicar el motivo para cambiar el estado a ${ctx.estado}.`,
            confirmButtonColor: COLORS.teal,
            customClass: { popup: 'ls-swal-popup' },
        });
        document.getElementById('modal_motivo')?.focus();
        return false;
    }

    /** Paso 2: confirmación previa. Devuelve true si el usuario confirma. */
    async function confirmar({ title, html, icon = 'question', confirmText = 'Sí, confirmar', color = COLORS.teal }) {
        injectStyles();
        const { isConfirmed } = await Swal.fire({
            title,
            html,
            icon,
            width: '500px',
            showCancelButton: true,
            confirmButtonColor: color,
            cancelButtonColor: COLORS.cancel,
            confirmButtonText: confirmText,
            cancelButtonText: 'Cancelar',
            reverseButtons: true,
            focusCancel: icon === 'warning',
            customClass: { popup: 'ls-swal-popup' },
        });
        return isConfirmed;
    }

    function mostrarCargando(titulo = 'Procesando movimiento...') {
        injectStyles();
        Swal.fire({
            title: titulo,
            html: '<p class="ls-footer-note">Registrando el cambio de estado y la trazabilidad del lote...</p>',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            customClass: { popup: 'ls-swal-popup' },
            didOpen: () => Swal.showLoading(),
        });
    }

    /** Paso 3: envío AJAX del formulario (PUT simulado con _method). */
    async function enviar(form) {
        const response = await fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': getCsrfToken(form),
            },
            credentials: 'same-origin',
        });

        const isJson = (response.headers.get('content-type') || '').includes('application/json');
        const body = isJson ? await response.json().catch(() => ({})) : null;

        if (!isJson) {
            throw { status: response.status, message: 'Tu sesión pudo haber expirado. Recarga la página e inicia sesión nuevamente.' };
        }
        if (!response.ok || body.ok === false) {
            throw { status: response.status, message: body.message, errors: body.errors };
        }
        return body;
    }

    async function mostrarError(err) {
        injectStyles();
        let title = 'No se pudo completar la operación';
        let html = `<p class="ls-confirm-lead">${esc(err?.message || 'Ocurrió un error inesperado. Intenta nuevamente.')}</p>`;

        if (err?.status === 422 && err.errors) {
            title = 'Revisa los datos ingresados';
            html = '<ul style="text-align:left;margin:0;padding-left:18px">' +
                Object.values(err.errors).flat().map((m) => `<li>${esc(m)}</li>`).join('') + '</ul>';
        } else if (err?.status === 419) {
            title = 'Sesión expirada';
            html = '<p class="ls-confirm-lead">El token de seguridad expiró. Recarga la página e intenta nuevamente.</p>';
        } else if (err?.status === 403) {
            title = 'Acceso denegado';
            html = '<p class="ls-confirm-lead">No tienes permisos para realizar este movimiento.</p>';
        } else if (err instanceof TypeError) {
            html = '<p class="ls-confirm-lead">No fue posible conectar con el servidor. Verifica tu conexión.</p>';
        }

        await Swal.fire({
            icon: 'error',
            title,
            html,
            confirmButtonColor: COLORS.teal,
            customClass: { popup: 'ls-swal-popup' },
        });
    }

    /* ------------------------------------------------------------------ *
     *  Comprobante de éxito (compatible con Light Mode y Dark Mode)
     * ------------------------------------------------------------------ */
    const THEMES = {
        a_merma:                  { title: '¡Merma Declarada con Éxito!',            badge: 'MERMA DEFINITIVA',         op: 'Declaración de Merma (Baja de Stock)',  theme: 'ls-theme-rose',    c: COLORS.rose },
        actualizacion_merma:      { title: '¡Registro de Merma Actualizado!',        badge: 'MERMA',                    op: 'Actualización de Lote en Merma',        theme: 'ls-theme-rose',    c: COLORS.rose },
        merma_a_cuarentena:       { title: '¡Lote Movido a Cuarentena con Éxito!',   badge: 'MERMA ➔ CUARENTENA',       op: 'Reingreso de Merma a Cuarentena',       theme: 'ls-theme-amber',   c: COLORS.amber },
        disponible_a_cuarentena:  { title: '¡Lote Enviado a Cuarentena con Éxito!',  badge: 'CUARENTENA PREVENTIVA',    op: 'Bloqueo Preventivo a Cuarentena',       theme: 'ls-theme-amber',   c: COLORS.amber },
        a_cuarentena:             { title: '¡Lote Enviado a Cuarentena con Éxito!',  badge: 'CUARENTENA PREVENTIVA',    op: 'Bloqueo Preventivo a Cuarentena',       theme: 'ls-theme-amber',   c: COLORS.amber },
        actualizacion_cuarentena: { title: '¡Control Técnico Actualizado!',          badge: 'EN CUARENTENA',            op: 'Control Técnico en Cuarentena',         theme: 'ls-theme-amber',   c: COLORS.amber },
        merma_a_disponible:       { title: '¡Lote Reactivado a Disponible!',         badge: 'MERMA ➔ DISPONIBLE',       op: 'Reactivación de Merma a Disponible',    theme: 'ls-theme-emerald', c: COLORS.emerald },
        cuarentena_a_disponible:  { title: '¡Lote Liberado a Disponible con Éxito!', badge: 'DISPONIBLE PARA DESPACHO', op: 'Liberación de Cuarentena a Disponible', theme: 'ls-theme-emerald', c: COLORS.emerald },
        disponible:               { title: '¡Lote Liberado a Disponible con Éxito!', badge: 'DISPONIBLE PARA DESPACHO', op: 'Liberación de Cuarentena a Disponible', theme: 'ls-theme-emerald', c: COLORS.emerald },
        reubicacion:              { title: '¡Lote Reubicado con Éxito!',             badge: 'REUBICACIÓN EN BODEGA',    op: 'Reubicación Física de Lote',            theme: 'ls-theme-teal',    c: COLORS.teal },
        actualizacion:            { title: '¡Movimiento Realizado con Éxito!',       badge: 'ACTUALIZACIÓN DE LOTE',    op: 'Actualización de Inventario',           theme: 'ls-theme-teal',    c: COLORS.teal },
    };

    function badgeEstado(estado) {
        const low = String(estado || '').toLowerCase();
        const cls = low.includes('merma')
            ? 'ls-status-rose'
            : low.includes('cuarentena')
            ? 'ls-status-amber'
            : 'ls-status-emerald';
        return `<span class="ls-status-badge ${cls}">${esc(estado || 'N/A')}</span>`;
    }

    function badgeUbicacion(ubic) {
        const clean = String(ubic || 'Sin asignar (P.A)');
        const isUnassigned = clean === 'Sin asignar (P.A)';
        return `<span class="ls-loc-badge ${isUnassigned ? 'ls-loc-empty' : ''}">${esc(clean)}</span>`;
    }

    function construirComprobante(p, t) {
        const metaItem = (label, valueHtml) =>
            valueHtml
                ? `<div class="ls-meta-item"><span class="ls-meta-label">${label}:</span><span class="ls-meta-value">${valueHtml}</span></div>`
                : '';

        const tieneMotivo = p.motivo && p.motivo !== 'Sin motivo especificado';

        return `
            <div class="ls-receipt ${t.theme}">
                <!-- Banner del comprobante -->
                <div class="ls-banner">
                    <div class="ls-banner-header">
                        <div class="ls-folio-wrap">
                            <span class="ls-folio-dot"></span>
                            <span class="ls-folio-title">Comprobante de Movimiento <span class="ls-folio-code">N° ${esc(p.correlativo)}</span></span>
                        </div>
                        <span class="ls-pill-badge">${esc(t.badge)}</span>
                    </div>
                    <div class="ls-meta-grid">
                        ${metaItem('Operación', esc(t.op))}
                        ${metaItem('Responsable', esc(p.usuario))}
                        ${metaItem('Fecha y Hora', esc(p.fecha_hora))}
                        ${metaItem('Total Afectado', `<span class="ls-meta-highlight">${esc(p.cantidad)} uds.</span>`)}
                        ${p.programa ? metaItem('Programa', esc(p.programa)) : ''}
                        ${p.valor_total ? metaItem('Valor Total Est.', `$ ${esc(p.valor_total)}`) : ''}
                    </div>
                </div>

                <!-- Tabla Resumen del Lote -->
                <div class="ls-section-label">Resumen del Lote Actualizado</div>
                <div class="ls-table-wrap">
                    <table class="ls-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Lote</th>
                                <th>Vencimiento</th>
                                <th>Estado Anterior</th>
                                <th>Nuevo Estado</th>
                                <th>Ubicación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <span class="ls-prod-name">${esc(p.producto)}</span>
                                    ${p.categoria ? `<span class="ls-prod-cat">${esc(p.categoria)}</span>` : ''}
                                </td>
                                <td><span class="ls-mono-chip">${esc(p.lote)}</span></td>
                                <td>${esc(p.fecha_vencimiento)}</td>
                                <td>${badgeEstado(p.estado_anterior)}</td>
                                <td>${badgeEstado(p.nuevo_estado)}</td>
                                <td>${badgeUbicacion(p.ubicacion)}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                ${tieneMotivo ? `
                    <div class="ls-reason-box">
                        <span class="ls-reason-label">Motivo / Justificación:</span>${esc(p.motivo)}
                    </div>
                ` : ''}

                <p class="ls-footer-note">${esc(p.mensaje)}</p>
            </div>`;
    }

    /** Paso 4: alerta de éxito. Se espera (await) a que el usuario la cierre. */
    async function mostrarExito(payload) {
        injectStyles();
        const t =
            THEMES[payload.tipo] ||
            (String(payload.tipo).includes('cuarentena') ? THEMES.disponible_a_cuarentena : THEMES.actualizacion);

        await Swal.fire({
            icon: 'success',
            title: t.title,
            html: construirComprobante(payload, t),
            width: '680px',
            confirmButtonColor: t.c,
            confirmButtonText:
                '<svg style="width:16px;height:16px;display:inline-block;vertical-align:-3px;margin-right:4px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg> Aceptar y Continuar',
            allowOutsideClick: false,
            customClass: { popup: 'ls-swal-popup' },
        });
    }

    /**
     * Orquestador común a los tres escenarios.
     */
    async function procesar({ form, confirm, onSuccess, refresh = true }) {
        if (enProceso) return false;

        const confirmado = await confirmar(confirm);
        if (!confirmado) return false;

        enProceso = true;
        mostrarCargando();

        try {
            const { data } = await enviar(form);

            // Cerrar modales del sistema ANTES de mostrar el éxito
            if (typeof window.closeEditLote === 'function') window.closeEditLote();
            if (typeof window.closeMerma === 'function') window.closeMerma();

            await mostrarExito(data);

            if (typeof onSuccess === 'function') await onSuccess(data);
            if (refresh) window.location.reload();
            return true;
        } catch (err) {
            console.error('[LoteStatus]', err);
            await mostrarError(err);
            return false;
        } finally {
            enProceso = false;
        }
    }

    /* ------------------------------------------------------------------ *
     *  ESCENARIO 3: MERMA
     * ------------------------------------------------------------------ */
    const Merma = {
        aplica(ctx) {
            return ctx.estado === 'Merma' || ctx.estadoInicial === 'Merma';
        },

        confirmConfig(ctx) {
            const prod = esc(ctx.producto);
            const lote = esc(ctx.lote);

            if (ctx.estadoInicial === 'Merma' && ctx.estado === 'Cuarentena') {
                return {
                    title: '¿Mover lote de Merma a Cuarentena?',
                    html: `
                        <div class="ls-confirm-wrap ls-theme-amber">
                            <p class="ls-confirm-lead">El producto <strong>${prod}</strong> (Lote: <strong>${lote}</strong>) será reactivado desde Merma y trasladado a <strong>Cuarentena</strong> para reevaluación técnica.</p>
                            <div class="ls-confirm-card">
                                <div class="ls-confirm-row"><strong>Transición:</strong><span>${badgeEstado('Merma')} ➔ ${badgeEstado('Cuarentena')}</span></div>
                                ${ctx.motivo ? `<div class="ls-confirm-row"><strong>Justificación:</strong><span>${esc(ctx.motivo)}</span></div>` : ''}
                            </div>
                        </div>`,
                    icon: 'warning', color: COLORS.amber, confirmText: 'Sí, mover a cuarentena',
                };
            }
            if (ctx.estadoInicial === 'Merma' && ctx.estado === 'Disponible') {
                return {
                    title: '¿Reactivar lote a Disponible?',
                    html: `
                        <div class="ls-confirm-wrap ls-theme-emerald">
                            <p class="ls-confirm-lead">El lote <strong>${lote}</strong> de <strong>${prod}</strong> será retirado de Merma y habilitado como <strong>Disponible</strong> para entrega y despacho.</p>
                            <div class="ls-confirm-card">
                                <div class="ls-confirm-row"><strong>Transición:</strong><span>${badgeEstado('Merma')} ➔ ${badgeEstado('Disponible')}</span></div>
                            </div>
                        </div>`,
                    icon: 'question', color: COLORS.emerald, confirmText: 'Sí, reactivar a disponible',
                };
            }
            if (ctx.estadoInicial === 'Merma' && ctx.estado === 'Merma') {
                return {
                    title: '¿Actualizar registro de Merma?',
                    html: `<p class="ls-confirm-lead">Se actualizará la información del lote <strong>${lote}</strong> de <strong>${prod}</strong> que permanece en Merma.</p>`,
                    icon: 'question', color: COLORS.rose, confirmText: 'Sí, actualizar',
                };
            }

            // Envío formal a merma
            const desdeCuarentena = ctx.estadoInicial === 'Cuarentena';
            return {
                title: desdeCuarentena ? '¿Declarar Merma desde Cuarentena?' : '¿Declarar Merma de Lote?',
                html: `
                    <div class="ls-confirm-wrap ls-theme-rose">
                        <p class="ls-confirm-lead">El lote <strong>${lote}</strong> de <strong>${prod}</strong> será dado de baja definitiva como <strong>Merma</strong> y retirado del stock disponible.</p>
                        <div class="ls-confirm-card">
                            <div class="ls-confirm-row"><strong>Transición:</strong><span>${badgeEstado(ctx.estadoInicial || 'Disponible')} ➔ ${badgeEstado('Merma')}</span></div>
                            ${ctx.cantidad ? `<div class="ls-confirm-row"><strong>Cantidad a dar de baja:</strong><span>${esc(ctx.cantidad)}</span></div>` : ''}
                            ${ctx.valorTotal ? `<div class="ls-confirm-row"><strong>Valor estimado:</strong><span>${esc(ctx.valorTotal)}</span></div>` : ''}
                            <div class="ls-confirm-row"><strong>Motivo:</strong><span>${esc(ctx.motivo)}</span></div>
                        </div>
                    </div>`,
                icon: 'warning', color: COLORS.rose, confirmText: 'Sí, declarar merma',
            };
        },

        async procesar(ctx) {
            const requiereMotivo = ctx.estado === 'Merma' || ctx.estado === 'Cuarentena';
            if (!(await validarMotivo(ctx, requiereMotivo, 'Debes indicar el motivo de la merma (daño, vencimiento, rechazo, etc.).'))) return false;
            return procesar({ form: ctx.form, confirm: this.confirmConfig(ctx) });
        },

        abrir() {
            document.getElementById('mermaModal')?.classList.remove('hidden');
            if (typeof window.filterMermas === 'function') window.filterMermas();
        },
        cerrar() {
            document.getElementById('mermaModal')?.classList.add('hidden');
        },
    };

    /* ------------------------------------------------------------------ *
     *  ESCENARIO 2: CUARENTENA (bloqueo preventivo y control técnico)
     * ------------------------------------------------------------------ */
    const Cuarentena = {
        confirmConfig(ctx) {
            const prod = esc(ctx.producto);
            const lote = esc(ctx.lote);

            if (ctx.estado === 'Cuarentena' && ctx.estadoInicial !== 'Cuarentena') {
                return {
                    title: '¿Enviar lote a Cuarentena?',
                    html: `
                        <div class="ls-confirm-wrap ls-theme-amber">
                            <p class="ls-confirm-lead">El producto <strong>${prod}</strong> (Lote: <strong>${lote}</strong>) quedará <strong>bloqueado preventivamente</strong> y pasará a control técnico en Cuarentena.</p>
                            <div class="ls-confirm-card">
                                <div class="ls-confirm-row"><strong>Transición:</strong><span>${badgeEstado(ctx.estadoInicial || 'Disponible')} ➔ ${badgeEstado('Cuarentena')}</span></div>
                                ${ctx.cantidad ? `<div class="ls-confirm-row"><strong>Unidades retenidas:</strong><span>${esc(ctx.cantidad)}</span></div>` : ''}
                                <div class="ls-confirm-row"><strong>Motivo:</strong><span>${esc(ctx.motivo)}</span></div>
                            </div>
                        </div>`,
                    icon: 'warning', color: COLORS.amber, confirmText: 'Sí, enviar a cuarentena',
                };
            }
            if (ctx.estado === 'Disponible') {
                return {
                    title: '¿Liberar lote a Disponible?',
                    html: `
                        <div class="ls-confirm-wrap ls-theme-emerald">
                            <p class="ls-confirm-lead">El producto <strong>${prod}</strong> (Lote: <strong>${lote}</strong>) será aprobado y reincorporado al inventario <strong>Disponible</strong> para despacho.</p>
                            <div class="ls-confirm-card">
                                <div class="ls-confirm-row"><strong>Transición:</strong><span>${badgeEstado('Cuarentena')} ➔ ${badgeEstado('Disponible')}</span></div>
                                ${ctx.motivo ? `<div class="ls-confirm-row"><strong>Observación:</strong><span>${esc(ctx.motivo)}</span></div>` : ''}
                            </div>
                        </div>`,
                    icon: 'question', color: COLORS.emerald, confirmText: 'Sí, liberar lote',
                };
            }
            return {
                title: '¿Mantener en Cuarentena?',
                html: `
                    <div class="ls-confirm-wrap ls-theme-amber">
                        <p class="ls-confirm-lead">Se actualizará la justificación técnica del lote <strong>${lote}</strong> de <strong>${prod}</strong> manteniéndolo inmovilizado en Cuarentena.</p>
                        <div class="ls-confirm-card">
                            <div class="ls-confirm-row"><strong>Motivo actualizado:</strong><span>${esc(ctx.motivo)}</span></div>
                        </div>
                    </div>`,
                icon: 'info', color: COLORS.amber, confirmText: 'Sí, guardar estado',
            };
        },

        async procesar(ctx) {
            if (!(await validarMotivo(ctx, true, 'Debes indicar la justificación u observación técnica correspondiente.'))) return false;
            return procesar({ form: ctx.form, confirm: this.confirmConfig(ctx) });
        },

        async onSubmit(event) {
            event.preventDefault();
            const form = event.target.closest('form') || document.getElementById('editLoteForm');
            const ctx = leerContexto(form);
            if (!ctx.estadoInicial) ctx.estadoInicial = 'Cuarentena';

            if (ctx.estado === 'Merma') return Merma.procesar(ctx);
            return Cuarentena.procesar(ctx);
        },
    };

    /* ------------------------------------------------------------------ *
     *  ESCENARIO 1: INVENTARIO (cambio de estado / reubicación)
     * ------------------------------------------------------------------ */
    const Inventario = {
        confirmConfig(ctx) {
            const prod = esc(ctx.producto);
            const lote = esc(ctx.lote);
            const cambioEstado = ctx.estado !== ctx.estadoInicial;

            if (!cambioEstado && huboCambioUbicacion(ctx)) {
                return {
                    title: '¿Mover Lote en Bodega?',
                    html: `
                        <div class="ls-confirm-wrap ls-theme-teal">
                            <p class="ls-confirm-lead">¿Confirmas el traslado físico del lote <strong>${lote}</strong> de <strong>${prod}</strong>?</p>
                            <div class="ls-confirm-card">
                                <div class="ls-confirm-row"><strong>Nueva Ubicación:</strong><span>${badgeUbicacion(ubicacionTexto(ctx.ubicacion))}</span></div>
                            </div>
                        </div>`,
                    icon: 'question', color: COLORS.teal, confirmText: 'Sí, mover lote',
                };
            }
            if (ctx.estadoInicial === 'Cuarentena' && ctx.estado === 'Disponible') {
                return {
                    title: '¿Liberar lote a Disponible?',
                    html: `
                        <div class="ls-confirm-wrap ls-theme-emerald">
                            <p class="ls-confirm-lead">El lote <strong>${lote}</strong> de <strong>${prod}</strong> será liberado de Cuarentena y habilitado como <strong>Disponible</strong> para entrega y despacho.</p>
                            <div class="ls-confirm-card">
                                <div class="ls-confirm-row"><strong>Transición:</strong><span>${badgeEstado('Cuarentena')} ➔ ${badgeEstado('Disponible')}</span></div>
                            </div>
                        </div>`,
                    icon: 'question', color: COLORS.emerald, confirmText: 'Sí, liberar lote',
                };
            }
            return {
                title: '¿Guardar Cambios?',
                html: `<p class="ls-confirm-lead">¿Confirmas actualizar la información del lote <strong>${lote}</strong> de <strong>${prod}</strong>?</p>`,
                icon: 'question', color: COLORS.emerald, confirmText: 'Sí, confirmar',
            };
        },

        async onSubmit(event) {
            event.preventDefault();
            const form = event.target.closest('form') || document.getElementById('editLoteForm');
            const ctx = leerContexto(form);
            if (!ctx.estadoInicial) ctx.estadoInicial = 'Disponible';

            if (Merma.aplica(ctx)) return Merma.procesar(ctx);
            if (ctx.estado === 'Cuarentena') return Cuarentena.procesar(ctx);

            return procesar({ form, confirm: Inventario.confirmConfig(ctx) });
        },
    };

    /* ------------------------------------------------------------------ *
     *  API pública
     * ------------------------------------------------------------------ */
    window.LoteStatus = {
        Inventario,
        Cuarentena,
        Merma,
        core: { confirmar, enviar, mostrarExito, mostrarError, procesar, leerContexto, construirComprobante },
    };
})(window, document);
