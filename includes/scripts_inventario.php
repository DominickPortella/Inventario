<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function prepararEdicion(p) {
        // Log para depuración: Abre la consola (F12) y verás si p trae los datos
        console.log("Datos recibidos para editar:", p);

        document.getElementById('edit_id').value = p.id;
        document.getElementById('edit_nombre').value = p.nombre;
        document.getElementById('edit_codigo').value = p.codigo_interno;
        document.getElementById('edit_unidad').value = p.unidad_medida;

        // CORRECCIÓN: Aseguramos que cargue fabricante y almacén aunque sean nulos
        document.getElementById('edit_fabricante').value = p.fabricante !== null ? p.fabricante : '';
        document.getElementById('edit_tipo').value = p.tipo;
        document.getElementById('edit_almacen').value = p.almacen !== null ? p.almacen : 'OB. MULTIFAM PARDO';

        document.getElementById('edit_stock_minimo').value = p.stock_minimo;

        // IMPORTANTE: Asegúrate que la propiedad sea p.precio_unitario (como en tu SQL)
        document.getElementById('edit_precio').value = p.precio_unitario !== null ? p.precio_unitario : 0;

        new bootstrap.Modal(document.getElementById('modalEditar')).show();
    }

    function prepararMovimiento(id, nombre) {
        document.getElementById('mov_producto_id').value = id;
        document.getElementById('mov_nombre_producto').value = nombre;
        new bootstrap.Modal(document.getElementById('modalMovimiento')).show();
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Buscador Realtime
        const buscador = document.getElementById('buscador');
        if (buscador) {
            buscador.addEventListener('keyup', function () {
                let filtro = this.value.toLowerCase();
                document.querySelectorAll('#filasInventario tr').forEach(fila => {
                    fila.style.display = fila.textContent.toLowerCase().includes(filtro) ? '' : 'none';
                });
            });
        }

        // Helper para AJAX de Productos
        const handleAJAX = (formId, url) => {
            const form = document.getElementById(formId);
            if (!form) return;
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const btn = this.querySelector('button[type="submit"]');
                btn.disabled = true;

                // FormData captura TODOS los inputs que tengan un atributo 'name'
                const datos = new FormData(this);

                fetch(url, { method: 'POST', body: datos })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({ icon: 'success', title: '¡Hecho!', timer: 1000, showConfirmButton: false })
                                .then(() => location.reload()); // RECARGA para ver cambios
                        } else {
                            Swal.fire('Error', data.message, 'error');
                            btn.disabled = false;
                        }
                    }).catch(err => {
                        console.error("Error en el fetch:", err);
                        Swal.fire('Error', 'Error de conexión al servidor', 'error');
                        btn.disabled = false;
                    });
            });
        };

        // Este script hace que el precio aparezca SOLO cuando eligen "Entrada"
        document.getElementById('move_tipo').addEventListener('change', function () {
            const contenedorPrecio = document.getElementById('contenedor_precio');
            if (this.value === 'entrada') {
                contenedorPrecio.style.display = 'block';
            } else {
                contenedorPrecio.style.display = 'none';
                // Opcional: limpiar el valor si eligen salida
                contenedorPrecio.querySelector('input').value = '';
            }
        });

        handleAJAX('formEditar', 'actualizar_producto_ajax.php');
        handleAJAX('formMovimiento', 'guardar_movimiento.php');
        handleAJAX('formNuevoMaterial', 'guardar_producto.php');
    });

    // Gestión de Usuarios (Sin cambios, está correcto)
    function editarUser(u) {
        document.getElementById('tituloFormUsuario').innerText = "Editando a: " + u.usuario;
        document.getElementById('user_action').value = "editar";
        document.getElementById('user_id_input').value = u.id;
        document.getElementById('user_name').value = u.usuario;
        document.getElementById('user_rol').value = u.rol;
        document.getElementById('user_pass').placeholder = "Nueva contraseña (opcional)";
        document.getElementById('passHelp').style.display = "block";
        document.getElementById('btnGuardarUser').className = "btn btn-warning w-100";
        document.getElementById('btnGuardarUser').innerText = "Actualizar Usuario";
        document.getElementById('btnCancelarEdicion').style.display = "block";
    }

    // El botón cancelar debe estar fuera del DOMContentLoaded si se llama desde el HTML
    const btnCancel = document.getElementById('btnCancelarEdicion');
    if (btnCancel) {
        btnCancel.onclick = function () {
            location.reload();
        };
    }

    function eliminarUser(id) {
        Swal.fire({
            title: '¿Eliminar usuario?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Sí, borrar'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('action', 'eliminar');
                formData.append('id', id);

                fetch('gestion_usuarios_ajax.php', { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            document.getElementById('user_row_' + id).remove();
                            Swal.fire('Borrado', 'Usuario eliminado', 'success');
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    });
            }
        });
    }

    const formUser = document.getElementById('formUsuario');
    if (formUser) {
        formUser.onsubmit = function (e) {
            e.preventDefault();
            fetch('gestion_usuarios_ajax.php', { method: 'POST', body: new FormData(this) })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({ icon: 'success', title: '¡Listo!', timer: 800, showConfirmButton: false })
                            .then(() => location.reload());
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                });
        };
    }

    function eliminarProducto(id, nombre) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: `Vas a eliminar el material: ${nombre}. Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Enviamos la petición por AJAX
                fetch('eliminar_producto.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${id}`
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire('¡Eliminado!', data.message, 'success')
                                .then(() => location.reload()); // Recargamos para actualizar la lista
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error', 'No se pudo procesar la solicitud', 'error');
                    });
            }
        });
    }
</script>