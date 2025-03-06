<?php
    $servername = "localhost";
    $username = "root"; 
    $password = "";
    $dbname = "formulario_db";

    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $edad = $_POST['edad'];

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Actualizar usuario existente
        $id = $_POST['id'];
        $sql = "UPDATE usuarios SET nombre='$nombre', email='$email', edad=$edad WHERE id=$id";

        if ($conn->query($sql) === TRUE) {
            echo "Usuario actualizado correctamente.";
        } else {
            echo "Error al actualizar: " . $conn->error;
        }
    } else {
        // Insertar nuevo usuario
        $sql = "INSERT INTO usuarios (nombre, email, edad) VALUES ('$nombre', '$email', $edad)";

        if ($conn->query($sql) === TRUE) {
            echo "Registro exitoso.";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    $conn->close();
    echo "<br><a href='ver_usuarios.php'>Volver</a>";
?>
