<?php

echo "Init Execution <br><br>";

include_once("com/cart/cart.php");
include_once("com/users/users.php");  // Para funciones relacionadas con usuarios
include_once("com/catalog/catalogo.php"); // Para funciones relacionadas con el catálogo de productos
include_once("com/utils/checkout.php");


// Mensaje de bienvenida
echo "<span style='color:#640D5F;'>BIENVENIDO</span> ";
echo "<span style='color:#D91656;'>A</span> ";
echo "<span style='color:#EE66A6;'>COLORES</span> ";
echo "<span style='color:#D91656;'>DEL</span> ";
echo "<span style='color:#B692C2;'>CAMPO</span>"; 
echo "<br>";
echo "----------------------------------------------------<br>";
echo "<br>";
echo "                  1 - Iniciar Sesión<br>";  //function function=Login&dni=2000&password=1987
echo "                  2 - Registrarse<br>";
echo "<br>";
echo "----------------------------------------------------<br>";

// Procesamiento del inicio de sesión
if (isset($_GET['function'])) {
    $function = $_GET['function'];
    switch ($function) {
        case 'Login':
            if (isset($_GET['dni']) && isset($_GET['password'])) {
                $dni = $_GET['dni'];
                $password = $_GET['password'];
    
                // Llamar a la función de inicio de sesión
                $loginResult = Login($dni, $password);
                
                if ($loginResult['success']) {
                    logUserAction('login', $dni); // Registrar solo si el login es exitoso
                    header("Location: main_Compras.php?nombre=" . urlencode($loginResult['nombre']) . "&apellido=" . urlencode($loginResult['apellido']));
                    exit();
                } else {
                    echo 'El usuario no existe o los datos son incorrectos.<br>';
                }
            } else {
                echo "Faltan parámetros para el inicio de sesión.<br>";
            }
            break;
        

        case 'AddToUser':
            // Obtener datos de la URL para el registro
            if (isset($_GET['dni'], $_GET['nombre'], $_GET['apellido'], $_GET['edad'], $_GET['password'])) {
                $dni = $_GET['dni'];
                $nombre = $_GET['nombre'];
                $apellido = $_GET['apellido'];
                $edad = $_GET['edad'];
                $password = $_GET['password'];

                // Llamar a la función de agregar usuario
                if (AddUser($nombre, $apellido, $dni, $edad, $password)) {
                    // Redirigir a la página de compras después de registrarse
                    header("Location: main_Compras.php?nombre=" . urlencode($nombre) . "&apellido=" . urlencode($apellido));
                    exit(); // Terminar la ejecución después de la redirección
                } else {
                    echo "Error al registrar el usuario.<br>";
                }
            } else {
                echo "Faltan parámetros para el registro.<br>";
            }
            break;

        
    }
}

?>






