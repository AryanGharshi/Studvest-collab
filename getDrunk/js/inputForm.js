// Auto-fill drink_type when user enters known drink
document.addEventListener('change', function(e) {

    console.log(e);

    // Determine clicked and target object
    let clicked_object = e.target;
    let target_object = clicked_object.matches('input#add-drink') ? document.getElementById('add-type')
                      : clicked_object.matches('input#mod-drink') ? document.getElementById('mod-type')
                      : null;

    console.log(target_object);

    // checks which element was clicked
    if(target_object != null) {
        let prefilled_drink_type = mapping_drinkType_selectIdx[mapping_drink_drinkType[clicked_object.value]];
        if(prefilled_drink_type!=null) {
            target_object.selectedIndex = mapping_drinkType_selectIdx[mapping_drink_drinkType[clicked_object.value]];
            target_object.disabled = true;
        }
        else {
            target_object.disabled = false;
        }
    }
})

// When user clicks "modify" button on inputForm.php
function req_modify(parent, id, columns) {

    // Identify parent div
    let parent_div = document.getElementById(parent);

    // transform static text into dynamic input fields
    columns.forEach(element => parent_div.querySelector("#"+element+id).disabled = false);
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
    console.log(source);
    console.log(document.getElementById(source));
    document.getElementById(source).style.display ='none';
    target = (target!=null) ? target : document.getElementById(source+"-source").value;
    document.getElementById(target).style.filter ='none';
}

