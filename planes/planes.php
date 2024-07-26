<?php
session_start();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light-dark">
    <title>Compare Planes</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.amber.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.2.3/css/flag-icons.min.css"/>

    <style>
        .card-deck {
            display: flex;
            overflow-x: scroll;
        }
        .card {
            min-width: 10rem;
            min-height: 14rem;

            margin: 10px;
        }

        /* Estilos para la capa de carga */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.75);
            color: #fff;
            display: none; /* Oculta la capa por defecto */
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 1s ease-in-out;
        }

        .loading-content {
            text-align: center;
            font-size: 1.5rem;
        }

        .loading-overlay.hidden {
            opacity: 0;
        }

        /* Añadir imagen de fondo */
        .loading-overlay::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('loadingScreens/loading0.jpg') no-repeat center center;
            background-size: cover;
            z-index: -1;
        }
    </style>
</head>

<!-- Capa de carga -->
<div id="loading-overlay" class="loading-overlay">
    <div class="loading-content">
        <span aria-busy="true">Searching planes...</span>
    </div>
</div>


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
                                <article class="card">
                                    <header>
                                        <h3>
                                            <?php
                                            if (isset($vehicle['identifier'])) {
                                                $name = $vehicle['identifier'];
                                                $name = str_replace("_", " ", $name);
                                                $name = strtoupper($name);
                                                echo htmlspecialchars($name);
                                            } else {
                                                echo "Sin identificador";
                                            }
                                            ?>
                                        </h3>
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
                                            if (isset($vehicle['vehicle_sub_types']) && is_array($vehicle['vehicle_sub_types'])) {
                                                $class = htmlspecialchars(implode(', ', $vehicle['vehicle_sub_types']));
                                                $class = strtoupper($class);
                                                $class = str_replace("_", " ", $class);
                                                echo $class;
                                            }
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

    <script>
        document.querySelector('form').addEventListener('submit', function(event) {
            document.getElementById('loading-overlay').style.display = 'flex';
            const nationsChecked = document.querySelectorAll("input[name='nations[]']:checked").length > 0;
            const classesChecked = document.querySelectorAll("input[name='classes[]']:checked").length > 0;
            const tiersChecked = document.querySelectorAll("input[name='tiers[]']:checked").length > 0;

            if (!nationsChecked || !classesChecked || !tiersChecked) {
                event.preventDefault();
                alert("Please select at least one option in each category.");
            }
        });

        const selectAll = (buttonId, checkboxSelector) => {
            document.getElementById(buttonId).addEventListener("click", () => {
                document.querySelectorAll(checkboxSelector).forEach(checkbox => checkbox.checked = true);
            });
        };

        selectAll("all-classes", "input[name='classes[]']");

        // Función para limitar la selección de checkboxes
        const limitCheckboxes = (checkboxClass, maxChecked) => {
            const checkboxes = document.querySelectorAll(`.${checkboxClass}`);
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', () => {
                    const checkedCount = document.querySelectorAll(`.${checkboxClass}:checked`).length;
                    if (checkedCount >= maxChecked) {
                        checkboxes.forEach(box => {
                            if (!box.checked) {
                                box.disabled = true;
                            }
                        });
                    } else {
                        checkboxes.forEach(box => box.disabled = false);
                    }
                });
            });
        };

        limitCheckboxes('nation-checkbox', 3);
        limitCheckboxes('class-checkbox', 3);
        limitCheckboxes('tier-checkbox', 3);
    </script>
</body>

</html>