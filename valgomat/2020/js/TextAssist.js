// Text assist v2
function TextAssist(text, elem) {
  'use strict';

  var build = document.createElement('div');
  build.classList.add('text-assist');

  var background = document.createElement('div');
  background.classList.add('text-assist-bg');
  build.appendChild(background);

  build.style.opacity = 0;
  setTimeout(function() { build.style.opacity = ''; }, 20);

  var p = document.createElement('p');
  p.classList.add('text-assist-text');
  build.appendChild(p);

  this.moveTo = function(text, elem) {
    build.style.height = elem.clientHeight + 'px';
    build.style.transform = 'translate3d(0, '
      + (elem.getBoundingClientRect().top
      + window.scrollY)
      +'px, 0)';
    p.textContent = text;

    if(this.open === false) {
      document.body.appendChild(build);
      this.open = true;
    }
  };

  this.close = function() {
    if(this.open === false) { return; }
    this.open = false;
    if(build.parentNode !== null) {
      build.parentNode.removeChild(build);
    }
  };

  this.moveTo(text, elem);

  document.body.appendChild(build);

  build.onclick = this.close;

  SP.assist = this;

  // Exposed vars
  this.build = build;
  this.text = text;
  this.elem = elem;
  this.open = true;
}
