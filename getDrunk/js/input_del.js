function del(section,id){
  var item=String(section+id);
  document.getElementById(item).style.display='none';
  console.log(id);
}

function reg(section,id){
  var item=String(section+id);
  item=document.getElementById(item);
  item.removeAttribute('disabled');
  console.log(id);
}
