<?php

function AddUser($nombre, $apellido, $dni, $edad, $password) {
    $user = GetUser(); // Llama a una función

    // Verificar si ya existe el usuario
    foreach ($user->children() as $child) {
        if ($child->Dni == $dni) {
            echo "El usuario con dni, $dni ya existe.<br>";
            return;
        }
        if ($child->Password == $password) {
            echo "La contraseña ya existe, intenta una diferente.<br>";
            return;
        }
    }

    // Nuevo Usuario
    $item = $user->addChild('user');
    $item->addChild('Dni', $dni);
    $item->addChild('Nombre', $nombre);
    $item->addChild('Apellido', $apellido);
    $item->addChild('Edad', $edad);
    $item->addChild('Password', $password);

    echo "Nuevo usuario creado correctamente.<br>";

    $user->asXML('xmldb/user.xml');

     // Redirigir a la página de compras
     header("Location: main_Compras.php?nombre=" . urlencode($nombre) . "&apellido=" . urlencode($apellido));
     exit();
  
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function Login($dni, $password) {
    $user = GetUser(); // Asume que esta función devuelve el XML con los usuarios
    // Verifica el DNI y la contraseña 
    foreach ($user->children() as $child) {
        if ($child->Dni == $dni && $child->Password == $password) { 
            // Registrando el inicio de sesión
            logUserAction('login', $dni);
            // Devuelve un array con la información del usuario
            return [
                'success' => true,
                'nombre' => (string)$child->Nombre,
                'apellido' => (string)$child->Apellido,
            ];
        }
    }
    // Si no encuentra al usuario, devuelve falso
    return [
        'success' => false,
    ];
}




/////////////////////////////////////////////
function Logout($dni, $password) {
    // Asume que esta función devuelve el XML con los usuarios
    $users = GetUser();

    // Verifica el DNI y la contraseña
    foreach ($users->children() as $child) {
        if ($child->Dni == $dni && $child->Password == $password) {
            // Registra la acción de logout
            logUserAction('logout', $dni);

            // Destruir la sesión
            session_start();
            session_unset(); // Elimina todas las variables de sesión
            session_destroy(); // Destruye la sesión

            // Devuelve un array indicando que el logout fue exitoso
            return [
                'success' => true,
                'message' => 'Logout exitoso.'
            ];
        }
    }

    // Si no encuentra al usuario, devuelve falso
    return [
        'success' => false,
        'message' => 'DNI o contraseña incorrectos.'
    ];
}




//////////////////////////////////////////////////////////////////////////////////////////////////////////////

function GetUser() {
    if (file_exists('xmldb/user.xml')) {
        $user = simplexml_load_file('xmldb/user.xml');
    } else {
        $user = new SimpleXMLElement('<users></users>');
    }
    return $user;
}
////////////////////
function logUserAction($action, $dni) {
    $filePath = 'xmldb/user_actions.xml';

    // Comprobar si el archivo existe y cargarlo o crear uno nuevo
    if (file_exists($filePath)) {
        $xml = simplexml_load_file($filePath);
    } else {
        // Crear la estructura inicial del XML
        $xml = new SimpleXMLElement('<logs/>');
    }

    // Buscar si el usuario ya existe en el XML
    $userNode = $xml->xpath("/logs/user[dni='$dni']");

    $currentTimestamp = new DateTime(); // Usar un objeto DateTime para comparación
    $recentInterval = new DateTime('-1 minute'); // Define un intervalo, aquí 1 minuto

    if ($userNode) {
        // Usuario ya existe, comprueba si la acción se ha registrado recientemente
        $actions = $userNode[0]->actions->entry;

        foreach ($actions as $entry) {
            // Verifica si la acción y la marca de tiempo son las mismas
            if ($entry->action == $action && new DateTime((string)$entry->timestamp) > $recentInterval) {
                // La acción ya ha sido registrada recientemente, no hacer nada
                return; // Salimos de la función, no se registra el login
            }
        }

        // Añadir nueva acción
        $entry = $userNode[0]->actions->addChild('entry');
        $entry->addChild('action', $action);
        $entry->addChild('timestamp', $currentTimestamp->format('Y-m-d H:i:s')); // Agrega la marca de tiempo
    } else {
        // Usuario nuevo, crea el nodo
        $user = $xml->addChild('user');
        $user->addChild('dni', $dni);
        $actions = $user->addChild('actions');
        $entry = $actions->addChild('entry');
        $entry->addChild('action', $action);
        $entry->addChild('timestamp', $currentTimestamp->format('Y-m-d H:i:s')); // Agrega la marca de tiempo
    }

    // Guardar el XML
    $xml->asXML($filePath);
}





?>
