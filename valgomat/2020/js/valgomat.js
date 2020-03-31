// VARIABLES
let SP = {};

SP.currentPanel = 0;

SP.priorities = [];

SP.userResponse = [];

SP.panels = [];

SP.ol = [];

let qURL = "https://docs.google.com/spreadsheets/d/e/2PACX-1vTj8EZpU2z43WPrTghBpq2_ykA1TVz4LWL5HBOYzUpU3ngVynSrHBrI-hQ4DZHZ9VK6Uk34BH6gbIo8/pub?gid=0&single=true&output=tsv";
let bylineURL = "https://docs.google.com/spreadsheets/d/e/2PACX-1vTj8EZpU2z43WPrTghBpq2_ykA1TVz4LWL5HBOYzUpU3ngVynSrHBrI-hQ4DZHZ9VK6Uk34BH6gbIo8/pub?gid=1754245989&single=true&output=tsv";

let a; // For testing purposes

console.log('v1.3: 2019');

// Global functions
function loadJSON(url) {
  'use strict';
  return new Promise(function(resolve, reject) {
    let xhr = new XMLHttpRequest();
    xhr.open('GET', url, true);
    xhr.onreadystatechange = function() {
      if(xhr.readyState === XMLHttpRequest.DONE) {
        if(xhr.status === 200) {
          resolve(xhr.responseText);
        } else {
          reject(xhr);
        }
      }
    };
    xhr.send(null);
  });
}

function barTranslateX(i) {
  'use strict';
  return 'translate3d(' + (1.453 * i) + 'em,0,0)';
}

function scoreForParty(party) {
  'use strict';
  let sum = SP.userResponse.reduce(function(sum, point, i) {
    if(point === undefined) { return sum; }
    let diff = Math.abs(SP.partyResponses[party][i] - point);
    let base = 2 - diff - (point % 2);
    let multiplier = 1;

    let userPriority = (SP.priorities.indexOf(i) > -1);
    if(userPriority) {
      multiplier *= SP.priorityMultiplier;

      let partyPriority = (SP.partyPriorities[party].indexOf(i) > -1);
      if(partyPriority) {
        multiplier *= SP.partyPriorityMultiplier;
      }
    }
    base *= multiplier;
    return sum + base;
  }, 0);
  SP.max = Math.max(SP.max, sum);
  return { party: party, sum: sum };
}

function scaleParty(o) {
  'use strict';
  o.elem = document.getElementById(o.party);
  let scalar = (o.sum == 0 && SP.max == 0) ? 50 : o.sum  / SP.max * 100;
  o.elem.style.height = 'calc(' + (scalar + (SP.currentPanel - SP.question.length)) + '% + .3em)';
  return o;
}

function relocateParty(o, i) {
  'use strict';
  o.elem.parentNode.style.transform = barTranslateX(i);
  o.elem.style.backgroundColor = '';
  return o;
}

function comparePartyScore(a, b) {
  'use strict';
  let comparison = 0;
  if(a.sum > b.sum) {
    comparison = -1;
  } else if (a.sum < b.sum) {
    comparison = 1;
  }
  return comparison;
}

function check(e, panel) {
  'use strict';
  let parent = e.parentNode;
  e = e.firstElementChild;
  if(e.classList.contains('poll-active')) { return; }

  let checked = parent.getElementsByClassName('poll-active')[0];
  if(checked === undefined) {
    panel.navbar.forward.classList.remove('navbtn-right-disabled');
  } else {
    checked.classList.replace('poll-active', 'poll-inactive');
  }

  e.classList.replace('poll-inactive', 'poll-active');
}

function scoreAll(final) {
  'use strict';
  SP.max = 0;
  let graph = document.getElementById('graph');
  let sortedParties = Object.keys(SP.partier)
    .map(scoreForParty) // Compare score to user response
    .map(scaleParty) // Scale party graph
    .slice()
    .sort(comparePartyScore); // Sort parties by size
  if(!final) {

    setTimeout(function() {
      graph.style.width = '';
      graph.classList.add('graph-notext');
      sortedParties.map(relocateParty);
    }, 250);

  } else {

    setTimeout(function () {
      graph.style.width = '24.8em';
      graph.classList.remove('graph-notext');
      sortedParties.forEach(function(o, i) {
        o.elem.parentNode.style.transform = 'translate3d('+(i*3.42)+'em,0,0)';
        o.elem.style.backgroundColor = 'var(--'+o.party+')';
        o.elem.getElementsByClassName('graph-text')[0].style.bottom = (i%2 ? '-3em' : '');
      });
    }, 250);
    return sortedParties.map((o) => o.party);
  }
}


function drawParties() {
  'use strict';
  let build = document.createDocumentFragment();
  Object.keys(SP.partier).forEach(function(party, i) {
    let fullname = SP.partier[party];

    let barContainer = document.createElement('div');
    barContainer.classList.add('bar-container');
    barContainer.style.transform = barTranslateX(i);

    let bar = document.createElement('div');
    bar.classList.add('bar');
    bar.id = party;
    bar.style.height = '50%';
    barContainer.appendChild(bar);

    let p = document.createElement('p');
    p.textContent = fullname;
    p.classList.add('graph-text', 'noselect');
    p.style.color = 'var(--'+party+')';
    bar.appendChild(p);

    build.appendChild(barContainer);
  });
  let div = document.getElementById('graph');
  div.classList.add('graph');
  div.appendChild(build);
  scoreAll(true);

  /*setTimeout(function () {
    new TextAssist('Når du tar valgomaten vil listene skjules. På slutten får du vite hvilke lister som er mest enig i dine valg', div);
  }, 1000);*/
}

// ONLOAD
window.addEventListener('load', function () {
  'use strict';
  let qCSV;
  let bylineCSV;
  try {
    qCSV = loadJSON(qURL);
    bylineCSV = loadJSON(bylineURL);
  } catch (e) {
    console.error(e);
  }
  let parser = parseCSV('\t')
  qCSV.then(parser).then(rows => {

    SP.partier = {}
    SP.partyResponses = {}
    SP.partyPriorities = {}
    SP.partyPriorityMultiplier = 2
    SP.priorityMultiplier = 2
    SP.question = []


    let parties = [] // local bookkeeping

    rows[0].slice(1,-3).forEach(field => {
        let party = field.toLowerCase().replace(/[^\w]/g, '');
        parties.push(party)
        SP.partier[party] = field
    });
    rows.shift();

    let partyCount = parties.length
    for (let i = 0; i < partyCount; i++) {
      let party = parties[i];
      SP.partyPriorities[party] = rows[0][i+1].replace(/[^\d,]+/g, '').split(',')
      SP.partyResponses[party] = rows.slice(1).map(row => +row[i+1] + 1)
    }

    rows.shift();
    rows.forEach(row => {
      SP.question[+row[0] - 1] = {
        id: row[0] - 1,
        text: row[1 + partyCount],
        shorthand: row[2 + partyCount],
        case: row[3 + partyCount]
      }
    });

    drawParties();
    let a = new QuestionPanel(0, true);
    a.display();
  });

  bylineCSV.then(parser).then(response => {
    response.shift();
    // TODO: Check if works
    SP.byline = response.map(row => {
      return {
        name: row[0],
        role: row[1],
        email: row[2],
        img: row[3]
      }
    });
    let credit = new Credit(SP.byline);
    document.getElementById('byline').appendChild(credit.build);
  });
}, {once:true});

function parseCSV(delimeter) {
  return function(csv) {
    let rows = csv.split(/(\r)?\n/gi);
    return rows.filter(e => e.trim() !== '').map(row => row.split(delimeter).filter(e => e.trim() !== ''));
  }
}
