<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grupo GS SAT</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,500,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/styles/index.css">
</head>

<body>
    <form action="index.php" method="POST" id="form">

        <div class="contenedor">
            <div class="header">
                <img src="images/logo_blanco.png" class="logo-index" alt="logo">
            </div>
           
                <div class="info-consignacion-adv">
                    <h1>Política de Privacidad</h1>
                    <br>
                    <p><strong>Fecha de entrada en vigor:</strong> 1 de abril de 2025</p>
                    <br>
                    <p>En <strong>Grupo GS</strong> ("nosotros", "nuestro" o "la Compañía"), nos comprometemos a proteger la privacidad de nuestros usuarios. Esta Política de Privacidad describe cómo recopilamos, usamos y protegemos la información personal que tú ("usuario") nos proporcionas al utilizar nuestros servicios a través del sitio web <a href="http://grupogs.rf.gd/">http://grupogs.rf.gd/</a></p><b></b>
                    <br>
                    <h2>1. Información que recopilamos</h2>
                    <br>
                    <p>Con tu consentimiento, recopilamos los siguientes datos para la correcta prestación de nuestros servicios:</p>
                    <br>
                    <ul>
                        <p>Nombre completo</p>
                        <p>Número de teléfono</p>
                        <p>Número de Nequi</p>
                    </ul>
                    <br>
                    <h2>2. Uso de la información</h2>
                    <br>
                    <p>Utilizamos tu información personal para:</p>
                    <br>
                    <ul>
                        <p>Prestar y personalizar los servicios.</p>
                        <p>Comunicarnos contigo.</p>
                        <p>Mejorar la experiencia del usuario.</p>
                    </ul>
                    <br>
                    <h2>3. Compartir información con terceros</h2>
                    <br>
                    <p>No compartimos, vendemos ni alquilamos tu información, salvo por requerimientos legales o o con proveedores que prestan servicios en nuestro nombre (por ejemplo, herramientas de mensajería o almacenamiento).</p>
                    <br>
                    <h2>4. Seguridad de los datos</h2>
                    <br>
                    <p>Adoptamos medidas técnicas y organizativas razonables para proteger tu información personal, aunque ningún sistema es completamente seguro.</p>
                    <br>
                    <h2>5. Tus derechos</h2>
                    <br>
                    <p>Puedes solicitar el acceso, corrección o eliminación de tus datos escribiéndonos a <a href="mailto:contacto.grupogs.sas@gmail.com">contacto.grupogs.sas@gmail.com</a>.</p>
                    <br>
                    <h2>6. Cambios en la política</h2>
                    <br>
                    <p>Adoptamos medidas técnicas y organizativas razonables para proteger tu información personal, aunque ningún sistema es completamente seguro.</p>
                    <br>
                    <h2>7. Contacto</h2>
                    <br>
                    <p>Para consultas, contáctanos en:</p>
                    <p><strong>Grupo GS</strong><br>
                        <a href="http://grupogs.rf.gd/">www.grupogs.rf.dg</a><br>
                        <a href="mailto:contacto.grupogs.sas@gmail.com">contacto.grupogs.sas@gmail.com</a>
                    </p>
                </div>
            <br>

                <button name="boton_cancelar">Regresar</button>
            <?php
                if (isset($_POST['boton_cancelar'])) {
                header("location: index.php");
            }
            ?>
            <br>
        </div>

    </form>
</body>

</html>