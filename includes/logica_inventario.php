<?php
// 1. Configuración de la zona horaria (Lima)
date_default_timezone_set('America/Lima');

/**
 * 2. PROCESAMIENTO DE GUARDADO / EDICIÓN
 * Este bloque debe ir antes de las consultas de la tabla para procesar cambios
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'])) {
    
    // CAPTURA DE DATOS CON VALIDACIÓN DE NULOS
    // Convertimos a NULL si vienen vacíos para evitar errores de la base de datos
    $codigo_interno = !empty($_POST['codigo_interno']) ? $_POST['codigo_interno'] : null;
    
    // Esta validación específica elimina el "Error 1366 Incorrect decimal value"
    $precio_unitario = (isset($_POST['precio_unitario']) && $_POST['precio_unitario'] !== '') ? $_POST['precio_unitario'] : null;
    
    $nombre = $_POST['nombre'];
    $tipo = $_POST['tipo'];
    $unidad = $_POST['unidad_medida'];
    $fabricante = $_POST['fabricante'];
    $almacen = $_POST['almacen'] ?? 'OB. MULTIFAM PARDO';
    $stock_actual = $_POST['stock_actual'] ?? 0;
    $stock_minimo = $_POST['stock_minimo'] ?? 0;
    $observaciones = $_POST['observaciones'] ?? '';

    try {
        // PREPARAR LA CONSULTA (INSERT O UPDATE según tu necesidad)
        // Ejemplo para un registro nuevo:
        $sql = "INSERT INTO productos (codigo_interno, nombre, tipo, unidad_medida, fabricante, almacen, stock_actual, stock_minimo, precio_unitario, observaciones) 
                VALUES (:codigo, :nombre, :tipo, :unidad, :fabricante, :almacen, :stock_a, :stock_m, :precio, :obs)";
        
        $stmt_insert = $pdo->prepare($sql);
        
        // EJECUTAR: Los valores NULL se insertarán correctamente sin que la BD obligue a poner un dato
        $stmt_insert->execute([
            ':codigo'     => $codigo_interno,
            ':nombre'     => $nombre,
            ':tipo'       => $tipo,
            ':unidad'     => $unidad,
            ':fabricante' => $fabricante,
            ':almacen'    => $almacen,
            ':stock_a'    => $stock_actual,
            ':stock_m'    => $stock_minimo,
            ':precio'     => $precio_unitario, // Aquí llega NULL si el input está vacío
            ':obs'        => $observaciones
        ]);

        // Redirigir para limpiar el formulario y evitar reenvíos
        header("Location: panel_inventario.php?success=1");
        exit();

    } catch (PDOException $e) {
        // Si sale un error, lo atrapamos aquí (como el cuadro rojo de tu imagen)
        die("Error en la base de datos: " . $e->getMessage());
    }
}

// 3. Consulta de productos para la tabla principal
$stmt = $pdo->query("SELECT * FROM productos ORDER BY nombre ASC");
$productos = $stmt->fetchAll();

// 4. Definición de CATEGORÍAS
$categorias_fijas = [
    "EPPS", "CONSUMIBLES", "SPAT", "ACC. PVC. DESG.", "ACC. COBRE",
    "HERRAMIENTAS", "ANDAMIO", "ECONOMATO", "HERRAMIENTAS DE PODER",
    "SUJECIONES", "PRUEBAS", "TUB.PEX-AL-PEX", "ACC.PVP.ELEC.",
    "BANDEJAS", "ACC.EMT", "TUB. PVC-P ELEC.", "TUB.LIV.SAN.",
    "TUB. PVC DESAG.", "TUB. FLEX. ELEC.", "TUB. EMT", "TUB. COBRE",
    "PLACAS", "TERMINALES", "ACC.GALVANIZADO", "CAJAS FG",
    "TABLEROS", "EQUIP. TABLEROS", "SEGURIDAD", "INSTRUMENTOS",
    "ACC.CISTERNA", "AISLAMIENTO"
];

$stmt_cat = $pdo->query("SELECT DISTINCT tipo FROM productos WHERE tipo IS NOT NULL AND tipo != ''");
$categorias_db = $stmt_cat->fetchAll(PDO::FETCH_COLUMN);
$todas_categorias = array_unique(array_merge($categorias_fijas, $categorias_db));
sort($todas_categorias);

// 5. Definición de FABRICANTES
$fabricantes_fijos = [
    "3BSI", "3M", "ACEROS ARQ", "ANYPSA", "ARTESCO", "BTICINO", "BURNDY",
    "BOSCH", "CELSA", "CLUTE", "DEWALT", "HILTI", "INDECO", "INGCO", 
    "MAKITA", "NICOLL", "STANLEY", "TRUPER", "TUBOPLAST", "WD-40",
    "N/A", "NACIONAL", "IMPORTADO", "ELECTRO CORRALES"
];

$stmt_fab = $pdo->query("SELECT DISTINCT fabricante FROM productos WHERE fabricante IS NOT NULL AND fabricante != ''");
$fabricantes_db = $stmt_fab->fetchAll(PDO::FETCH_COLUMN);
$todos_fabricantes = array_unique(array_merge($fabricantes_fijos, $fabricantes_db));
sort($todos_fabricantes);
?>