// Set default image for gallery
let galleryImgID = 0;
let numberOfGalleryImgs = 2;
gallery = document.getElementById("galleryDiv").style.backgroundImage = "url("+galleryImgs[galleryImgID]+")";

// Start automatic slideshow
automaticSlideshow();

function automaticSlideshow() {
    setTimeout(automaticSlideshow, 5000); // Change image every 5 seconds
    changeGalleryImage(+1);
}

// Change Images in gallery
function changeGalleryImage (direction) {

    // Change ID of gallery image
    let newGalleryImgID = (galleryImgID+direction+numberOfGalleryImgs)%numberOfGalleryImgs;
    galleryImgID = newGalleryImgID;

    // Change gallery image
    let gallery = document.getElementById("galleryDiv");
    gallery.style.backgroundImage = "url("+galleryImgs[newGalleryImgID]+")";
}


// Print menu
/*function printMenu (tab) {

    // Update menu switch
    let newSwitchTable = "<table class='menuSwitchTable'><tr>";
    let menuTitles = Object.keys(menus);
    for (let i=0; i<menuTitles.length; i++) {
        if(i==tab) {
            newSwitchTable += "<td class='colHighlighted' onclick='printMenu("+i+")'>"
        }
        else {
            newSwitchTable += "<td class='colNormal' onclick='printMenu("+i+")'>"
        }
        newSwitchTable += menuTitles[i];
        newSwitchTable += "</td>";
    }
    newSwitchTable += "</tr></table>";
    let menuSwitch = document.getElementById("menuSwitch");
    menuSwitch.innerHTML = newSwitchTable;
    
    // Update menu body with drinks
    let newMenuTable = "<table class='menuTable'>";
    for (let i=0; i<menus[menuTitles[tab]].length; i++) {
        newMenuTable += "<tr>"
        newMenuTable += "<td class='firstCol'>"  + menus[menuTitles[tab]][i]['drink_name'] + "</td>"
        newMenuTable += "<td class='secondCol'>" + menus[menuTitles[tab]][i]['size'] + "l</td>"
        newMenuTable += "<td class='thirdCol'>"  + menus[menuTitles[tab]][i]['price'] + ".-</td>"
        newMenuTable += "</tr>"
    }
    newMenuTable += "</table";
    let menuBody = document.getElementById("menuBody");
    menuBody.innerHTML = newMenuTable;
}*/