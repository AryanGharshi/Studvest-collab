
var modal={
  popup: document.getElementsByClassName('popup'),
  main : document.getElementById('are'),
  foot : document.getElementById('side_foot'),
  btn:document.getElementsByClassName('btn'),
  close:document.getElementsByClassName('close'),
  welcome:document.getElementById('main'),
  add:document.getElementById('addNewBar'),
  edit:document.getElementById('editForm'),
}
modal.add.addEventListener('click',
  function() {
        modal.welcome.style.display='none';
        modal.edit.style.display='block';

  }
);

modal.btn[0].addEventListener('click',
  function() {
        modal.popup[0].style.display='block';
        modal.main.style.display='none';
        modal.foot.style.display='none';
  }

);

modal.close[0].addEventListener('click',
  function() {
        modal.foot.style.display='block';
        modal.popup[0].style.display='none';
        modal.main.style.display='block';
  }
);


modal.btn[1].addEventListener('click',
  function() {
        modal.popup[1].style.display='block';
        modal.main.style.display='none';
        modal.foot.style.display='none';
  }

);

modal.close[1].addEventListener('click',
  function() {
        modal.foot.style.display='block';
        modal.popup[1].style.display='none';
        modal.main.style.display='block';
  }
);

modal.btn[2].addEventListener('click',
  function() {
        modal.popup[2].style.display='block';
        modal.main.style.display='none';
        modal.foot.style.display='none';
  }

);

modal.close[2].addEventListener('click',
  function() {
        modal.foot.style.display='block';
        modal.popup[2].style.display='none';
        modal.main.style.display='block';
  }
);
