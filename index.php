<?php
//Si existe el archivo de registro
if(file_exists("archivo.txt")){
    $aJsonClientes = file_get_contents("archivo.txt");
    $aClientes = json_decode($aJsonClientes, true);
} else {
    $aClientes[] = array();
}
$id = (isset($_GET["id"]) && $_GET["id"] != "") ? $_GET["id"] : "";
//Si se envian datos por el formulario
if($_POST){
    $dni = $_REQUEST["txtDni"];
    $nombre = $_REQUEST["txtNombre"];
    $telefono = $_REQUEST["txtTelefono"];
    $correo = $_REQUEST["txtCorreo"];
    //Si se envio un archivo por el formulario
    if($_FILES["archivo"]["error"] === UPLOAD_ERR_OK){
        $nombreAleatorio = date("YmdHims");
        $nombreOriginal = $_FILES["archivo"]["name"];
        $nombreTemporal = $_FILES["archivo"]["tmp_name"];
        $extension = pathinfo($nombreOriginal, PATHINFO_EXTENSION);
        $nuevoNombre = "$nombreAleatorio.$extension";
        move_uploaded_file($nombreTemporal, "imagenes/" . $nuevoNombre);
    }
    //Si se desea modificar un registro
    if($id != ""){
        //Si se subio una nueva imagen
        if($_FILES["archivo"]["error"] === UPLOAD_ERR_OK){
            if(file_exists("imagenes/" . $aClientes[$id]["imagen"])){
                unlink("imagenes/" . $aClientes[$id]["imagen"]);
            }
        //Si no se subio una nueva imagen
        } else {
            $nuevoNombre = $aClientes[$id]["imagen"];
        }
        $aClientes[$id] = array(
            "dni" => $dni,
            "nombre" => $nombre,
            "telefono" => $telefono,
            "correo" => $correo,
            "imagen" => $nuevoNombre
        );
    } else {
        //Añadiendo un registro nuevo
        $aClientes[] = array(
            "dni" => $dni,
            "nombre" => $nombre,
            "telefono" => $telefono,
            "correo" => $correo,
            "imagen" => $nuevoNombre
        );
    }
    $aJsonClientes = json_encode($aClientes);
    file_put_contents("archivo.txt", $aJsonClientes);
    $mensaje = "¡Se ha guardado el registro exitosamente!";
    header("Location: index.php");
}
//Si se desea eliminar un registro
if(isset($_GET["id"]) && $_GET["id"] != "" && isset($_GET["do"]) && $_GET["do"] == "eliminar"){
    unlink("imagenes/" . $aClientes[$id]["imagen"]);
    unset($aClientes[$id]);
    $aJsonClientes = json_encode($aClientes);
    file_put_contents("archivo.txt", $aJsonClientes);
    $mensaje = "¡Se ha eliminado el registro exitosamente";
    header("Location: index.php");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <link rel="stylesheet" href="css/Font-awesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="css/Font-awesome/css/all.min.css">
    <link rel="stylesheet" href="css/estilos.css">
    <script src="js/bootstrap.min.js"></script>
    <title>Registro de Clientes</title>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-12 pt-5 pb-3">
                <h1 class="text-center">Registro de clientes</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-6">
                <form method="POST" action="" enctype="multipart/form-data">
                    <div>
                        <label for="txtDni">DNI:</label>
                        <input class="form-control" type="text" name="txtDni" id="txtDni" value="<?php echo isset($aClientes[$id]) ? $aClientes[$id]["dni"] : "" ?>" required>
                    </div>
                    <div>
                        <label for="txtNombre">Nombre:</label>
                        <input type="text" name="txtNombre" id="txtNombre" class="form-control" value="<?php echo isset($aClientes[$id]) ? $aClientes[$id]["nombre"] : "" ?>" required>

                    </div>
                    <div>
                        <label for="txtTelefono">Teléfono:</label>
                        <input class="form-control" type="text" name="txtTelefono" id="txtTelefono" value="<?php echo isset($aClientes[$id]) ? $aClientes[$id]["telefono"] : "" ?>" required>
                    </div>
                    <div>
                        <label for="txtEmail">Email:</label>
                        <input class="form-control" type="email" name="txtCorreo" id="txtCorreo" value="<?php echo isset($aClientes[$id]) ? $aClientes[$id]["correo"] : "" ?>" required>
                    </div>
                    <div>
                        <label for="archivo">Adjuntar archivo:</label>
                        <input class="form-control" type="file" name="archivo" id="archivo" accept=".jpg, .jpeg, .png">
                        <small class="d-block">Archivos admitidos: .jpg, .jpeg, .png</small>
                    </div>
                    <div>
                        <button class="btn btn-primary" type="submit">Guardar</button>
                    </div>
                </form>
            </div>
            <div class="col-12 col-sm-6 py-4">
                <table class="table table-striped text-center border">
                    <tr class="table-primary">
                        <th>Imagen</th>
                        <th>DNI</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Acciones</th>
                    </tr>
                    <?php foreach($aClientes as $indice => $cliente){ ?>
                    <tr>
                        <td><img class="img-thumbnail" src="imagenes/<?php echo $cliente["imagen"]; ?>" alt=""></td>
                        <td><?php echo $cliente["dni"];?></td>
                        <td><?php echo $cliente["nombre"];?></td>
                        <td><?php echo $cliente["correo"];?></td>
                        <td>
                            <a class="p-1 btn btn-primary" href="index.php?id=<?php echo $indice; ?>" title="Modificar"><i class="fas fa-edit"></i></a>
                            <a class="p-1 btn btn-danger" href="index.php?id=<?php echo $indice; ?>&do=eliminar" title="Borrar"><i class="fas fa-trash-alt"></i></a>
                        </td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-12 py-3">
                <?php if(isset($mensaje)){ ?>
                    <div class="alert alert-primary" role="alert"><?php echo $mensaje; ?></div>
                <?php } ?>
            </div>
        </div>
    </div>
</body>
</html>