'use strict'
function Credit(persons) {
  // constructor
  var build = document.createDocumentFragment();

  var aboutDiv = document.createElement('div');
  aboutDiv.classList.add('about-wrap');
  aboutDiv.id = "about";

  var github = document.createElement('a');
  github.href = "https://github.com/Havegum/valgomat-studvest";
  github.target = "_blank";
  github.textContent = "GitHub repo";
  aboutDiv.appendChild(github);

  var about = document.createElement('a');
  github.target = "_blank";
  about.href = "https://www.studvest.no/slik-lagde-vi-valgomat/";
  about.textContent = "Om valgomaten";
  aboutDiv.appendChild(about);

  build.appendChild(aboutDiv);

  var div = document.createElement('div');
  div.classList.add('byline');

  persons
    .map(this.toHTML)
    .forEach(el => {
      div.appendChild(el);
    });

  build.appendChild(div);

  // exposed vars
  this.build = build;
}

Credit.prototype.toHTML = function(person) {
  let div = document.createElement('a');
  div.classList.add('credit-person-wrap');
  div.href =  'mailto:' + person.email + '?Subject=Om%20Studvests%20valgomat';

  let img = document.createElement('img');
  img.classList.add('credit-portrait');
  img.src = "./img/" + person.img;

  let name = document.createElement('p');
  name.classList.add('credit-name', 'byline-text');
  name.textContent = person.name;

  let role = document.createElement('p');
  role.classList.add('credit-role', 'byline-text');
  role.textContent = person.role;

  div.appendChild(img);
  div.appendChild(name);
  div.appendChild(role);

  return div;
}
