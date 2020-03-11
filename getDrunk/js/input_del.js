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
function del($i){
  item=document.getElementsByClassName('item');
  item[$i].style.display='none';
}
