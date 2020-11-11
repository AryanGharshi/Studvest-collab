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

// When user clicks "modify" button on inputForm_add.php
function modify(drink_relationship_id) {

    // access the columns from the drinks list
    let td_name          = document.getElementById('drink-'+drink_relationship_id+'-name');
    let td_type          = document.getElementById('drink-'+drink_relationship_id+'-type');
    let td_menu          = document.getElementById('drink-'+drink_relationship_id+'-menu');
    let td_vol           = document.getElementById('drink-'+drink_relationship_id+'-volume');
    let td_price         = document.getElementById('drink-'+drink_relationship_id+'-price');
    let td_student_price = document.getElementById('drink-'+drink_relationship_id+'-student-price');
    let td_modify        = document.getElementById('drink-'+drink_relationship_id+'-modify');

    // transform static text into dynamic input fields
    td_name.innerHTML = "<input type='text' class='input-drink' id='mod-drink' name='drink' value='"+td_name.innerHTML+"' list='drinkList' required>";
    td_menu.innerHTML = "<input type='text' class='input-menu' id='mod-menu'  name='menu' value='"+td_menu.innerHTML+"' list='menuList'>";
    td_vol.innerHTML = "<input type='number' class='input-vol' id='mod-vol'   name='vol'   value='"+td_vol.innerHTML.slice(0, -3)+"' min=2 step=1 required>";
    td_price.innerHTML = "<input type='number' class='input-price' id='mod-price' name='price' value='"+td_price.innerHTML.slice(0, -2)+"' min=10 step=1 required>";
    td_student_price.innerHTML = "<input type='number' class='input-price' id='mod-student-price' name='student-price' value='"+td_student_price.innerHTML.slice(0, -2)+"' min=10 step=1>";


    // add select box for drink types
    let select_box = "<select class='input-type' id='mod-type' name='drink_type' disabled required>";
    for (const key of Object.keys(mapping_drinkType_selectIdx)) {
        select_box += "<option value='"+key+"'>"+key+"</option>"
    }
    select_box += "</select>";
    td_type.innerHTML = select_box;

    // disable all modify/delete buttons
    document.querySelectorAll('.modify').forEach(function(button) { button.disabled = true; });
    document.querySelectorAll('.delete').forEach(function(button) { button.disabled = true; });

    // disable input forms
    document.getElementById('add-drink').disabled = true;
    document.getElementById('add-type').disabled = true;
    document.getElementById('add-menu').disabled = true;
    document.getElementById('add-price').disabled = true;
    document.getElementById('add-student-price').disabled = true;
    document.getElementById('add-vol').disabled = true;
    document.getElementById('add-submit').disabled = true;

    // display add button
    td_modify.innerHTML = "<button type='submit' class='add' name='add_drink' value="+drink_relationship_id+" formaction=''>save</button>";
}

// When user clicks "modify" butoon on inputForm.php
function req_modify(id, columns) {

    // transform static text into dynamic input fields
    columns.forEach(element => document.getElementById(element+id).disabled = false);

    // disable all modify/delete buttons
    document.querySelectorAll('.modify').forEach(function(button) { button.disabled = true; });
    document.querySelectorAll('.delete').forEach(function(button) { button.disabled = true; });

    // display save button
    document.getElementById('add'+id).style.display = 'inline';
    document.getElementById('mod'+id).style.display = 'none';
}

// When user clicks "delete" button
function req_delete(id, section, source) {
    document.getElementById('confirm-delete').value = id;
    document.getElementById('confirm-section').value = section;
    document.getElementById('popup_confirmation-source').value = source;
    open_popup("popup_confirmation", source)
}

function open_popup(target, source) {
    document.getElementById(target).style.display ='block';
    document.getElementById(source).style.filter ='blur(6px)';
}

function close_popup(source, target) {
    console.log(source);
    console.log(document.getElementById(source));
    document.getElementById(source).style.display ='none';
    target = (target!=null) ? target : document.getElementById(source+"-source").value;
    document.getElementById(target).style.filter ='none';
}