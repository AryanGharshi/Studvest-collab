function del(section,id){
  var item=String(section+id);
  document.getElementById(item).style.display='none';
  console.log(id);
}

function reg(section,id,input){
  var item=String(section+id);
  var modify_txt=String('modify'+section+id);
  item=document.getElementById(item);
  modify_txt=document.getElementById(modify_txt);
  if (modify_txt.innerHTML == 'modify' ) {
    item.removeAttribute('disabled');
    modify_txt.innerHTML="add";
    modify_txt.style.border='green solid 1px';
    modify_txt.style.color='green';
    item.style.background="white";
    item.style.color="#000000";
    console.log(id);
    console.log(modify_txt);
    console.log(modify_txt.innerHTML);
    }
    else {
      item.setAttribute('disabled','disabled');
      modify_txt.innerHTML='modify';
      modify_txt.style.border= '#FFC700 solid 1px';
      modify_txt.style.color= '#FFC700';
      item.style.background="none";
      item.style.color="#777777";
      item.style.border="none";
    }

}
