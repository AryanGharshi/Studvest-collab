// Set default image for gallery
let galleryImgID = 0;
let numberOfGalleryImgs = 2;
let galleryImgs = ['https://www.adressa.no/pluss/meninger/article13853663.ece/iftmam/BINARY/w980/HeidisBierBarOslo01.jpg', "https://www.heidisbierbar.no/media/foedselsdag-no2.png"];
gallery = document.getElementById("galleryDiv").style.backgroundImage = "url("+galleryImgs[galleryImgID]+")";

printMenu (0);

// Start automatic slideshow
automaticSlideshow();


// Change Images in gallery
function changeGalleryImage (direction) {

    // Change ID of gallery image
    let newGalleryImgID = (galleryImgID+direction+numberOfGalleryImgs)%numberOfGalleryImgs;
    galleryImgID = newGalleryImgID;

    // Change gallery image
    let gallery = document.getElementById("galleryDiv");
    gallery.style.backgroundImage = "url("+galleryImgs[newGalleryImgID]+")";
}

function automaticSlideshow() {
    setTimeout(automaticSlideshow, 5000); // Change image every 2 seconds
    changeGalleryImage(+1);
}



// Print menu
function printMenu (tab) {

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
}
