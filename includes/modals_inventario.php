<div class="container-fluid mt-4 mb-5 px-4">
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-3">
            <div class="card custom-card p-3 d-flex flex-row align-items-center bg-white h-100">
                <div class="stat-icon me-3"><i class="bi bi-box-seam fs-3"></i></div>
                <div>
                    <h6 class="text-muted mb-0">Artículos Totales</h6>
                    <h3 class="fw-bold mb-0 text-primary"><?php echo count($productos); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-9">
            <div class="row g-2 h-100">
                <div class="col-md-5">
                    <div class="input-group border rounded-3 bg-white shadow-sm h-100">
                        <span class="input-group-text bg-white border-0"><i class="bi bi-search"></i></span>
                        <input type="text" id="buscador" class="form-control border-0"
                            placeholder="Buscar por nombre, código o categoría...">
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <a href="exportar_excel.php"
                        class="btn btn-success w-100 h-100 d-flex align-items-center justify-content-center fw-bold shadow-sm">
                        <i class="bi bi-file-earmark-excel me-2"></i> Excel
                    </a>
                </div>
                <div class="col-6 col-md-2">
                    <button
                        class="btn btn-dark w-100 h-100 d-flex align-items-center justify-content-center fw-bold shadow-sm"
                        data-bs-toggle="modal" data-bs-target="#modalUsuarios">
                        <i class="bi bi-people me-2"></i> Usuarios
                    </button>
                </div>
                <div class="col-6 col-md-2">
                    <a href="historial.php"
                        class="btn btn-white border w-100 h-100 d-flex align-items-center justify-content-center fw-bold bg-white shadow-sm">
                        <i class="bi bi-clock-history me-2"></i> Historial
                    </a>
                </div>
                <div class="col-12 col-md-3">
                    <button class="btn btn-primary shadow w-100 h-100 fw-bold" data-bs-toggle="modal"
                        data-bs-target="#modalNuevo">
                        <i class="bi bi-plus-circle me-2"></i> Nuevo Material
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card custom-card overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tablaInventario">
                <thead class="bg-light text-muted">
                    <tr>
                        <th class="ps-4">Código</th>
                        <th>Descripción</th>
                        <th>Unidad</th>
                        <th>Fabricante</th>
                        <th>Categoría</th>
                        <th>Almacén</th>
                        <th class="text-center">Stock Actual</th>
                        <th>Precio S/</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody id="filasInventario">
                    <?php foreach ($productos as $p):
                        $isLow = ($p['stock_actual'] <= $p['stock_minimo']);
                        $badgeClass = $isLow ? 'status-low' : 'status-ok';
                        ?>
                        <tr>
                            <td class="ps-4 fw-bold small text-primary"><?php echo $p['codigo_interno']; ?></td>
                            <td>
                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($p['nombre']); ?></div>
                            </td>
                            <td class="small"><?php echo $p['unidad_medida']; ?></td>
                            <td class="small"><?php echo $p['fabricante'] ?? '-'; ?></td>
                            <td><span class="badge bg-light text-dark border small"><?php echo $p['tipo']; ?></span>
                            </td>
                            <td class="small text-muted"><?php echo $p['almacen'] ?? 'OB. MULTIFAM PARDO'; ?></td>
                            <td class="text-center">
                                <span class="stock-badge <?php echo $badgeClass; ?>">
                                    <?php echo number_format($p['stock_actual'], 2); ?>
                                </span>
                            </td>
                            <td class="fw-bold small">S/ <?php echo number_format($p['precio_unitario'], 2); ?></td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm"
                                        onclick='prepararEdicion(<?php echo json_encode($p); ?>)' title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-primary btn-sm"
                                        onclick="prepararMovimiento(<?php echo $p['id']; ?>, '<?php echo htmlspecialchars($p['nombre'], ENT_QUOTES); ?>')"
                                        title="Movimiento">
                                        <i class="bi bi-arrow-left-right"></i>
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
        <form action="guardar_producto.php" method="POST" class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-lg me-2"></i>Registrar Nuevo Artículo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">CÓDIGO INTERNO</label>
                        <input type="text" name="codigo_interno" class="form-control" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-bold small">DESCRIPCIÓN / NOMBRE</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">UNIDAD</label>
                        <input type="text" name="unidad_medida" class="form-control" placeholder="UND, KG, PAR">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">FABRICANTE</label>
                        <input list="datalistFabricantes" name="fabricante" class="form-control"
                            placeholder="Buscar fabricante..." autocomplete="off">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">CATEGORÍA</label>
                        <input list="datalistCategorias" name="tipo" class="form-control"
                            placeholder="Buscar categoría..." required autocomplete="off">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">ALMACÉN</label>
                        <input type="text" name="almacen" class="form-control" value="OB. MULTIFAM PARDO">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">STOCK INICIAL</label>
                        <input type="number" step="0.01" name="stock_actual" class="form-control" value="0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">STOCK MÍNIMO</label>
                        <input type="number" step="0.01" name="stock_minimo" class="form-control" value="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">PRECIO UNITARIO (S/)</label>
                        <input type="number" step="0.01" name="precio_unitario" class="form-control">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-bold small">OBSERVACIONES</label>
                        <textarea name="observaciones" class="form-control" rows="1"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary px-4 fw-bold">Guardar Material</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="formEditar" class="modal-content border-0 shadow">
            <div class="modal-header bg-secondary text-white border-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Editar Información</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="id" id="edit_id">
                <div class="row g-3">
                    <div class="col-md-4"><label class="form-label fw-bold small">CÓDIGO</label><input type="text"
                            name="codigo_interno" id="edit_codigo" class="form-control"></div>
                    <div class="col-md-8"><label class="form-label fw-bold small">NOMBRE</label><input type="text"
                            name="nombre" id="edit_nombre" class="form-control"></div>
                    <div class="col-md-4"><label class="form-label fw-bold small">UNIDAD</label><input type="text"
                            name="unidad_medida" id="edit_unidad" class="form-control"></div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">FABRICANTE</label>
                        <input list="datalistFabricantes" name="fabricante" id="edit_fabricante" class="form-control"
                            autocomplete="off">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">CATEGORÍA</label>
                        <input list="datalistCategorias" name="tipo" id="edit_tipo" class="form-control"
                            autocomplete="off">
                    </div>
                    <div class="col-md-6"><label class="form-label fw-bold small">ALMACÉN</label><input type="text"
                            name="almacen" id="edit_almacen" class="form-control"></div>
                    <div class="col-md-3"><label class="form-label fw-bold small">STOCK MÍN.</label><input type="number"
                            step="0.01" name="stock_minimo" id="edit_stock_minimo" class="form-control"></div>
                    <div class="col-md-3"><label class="form-label fw-bold small">PRECIO S/</label><input type="number"
                            step="0.01" name="precio_unitario" id="edit_precio" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-secondary px-4 fw-bold">Actualizar Cambios</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalMovimiento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formMovimiento" class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white border-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-arrow-left-right me-2"></i>Movimiento de Stock</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="producto_id" id="mov_producto_id">
                <div class="mb-3">
                    <label class="form-label fw-bold">Material Seleccionado</label>
                    <input type="text" id="mov_nombre_producto" class="form-control bg-light" readonly>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tipo</label>
                        <select name="tipo_movimiento" class="form-select" required>
                            <option value="entrada">➕ Entrada</option>
                            <option value="salida">➖ Salida</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Cantidad</label>
                        <input type="number" step="0.01" name="cantidad" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold small">Responsable / Ubicación / Frente</label>
                        <input type="text" name="referencia" class="form-control" placeholder="Ej: Portella - Torre A">
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary px-4 fw-bold">Confirmar</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalUsuarios" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title"><i class="bi bi-people-fill me-2"></i>Gestión de Personal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-md-4 border-end">
                        <h6 id="tituloFormUsuario" class="fw-bold mb-3">Crear Nuevo Usuario</h6>
                        <form id="formUsuario">
                            <input type="hidden" name="action" id="user_action" value="crear">
                            <input type="hidden" name="id" id="user_id_input">
                            <div class="mb-3">
                                <label class="small fw-bold">Usuario</label>
                                <input type="text" name="usuario" id="user_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="small fw-bold">Contraseña</label>
                                <input type="password" name="password" id="user_pass" class="form-control"
                                    placeholder="Mín. 6 caracteres">
                                <small class="text-muted" id="passHelp" style="display:none;">Dejar en blanco para
                                    no cambiar</small>
                            </div>
                            <div class="mb-3">
                                <label class="small fw-bold">Rol</label>
                                <select name="rol" id="user_rol" class="form-select">
                                    <option value="operador">Operador</option>
                                    <option value="admin">Administrador</option>
                                </select>
                            </div>
                            <button type="submit" id="btnGuardarUser" class="btn btn-primary w-100">Guardar
                                Usuario</button>
                            <button type="button" id="btnCancelarEdicion"
                                class="btn btn-link w-100 text-decoration-none mt-2" style="display:none;">Cancelar
                                Edición</button>
                        </form>
                    </div>
                    <div class="col-md-8">
                        <div class="table-responsive">
                            <table class="table table-sm align-middle">
                                <thead>
                                    <tr>
                                        <th>Usuario</th>
                                        <th>Rol</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="listaUsuarios">
                                    <?php
                                    $all_users = $pdo->query("SELECT id, usuario, rol FROM usuarios")->fetchAll();
                                    foreach ($all_users as $u): ?>
                                        <tr id="user_row_<?php echo $u['id']; ?>">
                                            <td class="fw-bold"><?php echo $u['usuario']; ?></td>
                                            <td><span class="badge bg-secondary"><?php echo $u['rol']; ?></span></td>
                                            <td class="text-end">
                                                <button class="btn btn-sm btn-outline-primary"
                                                    onclick='editarUser(<?php echo json_encode($u); ?>)'><i
                                                        class="bi bi-pencil"></i></button>
                                                <button class="btn btn-sm btn-outline-danger"
                                                    onclick="eliminarUser(<?php echo $u['id']; ?>)"><i
                                                        class="bi bi-trash"></i></button>
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