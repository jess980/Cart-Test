<?php

/////////////////
// Descuento del 15% al comprar más de 10 articulos del mismo producto
// Descuento del 10% al comprar más de 20 articulos totales
/////////////

// Función para calcular el subtotal de un producto
function calculateSubtotal($child) {
    return (int)$child->quantity * (float)$child->price_item->price; // Retorna el subtotal
}

// Función para aplicar descuento si es necesario// Función para aplicar descuentos si es necesario
// Función para aplicar descuentos si es necesario
// Función para aplicar descuentos si es necesario
function applyDiscount($total_cost, $total_quantity) {
    $discount_amount = 0;
    $discount_message = "";

    // Descuento del 10% si hay más de 20 productos
    if ($total_quantity > 20) {
        $discount_amount += $total_cost * 0.10; // 10% de descuento
        $discount_message .= "Descuento del 10% por pedir más de 20 productos.<br>";
    }
    // Descuento del 15% si hay más de 10 productos
    if ($total_quantity > 10) {
        $discount_amount += $total_cost * 0.15; // 15% de descuento
        $discount_message .= "Descuento del 15% por pedir más de 10 productos.<br>";
    }

    // Aplica el descuento total al costo
    $total_cost -= $discount_amount;

    // Retorna el costo total (modificado o no), el monto total de descuento aplicado y el mensaje
    return [$total_cost, $discount_amount, $discount_message]; 
}

// Función para imprimir los detalles de cada producto
function printProductDetails($product_number, $child, $subtotal_cost) {
    echo "<strong>Producto {$product_number}:</strong><br>";
    echo "<strong>Nombre:</strong> <span style='color:blue;'>{$child->product_name}</span><br>";
    echo "<strong>Cantidad:</strong> <span style='color:green;'>{$child->quantity}</span><br>";
    echo "<strong>Precio:</strong> <span style='color:red;'>\${$child->price_item->price}</span><br>";
    echo "<strong>Moneda:</strong> {$child->price_item->currency}<br>";
    echo "<strong>Costo Subtotal:</strong> \${$subtotal_cost}<br>";
    echo "-----------------------------<br>";
}

//IVA FUNCIÓN
// Función para calcular el IVA
function calculateIVA($amount, $iva_rate = 21) {
    return $amount * ($iva_rate / 100); // Calcula el IVA sobre la cantidad dada
}

?>