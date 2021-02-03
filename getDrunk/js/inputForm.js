// Auto-fill drink_type when user enters known drink
function autoFillDrinkType(clicked_object) {
    let target_object = document.getElementById(clicked_object.id.replace("drink-name", "drink-type"));
    let drink_type_rank = mapping_drink_drinkTypeIdx[clicked_object.value];
    if(drink_type_rank!=null) {
        target_object.selectedIndex = drink_type_rank;
        target_object.disabled = true;
    }
    else {
        target_object.disabled = false;
    }
    return drink_type_rank;
}

// Auto-fill volume unit when user changed the drink type
function autoFillVolumeUnit(target_object_id, drink_type_rank) {
    let volume_unit = mapping_drinkTypeIdx_volumeUnit[drink_type_rank];
    volume_unit = volume_unit!=null ? volume_unit : "ml";
    document.getElementById(target_object_id).innerText = volume_unit;
    document.getElementById('drink-'+target_object_id).value = volume_unit;
}

document.addEventListener('change', function(e) {

    console.log(e);

    // Determine clicked and target object
    let clicked_object = e.target;

    if(clicked_object.matches('.input-drink')) {
        let drink_type_rank = autoFillDrinkType(clicked_object);
        let target_object_id = clicked_object.id.replace("drink-name", "volume-unit")
        console.log(target_object_id);
        autoFillVolumeUnit(target_object_id, drink_type_rank);
    }

    // Auto-fill unit
    if(clicked_object.matches('.input-type')) {
        let target_object_id = clicked_object.id.replace("drink-type", "volume-unit")
        let drink_type_rank = clicked_object.selectedIndex;
        autoFillVolumeUnit(target_object_id, drink_type_rank);
    }
})

// When user clicks "modify" button on inputForm.php
function req_modify(parent, id, columns, columns_inactivate=[]) {

    // Identify parent div
    let parent_div = document.getElementById(parent);

    // transform static text into dynamic input fields
    columns.forEach(element => parent_div.querySelector("#"+element+id).disabled = false);
    columns_inactivate.forEach(element => parent_div.querySelector("#"+element+id).disabled = true);
    document.getElementById('row'+id).querySelectorAll(".unit").forEach(function(element) { element.classList.add("unit-active") });

    // Disable input fields in first row
    try {
        columns.forEach(element => parent_div.querySelector("#"+element+"0").disabled = true);
        parent_div.querySelector('#add'+0).disabled = true;
    }
    catch(err) { console.log("Note: Grid doesn't seem to have a dedicated row to add new items"); }

    // disable all other add/modify/delete buttons
    parent_div.querySelectorAll('.modify').forEach(function(button) { button.disabled = true; });
    parent_div.querySelectorAll('.delete').forEach(function(button) { button.disabled = true; });

    // display save button
    parent_div.querySelector('#add'+id).style.display = 'inline';
    parent_div.querySelector('#mod'+id).style.display = 'none';
}

// When user clicks "delete" button, confirmation check should appear
function req_delete(id, section, source) {
    document.getElementById('confirm-delete').value = id;
    document.getElementById('confirm-section').value = section;
    document.getElementById('popup_confirmation-source').value = source;
    open_popup("popup_confirmation", source)
}

// Open a popup window
function open_popup(target, source) {
    document.getElementById(target).style.display ='block';
    document.getElementById(source).style.filter ='blur(6px)';
}

// Close a popup window
function close_popup(source, target) {
    document.getElementById(source).style.display ='none';
    target = (target!=null) ? target : document.getElementById(source+"-source").value;
    document.getElementById(target).style.filter ='none';
}


// INFORMATION //

function openNav() {
  document.getElementById("mySidenav").style.width = "250px";
}

/* Set the width of the side navigation to 0 */
function closeNav() {
  document.getElementById("mySidenav").style.width = "0";
}
