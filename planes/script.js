document.querySelector('form').addEventListener('submit', function(event) {
    const nationsChecked = document.querySelectorAll("input[name='nations[]']:checked").length > 0;
    const classesChecked = document.querySelectorAll("input[name='classes[]']:checked").length > 0;
    const tiersChecked = document.querySelectorAll("input[name='tiers[]']:checked").length > 0;
    
    if (!nationsChecked || !classesChecked || !tiersChecked) {
        event.preventDefault();
        alert("Please select at least one option in each category.");
    } else {
        document.getElementById('loading-overlay').style.display = 'flex';
    }
});

const selectAll = (buttonId, checkboxSelector) => {
    document.getElementById(buttonId).addEventListener("click", () => {
        document.querySelectorAll(checkboxSelector).forEach(checkbox => checkbox.checked = true);
    });
};

selectAll("all-classes", "input[name='classes[]']");

// Función para limitar la selección de checkboxes
const limitCheckboxes = (checkboxClass) => {
    const checkboxes = document.querySelectorAll(`.${checkboxClass}`);
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            const checkedCount = document.querySelectorAll(`.${checkboxClass}:checked`).length;
            if (checkedCount >= 3) {
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

limitCheckboxes('nation-checkbox');
limitCheckboxes('class-checkbox');
limitCheckboxes('tier-checkbox');

function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function showPlane(identifier) {
    const modal = document.getElementById('modal');
    const modalContent = modal.querySelector('article');

    // Aquí deberías cargar los datos del avión basados en el identificador
    // Para simplificar, solo voy a mostrar el identificador
    modalContent.querySelector('h2').innerText = identifier.toUpperCase();

    //? Call API
    const url = "https://www.wtvehiclesapi.sgambe.serv00.net/api/vehicles/" + identifier;

    fetch(url)
        .then (response => {
            if (!response.ok) {
                throw new Error ("Network response was not ok");
            }
            return response.json();
        })
        .then(data => {
            //? Aquí deberías procesar los datos del avión y mostrarlos en el modal
            modalContent.querySelector('#planePicture').innerHTML = "<img src='https://"+ data.images.image +"' alt='No image...'/>";

            //? Detalles del avión
            const planeInfo = modalContent.querySelector('#planeInfo');
            planeInfo.innerHTML = "<h2>Performance</h2>";
            const detailList = document.createElement('ul');

            //? Agregar los elementos
            const details = [
                {label: "Arcade BR", value: data.arcade_br, unit: ""},
                {label: "Realistic BR", value: data.realistic_br, unit: ""},
                {label: "Max speed", value: numberWithCommas(data.engine.max_speed), unit: "km/h"},
                {label: "Max altitude", value: numberWithCommas(data.aerodynamics.max_altitude), unit: "m"},
                {label: "Max speed at altitude", value: numberWithCommas(data.aerodynamics.max_speed_at_altitude), unit: "m"},
                {label: "Turn time", value: data.aerodynamics.turn_time, unit: "s"},
            ];

            details.forEach (detail => {
                const listItem = document.createElement('li');
                listItem.innerText = `${detail.label}: ${detail.value} ${detail.unit}`;
                detailList.appendChild(listItem);
            });

            planeInfo.appendChild(detailList);

            //? Mostrar armas del avión
            const planeWeapons = modalContent.querySelector('#planeWeapons');
            planeWeapons.innerHTML = "<h2>Weapons</h2>";
            const weaponList = document.createElement('ul');

            const types = [
                "gun",
                "cannon",
                "turret",
            ];
            const substringsToRemove = [
                "early",
                "usaaf",
                "1935",
                "late",
            ];
            
            //? Recorrer cada arma
            data.weapons.forEach (weapon => {
                let weaponName = weapon.name;
                let removedSubstring = '';

                types.forEach(type => {
                    if (weaponName.includes(type)) {
                        removedType = type;
                        const regex = new RegExp(type, 'gi');
                        weaponName = weaponName.replace(regex, '');
                    }
                });

                substringsToRemove.forEach(substring => {
                    const regex = new RegExp(substring, 'gi');
                    weaponName = weaponName.replace(regex, '');
                });

                weaponName = weaponName.replace(/_/g, ' ');

                const weaponItem = document.createElement('li');
                weaponItem.innerHTML = `
                <strong>${weaponName.trim()}</strong> (${removedType}) x${weapon.count}
                `;
                weaponList.appendChild(weaponItem);
            })
            planeWeapons.appendChild(weaponList);
        })
        .catch(error => {
            console.error('Error:', error);
        });
        
    modal.showModal();
}

// Función para cerrar el modal
document.querySelector('dialog footer .secondary').addEventListener('click', function() {
    document.getElementById('modal').close();
});