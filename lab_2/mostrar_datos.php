<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "formulario_db";

    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Eliminar usuario si se recibe una solicitud con ?eliminar=id
    if (isset($_GET['eliminar'])) {
        $id = intval($_GET['eliminar']); // Asegurar que es un número entero
        $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo "<p class='mensaje exito'>Usuario eliminado correctamente.</p>";
        } else {
            echo "<p class='mensaje error'>Error al eliminar: " . $conn->error . "</p>";
        }
        $stmt->close();
    }

    // Si se está editando un usuario, cargar sus datos en el formulario
    $editando = false;
    $nombre = $email = $edad = "";
    if (isset($_GET['editar'])) {
        $editando = true;
        $id = intval($_GET['editar']);
        $stmt = $conn->prepare("SELECT nombre, email, edad FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($nombre, $email, $edad);
        if (!$stmt->fetch()) {
            echo "<p class='mensaje error'>Usuario no encontrado.</p>";
            $editando = false;
        }
        $stmt->close();
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            margin: 20px;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: inline-block;
        }
        h1 {
            color: #333;
        }
        label {
            display: block;
            font-weight: bold;
            margin-top: 10px;
        }
        input {
            width: 100%;
            padding: 8px;
            margin: 5px 0 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        .mensaje {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .exito {
            background-color: #28a745;
            color: white;
        }
        .error {
            background-color: #dc3545;
            color: white;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo $editando ? "Editar Usuario" : "Registrar Usuario"; ?></h1>
        <form method="POST" action="procesar_formulario.php">
            <?php if ($editando): ?>
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
            <?php endif; ?>
            <label>Nombre:</label>
            <input type="text" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>
            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            <label>Edad:</label>
            <input type="number" name="edad" value="<?php echo htmlspecialchars($edad); ?>" required>
            <button type="submit"><?php echo $editando ? "Actualizar" : "Registrar"; ?></button>
        </form>
    </div>
    
    <h1>Usuarios Registrados</h1>
    <div class="container">
        <?php
            $sql = "SELECT id, nombre, email, edad FROM usuarios";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                echo "<table>";
                echo "<tr><th>ID</th><th>Nombre</th><th>Email</th><th>Edad</th><th>Acciones</th></tr>";
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["nombre"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["edad"]) . "</td>";
                    echo "<td>
                            <a href='?editar=" . htmlspecialchars($row["id"]) . "'>Editar</a> | 
                            <a href='?eliminar=" . htmlspecialchars($row["id"]) . "' onclick='return confirm(\"¿Estás seguro?\")'>Eliminar</a>
                          </td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No hay usuarios registrados.</p>";
            }
            $conn->close();
        ?>
    </div>
</body>
</html>
