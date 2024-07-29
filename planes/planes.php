<?php
session_start();
//todo Considerar añadir sección de comentarios
//todo ? Requerirá sistema de autenticación de usuarios (Google / Facebook / GitHub)
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light-dark">
    <title>WT Compare</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.amber.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.2.3/css/flag-icons.min.css"/>

    <link rel="stylesheet" href="styles.css">
</head>

<!-- Capa de carga -->
<div id="loading-overlay" class="loading-overlay">
    <div class="loading-content">
        <span aria-busy="true">Searching planes...</span>
    </div>
</div>

<!--
//todo Vista al momento de elegir una carta, que muestre datos detallados del avión
//todo Confirmación de la selección y proceder a elegir al rival
-->

<body>
    <header class="container-fluid">
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
        <dialog id="modal" class="wide">
            <article id="wide-content">
                <h2>Plane name</h2>
                
                <div class="grid">
                    <div id="planePicture">Picture</div>
                    <div id="planeInfo">Info</div>
                    <div id="planeWeapons">Weapons</div>
                </div>

                <footer>
                    <button class="secondary">Cancel</button>
                    <button>Confirm</button>
                </footer>
            </article>
        </dialog>

        <div>
            <!-- Filtro de búsqueda de aviones -->
            <form method="post" action="filter.php">
                <div class="grid">
                    <fieldset>
                    <!-- Naciones -->
                        <details class="dropdown">
                            <summary>Select one or more nations</summary>
                            <ul>
                            <?php
                            $names = [
                                "America" => "usa-us",
                                "Germany" => "germany-de",
                                "Russia" => "ussr-ru",
                                "United Kingdom" => "britain-gb",
                                "Japan" => "japan-jp",
                                "France" => "france-fr",
                                "China" => "china-cn",
                                "Sweden" => "sweden-se",
                                "Italy" => "italy-it",
                                "Israel" => "israel-il",
                            ];
                            
                            foreach ($names as $nation => $code) {
                                list($nationCode, $flagCode) = explode("-", $code);
                            ?>
                                <li>
                                    <label>
                                        <input type="checkbox" value="<?= htmlspecialchars($nationCode); ?>" name="nations[]" class="nation-checkbox">
                                        <span class="fi fi-<?= htmlspecialchars($flagCode); ?>"></span>
                                        <?= htmlspecialchars($nation); ?>
                                    </label>
                                </li>
                            <?php
                            }
                            ?>
                            </ul>
                        </details>
                    </fieldset>

                    <fieldset>
                        <!-- Tipos -->
                        <details class="dropdown">
                            <summary>Select one or more aircraft classes</summary>
                            <ul>
                                <li>
                                    <label>
                                        <input type="checkbox" value="fighter" name="classes[]" class="class-checkbox">
                                        Fighters
                                    </label>
                                </li>

                                <li>
                                    <label>
                                        <input type="checkbox" value="assault" name="classes[]" class="class-checkbox">
                                        Strike aircrafts
                                    </label>
                                </li>

                                <li>
                                    <label>
                                        <input type="checkbox" value="bomber" name="classes[]" class="class-checkbox">
                                        Bombers
                                    </label>
                                </li>
                                <!-- Analizar la viabilidad de filtrar por subtipo, dinamicamente al haber elegido el tipo -->
                            </ul>
                        </details>
                        <div id="all-classes" class="outline secondary" role="button">Select all classes</div>
                    </fieldset>

                    <fieldset>
                        <!-- Eras -->
                        <details class="dropdown">
                            <summary>Select one or more tiers</summary>
                            <ul>
                            <?php
                            $num = 8;
                            $romNums = ["I", "II", "III", "IV", "V", "VI", "VII", "VIII"];
                            for ($i = 1; $i <= $num; $i++) {
                            ?>
                                <li>
                                    <label>
                                        <input type="checkbox" value="<?= $i; ?>" name="tiers[]" class="tier-checkbox">
                                        <?= $romNums[$i - 1]; ?>
                                    </label>
                                </li>
                            <?php
                            }
                            ?>
                            </ul>
                        </details>
                    </fieldset>
                </div>

                <input type="submit" value="Search">
            </form>
        </div>

        <div class="card-deck">
        <?php
            if (isset($_SESSION['datos'])) {
                $datos = $_SESSION['datos'];

                //? Generar cards dinamicamente
                foreach ($datos as $country => $types) {
                    foreach ($types as $type => $eras) {
                        foreach ($eras as $era => $vehicles) {
                            foreach ($vehicles as $vehicle) {
                                ?>
                                <article onclick="showPlane('<?= $vehicle['identifier']; ?>')" class="card">
                                    <header>
                                        <?php
                                        if (isset($vehicle['identifier'])) {
                                            $name = $vehicle['identifier'];
                                            echo "<input type='hidden' name='identifier' value='$name'>";

                                            $name = str_replace("_", " ", $name);
                                            $name = strtoupper($name);
                                            echo htmlspecialchars($name);
                                        } else {
                                            echo "Sin identificador";
                                        }
                                        ?>
                                    </header>
                                    <?php
                                    if (isset($vehicle['images']['image'])) {
                                        echo "<img src='https://". htmlspecialchars($vehicle['images']['image']) ."' alt='...'>";
                                    } else {
                                        echo "Sin imagen";
                                    }
                                    ?>
                                    <footer>
                                        <?php
                                        //? BR
                                        if (isset($vehicle['arcade_br'])) {
                                            $brArcade = $vehicle['arcade_br'];
                                            echo "<p>Arcade BR: <strong style='color: #b18e45;'>" . $brArcade . "</strong></p>";
                                        }
                                        if (isset($vehicle['realistic_br'])) {
                                            $brRealistic = $vehicle['realistic_br'];
                                            echo "<p>Realistic BR: <strong style='color: #b18e45;'>" . $brRealistic . "</strong></p>";
                                        }

                                        //? Subtipos
                                        /*
                                            if (isset($vehicle['vehicle_sub_types']) && is_array($vehicle['vehicle_sub_types'])) {
                                                $class = htmlspecialchars(implode(', ', $vehicle['vehicle_sub_types']));
                                                $class = strtoupper($class);
                                                $class = str_replace("_", " ", $class);
                                                echo "<p>" . $class . "</p>";
                                            }
                                        */
                                        ?>
                                    </footer>
                                </article>
                                <?php
                            }
                        }
                    }
                }

                //? Limpiar
                unset($_SESSION['datos']);
            }
        ?>
        </div>
    </main>

    <script src="script.js"></script>
</body>

</html>