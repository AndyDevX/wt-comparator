<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light-dark">
    <title>Home</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.amber.min.css"/>
</head>

<body>
    <header class = "container-fluid">
        <nav>
            <ul>
                <li><strong>War Thunder comparator</strong></li>
            </ul>
            <ul>
                <li><a href="#">Acerca de</a></li>
                <li>
                    <details class="dropdown">
                        <summary>
                            Vehículos
                        </summary>
                        <ul dir="rtl">
                            <li><a href="planes/planes.php">Aviones</a></li>
                            <li><a href="#">Tanques</a></li>
                            <li><a href="#">Helicópteros</a></li>
                            <li><a href="#">Barcos</a></li>
                        </ul>
                    </details>
                </li>
            </ul>
        </nav>
    </header>

    <main class="container">
        <?php
        /*
            $url = "https://www.wtvehiclesapi.sgambe.serv00.net/api/vehicles?limit=200&page=0&country=usa&type=fighter&isPremium=false&isPack=false&isSquadronVehicle=false&isOnMarketplace=false&excludeKillstreak=true&excludeEventVehicles=true";
            $result = file_get_contents($url);
            $data = json_decode($result, true);
        */
        ?>
        <!-- <img src="https://<?= $data[0]["images"]["image"];?>" alt="ey"> -->
    </main>
    
    <footer class="container-fluid"></footer>
</body>

</html>