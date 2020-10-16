document.addEventListener('click', function(e) {
    // event listener listens on every single object of the document
    console.log(e);
    let clicked_object = e.target;
                                                 // checks which element was clicked
    if(clicked_object.matches('.btn') || clicked_object.matches('.add')) {       // checks if the clicked element is from class 'btn'
        let target_popup_id = clicked_object.getAttribute('value')
        console.log(target_popup_id);              // reads the id of the target popup from value attribute (<button type='button' class='btn' value="popup_1">)
        let target_popup = document.getElementById(target_popup_id);             // retrieves the object with the respective id
        target_popup.style.display='block';                                      // displays the popup
        document.getElementById('are').style.display='none';           // hides the main div
        document.getElementById('side_foot').style.display='none';     // hides the footer
    }

    if(clicked_object.matches('.close')) {                                       // checks if the clicked element is from class 'close'
      window.location.replace("http://i.studvest.no/barguide/html/inputForm.php")
    }
  })

document.addEventListener('change', function(e) {
    // event listener listens on every single object of the document
    console.log(e);
    let clicked_object = e.target;
    // checks which element was clicked
    if(clicked_object.matches('input#drink')) {       // checks if the clicked element is from class 'btn'
        let target_object = document.getElementById('drink_type');
        let drink_type = mapping_drinkTypeId_selectIdx[mapping_drink_drinkTypeId[clicked_object.value]];
        if(drink_type!=null) {
            target_object.selectedIndex = mapping_drinkTypeId_selectIdx[mapping_drink_drinkTypeId[clicked_object.value]];
            target_object.disabled = true;
        }
        else {
            target_object.disabled = false;
        }
    }
})
