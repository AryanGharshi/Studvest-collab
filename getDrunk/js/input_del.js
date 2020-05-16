function del(section,id){
  var item=String(section+id);
  document.getElementById(item).style.display='none';
  console.log(id);
}

function reg(section,id,input,itemlist_id){
  var itemlist_id_txt=String('tags'+id);
  var modify_txt=String('modify'+section+id);
  var modify_btn=document.getElementById(modify_txt);
  modify_btn.style.display = 'none';

  var add_txt=String('submit'+section+id);
  var add_btn=document.getElementById(add_txt);
  add_btn.style.display = 'block';

  var item=String(section+id);
  item=document.getElementById(item);
  item.removeAttribute('disabled');
  item.style.background="white";
  item.style.color="#000000";
  console.log('itemblock is' + document.getElementsByClassName('item')[0]);
  var itemlist=document.getElementsByClassName('item');
  var modify=document.getElementsByClassName('modify');
  var move=document.getElementsByClassName('delete');
  for (var i=0; i<itemlist.length; i++){
        itemlist[i].style.background="hsla(0,0%,100%,.3)";
        modify[i].style.border="#777777 solid 1px";
        move[i].style.border="#777777 solid 1px";
        modify[i].style.color="#777777";
        move[i].style.color="#777777";
        modify[i].setAttribute('disabled','false');
        move[i].setAttribute('disabled','false');
      }
  console.log('item_default is '+item_default);
  if (section=='drinksinput') {
    var item_default=document.getElementById(String('drinks'+id));
    var delete_defalult=document.getElementById(String('deletedrinksinput'+id));
    item_default.style.background="none";
    delete_defalult.style.border="#CB1919 solid 1px";
    delete_defalult.style.color="#CB1919";d
    delete_defalult.removeAttribute('disabled');

  }
  else if (section=='menusinput') {
    var item_default=document.getElementById(String('menus'+id));
    var delete_defalult=document.getElementById(String('deletemenusinput'+id));
    item_default.style.background="none";
    delete_defalult.style.border="#CB1919 solid 1px";
    delete_defalult.style.color="#CB1919";
    delete_defalult.removeAttribute('disabled');
  }
  else if (section=='tagsinput') {
    var item_default=document.getElementById(String('tags'+id));
    var delete_defalult=document.getElementById(String('deletetagsinput'+id));
    item_default.style.background="none";
    delete_defalult.style.border="#CB1919 solid 1px";
    delete_defalult.style.color="#CB1919";
    delete_defalult.removeAttribute('disabled');

  }


}

function sub(section,id,input) {
  var add_txt=String('#submit'+section+id);
  console.log(add_txt);
  $(String(add_txt)).ajaxSubmit(function () {
        window.alert('hi');
    });
}
