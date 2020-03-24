/*var remove={
  del_one: document.getElementsByClassName('delete'),
  item: document.getElementsByClassName('item'),
}
remove.del_one[0].addEventListener('click',
  function() {
        remove.item[0].style.display='none';
  }
);
*/
function del($n){
  var item=String('barname'+$n);
  document.getElementById(item).style.display='none';
}

function del2($n){
  var item=String('drinks'+$n);
  document.getElementById(item).style.display='none';
}

function del3($n){
  var item=String('tags'+$n);
  document.getElementById(item).style.display='none';
}

function del4($n){
  var item=String('menus'+$n);
  document.getElementById(item).style.display='none';
}
