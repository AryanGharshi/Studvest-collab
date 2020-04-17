function del(section,id){
  var item=String(section+id);
  document.getElementById(item).style.display='none';
  console.log(id);
}

function reg(section,id,input){
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
}
