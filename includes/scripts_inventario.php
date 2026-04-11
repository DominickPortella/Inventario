<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function prepararEdicion(p) {
        document.getElementById('edit_id').value = p.id;
        document.getElementById('edit_nombre').value = p.nombre;
        document.getElementById('edit_codigo').value = p.codigo_interno;
        document.getElementById('edit_unidad').value = p.unidad_medida;
        document.getElementById('edit_fabricante').value = p.fabricante || '';
        document.getElementById('edit_tipo').value = p.tipo;
        document.getElementById('edit_almacen').value = p.almacen || 'OB. MULTIFAM PARDO';
        document.getElementById('edit_stock_minimo').value = p.stock_minimo;
        document.getElementById('edit_precio').value = p.precio_unitario || 0;
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

                fetch(url, { method: 'POST', body: new FormData(this) })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({ icon: 'success', title: '¡Hecho!', timer: 1000, showConfirmButton: false })
                                .then(() => location.reload());
                        } else {
                            Swal.fire('Error', data.message, 'error');
                            btn.disabled = false;
                        }
                    }).catch(() => {
                        Swal.fire('Error', 'Error de conexión', 'error');
                        btn.disabled = false;
                    });
            });
        };

        handleAJAX('formEditar', 'actualizar_producto_ajax.php');
        handleAJAX('formMovimiento', 'guardar_movimiento.php');
    });

    // Gestión de Usuarios
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

    document.getElementById('btnCancelarEdicion').onclick = function () {
        location.reload();
    };

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

    document.getElementById('formUsuario').onsubmit = function (e) {
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
</script>