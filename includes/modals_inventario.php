<link rel="stylesheet" href="css/tu_archivo_estilos.css">

<div class="container-fluid mt-4 mb-5 px-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-0">Panel de Inventario</h4>
            <p class="text-muted small mb-0">Gestión de materiales - Obra LPDC</p>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <a href="historial.php" class="btn btn-outline-dark btn-modern shadow-sm">
                <i class="bi bi-clock-history me-2"></i>Historial
            </a>

            <button class="btn btn-dark btn-modern shadow-sm" data-bs-toggle="modal" data-bs-target="#modalUsuarios">
                <i class="bi bi-people me-2"></i>Usuarios
            </button>

            <a href="modules/exportar_excel.php" class="btn btn-success btn-modern shadow-sm">
                <i class="bi bi-file-earmark-excel me-2"></i>Exportar
            </a>

            <button class="btn btn-primary btn-modern shadow" data-bs-toggle="modal" data-bs-target="#modalNuevo">
                <i class="bi bi-plus-lg me-2"></i>Nuevo Material
            </button>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-8">
            <div class="input-group input-group-modern p-1">
                <span class="input-group-text bg-transparent border-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" id="buscador" class="form-control border-0 shadow-none"
                    placeholder="Escribe para buscar código, descripción o fabricante...">
            </div>
        </div>
        <div class="col-md-4">
            <div class="card custom-card h-100 py-2 px-3 border-start border-primary border-4">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 text-primary me-3">
                        <i class="bi bi-box-seam fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Items Registrados</small>
                        <span class="fw-bold fs-5"><?php echo count($productos); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card custom-card overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Código</th>
                        <th>Descripción del Material</th>
                        <th>Fabricante / Marca</th>
                        <th>Categoría</th>
                        <th class="text-center">Stock</th>
                        <th>Precio</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody id="filasInventario">
                    <?php foreach ($productos as $p):
                        $isLow = ($p['stock_actual'] <= $p['stock_minimo']);
                        $badgeClass = $isLow ? 'status-low' : 'status-ok';
                        ?>
                        <tr>
                            <td class="ps-4 text-primary fw-semibold small"><?php echo $p['codigo_interno']; ?></td>
                            <td>
                                <div class="fw-bold"><?php echo htmlspecialchars($p['nombre']); ?></div>
                                <div class="text-muted" style="font-size: 0.75rem;">
                                    <i class="bi bi-geo-alt me-1"></i><?php echo $p['almacen'] ?? 'Principal'; ?>
                                </div>
                            </td>
                            <td class="small"><?php echo $p['fabricante'] ?: '<span class="text-muted">-</span>'; ?></td>
                            <td><span class="badge bg-light text-dark border-0 px-2 py-1"><?php echo $p['tipo']; ?></span>
                            </td>
                            <td class="text-center">
                                <span class="stock-badge <?php echo $badgeClass; ?>">
                                    <?php echo number_format($p['stock_actual'], 2); ?>
                                    <small class="ms-1 fw-normal"><?php echo $p['unidad_medida']; ?></small>
                                </span>
                            </td>
                            <td class="fw-bold text-dark">S/ <?php echo number_format($p['precio_unitario'], 2); ?></td>
                            <td class="text-center">
                                <div class="btn-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                    <button class="btn btn-white btn-sm border-end"
                                        onclick='prepararEdicion(<?php echo json_encode($p); ?>)' title="Editar">
                                        <i class="bi bi-pencil-fill text-secondary"></i>
                                    </button>
                                    <button class="btn btn-white btn-sm border-end"
                                        onclick="prepararMovimiento(<?php echo $p['id']; ?>, '<?php echo addslashes($p['nombre']); ?>')"
                                        title="Movimiento">
                                        <i class="bi bi-arrow-left-right text-primary"></i>
                                    </button>
                                    <button class="btn btn-white btn-sm"
                                        onclick="eliminarProducto(<?php echo $p['id']; ?>, '<?php echo addslashes($p['nombre']); ?>')"
                                        title="Eliminar">
                                        <i class="bi bi-trash3-fill text-danger"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalNuevo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="formNuevoMaterial" class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header bg-primary text-white border-0 py-3"
                style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-plus-lg me-2"></i>Registrar Nuevo Artículo
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <h6 class="text-secondary fw-bold text-uppercase mb-3 pb-2 border-bottom"
                    style="font-size: 0.8rem; letter-spacing: 1px;">
                    <i class="bi bi-box me-1"></i> Información General
                </h6>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-secondary">CÓDIGO INTERNO</label>
                        <input type="text" name="codigo_interno" class="form-control" placeholder="Ingrese código"
                            required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label small fw-bold text-secondary">DESCRIPCIÓN / NOMBRE</label>
                        <input type="text" name="nombre" class="form-control" placeholder="Nombre completo del material"
                            required>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-secondary">UNIDAD</label>
                        <input type="text" name="unidad_medida" class="form-control" placeholder="UND, KG, PAR, M3">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-secondary">FABRICANTE</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-tools"></i></span>
                            <input list="datalistFabricantes" name="fabricante" class="form-control border-start-0"
                                placeholder="Buscar o crear...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-secondary">CATEGORÍA</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-tags"></i></span>
                            <input list="datalistCategorias" name="tipo" class="form-control border-start-0"
                                placeholder="Buscar o crear..." required>
                        </div>
                    </div>
                </div>

                <h6 class="text-secondary fw-bold text-uppercase mb-3 pb-2 border-bottom"
                    style="font-size: 0.8rem; letter-spacing: 1px;">
                    <i class="bi bi-safe2 me-1"></i> Inventario y Costos
                </h6>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-secondary">ALMACÉN</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-geo-alt"></i></span>
                            <input type="text" name="almacen" class="form-control border-start-0"
                                placeholder="Ubicación (Ej: Torre A, Almacén Central)">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-secondary">STOCK INICIAL</label>
                        <input type="number" step="0.01" name="stock_actual" class="form-control text-primary fw-bold"
                            value="0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-secondary">STOCK MÍNIMO</label>
                        <input type="number" step="0.01" name="stock_minimo" class="form-control text-danger fw-bold"
                            value="0">
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-secondary">PRECIO UNITARIO (S/)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 fw-semibold text-primary">S/</span>
                            <input type="number" step="0.01" name="precio_unitario"
                                class="form-control border-start-0 fw-bold" placeholder="0.00">
                        </div>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label small fw-bold text-secondary">OBSERVACIONES</label>
                        <textarea name="observaciones" class="form-control" rows="2"
                            placeholder="Notas internas adicionales..."></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 bg-light p-3"
                style="border-bottom-left-radius: 15px; border-bottom-right-radius: 15px;">
                <button type="button" class="btn btn-outline-secondary border-0 fw-bold"
                    data-bs-dismiss="modal">CANCELAR</button>
                <button type="submit" class="btn btn-primary px-5 fw-bold shadow">
                    <i class="bi bi-check-circle me-2"></i>GUARDAR MATERIAL
                </button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="formEditar" class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header bg-secondary text-white border-0 py-3"
                style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Actualizar Información</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="id" id="edit_id">

                <h6 class="text-secondary fw-bold text-uppercase mb-3 pb-2 border-bottom"
                    style="font-size: 0.8rem; letter-spacing: 1px;">
                    <i class="bi bi-info-circle me-1"></i> Datos Principales
                </h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-secondary">CÓDIGO</label>
                        <input type="text" name="codigo_interno" id="edit_codigo" class="form-control fw-bold">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label small fw-bold text-secondary">NOMBRE DEL MATERIAL</label>
                        <input type="text" name="nombre" id="edit_nombre" class="form-control">
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-secondary">UNIDAD</label>
                        <input type="text" name="unidad_medida" id="edit_unidad" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-secondary">FABRICANTE</label>
                        <input list="datalistFabricantes" name="fabricante" id="edit_fabricante" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-secondary">CATEGORÍA</label>
                        <input list="datalistCategorias" name="tipo" id="edit_tipo" class="form-control">
                    </div>
                </div>

                <h6 class="text-secondary fw-bold text-uppercase mb-3 pb-2 border-bottom"
                    style="font-size: 0.8rem; letter-spacing: 1px;">
                    <i class="bi bi-gear me-1"></i> Configuración de Inventario
                </h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-secondary">ALMACÉN</label>
                        <input type="text" name="almacen" id="edit_almacen" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-secondary">STOCK MÍN.</label>
                        <input type="number" step="0.01" name="stock_minimo" id="edit_stock_minimo"
                            class="form-control border-danger-subtle text-danger fw-bold">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-secondary">PRECIO (S/)</label>
                        <input type="number" step="0.01" name="precio_unitario" id="edit_precio"
                            class="form-control border-primary-subtle text-primary fw-bold">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light p-3"
                style="border-bottom-left-radius: 15px; border-bottom-right-radius: 15px;">
                <button type="button" class="btn btn-outline-secondary border-0"
                    data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-secondary px-4 fw-bold shadow">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalMovimiento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formMovimiento" class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white border-0 py-3">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-arrow-left-right me-2"></i>Registrar Movimiento
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <input type="hidden" name="producto_id" id="mov_producto_id">

                <div class="mb-4">
                    <label class="form-label small fw-bold text-uppercase text-muted">Material Seleccionado</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-box-seam"></i></span>
                        <input type="text" id="mov_nombre_producto" class="form-control bg-light border-0 fw-bold"
                            readonly>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-secondary"><i
                                class="bi bi-person me-1"></i>Responsable</label>
                        <input type="text" name="responsable" class="form-control shadow-sm" placeholder="Juan Pérez"
                            required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-secondary"><i
                                class="bi bi-geo-alt me-1"></i>Ubicación</label>
                        <input type="text" name="ubicacion" class="form-control shadow-sm"
                            placeholder="Ej: Los Portales">
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-secondary">Tipo de Movimiento</label>
                        <select name="tipo_movimiento" id="move_tipo" class="form-select shadow-sm fw-semibold">
                            <option value="entrada" class="text-success">➕ Entrada (Suma)</option>
                            <option value="salida" class="text-danger">➖ Salida (Resta)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-secondary">Cantidad</label>
                        <input type="number" step="0.01" name="cantidad"
                            class="form-control shadow-sm fw-bold text-primary" placeholder="0.00" required>
                    </div>

                    <div class="col-12" id="contenedor_precio">
                        <label class="form-label small fw-bold text-success">
                            <i class="bi bi-currency-dollar me-1"></i>COSTO TOTAL DEL INGRESO (Soles)
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-success text-white border-0">S/</span>
                            <input type="number" step="0.01" name="precio_movimiento"
                                class="form-control border-success shadow-sm fw-bold" placeholder="0.00">
                        </div>
                        <small class="text-muted" style="font-size: 0.75rem;">Indica cuánto costó este nuevo ingreso
                            para acumularlo al precio actual.</small>
                    </div>
                </div>

                <div class="mb-0">
                    <label class="form-label small fw-bold text-secondary">Observaciones Adicionales</label>
                    <textarea name="observaciones" class="form-control shadow-sm" rows="2"
                        placeholder="Notas internas..."></textarea>
                </div>
            </div>

            <div class="modal-footer border-0 bg-light p-3">
                <button type="button" class="btn btn-outline-secondary border-0 fw-bold"
                    data-bs-dismiss="modal">CANCELAR</button>
                <button type="submit" class="btn btn-primary px-5 fw-bold shadow">
                    <i class="bi bi-check-circle me-2"></i>CONFIRMAR
                </button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalUsuarios" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header bg-dark text-white border-0 py-3"
                style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
                <h5 class="modal-title fw-bold"><i class="bi bi-people-fill me-2"></i>Gestión de Personal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-md-4 border-end pe-4">
                        <div class="bg-light p-3 rounded-3 mb-3">
                            <h6 id="tituloFormUsuario" class="fw-bold mb-0 text-dark">
                                <i class="bi bi-person-plus me-2"></i>Crear Usuario
                            </h6>
                        </div>
                        <form id="formUsuario">
                            <input type="hidden" name="action" id="user_action" value="crear">
                            <input type="hidden" name="id" id="user_id_input">

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary">NOMBRE DE USUARIO</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-at"></i></span>
                                    <input type="text" name="usuario" id="user_name" class="form-control border-start-0"
                                        placeholder="Escriba su nombre" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary">CONTRASEÑA</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i
                                            class="bi bi-key"></i></span>
                                    <input type="password" name="password" id="user_pass"
                                        class="form-control border-start-0" placeholder="••••••••">
                                </div>
                                <small class="text-muted mt-1 d-block" id="passHelp" style="display:none;">
                                    <i class="bi bi-info-circle me-1"></i>Dejar en blanco para no cambiar
                                </small>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-secondary">ROL ASIGNADO</label>
                                <div class="form-control bg-light fw-bold text-primary border-0 shadow-sm d-flex align-items-center"
                                    style="height: 38px;">
                                    ADMINISTRADOR
                                </div>
                                <input type="hidden" name="rol" id="user_rol" value="admin">
                            </div>

                            <button type="submit" id="btnGuardarUser"
                                class="btn btn-primary w-100 fw-bold shadow-sm py-2">
                                <i class="bi bi-save me-2"></i>GUARDAR USUARIO
                            </button>
                            <button type="button" id="btnCancelarEdicion"
                                class="btn btn-link w-100 text-decoration-none mt-2 text-danger small fw-bold"
                                style="display:none;">
                                CANCELAR EDICIÓN
                            </button>
                        </form>
                    </div>

                    <div class="col-md-8 ps-4">
                        <h6 class="fw-bold mb-3 text-secondary text-uppercase small" style="letter-spacing: 1px;">
                            Usuarios en el Sistema</h6>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle border-top">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="border-0 py-3">Usuario</th>
                                        <th class="border-0 py-3">Rol</th>
                                        <th class="border-0 py-3 text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="listaUsuarios">
                                    <?php
                                    $all_users = $pdo->query("SELECT id, usuario, rol FROM usuarios")->fetchAll();
                                    foreach ($all_users as $u): ?>
                                        <tr id="user_row_<?php echo $u['id']; ?>">
                                            <td class="fw-bold text-dark">
                                                <i
                                                    class="bi bi-person-circle me-2 text-primary"></i><?php echo $u['usuario']; ?>
                                            </td>
                                            <td><span
                                                    class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3"><?php echo $u['rol']; ?></span>
                                            </td>
                                            <td class="text-end">
                                                <button class="btn btn-sm btn-light border shadow-sm mx-1"
                                                    onclick='editarUser(<?php echo json_encode($u); ?>)' title="Editar">
                                                    <i class="bi bi-pencil text-primary"></i>
                                                </button>
                                                <button class="btn btn-sm btn-light border shadow-sm"
                                                    onclick="eliminarUser(<?php echo $u['id']; ?>)" title="Eliminar">
                                                    <i class="bi bi-trash text-danger"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<datalist id="datalistCategorias">
    <?php foreach ($todas_categorias as $cat): ?>
        <option value="<?php echo htmlspecialchars($cat); ?>">
        <?php endforeach; ?>
</datalist>

<datalist id="datalistFabricantes">
    <?php foreach ($todos_fabricantes as $fab): ?>
        <option value="<?php echo htmlspecialchars($fab); ?>">
        <?php endforeach; ?>
</datalist>