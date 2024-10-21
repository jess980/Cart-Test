<?php
session_start();
include_once("com/cart/cart.php");
include_once("com/users/users.php");  // Para funciones relacionadas con usuarios
include_once("com/catalog/catalogo.php"); // Para funciones relacionadas con el catálogo de productos
include_once("com/utils/checkout.php");

// Comprobar si el usuario está logueado o se ha registrado
if (isset($_GET['nombre']) && isset($_GET['apellido'])) {
    $nombre = htmlspecialchars($_GET['nombre']);
    $apellido = htmlspecialchars($_GET['apellido']);

    echo "<h1>Bienvenido a la tienda, {$nombre} {$apellido}!</h1>";
    echo "<p>Aquí puedes ver nuestros productos y realizar tu compra.</p>";

    // Mostrar catálogo de productos
    DisplayProductCatalog();

    echo "----------------------------------------------------<br>";
    echo "<br>";
    echo "                  1 - Agregar productos<br>"; // function=AddToCart&id_producto=1&quantity=2&dni=8886&password=2003
    echo "                  2 - Eliminar productos<br>"; // function=RemoveFromCart&id_producto=1&dni=8886&password=2003
    echo "                  3 - Ver carrito <br>"; // function=ViewCart&dni=8886&password=2003
    echo "                  4 - Cerrar Sesión<br>";
    echo "<br>";
    echo "----------------------------------------------------<br>";
}

// Procesamiento del inicio de sesión
if (isset($_GET['function'])) {
    $function = $_GET['function'];

    switch ($function) {
        case 'AddToCart':
            // Obtener datos de la URL para añadir al carrito
            if (isset($_GET['dni']) && isset($_GET['password']) && isset($_GET['id_producto']) && isset($_GET['quantity'])) {
                $dni = $_GET['dni'];
                $password = $_GET['password'];
                $id_producto = $_GET['id_producto']; // ID del producto a añadir
                $quantity = $_GET['quantity']; // Cantidad a añadir

                // Llamar a la función para añadir al carrito
                AddToCart($id_producto, $quantity, $dni, $password);
            } else {
                echo "Faltan parámetros para añadir al carrito.<br>";
            }
            break;

        case 'RemoveFromCart':
            // Obtener datos para eliminar del carrito
            if (isset($_GET['id_producto']) && isset($_GET['dni']) && isset($_GET['password'])) {
                $id_producto = $_GET['id_producto'];
                $dni = $_GET['dni'];
                $password = $_GET['password'];

                // Llamar a la función para eliminar el producto del carrito
                RemoveFromCart($id_producto, $dni, $password);
            } else {
                echo "Faltan parámetros para eliminar del carrito.<br>";
            }
            break;

        case 'ViewCart':
            // Ver carrito
            if (isset($_GET['dni']) && isset($_GET['password'])) {
                $dni = $_GET['dni'];
                $password = $_GET['password'];

                ViewCart($dni, $password);
            } else {
                echo "Parámetros incorrectos <br>"; // Corregido: "Parámetros" en lugar de "Parametros"
            }
            break;

        case 'Logout':
            // Obtener datos de la URL para cerrar sesión
            if (isset($_GET['dni']) && isset($_GET['password'])) {
                $dni = $_GET['dni'];
                $password = $_GET['password'];

                // Llamar a la función de logout
                $logoutResult = Logout($dni, $password);

                if ($logoutResult['success']) {

                    header("Location: main.php");; // Redirige después de 2 segundos
                    exit();
                } else {
                    echo $logoutResult['message'] . "<br>";
                }
            } else {
                echo "Faltan parámetros para cerrar sesión.<br>";
            }
            break;

        default:
            echo 'Función no definida.<br>';
            break;
    }
}
?>
