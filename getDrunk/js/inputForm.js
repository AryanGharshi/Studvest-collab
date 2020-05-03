document.addEventListener('click', function(e) {
    // event listener listens on every single object of the document
    console.log(e);
    let clicked_object = e.target;
                                                 // checks which element was clicked
    if(clicked_object.matches('.btn') || clicked_object.matches('.add')) {       // checks if the clicked element is from class 'btn'
        let target_popup_id = clicked_object.getAttribute('value')
        let target_popup = document.getElementById(target_popup_id);             // retrieves the object with the respective id
        target_popup.style.display='block';                                      // displays the popup
        document.getElementById('are').style.display='none';           // hides the main div
        document.getElementById('side_foot').style.display='none';     // hides the footer
    }

    if(clicked_object.matches('.close')) {                                       // checks if the clicked element is from class 'close'
        let current_popup = clicked_object.parentElement.parentElement;          // takes the parent of the parent element (in our case that's the div for the entire popup)
        current_popup.style.display='none';                                      // hides the popup
        document.getElementById('are').style.display='block';          // displays the main div
        document.getElementById('side_foot').style.display='block';    // displays the footer
        var n = document.getElementsByClass('add').length();
        for (var i = 0; i < n.length; i++) {
          n = document.getElementsByClass('add')[n].style.display='none';
        }
    }
  })
