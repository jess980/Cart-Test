<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
// Función para obtener un producto específico del catálogo por su ID
function GetProductCatalog($id_prod) {
    if (file_exists('xmldb/catalogo.xml')) {
        // Cargar el archivo catalogo.xml
        $catalogo = simplexml_load_file('xmldb/catalogo.xml');

        // Buscar el producto por su ID
        foreach ($catalogo->producto as $producto) {
            // Depuración: Imprimir el ID del producto actual
            //echo "Verificando producto ID: " . $producto->id . "<br>";
            if ((int)$producto->id === (int)$id_prod) {
                return $producto; // devuelve el producto una vez lo encuentra
            }
        }
    } else {
        echo "El archivo catalogo.xml no se encontró.<br>";
    }
    return false; 
}

////////////////////////////////////////////
function UpdateInventory($id_producto, $quantityChange) {
    $filePath = 'xmldb/catalogo.xml';

    // Verifica si el archivo existe
    if (!file_exists($filePath)) {
        throw new Exception("El archivo catalogo.xml no se encontró.");
    }

    // Cargar el archivo catalogo.xml
    $catalogo = simplexml_load_file($filePath);

    // Buscar el producto por su ID
    foreach ($catalogo->producto as $producto) {
        if ((int)$producto->id === (int)$id_producto) {
            $current_quantity = (int)$producto->inventario;
            $producto->inventario = max(0, $current_quantity + $quantityChange); // Asegura que no baje a menos de 0
            break; // Sale del bucle una vez que se encuentra el producto
        }
    }

    // Guarda los cambios en el archivo catalogo.xml
    $catalogo->asXML($filePath);
}

function GetAllProducts() {
    $filePath = 'xmldb/catalogo.xml';

    // Verifica si el archivo existe
    if (!file_exists($filePath)) {
        throw new Exception("El archivo catalogo.xml no se encontró.");
    }

    // Cargar el archivo catalogo.xml
    $catalogo = simplexml_load_file($filePath);

    return $catalogo->producto; // Retorna todos los productos
}

function DisplayProductCatalog() {
    $productos = GetAllProducts();

    echo "<h2>Catálogo de Productos</h2>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Inventario</th>
          </tr>";

    foreach ($productos as $producto) {
        echo "<tr>
                <td>{$producto->id}</td>
                <td>{$producto->nombre}</td>
                <td>{$producto->precio}</td>
                <td>{$producto->inventario}</td>
              </tr>";
    }

    echo "</table>";
}

?>
