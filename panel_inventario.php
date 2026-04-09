<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// 1. Consulta de productos para la tabla
$stmt = $pdo->query("SELECT * FROM productos ORDER BY nombre ASC");
$productos = $stmt->fetchAll();

// 2. Definición de CATEGORÍAS (Lista fija + DB)
$categorias_fijas = [
    "EPPS", "CONSUMIBLES", "SPAT", "ACC. PVC. DESG.", "ACC. COBRE", 
    "HERRAMIENTAS", "ANDAMIO", "ECONOMATO", "HERRAMIENTAS DE PODER", 
    "SUJECIONES", "PRUEBAS", "TUB.PEX-AL-PEX", "ACC.PVP.ELEC.", 
    "BANDEJAS", "ACC.EMT", "TUB. PVC-P ELEC.", "TUB.LIV.SAN.", 
    "TUB. PVC DESAG.", "TUB. FLEX. ELEC.", "TUB. EMT", "TUB. COBRE", 
    "PLACAS", "TERMINALES", "ACC.GALVANIZADO", "CAJAS FG", "TABLEROS", 
    "EQUIP. TABLEROS", "SEGURIDAD", "INSTRUMENTOS", "ACC.CISTERNA", "AISLAMIENTO"
];

$stmt_cat = $pdo->query("SELECT DISTINCT tipo FROM productos WHERE tipo IS NOT NULL AND tipo != ''");
$categorias_db = $stmt_cat->fetchAll(PDO::FETCH_COLUMN);
$todas_categorias = array_unique(array_merge($categorias_fijas, $categorias_db));
sort($todas_categorias);

// 3. Definición de FABRICANTES (Lista depurada + DB)
$fabricantes_fijos = [
    "3BSI", "3M", "ACEROS ARQ", "ANYPSA", "ARTESCO", "BELLSAFE", "BOSCH", "BROTHER", "BTICINO", 
    "BURNDY", "CADWEL", "CAYKEN", "CELSA", "CLUTE", "CONYCON", "CYTEX", "DCA", "DEWALT", 
    "DRAKE", "EMTOP", "EPC", "EPSON", "ERICO GEM", "ES", "HILTI", "HP", "HUQSVARNA", 
    "HYDRAULIC", "IERICAN ELECTF", "IMACO", "INDECO", "INGCO", "INTELLI", "IMPORTADO", 
    "IUSA", "JAM", "JET", "JET PIPE", "JORMEN", "KENWOOD", "KNAUFF", "KYORITSU", "LAYHER", 
    "LEGRAND", "LENOVO", "LG", "LS", "MAKITA", "MATUSITA", "MW", "NACIONAL", "NEXANS", 
    "NIBCO", "NICOLL", "OATEY", "OPALUX", "PHILIPS", "SANDFLEX", "SHURTAPE", "SIEVERT", 
    "SINTHESI", "SOL", "SOLDEXA", "STANLEY", "SUPERLON", "TABLEROS", "TAUMM", "TECNO-BENT", 
    "TECNO-SAL", "TECNOWELD", "TECREAL", "THERMOWELD", "TOTAL", "TRUPER", "TUBOPLAST", 
    "TUMI", "WD-40", "WELDWELL", "WINTERS"
];

$stmt_fab = $pdo->query("SELECT DISTINCT fabricante FROM productos WHERE fabricante IS NOT NULL AND fabricante != ''");
$fabricantes_db = $stmt_fab->fetchAll(PDO::FETCH_COLUMN);
$todos_fabricantes = array_unique(array_merge($fabricantes_fijos, $fabricantes_db));
sort($todos_fabricantes);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario Obra - LPDC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --primary-color: #0d6efd; --bg-body: #f4f7f6; }
        body { background-color: var(--bg-body); font-family: 'Inter', sans-serif; }
        .custom-card { border: none; border-radius: 16px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); }
        .stat-icon { background: var(--primary-color); color: white; border-radius: 12px; padding: 1rem; }
        .stock-badge { padding: 0.4rem 0.8rem; border-radius: 8px; font-size: 0.85rem; font-weight: 700; }
        .status-ok { background-color: #d1e7dd; color: #0f5132; }
        .status-low { background-color: #f8d7da; color: #842029; border: 1px solid #f5c2c7; }
        .table thead th { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; }
        input::-webkit-calendar-picker-indicator { display: none !important; }
    </style>
</head>

<body>
    <nav class="navbar navbar-dark bg-dark py-3 shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#"><i class="bi bi-building-gear me-2 text-primary"></i>OBRA LPDC</a>
            <div class="d-flex align-items-center">
                <div class="text-white me-3 d-none d-md-block">
                    <small class="text-muted">Hola,</small> <strong><?php echo $_SESSION['username']; ?></strong>
                </div>
                <a href="logout.php" class="btn btn-outline-danger btn-sm rounded-pill px-3">Salir</a>
            </div>
        </div>
    </nav>

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
                            <input type="text" id="buscador" class="form-control border-0" placeholder="Buscar por nombre, código o categoría...">
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <a href="exportar_excel.php" class="btn btn-success w-100 h-100 d-flex align-items-center justify-content-center fw-bold shadow-sm">
                            <i class="bi bi-file-earmark-excel me-2"></i> Excel
                        </a>
                    </div>
                    <div class="col-6 col-md-2">
                        <a href="historial.php" class="btn btn-white border w-100 h-100 d-flex align-items-center justify-content-center fw-bold bg-white shadow-sm">
                            <i class="bi bi-clock-history me-2"></i> Historial
                        </a>
                    </div>
                    <div class="col-12 col-md-3">
                        <button class="btn btn-primary shadow w-100 h-100 fw-bold" data-bs-toggle="modal" data-bs-target="#modalNuevo">
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
                            <td><div class="fw-bold text-dark"><?php echo htmlspecialchars($p['nombre']); ?></div></td>
                            <td class="small"><?php echo $p['unidad_medida']; ?></td>
                            <td class="small"><?php echo $p['fabricante'] ?? '-'; ?></td>
                            <td><span class="badge bg-light text-dark border small"><?php echo $p['tipo']; ?></span></td>
                            <td class="small text-muted"><?php echo $p['almacen'] ?? 'OB. MULTIFAM PARDO'; ?></td>
                            <td class="text-center">
                                <span class="stock-badge <?php echo $badgeClass; ?>">
                                    <?php echo number_format($p['stock_actual'], 2); ?>
                                </span>
                            </td>
                            <td class="fw-bold small">S/ <?php echo number_format($p['precio_unitario'], 2); ?></td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" onclick='prepararEdicion(<?php echo json_encode($p); ?>)' title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-primary btn-sm" onclick="prepararMovimiento(<?php echo $p['id']; ?>, '<?php echo htmlspecialchars($p['nombre'], ENT_QUOTES); ?>')" title="Movimiento">
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
                            <input list="datalistFabricantes" name="fabricante" class="form-control" placeholder="Buscar fabricante..." autocomplete="off">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">CATEGORÍA</label>
                            <input list="datalistCategorias" name="tipo" class="form-control" placeholder="Buscar categoría..." required autocomplete="off">
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
                        <div class="col-md-4"><label class="form-label fw-bold small">CÓDIGO</label><input type="text" name="codigo_interno" id="edit_codigo" class="form-control"></div>
                        <div class="col-md-8"><label class="form-label fw-bold small">NOMBRE</label><input type="text" name="nombre" id="edit_nombre" class="form-control"></div>
                        <div class="col-md-4"><label class="form-label fw-bold small">UNIDAD</label><input type="text" name="unidad_medida" id="edit_unidad" class="form-control"></div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">FABRICANTE</label>
                            <input list="datalistFabricantes" name="fabricante" id="edit_fabricante" class="form-control" autocomplete="off">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">CATEGORÍA</label>
                            <input list="datalistCategorias" name="tipo" id="edit_tipo" class="form-control" autocomplete="off">
                        </div>
                        <div class="col-md-6"><label class="form-label fw-bold small">ALMACÉN</label><input type="text" name="almacen" id="edit_almacen" class="form-control"></div>
                        <div class="col-md-3"><label class="form-label fw-bold small">STOCK MÍN.</label><input type="number" step="0.01" name="stock_minimo" id="edit_stock_minimo" class="form-control"></div>
                        <div class="col-md-3"><label class="form-label fw-bold small">PRECIO S/</label><input type="number" step="0.01" name="precio_unitario" id="edit_precio" class="form-control"></div>
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

    <datalist id="datalistCategorias">
        <?php foreach($todas_categorias as $cat): ?>
            <option value="<?php echo htmlspecialchars($cat); ?>">
        <?php endforeach; ?>
    </datalist>

    <datalist id="datalistFabricantes">
        <?php foreach($todos_fabricantes as $fab): ?>
            <option value="<?php echo htmlspecialchars($fab); ?>">
        <?php endforeach; ?>
    </datalist>

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

    document.addEventListener('DOMContentLoaded', function() {
        const buscador = document.getElementById('buscador');
        if(buscador) {
            buscador.addEventListener('keyup', function() {
                let filtro = this.value.toLowerCase();
                document.querySelectorAll('#filasInventario tr').forEach(fila => {
                    fila.style.display = fila.textContent.toLowerCase().includes(filtro) ? '' : 'none';
                });
            });
        }

        const handleAJAX = (formId, url) => {
            const form = document.getElementById(formId);
            if(!form) return;
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = this.querySelector('button[type="submit"]');
                btn.disabled = true;
                
                fetch(url, { method: 'POST', body: new FormData(this) })
                .then(res => res.json())
                .then(data => {
                    if(data.status === 'success') {
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
    </script>
</body>
</html>