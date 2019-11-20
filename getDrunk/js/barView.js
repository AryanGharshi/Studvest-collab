// Set default image for gallery
let galleryImgID = 0;
let numberOfGalleryImgs = 2;
let galleryImgs = ['https://www.adressa.no/pluss/meninger/article13853663.ece/iftmam/BINARY/w980/HeidisBierBarOslo01.jpg', "https://www.heidisbierbar.no/media/foedselsdag-no2.png"];
gallery = document.getElementById("galleryDiv").style.backgroundImage = "url("+galleryImgs[galleryImgID]+")";

// Print default menu
//let menuTitles = ['Beers', 'Ciders'];
let menu = [{'Category': 'Beers',
    'Drinks': ['Hansa', 'Haandbukk', 'Humlesus', 'Prestesonen'],
    'Fill':   ['0.4l', '0.4l', '0.5l', '0.5l'],
    'Prices': ['42,-', '59,-', '79,-', '89,-']
},
    {'Category': 'Ciders',
        'Drinks': ['Bulmers', 'Grevens', 'Seattle Cider Dry'],
        'Fill':   ['0.5l', '0.5l', '0.5l'],
        'Prices': ['79,-', '89,-', '109,-']
    }
];
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

    // Update menu title
    let menuHeader = document.getElementById("menuHeader");
    menuHeader.innerText = menuTitles[tab];

    // Update menu body
    let newMenuTable = "<table class='menuTable'>";
    let relevantMenuItems = menu[tab];
    for (let i=0; i<relevantMenuItems.Drinks.length; i++) {
        newMenuTable += "<tr>"
        newMenuTable += "<td class='firstCol'>"  + relevantMenuItems.Drinks[i] + "</td>"
        newMenuTable += "<td class='secondCol'>" + relevantMenuItems.Fill[i]   + "</td>"
        newMenuTable += "<td class='thirdCol'>"  + relevantMenuItems.Prices[i] + "</td>"
        newMenuTable += "</tr>"
    }
    newMenuTable += "</table";
    let menuBody = document.getElementById("menuBody");
    menuBody.innerHTML = newMenuTable;
}
