<?php

//FUNCIONES///////////////////////////////
// AGREGAR PRODUCTO AL CARRITO/

function AddToCart($id_producto, $quantity, $dni, $password) {
    if (Login($dni, $password)) { // Verifica el login
        $filePath = 'xmldb/catalogo.xml';
        $catalogo = simplexml_load_file($filePath);
        $productName = "";
        $productPrice = "";

        // Buscar el producto en el catálogo
        foreach ($catalogo->producto as $producto) {
            if ((int)$producto->id === (int)$id_producto) {
                $current_quantity = (int)$producto->inventario;
                $productName = (string)$producto->nombre;
                $productPrice = (string)$producto->precio;

                // Verifica que haya suficiente inventario
                if ($current_quantity >= $quantity) {
                    // Obtener el carrito del usuario específico
                    $cart = GetCart($dni);
                    $found = false;

                    foreach ($cart->product_item as $item) {
                        if ((int)$item->id_product === (int)$id_producto && (int)$item->dni === (int)$dni) {
                            // Si el producto ya está en el carrito, sumamos la cantidad
                            $item->quantity = (int)$item->quantity + $quantity;
                            $found = true;
                            break;
                        }
                    }

                    if (!$found) {
                        // Si no existe, se agrega como un nuevo producto
                        $new_item = $cart->addChild('product_item');
                        $new_item->addChild('id_product', $id_producto);
                        $new_item->addChild('product_name', $productName);
                        $new_item->addChild('quantity', $quantity);
                        $new_item->addChild('dni', $dni);
                        $price_item = $new_item->addChild('price_item');
                        $price_item->addChild('price', $productPrice);
                        $price_item->addChild('currency', 'EU');
                    }

                    // Actualiza el inventario
                    UpdateInventory($id_producto, -$quantity);

                    // Mensajes de retroalimentación
                    echo "<strong>Producto:</strong> {$productName}<br>";
                    echo "<strong>Precio:</strong> {$productPrice}<br>";
                    echo "<strong>Cantidad agregada al carrito:</strong> {$quantity}<br>";

                    // Guarda el carrito del usuario específico
                    $cart->asXML("xmldb/cart_{$dni}.xml");
                    return;
                } else {
                    echo "No hay suficiente inventario para agregar el producto: '{$productName}'.<br>";
                    return;
                }
            }
        }

        echo "Producto con ID '{$id_producto}' no encontrado en el inventario.<br>";
    } else {
        echo "El usuario no existe o los datos son incorrectos.<br>";
    }
}



///////////////////////
//ELIMINAR PRODUCTO - POR ID
function RemoveFromCart($id_prod, $dni, $password) {
    if (Login($dni, $password)) {
        if (_Product_Cart($id_prod, $dni)) {
            _ExecuteRemoveFromCart($id_prod, $dni);
        } else {
            echo 'No existe el producto en el carrito.<br>';
        }
    } else {
        echo 'El usuario no existe o los datos son incorrectos.<br>';
    }
}

//////////////////////
// VERIFICAR- SI UN PRODUCTO EXISTE EN EL CARRITO
//////////////////////
// VERIFICAR SI UN PRODUCTO EXISTE EN EL CARRITO
function _Product_Cart($id_prod, $dni) {
    $xml = GetCart($dni);  // Corregido: Pasar $dni al llamar a GetCart()

    // Busca sobre cada nodo
    foreach ($xml->product_item as $child) {
        // Verifica si el ID del producto y el DNI del usuario coinciden
        if ($child->id_product == $id_prod && $child->dni == $dni) {
            return true; // El producto existe en el cart
        }
    }
    return false; // El producto no existe en el cart
}

///////////////////////////////
// FUNCIÓN OBTENER EL CART.XML

function GetCart($dni) {
    $filePath = "xmldb/cart_{$dni}.xml"; // Nombre del archivo específico para cada usuario

    if (file_exists($filePath)) {
        // Si el archivo ya existe, lo cargamos
        return simplexml_load_file($filePath);
    } else {
        // Si no existe, creamos un nuevo carrito
        return new SimpleXMLElement('<cart></cart>');
    }
}





// FUNCIÓN PARA VER EL CARRITO
function ViewCart($dni, $password) {
    if (Login($dni, $password)) {
        $xml = GetCart($dni);

        if (!$xml) {
            echo "El carrito está vacío o no se pudo cargar.<br>";
            return;
        }

        $total_quantity = 0;
        $total_cost = 0;
        $discount_amount = 0;
        $discount_message = "";
        $product_number = 1;

        foreach ($xml->product_item as $child) {
            $productName = (string)$child->product_name;
            $productQuantity = (int)$child->quantity;
            $productPrice = (float)$child->price_item->price;
            $subtotal = $productQuantity * $productPrice;

            printProductDetails($product_number, $child, $subtotal);
            $total_quantity += $productQuantity;
            $total_cost += $subtotal;
            $product_number++;
        }

        echo "==============================<br>";
        // Aplicar descuentos
        list($total_cost_after_discount, $discount_amount, $discount_message) = applyDiscount($total_cost, $total_quantity);

        // Calcular IVA
        $iva = calculateIVA($total_cost_after_discount); // Llama a la función para calcular el IVA

        // Calcular total final
        $total_final = $total_cost_after_discount + $iva; // Sumar IVA al costo después del descuento

        echo "Subtotal Total: \${$total_cost}<br>";
        if ($discount_amount > 0) {
            echo $discount_message;
            echo " <strong>Descuento Total: </strong> \${$discount_amount}<br>";
        }
        echo "==============================<br>";
        echo "<strong>Subtotal después del Descuento: </strong> \${$total_cost_after_discount}<br>";
        echo "<strong>IVA (21%): </strong> \${$iva}<br>"; // Muestra el IVA
        echo "<strong>Costo Total: </strong> \${$total_final}<br>"; // Muestra el costo total incluyendo IVA
    } else {
        echo "El usuario no existe o los datos son incorrectos.<br>";
    }
}





// EXECUTES
////////////////////////////////////////////////////////////////////////////////////////////////////
// EXECUTE- AGREGAR PRODUCTO AL CARRITO
function _ExecuteAddToCart($id_producto, $product_name, $quantity, $dni) {
    // Obtener el carrito específico del usuario basado en su DNI
    $cart = GetCart($dni); // Modificado para obtener el carrito del usuario
    $producto = GetProductCatalog($id_producto); // Obtén el producto del catálogo

    // Comprueba si el producto fue encontrado
    if ($producto) {
        // Añade un nuevo producto al carrito
        $item = $cart->addChild('product_item');

        $item->addChild('id_product', $id_producto); // Agregar el ID del producto al carrito
        $item->addChild('product_name', $product_name); // También agregamos el nombre del producto
        $item->addChild('quantity', $quantity);
        $item->addChild('dni', $dni); // Agregar el DNI del usuario

        // Asegúrate de agregar el precio del producto desde el catálogo
        $price_item = $item->addChild('price_item');
        $price_item->addChild('price', (string)$producto->precio); // Ahora usamos el precio real del producto
        $price_item->addChild('currency', 'EU'); // Suponiendo que la moneda es siempre EU

        // Guarda el carrito en el archivo XML específico del usuario
        $cart->asXML("xmldb/cart_{$dni}.xml"); // Modificado para guardar el carrito en el archivo del usuario

        echo "Producto: <strong>$product_name</strong> (ID: $id_producto) agregado al carrito con cantidad: $quantity.<br>";
    } else {
        echo "El producto con ID $id_producto no se encontró en el catálogo.<br>";
    }
}

////////////////////////////////////////////////////////////////////////////////////////////////////
// EXECUTE- ELIINAR PRODUCTO
function _ExecuteRemoveFromCart($id_prod, $dni) {
    $xml = GetCart($dni);
    $found = false;

    $new_cart = new SimpleXMLElement('<cart/>');
    $quantityRemoved = 0;

    foreach ($xml->product_item as $child) {
        if ($child->id_product != $id_prod || $child->dni != $dni) {
            $new_item = $new_cart->addChild('product_item');
            $new_item->addChild('id_product', $child->id_product);
            $new_item->addChild('product_name', $child->product_name);
            $new_item->addChild('quantity', $child->quantity);
            $new_item->addChild('dni', $child->dni);
            $price_item = $new_item->addChild('price_item');
            $price_item->addChild('price', $child->price_item->price);
            $price_item->addChild('currency', $child->price_item->currency);
        } else {
            $found = true;
            $quantityRemoved = (int)$child->quantity;
        }
    }

    if ($found) {
        $new_cart->asXML("xmldb/cart_{$dni}.xml");
        UpdateInventory($id_prod, $quantityRemoved);
        echo "Producto con ID '{$id_prod}' eliminado del carrito.<br>";
    } else {
        echo "Producto con ID '{$id_prod}' no encontrado en el carrito.<br>";
    }
}


?>
