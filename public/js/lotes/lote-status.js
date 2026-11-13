// lotestatus — gestion de estados de lote con sweetalert2 + ajax (async/await)
(function (window, document) {
    'use strict';

     // 1. inyeccion de estilos adaptativos (light mode + dark mode)
    function injectStyles() {
        if (document.getElementById('lote-status-swal-styles')) return;

        const style = document.createElement('style');
        style.id = 'lote-status-swal-styles';
        style.textContent = `
            // variables base (modo claro)
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

                // variantes de color en modo claro
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

            // variables para modo oscuro (html.dark)
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

                // variantes de color en modo oscuro
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

            // contenedor general del popup sweetalert2
            .swal2-popup.ls-swal-popup {
                background: var(--ls-popup-bg) !important;
                color: var(--ls-text) !important;
                border: 1px solid var(--ls-popup-border) !important;
                border-radius: 18px !important;
                box-shadow: var(--ls-popup-shadow) !important;
                padding: 1.5rem 1.5rem 1.35rem !important;
                font-family: 'IBM Plex Sans', system-ui, -apple-system, sans-serif !important;
            }

            // solucion al parche blanco del icono success de sweetalert2 en dark mode
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
                align-items: center !important;
                justify-content: center !important;
                gap: 0.45rem !important;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.14) !important;
                transition: transform 0.12s ease, filter 0.15s ease !important;
            }
            .swal2-popup.ls-swal-popup .swal2-styled:not([style*="display: none"]):not([style*="display:none"]) {
                display: inline-flex;
            }
            .swal2-popup.ls-swal-popup .swal2-styled[style*="display: none"],
            .swal2-popup.ls-swal-popup .swal2-styled[style*="display:none"],
            .swal2-popup.ls-swal-popup .swal2-actions.swal2-loading .swal2-styled {
                display: none !important;
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

            // paletas tematicas aplicadas por clase (.ls-theme-amber | rose | emerald | teal)
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

            // estructura del comprobante de exito
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

            // ficha / tabla del lote actualizado
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

            // badges de estado (disponible, cuarentena, merma)
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

            // caja de motivo / justificacion
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

            // estilos para modales de confirmacion previa
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
     // core: utilidades compartidas

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

    // lee el contexto del modal de edicion de lote (compartido por inventario y cuarentena)
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

    // valida que exista motivo cuando corresponde. devuelve true si es valido
    async function validarMotivo(ctx, requerido, mensaje) {
        if (!requerido || ctx.motivo) return true;
        injectStyles();
        await Swal.fire({
            icon: 'warning',
            title: 'Motivo requerido',
            text: mensaje || `Debe indicar el motivo para cambiar el estado a ${ctx.estado}.`,
            showDenyButton: false,
            showCancelButton: false,
            confirmButtonColor: COLORS.teal,
            customClass: { popup: 'ls-swal-popup' },
        });
        document.getElementById('modal_motivo')?.focus();
        return false;
    }

    // paso 2: confirmacion previa. devuelve true si el usuario confirma
    async function confirmar({ title, html, icon = 'question', confirmText = 'Sí, confirmar', color = COLORS.teal }) {
        injectStyles();
        const { isConfirmed } = await Swal.fire({
            title,
            html,
            icon,
            width: '500px',
            showDenyButton: false,
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
            showDenyButton: false,
            showCancelButton: false,
            customClass: { popup: 'ls-swal-popup' },
            didOpen: () => Swal.showLoading(),
        });
    }

    // paso 3: envio ajax del formulario (put simulado con _method)
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
            throw { status: response.status, message: 'Su sesión pudo haber expirado. Recargue la página e inicie sesión nuevamente.' };
        }
        if (!response.ok || body.ok === false) {
            throw { status: response.status, message: body.message, errors: body.errors };
        }
        return body;
    }

    async function mostrarError(err) {
        injectStyles();
        let title = 'No se pudo completar la operación';
        let html = `<p class="ls-confirm-lead">${esc(err?.message || 'Ocurrió un error inesperado. Intente nuevamente.')}</p>`;

        if (err?.status === 422 && err.errors) {
            title = 'Revise los datos ingresados';
            html = '<ul style="text-align:left;margin:0;padding-left:18px">' +
                Object.values(err.errors).flat().map((m) => `<li>${esc(m)}</li>`).join('') + '</ul>';
        } else if (err?.status === 419) {
            title = 'Sesión expirada';
            html = '<p class="ls-confirm-lead">El token de seguridad expiró. Recarga la página e intenta nuevamente.</p>';
        } else if (err?.status === 403) {
            title = 'Acceso denegado';
            html = '<p class="ls-confirm-lead">No tiene permisos para realizar este movimiento.</p>';
        } else if (err instanceof TypeError) {
            html = '<p class="ls-confirm-lead">No fue posible conectar con el servidor. Verifique su conexión.</p>';
        }

        await Swal.fire({
            icon: 'error',
            title,
            html,
            showDenyButton: false,
            showCancelButton: false,
            confirmButtonColor: COLORS.teal,
            customClass: { popup: 'ls-swal-popup' },
        });
    }

     // comprobante de exito (compatible con light mode y dark mode)

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
        reubicacion:              { title: '¡Ubicación de Producto Actualizada con Éxito!', badge: 'REUBICACIÓN EN BODEGA', op: 'Reubicación Física de Lote en Bodega', theme: 'ls-theme-teal', c: COLORS.teal },
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
        const cambioUbic = Boolean(
            p.cambio_ubicacion ||
            p.tipo === 'reubicacion' ||
            (p.ubicacion_anterior && p.ubicacion && p.ubicacion_anterior !== p.ubicacion)
        );

        return `
            <div class="ls-receipt ${t.theme}">
                <!-- banner del comprobante -->
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

                ${cambioUbic ? `
                    <div class="ls-confirm-card" style="margin-bottom:0.85rem">
                        <div class="ls-confirm-row">
                            <strong>Ubicación Anterior:</strong>
                            <span>${badgeUbicacion(p.ubicacion_anterior || 'Sin asignar (P.A)')}</span>
                        </div>
                        <div class="ls-confirm-row">
                            <strong>Nueva Ubicación Asignada:</strong>
                            <span>${badgeUbicacion(p.ubicacion || 'Sin asignar (P.A)')}</span>
                        </div>
                    </div>
                ` : ''}

                <!-- tabla resumen del lote -->
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

    // paso 4: alerta de exito. se espera (await) a que el usuario la cierre
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
            showDenyButton: false,
            showCancelButton: false,
            confirmButtonColor: t.c,
            confirmButtonText:
                '<svg style="width:16px;height:16px;display:inline-block;vertical-align:-3px;margin-right:4px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg> Aceptar y Continuar',
            allowOutsideClick: false,
            customClass: { popup: 'ls-swal-popup' },
        });
    }

    // orquestador comun a los tres escenarios
    async function procesar({ form, confirm, onSuccess, refresh = true }) {
        if (enProceso) return false;

        const confirmado = await confirmar(confirm);
        if (!confirmado) return false;

        enProceso = true;
        mostrarCargando();

        try {
            const { data } = await enviar(form);

            // cerrar modales del sistema antes de mostrar el exito
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

    // escenario 3: merma
    const Merma = {
        aplica(ctx) {
            return ctx.estado === 'Merma' || ctx.estadoInicial === 'Merma';
        },

        confirmConfig(ctx) {
            const prod = esc(ctx.producto);
            const lote = esc(ctx.lote);
            const cambioUbic = huboCambioUbicacion(ctx);
            const ubicHtml = cambioUbic
                ? `<div class="ls-confirm-row"><strong>Ubicación Anterior:</strong><span>${badgeUbicacion(ubicacionTexto(ctx.ubicacionInicial))}</span></div>
                   <div class="ls-confirm-row"><strong>Nueva Ubicación:</strong><span>${badgeUbicacion(ubicacionTexto(ctx.ubicacion))}</span></div>`
                : '';

            if (ctx.estadoInicial === 'Merma' && ctx.estado === 'Cuarentena') {
                return {
                    title: '¿Mover lote de Merma a Cuarentena?',
                    html: `
                        <div class="ls-confirm-wrap ls-theme-amber">
                            <p class="ls-confirm-lead">El producto <strong>${prod}</strong> (Lote: <strong>${lote}</strong>) será reactivado desde Merma y trasladado a <strong>Cuarentena</strong> para reevaluación técnica.</p>
                            <div class="ls-confirm-card">
                                <div class="ls-confirm-row"><strong>Transición:</strong><span>${badgeEstado('Merma')} ➔ ${badgeEstado('Cuarentena')}</span></div>
                                ${ubicHtml}
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
                                ${ubicHtml}
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

            // envio formal a merma
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
            if (!(await validarMotivo(ctx, requiereMotivo, 'Debe indicar el motivo de la merma (daño, vencimiento, rechazo, etc.).'))) return false;
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

     // escenario 2: cuarentena (bloqueo preventivo y control tecnico)

    const Cuarentena = {
        confirmConfig(ctx) {
            const prod = esc(ctx.producto);
            const lote = esc(ctx.lote);
            const cambioUbic = huboCambioUbicacion(ctx);
            const ubicHtml = cambioUbic
                ? `<div class="ls-confirm-row"><strong>Ubicación Anterior:</strong><span>${badgeUbicacion(ubicacionTexto(ctx.ubicacionInicial))}</span></div>
                   <div class="ls-confirm-row"><strong>Nueva Ubicación:</strong><span>${badgeUbicacion(ubicacionTexto(ctx.ubicacion))}</span></div>`
                : '';

            if (ctx.estado === 'Cuarentena' && ctx.estadoInicial !== 'Cuarentena') {
                return {
                    title: '¿Enviar lote a Cuarentena?',
                    html: `
                        <div class="ls-confirm-wrap ls-theme-amber">
                            <p class="ls-confirm-lead">El producto <strong>${prod}</strong> (Lote: <strong>${lote}</strong>) quedará <strong>bloqueado preventivamente</strong> y pasará a control técnico en Cuarentena.</p>
                            <div class="ls-confirm-card">
                                <div class="ls-confirm-row"><strong>Transición:</strong><span>${badgeEstado(ctx.estadoInicial || 'Disponible')} ➔ ${badgeEstado('Cuarentena')}</span></div>
                                ${ctx.cantidad ? `<div class="ls-confirm-row"><strong>Unidades retenidas:</strong><span>${esc(ctx.cantidad)}</span></div>` : ''}
                                ${ubicHtml}
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
                                ${ubicHtml}
                                ${ctx.motivo ? `<div class="ls-confirm-row"><strong>Observación:</strong><span>${esc(ctx.motivo)}</span></div>` : ''}
                            </div>
                        </div>`,
                    icon: 'question', color: COLORS.emerald, confirmText: 'Sí, liberar lote',
                };
            }
            if (cambioUbic) {
                return {
                    title: '¿Mover Ubicación de Lote en Cuarentena?',
                    html: `
                        <div class="ls-confirm-wrap ls-theme-teal">
                            <p class="ls-confirm-lead">¿Confirma el cambio de ubicación física en bodega para el lote <strong>${lote}</strong> de <strong>${prod}</strong>?</p>
                            <div class="ls-confirm-card">
                                ${ubicHtml}
                                ${ctx.motivo ? `<div class="ls-confirm-row"><strong>Motivo:</strong><span>${esc(ctx.motivo)}</span></div>` : ''}
                            </div>
                        </div>`,
                    icon: 'question', color: COLORS.teal, confirmText: 'Sí, mover ubicación',
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
            const soloReubicacion = ctx.estado === ctx.estadoInicial && huboCambioUbicacion(ctx);
            if (!soloReubicacion && !(await validarMotivo(ctx, true, 'Debe indicar la justificación u observación técnica correspondiente.'))) return false;
            if (ctx.estado !== 'Merma' && !(await Ubicacion.validarSeleccionEnModal(ctx))) return false;
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

     // escenario 1: inventario (cambio de estado / reubicacion)
    const Inventario = {
        confirmConfig(ctx) {
            const prod = esc(ctx.producto);
            const lote = esc(ctx.lote);
            const cambioEstado = ctx.estado !== ctx.estadoInicial;
            const cambioUbic = huboCambioUbicacion(ctx);

            if (!cambioEstado && cambioUbic) {
                return {
                    title: '¿Mover Ubicación de Producto en Bodega?',
                    html: `
                        <div class="ls-confirm-wrap ls-theme-teal">
                            <p class="ls-confirm-lead">¿Confirma el cambio de ubicación física en bodega para el lote <strong>${lote}</strong> de <strong>${prod}</strong>?</p>
                            <div class="ls-confirm-card">
                                <div class="ls-confirm-row"><strong>Ubicación Anterior:</strong><span>${badgeUbicacion(ubicacionTexto(ctx.ubicacionInicial))}</span></div>
                                <div class="ls-confirm-row"><strong>Nueva Ubicación:</strong><span>${badgeUbicacion(ubicacionTexto(ctx.ubicacion))}</span></div>
                            </div>
                        </div>`,
                    icon: 'question', color: COLORS.teal, confirmText: 'Sí, mover ubicación',
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
                                ${cambioUbic ? `<div class="ls-confirm-row"><strong>Ubicación Anterior:</strong><span>${badgeUbicacion(ubicacionTexto(ctx.ubicacionInicial))}</span></div>
                                <div class="ls-confirm-row"><strong>Nueva Ubicación:</strong><span>${badgeUbicacion(ubicacionTexto(ctx.ubicacion))}</span></div>` : ''}
                            </div>
                        </div>`,
                    icon: 'question', color: COLORS.emerald, confirmText: 'Sí, liberar lote',
                };
            }
            return {
                title: '¿Guardar Cambios?',
                html: `<p class="ls-confirm-lead">¿Confirma actualizar la información del lote <strong>${lote}</strong> de <strong>${prod}</strong>?</p>`,
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

            if (!(await Ubicacion.validarSeleccionEnModal(ctx))) return false;

            return procesar({ form, confirm: Inventario.confirmConfig(ctx) });
        },
    };

    // gestion jerarquica de ubicaciones fisicas y deteccion de posiciones/niveles en uso
    const Ubicacion = {
        normStr(v) {
            const s = String(v ?? '').trim();
            return (s.toUpperCase() === 'P.A' || s.toUpperCase() === 'N/A') ? '' : s;
        },
        normKey(v) {
            return this.normStr(v).toLowerCase();
        },
        normNivel(v) {
            const s = this.normStr(v);
            if (!s) return '';
            return s.replace(/^nivel\s*/i, '').trim() || s;
        },
        normPos(v) {
            const s = this.normStr(v);
            if (!s) return '';
            return s.replace(/^posici[oó]n\s*/i, '').trim() || s;
        },
        getJerarquia() {
            return Array.isArray(window.distribucionJerarquia) ? window.distribucionJerarquia : [];
        },
        getOcupadas() {
            return Array.isArray(window.ubicacionesOcupadas) ? window.ubicacionesOcupadas : [];
        },
        buscarEstanteInfo(estanteNombre) {
            const key = this.normKey(estanteNombre);
            if (!key) return null;
            return this.getJerarquia().find((e) => this.normKey(e.estante) === key) || null;
        },
        // devuelve todos los ocupantes activos en un estante (y opcionalmente nivel / posicion), excluyendo el lote que se esta editando (`ignoreloteid`) o el
        obtenerOcupantesEnEstante(estanteNombre, opts = {}) {
            const estKey = this.normKey(estanteNombre);
            if (!estKey) return [];

            const ignoreId = opts.ignoreLoteId ? parseInt(opts.ignoreLoteId, 10) : null;
            const mismoProd = this.normKey(opts.producto || '');
            const mismoLote = this.normKey(opts.lote || '');

            const ocupantes = [];

            for (const item of this.getOcupadas()) {
                if (ignoreId && parseInt(item.id, 10) === ignoreId) continue;
                if (this.normKey(item.estante) !== estKey) continue;

                if (mismoProd && this.normKey(item.producto) === mismoProd) {
                    const itemLoteNorm = this.normKey(item.lote === 'S/L' ? '' : item.lote);
                    const targetLoteNorm = (mismoLote === 's/l') ? '' : mismoLote;
                    if (itemLoteNorm === targetLoteNorm) continue;
                }

                ocupantes.push({
                    id: item.id,
                    estante: item.estante,
                    nivel: this.normNivel(item.nivel),
                    posicion: this.normPos(item.posicion),
                    producto: item.producto || 'Producto',
                    lote: item.lote || 'S/L',
                    cantidad: item.cantidad || 0,
                    origen: 'bodega',
                });
            }

            // verificar tambien otras filas del formulario de recepcion si aplica
            if (opts.rowElement) {
                const todasFilas = document.querySelectorAll('#productos_container .producto-item');
                todasFilas.forEach((fila, idx) => {
                    if (fila === opts.rowElement) return;
                    const selEst = fila.querySelector('select[name="estante[]"]');
                    const selNiv = fila.querySelector('select[name="nivel[]"]');
                    const selPos = fila.querySelector('select[name="posicion[]"]');
                    const selProd = fila.querySelector('select[name="product_id[]"]');
                    const inpLote = fila.querySelector('input[name="lote[]"]');

                    const estFila = this.normStr(selEst?.value);
                    if (this.normKey(estFila) !== estKey) return;

                    const nivFila = this.normNivel(selNiv?.value);
                    const posFila = this.normPos(selPos?.value);
                    if (!nivFila) return;

                    const nomProdFila = selProd && selProd.selectedIndex >= 0 && selProd.value
                        ? selProd.options[selProd.selectedIndex].text.trim()
                        : `Fila #${idx + 1} en esta recepción`;
                    const loteFila = this.normStr(inpLote?.value) || 'S/L';

                    ocupantes.push({
                        id: null,
                        estante: estFila,
                        nivel: nivFila,
                        posicion: posFila,
                        producto: nomProdFila,
                        lote: loteFila,
                        cantidad: 0,
                        origen: 'formulario',
                    });
                });
            }

            return ocupantes;
        },

        ocupanteDePosicion(estante, nivel, posicion, opts = {}) {
            const nivKey = this.normKey(this.normNivel(nivel));
            const posKey = this.normKey(this.normPos(posicion));
            if (!estante || !nivKey) return null;

            // un nivel sin posicion es un espacio compartido: lo limita la capacidad del estante, no un lote
            if (!posKey) return null;

            const ocupantesEst = this.obtenerOcupantesEnEstante(estante, opts);
            for (const oc of ocupantesEst) {
                if (this.normKey(oc.nivel) !== nivKey) continue;
                // solo ocupa la posicion el lote que esta guardado en esa posicion (un lote sin posicion ya no bloquea todas las posiciones del nivel)
                if (this.normKey(oc.posicion) === posKey) return oc;
            }
            return null;
        },

        evaluarNivel(estante, nivel, posicionesNivel = [], opts = {}) {
            const nivStr = this.normNivel(nivel);
            const nivKey = this.normKey(nivStr);
            const ocupantesEst = this.obtenerOcupantesEnEstante(estante, opts).filter(
                (o) => this.normKey(o.nivel) === nivKey
            );

            const listaPos = Array.isArray(posicionesNivel) ? posicionesNivel.map((p) => this.normPos(p)).filter(Boolean) : [];

            if (listaPos.length === 0) {
                // nivel sin posiciones definidas: espacio compartido, varios lotes pueden quedar aqui
                return {
                    nivel: nivStr,
                    totalPosiciones: 1,
                    ocupadasCount: 0,
                    disponiblesCount: 1,
                    completamenteOcupado: false,
                    compartido: true,
                    ocupantes: ocupantesEst,
                    detallePosiciones: [],
                };
            }

            // cada posicion la ocupa solo el lote guardado en ella
            let ocupadasCount = 0;
            const detallePosiciones = listaPos.map((pos) => {
                const posKey = this.normKey(pos);
                const oc = ocupantesEst.find((o) => this.normKey(o.posicion) === posKey) || null;
                if (oc) ocupadasCount++;
                return { posicion: pos, ocupante: oc };
            });

            const totalPosiciones = listaPos.length;
            const disponiblesCount = Math.max(0, totalPosiciones - ocupadasCount);

            return {
                nivel: nivStr,
                totalPosiciones,
                ocupadasCount,
                disponiblesCount,
                // el nivel no se bloquea entero: se puede usar sin posicion y las posiciones en uso se bloquean una por una
                completamenteOcupado: false,
                sinPosicionesLibres: disponiblesCount === 0 && totalPosiciones > 0,
                ocupantes: ocupantesEst,
                detallePosiciones,
            };
        },

        evaluarEstante(infoEstante, opts = {}) {
            if (!infoEstante || !infoEstante.estante) {
                return { totalSlots: 0, ocupadosCount: 0, disponiblesCount: 0, completamenteOcupado: false, nivelesEval: [], ocupantes: [] };
            }
            const nivelesRaw = Array.isArray(infoEstante.niveles) ? infoEstante.niveles : [];
            const ocupantes = this.obtenerOcupantesEnEstante(infoEstante.estante, opts);

            const capacidadEst = parseInt(infoEstante.capacidad, 10) || 0;
            const unidadesEst = ocupantes.reduce((a, o) => a + (parseInt(o.cantidad, 10) || 0), 0);

            if (nivelesRaw.length === 0) {
                const sinCap = capacidadEst > 0 && unidadesEst >= capacidadEst;
                return {
                    totalSlots: 0,
                    ocupadosCount: 0,
                    disponiblesCount: 1,
                    capacidad: capacidadEst,
                    unidades: unidadesEst,
                    sinCapacidad: sinCap,
                    completamenteOcupado: sinCap,
                    nivelesEval: [],
                    ocupantes,
                };
            }

            let totalSlots = 0;
            let ocupadosCount = 0;
            const nivelesEval = nivelesRaw.map((nObj) => {
                const ev = this.evaluarNivel(infoEstante.estante, nObj.nivel, nObj.posiciones || [], opts);
                totalSlots += ev.totalPosiciones;
                ocupadosCount += ev.ocupadasCount;
                return ev;
            });

            const disponiblesCount = Math.max(0, totalSlots - ocupadosCount);

            // capacidad del estante en unidades (0 = sin limite)
            const capacidad = parseInt(infoEstante.capacidad, 10) || 0;
            const unidades = ocupantes.reduce((a, o) => a + (parseInt(o.cantidad, 10) || 0), 0);
            const sinCapacidad = capacidad > 0 && unidades >= capacidad;
            const sinPosiciones = totalSlots > 0 && disponiblesCount === 0;

            return {
                totalSlots,
                ocupadosCount,
                disponiblesCount,
                capacidad,
                unidades,
                sinCapacidad,
                sinPosiciones,
                // el estante solo se bloquea por capacidad; las posiciones en uso se bloquean una por una
                completamenteOcupado: sinCapacidad,
                nivelesEval,
                ocupantes,
            };
        },

        async alertaUbicacionOcupada({ titulo, subtitulo, ocupantes = [], estante = '', nivel = '', posicion = '' }) {
            injectStyles();
            const ubicParts = [];
            if (estante) ubicParts.push(`Estante ${estante}`);
            if (nivel) ubicParts.push(`Nivel ${nivel}`);
            if (posicion) ubicParts.push(`Posición ${posicion}`);
            const ubicLabel = ubicParts.join(' · ') || 'Ubicación seleccionada';

            const filasHtml = ocupantes.slice(0, 6).map((oc) => {
                const det = [
                    oc.nivel ? `Nivel ${oc.nivel}` : '',
                    oc.posicion ? `Posición ${oc.posicion}` : 'Nivel completo',
                ].filter(Boolean).join(' · ');
                return `
                    <div class="ls-confirm-row" style="padding:3px 0;border-bottom:1px dashed var(--t-border)">
                        <strong>${esc(det || ubicLabel)}:</strong>
                        <span>${esc(oc.producto)} <code style="font-size:0.72rem;opacity:0.85">(Lote: ${esc(oc.lote)})</code></span>
                    </div>`;
            }).join('');

            await Swal.fire({
                icon: 'warning',
                title: titulo || 'Ubicación en Uso / Ocupada',
                html: `
                    <div class="ls-confirm-wrap ls-theme-amber">
                        <p class="ls-confirm-lead">${subtitulo || `La ubicación <strong>${esc(ubicLabel)}</strong> ya se encuentra ocupada y no está disponible para seleccionar.`}</p>
                        ${filasHtml ? `<div class="ls-confirm-card">${filasHtml}</div>` : ''}
                    </div>`,
                showDenyButton: false,
                showCancelButton: false,
                confirmButtonColor: COLORS.amber,
                confirmButtonText: 'Entendido',
                customClass: { popup: 'ls-swal-popup' },
            });
        },

        // actualiza los <option> de pasillo, estante, nivel y posicion manteniendo relacion jerarquica estricta y deshabilitando cualquier posicion o nivel que
        sincronizarSelectores(cfg, trigger = 'init', silentAlert = true) {
            const {
                selectPasillo,
                selectEstante,
                selectNivel,
                selectPosicion,
                statusEl,
                ignoreLoteId = null,
                rowElement = null,
                producto = '',
                lote = '',
                initialUbicacion = null,
            } = cfg;

            const opts = { ignoreLoteId, rowElement, producto, lote };
            const jerarquia = this.getJerarquia();

            // 1. si cambio el estante, auto-asignar su pasillo correspondiente
            let estanteVal = this.normStr(selectEstante?.value);
            let infoEstante = this.buscarEstanteInfo(estanteVal);

            if (trigger === 'estante' && infoEstante && infoEstante.pasillo && selectPasillo) {
                selectPasillo.value = infoEstante.pasillo;
            }

            const pasilloVal = this.normStr(selectPasillo?.value);
            const pasilloKey = this.normKey(pasilloVal);

            // 2. poblar estantes filtrados por pasillo
            if (selectEstante) {
                // al abrir el modal el <select> aun no tiene opciones, asi que su valor viene vacio: se usa la ubicacion actual del lote para no perderla
                const estanteInicial = initialUbicacion ? this.normStr(initialUbicacion.estante) : '';
                const prevEstante = estanteVal || (trigger === 'init' ? estanteInicial : '');
                selectEstante.innerHTML = '<option value="">Seleccione Estante...</option>';

                let estantesCandidatos = jerarquia.filter(
                    (item) => !pasilloKey || this.normKey(item.pasillo) === pasilloKey
                );
                if (estantesCandidatos.length === 0 && !pasilloKey && Array.isArray(window.todosLosEstantes)) {
                    estantesCandidatos = window.todosLosEstantes.map((e) => ({ estante: e, pasillo: '', niveles: [] }));
                }

                estantesCandidatos.forEach((item) => {
                    const evEst = this.evaluarEstante(item, opts);
                    const opt = document.createElement('option');
                    opt.value = item.estante;

                    const esMismoInicial = initialUbicacion && this.normKey(initialUbicacion.estante) === this.normKey(item.estante);
                    const fmtU = (n) => Number(n || 0).toLocaleString('es-CL');
                    const textoCap = evEst.capacidad > 0 ? ` · ${fmtU(evEst.unidades)}/${fmtU(evEst.capacidad)} u` : '';
                    if (evEst.completamenteOcupado && !esMismoInicial) {
                        opt.disabled = true;
                        opt.textContent = `${item.estante} — ⛔ SIN CAPACIDAD (${fmtU(evEst.unidades)}/${fmtU(evEst.capacidad)} u)`;
                    } else if (evEst.ocupadosCount > 0) {
                        opt.textContent = `${item.estante} (${evEst.disponiblesCount} pos. libres${textoCap})`;
                    } else {
                        opt.textContent = `${item.estante}${textoCap ? ` (${textoCap.slice(3)})` : ''}`;
                    }

                    if (this.normKey(item.estante) === this.normKey(prevEstante) && !opt.disabled) {
                        opt.selected = true;
                    }
                    selectEstante.appendChild(opt);
                });

                // si el estante actual del lote no aparece en la lista del pasillo, se agrega igual para que guardar sin tocar la ubicacion nunca la borre
                const estanteEnLista = estantesCandidatos.some((item) => this.normKey(item.estante) === this.normKey(prevEstante));
                if (trigger === 'init' && prevEstante && prevEstante.toUpperCase() !== 'P.A' && !estanteEnLista) {
                    const optActual = document.createElement('option');
                    optActual.value = prevEstante;
                    optActual.textContent = `${prevEstante} (ubicación actual)`;
                    optActual.selected = true;
                    selectEstante.appendChild(optActual);
                }

                estanteVal = this.normStr(selectEstante.value);
                infoEstante = this.buscarEstanteInfo(estanteVal);
            }

            // 3. poblar niveles exclusivos del estante seleccionado
            if (selectNivel) {
                const prevNivel = this.normNivel(selectNivel.value) || (trigger === 'init' && initialUbicacion ? this.normNivel(initialUbicacion.nivel) : '');
                selectNivel.innerHTML = '<option value="">Seleccione Nivel...</option>';

                if (infoEstante && Array.isArray(infoEstante.niveles) && infoEstante.niveles.length > 0) {
                    infoEstante.niveles.forEach((nObj) => {
                        const nivStr = this.normNivel(nObj.nivel);
                        const evNiv = this.evaluarNivel(infoEstante.estante, nivStr, nObj.posiciones || [], opts);
                        const opt = document.createElement('option');
                        opt.value = nivStr;

                        const esNivelInicial = initialUbicacion &&
                            this.normKey(initialUbicacion.estante) === this.normKey(infoEstante.estante) &&
                            this.normKey(this.normNivel(initialUbicacion.nivel)) === this.normKey(nivStr);

                        if (evNiv.completamenteOcupado && !esNivelInicial) {
                            opt.disabled = true;
                            const resumenOc = evNiv.ocupantes.slice(0, 2).map((o) => `${o.producto} (Lote ${o.lote})`).join(', ');
                            opt.textContent = `Nivel ${nivStr} — ⛔ OCUPADO (${resumenOc || 'En uso'})`;
                        } else if (evNiv.compartido) {
                            opt.textContent = evNiv.ocupantes.length > 0
                                ? `Nivel ${nivStr} (${evNiv.ocupantes.length} lote(s) aquí · espacio compartido)`
                                : `Nivel ${nivStr} (Disponible)`;
                        } else if (evNiv.ocupadasCount > 0) {
                            opt.textContent = evNiv.sinPosicionesLibres
                                ? `Nivel ${nivStr} (todas las posiciones en uso)`
                                : `Nivel ${nivStr} (${evNiv.disponiblesCount} de ${evNiv.totalPosiciones} pos. libres)`;
                        } else {
                            opt.textContent = `Nivel ${nivStr} (Disponible)`;
                        }

                        if (this.normKey(nivStr) === this.normKey(prevNivel) && !opt.disabled) {
                            opt.selected = true;
                        }
                        selectNivel.appendChild(opt);
                    });
                } else if (estanteVal && Array.isArray(window.todosLosNiveles)) {
                    window.todosLosNiveles.forEach((nivRaw) => {
                        const nivStr = this.normNivel(nivRaw);
                        if (!nivStr) return;
                        const evNiv = this.evaluarNivel(estanteVal, nivStr, window.todosLasPosiciones || [], opts);
                        const opt = document.createElement('option');
                        opt.value = nivStr;
                        if (evNiv.completamenteOcupado) {
                            opt.disabled = true;
                            opt.textContent = `Nivel ${nivStr} — ⛔ OCUPADO (En uso)`;
                        } else if (evNiv.ocupadasCount > 0) {
                            opt.textContent = `Nivel ${nivStr} (${evNiv.disponiblesCount} disp.)`;
                        } else {
                            opt.textContent = `Nivel ${nivStr}`;
                        }
                        if (this.normKey(nivStr) === this.normKey(prevNivel) && !opt.disabled) {
                            opt.selected = true;
                        }
                        selectNivel.appendChild(opt);
                    });
                }
            }

            // 4. poblar posiciones exclusivas del (estante + nivel) seleccionado, deshabilitando las ocupadas
            const nivelVal = this.normNivel(selectNivel?.value);
            if (selectPosicion) {
                const prevPos = this.normPos(selectPosicion.value) || (trigger === 'init' && initialUbicacion ? this.normPos(initialUbicacion.posicion) : '');
                selectPosicion.innerHTML = '<option value="">Seleccione Posición...</option>';

                let posicionesDelNivel = [];
                if (infoEstante && nivelVal && Array.isArray(infoEstante.niveles)) {
                    const infoNiv = infoEstante.niveles.find(
                        (n) => this.normKey(this.normNivel(n.nivel)) === this.normKey(nivelVal)
                    );
                    if (infoNiv && Array.isArray(infoNiv.posiciones)) {
                        posicionesDelNivel = infoNiv.posiciones;
                    }
                } else if (estanteVal && nivelVal && Array.isArray(window.todosLasPosiciones)) {
                    posicionesDelNivel = window.todosLasPosiciones;
                }

                posicionesDelNivel.forEach((posRaw) => {
                    const posStr = this.normPos(posRaw);
                    if (!posStr) return;
                    const ocupante = this.ocupanteDePosicion(estanteVal, nivelVal, posStr, opts);
                    const opt = document.createElement('option');
                    opt.value = posStr;

                    if (ocupante) {
                        opt.disabled = true;
                        opt.textContent = `Posición ${posStr} — ⛔ EN USO (${ocupante.producto} · Lote ${ocupante.lote})`;
                    } else {
                        opt.textContent = `Posición ${posStr} (Disponible)`;
                        if (this.normKey(posStr) === this.normKey(prevPos)) {
                            opt.selected = true;
                        }
                    }
                    selectPosicion.appendChild(opt);
                });
            }

            // 5. alertas sweetalert2 cuando el usuario selecciona un estante o nivel con ubicaciones ocupadas
            if (!silentAlert) {
                if (trigger === 'estante' && infoEstante) {
                    const evEst = this.evaluarEstante(infoEstante, opts);
                    if (evEst.completamenteOcupado) {
                        this.alertaUbicacionOcupada({
                            titulo: 'Estante sin capacidad disponible',
                            subtitulo: `El <strong>${esc(infoEstante.estante)}</strong> ya tiene <strong>${evEst.unidades.toLocaleString('es-CL')}</strong> de <strong>${evEst.capacidad.toLocaleString('es-CL')}</strong> unidades. Elija otro estante o aumente su capacidad en Distribución.`,
                            ocupantes: evEst.ocupantes,
                            estante: infoEstante.estante,
                        });
                        if (selectEstante) selectEstante.value = '';
                    }
                } else if (trigger === 'nivel' && estanteVal && nivelVal) {
                    const posList = (infoEstante?.niveles?.find((n) => this.normKey(this.normNivel(n.nivel)) === this.normKey(nivelVal))?.posiciones) || [];
                    const evNiv = this.evaluarNivel(estanteVal, nivelVal, posList, opts);
                    if (evNiv.completamenteOcupado) {
                        this.alertaUbicacionOcupada({
                            titulo: 'Nivel Ocupado / Sin Posiciones Libres',
                            subtitulo: `El <strong>Nivel ${esc(nivelVal)}</strong> del <strong>${esc(estanteVal)}</strong> ya se encuentra ocupado en su totalidad.`,
                            ocupantes: evNiv.ocupantes,
                            estante: estanteVal,
                            nivel: nivelVal,
                        });
                        if (selectNivel) selectNivel.value = '';
                    }
                } else if (trigger === 'posicion' && estanteVal && nivelVal && selectPosicion?.value) {
                    const posVal = this.normPos(selectPosicion.value);
                    const ocupante = this.ocupanteDePosicion(estanteVal, nivelVal, posVal, opts);
                    if (ocupante) {
                        this.alertaUbicacionOcupada({
                            titulo: 'Posición Ocupada',
                            subtitulo: `La <strong>Posición ${esc(posVal)}</strong> del <strong>Nivel ${esc(nivelVal)} (${esc(estanteVal)})</strong> ya está en uso por otro lote.`,
                            ocupantes: [ocupante],
                            estante: estanteVal,
                            nivel: nivelVal,
                            posicion: posVal,
                        });
                        selectPosicion.value = '';
                    }
                }
            }

            // 6. actualizar indicador visual de estado de ubicacion debajo de los desplegables
            this.actualizarIndicadorVisual(cfg);
        },

        actualizarIndicadorVisual(cfg) {
            const {
                selectPasillo,
                selectEstante,
                selectNivel,
                selectPosicion,
                statusEl,
                ignoreLoteId = null,
                rowElement = null,
                producto = '',
                lote = '',
                initialUbicacion = null,
            } = cfg;

            if (!statusEl) return;

            const pas = this.normStr(selectPasillo?.value);
            const est = this.normStr(selectEstante?.value);
            const niv = this.normNivel(selectNivel?.value);
            const pos = this.normPos(selectPosicion?.value);
            const opts = { ignoreLoteId, rowElement, producto, lote };

            if (!est) {
                statusEl.className = 'mt-2.5 px-3 py-2 rounded-lg text-[11.5px] font-medium bg-slate-100 text-slate-600 border border-slate-200 flex items-center justify-between gap-2';
                statusEl.innerHTML = '<span>Seleccione Pasillo y Estante para ver sus niveles y posiciones disponibles.</span>';
                return;
            }

            const infoEst = this.buscarEstanteInfo(est);
            const evEst = infoEst ? this.evaluarEstante(infoEst, opts) : null;

            if (est && niv && pos) {
                const ocupante = this.ocupanteDePosicion(est, niv, pos, opts);
                if (ocupante) {
                    statusEl.className = 'mt-2.5 px-3 py-2 rounded-lg text-[11.5px] font-bold bg-rose-50 text-rose-800 border border-rose-200 flex flex-wrap items-center justify-between gap-2';
                    statusEl.innerHTML = `<span>⛔ Ubicación ocupada por <strong>${esc(ocupante.producto)}</strong> (Lote: ${esc(ocupante.lote)})</span>`;
                    return;
                }

                const nuevaTxt = ubicacionTexto({ pasillo: pas, estante: est, nivel: niv, posicion: pos });
                const inicialTxt = initialUbicacion ? ubicacionTexto(initialUbicacion) : '';
                const cambio = initialUbicacion && inicialTxt !== nuevaTxt;

                statusEl.className = 'mt-2.5 px-3 py-2 rounded-lg text-[11.5px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200 flex flex-wrap items-center justify-between gap-2';
                statusEl.innerHTML = cambio
                    ? `<span>✅ Ubicación disponible</span><span class="font-mono text-[11px]">${esc(inicialTxt)} ➔ <strong>${esc(nuevaTxt)}</strong></span>`
                    : `<span>✅ Ubicación disponible: <strong>${esc(nuevaTxt)}</strong></span>`;
                return;
            }

            if (evEst && evEst.ocupadosCount > 0) {
                const resumenOcupados = evEst.ocupantes
                    .slice(0, 3)
                    .map((o) => `N${o.nivel}${o.posicion ? '-P' + o.posicion : ''} (${o.producto})`)
                    .join(' · ');
                statusEl.className = 'mt-2.5 px-3 py-2 rounded-lg text-[11.5px] font-medium bg-amber-50 text-amber-800 border border-amber-200 flex flex-wrap items-center justify-between gap-2';
                statusEl.innerHTML = `<span>⚠️ <strong>${esc(est)}</strong>: ${evEst.disponiblesCount} pos. libre(s) · ${evEst.ocupadosCount} en uso (${esc(resumenOcupados)})</span>`;
            } else {
                statusEl.className = 'mt-2.5 px-3 py-2 rounded-lg text-[11.5px] font-medium bg-teal-50 text-teal-800 border border-teal-200 flex items-center justify-between gap-2';
                statusEl.innerHTML = `<span>✅ <strong>${esc(est)}</strong>: Todos sus niveles y posiciones están disponibles.</span>`;
            }
        },

        async validarSeleccionEnModal(ctx) {
            const est = this.normStr(ctx.ubicacion?.estante);
            const niv = this.normNivel(ctx.ubicacion?.nivel);
            const pos = this.normPos(ctx.ubicacion?.posicion);
            if (!est || !niv) return true;

            const ignoreLoteId = ctx.form?.dataset?.loteId ? parseInt(ctx.form.dataset.loteId, 10) : null;
            const ocupante = this.ocupanteDePosicion(est, niv, pos, {
                ignoreLoteId,
                producto: ctx.producto,
                lote: ctx.lote,
            });

            if (ocupante) {
                await this.alertaUbicacionOcupada({
                    titulo: 'Ubicación Ocupada / En Uso',
                    subtitulo: `No es posible asignar <strong>Estante ${esc(est)} · Nivel ${esc(niv)}${pos ? ' · Posición ' + esc(pos) : ''}</strong> porque ya está ocupada por otro lote.`,
                    ocupantes: [ocupante],
                    estante: est,
                    nivel: niv,
                    posicion: pos,
                });
                return false;
            }
            return true;
        },
    };

   // api publica
    window.LoteStatus = {
        Inventario,
        Cuarentena,
        Merma,
        Ubicacion,
        core: { confirmar, enviar, mostrarExito, mostrarError, procesar, leerContexto, construirComprobante },
    };
})(window, document);
