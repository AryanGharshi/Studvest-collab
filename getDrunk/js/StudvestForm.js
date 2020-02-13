var i = 1;

function add_fields() {
    i++;
    var d = document.createElement('div');
    d.innerHTML = '<div class="label">Drink ' + i +':</div><div class="content"><span>Name: <input type="text" style="width:48px;" name="DrinkName" value="" /><span>Size: <input type="text" style="width:48px;" name="size" value="" /></span><span>Price: <input type="text" style="width:48px;" name="price" value="" /></span></div>';
    document.getElementById('aNewDrink').appendChild(d);
}

function removeFields(d) {
    var element = document.getElementById('div');
    element.parentNode.removeChild(element);
}