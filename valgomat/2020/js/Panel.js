function Panel() {
  'use strict';
  // constructor
  var panel = document.createElement('div');
  panel.classList.add('valgomat-full');

  var about = document.createElement('a');
  about.classList.add('about');
  about.href = "#about";
  about.textContent = "?";
  panel.appendChild(about);

  panel.addEventListener('mousedown', () => {
    if(SP.assist) { SP.assist.close(); }
  });

  // exposed vars
  this.panel = panel;
}

// Navbar constructor
Panel.prototype.Navbar = function(panel, forwardDisabled, noReturn) {
  'use strict';
  forwardDisabled = forwardDisabled || true;
  var build = document.createElement('div');
  build.classList.add('valgomat-navbar');

  if(!noReturn) {
    var backBtn = document.createElement('div');
    backBtn.classList.add('navbtn-left', 'noselect');
    build.appendChild(backBtn);
    backBtn.addEventListener('click', () => {
      if(panel.interactable) panel.jumpTo(panel.questionNum - 1);
    });
  }

  var forwardBtn = document.createElement('div');
  forwardBtn.classList.add('navbtn-right', 'noselect');

  if(forwardDisabled) {
    forwardBtn.classList.add('navbtn-right-disabled');
  }

  forwardBtn.addEventListener('click', () => {
    if(!panel.interactable) return;
    if(!forwardBtn.classList.contains('navbtn-right-disabled')) {
      if(SP.assist) { SP.assist.close(); }
      panel.jumpTo(panel.questionNum + 1);
    } else {
      let error = panel.forwardError;
      if(SP.assist.text !== error || !SP.assist.open) {
        SP.assist.moveTo(error, document.getElementsByClassName('valgomat-navbar')[0]);
        panel.panel.addEventListener('mousedown', SP.assist.close, {once:true});
      }
    }
  });

  build.appendChild(forwardBtn);

  this.build = build;
  this.forward = forwardBtn;
}

// Save data
Panel.prototype.store = function (questionNum, response) {
  SP.userResponse[questionNum] = response;
};

// Place on screen
Panel.prototype.display = function (fromLeft) {
  document.getElementById('valgomat').appendChild(this.panel);
  this.slidein(fromLeft);
};

// Slide in from left or right
Panel.prototype.slidein = function (fromLeft) {
  this.interactable = false;
  var slideclass = 'slidein-' + (fromLeft ? 'left':'right');

  this.panel.classList.add(slideclass);
  setTimeout(() => {
    this.panel.classList.remove(slideclass);
    this.interactable = true;
  }, 500);
};

// Slide out to left or right
Panel.prototype.slideout = function (toRight) {
  this.interactable = false;
  var slideclass = 'slideout-' + (toRight ? 'right':'left');
  this.panel.classList.add(slideclass);
  setTimeout(() => {
    this.panel.classList.remove(slideclass);
    this.panel.parentNode.removeChild(this.panel);
    this.interactable = true;
  }, 500);
};

// jumpTo(n)
Panel.prototype.jumpTo = function(target) {
  'use strict';
  if(target === this.questionNum) { return this; }
  var incomingPanel, slideLeft;

  switch (target) {
    case 'pri': target = SP.question.length; break;
    case 'score': target = SP.question.length + 1; break;
  }

  slideLeft = (target < this.questionNum);

  this.slideout(slideLeft);

  if (SP.panels[target]) {
    // If panel already exists, use that panel.
    incomingPanel = SP.panels[target];

  } else {
    // Otherwise, make a new appropriate panel
    switch (target) {
      case SP.question.length:
        incomingPanel = new PrioritiesPanel();
        break;

      case SP.question.length + 1:
        incomingPanel = new ScorePanel();
        break;

      default:
        incomingPanel = new QuestionPanel(target);
    }
  }

  SP.currentPanel = target;
  incomingPanel.display(slideLeft)

  a = incomingPanel;
  return incomingPanel;
}

function QuestionPanel(questionNum, noReturn) {
  Panel.call(this); // Super

  var panel = this.panel;
  panel.style.zIndex = 10 + questionNum;

  // Progress bar
  var progress = new this.Progress(questionNum);
  panel.appendChild(progress.build);

  // Inner (Question + BTNS)
  var question = SP.question[questionNum].text;
  var inner = new this.Inner(question, this);
  panel.appendChild(inner.build);

  // Navbar
  var forwardDisabled = true; // REVIEW: Useless variable?
  var navbar = new this.Navbar(this, forwardDisabled, noReturn);
  panel.appendChild(navbar.build);

  var caseBlurb = new this.CaseBlurb(SP.question[questionNum].case);
  panel.appendChild(caseBlurb.build);

  // Exposed variables
  this.panel = panel;
  this.progress = progress;
  this.inner = inner;
  this.navbar = navbar;
  this.caseBlurb = caseBlurb;
  this.questionNum = questionNum;
  this.interactable = true;
  this.forwardError = 'Du må ta stilling til påstanden før du kan gå videre';

  // Store globally
  SP.panels[questionNum] = this;
}
QuestionPanel.prototype = Object.create(Panel.prototype);

// Progress constructor
QuestionPanel.prototype.Progress = function(n) {
  'use strict';
  var build = document.createElement('div');
  build.classList.add('progress');
  build.id = 'progress';

  var pText = document.createElement('div');
  pText.classList.add('progress-text');
  pText.id = 'progress-text';

  var text = document.createElement('p');
  text.textContent = 'Spørsmål ' + (n + 1);

  var totalQuestions = document.createElement('span');
  totalQuestions.textContent = ' / ' + (SP.question.length);

  pText.appendChild(text);
  pText.appendChild(totalQuestions);

  build.appendChild(pText);

  var pBarBG = document.createElement('div');
  pBarBG.classList.add('progress-bar-background');
  build.appendChild(pBarBG);

  var pBarFill = document.createElement('div');
  pBarFill.classList.add('progress-bar-fill');
  pBarFill.id = 'progress-bar-fill';
  pBarFill.style.width = (n / (SP.question.length-1) * 100) + '%';
  build.appendChild(pBarFill);

  var pBarMarkers = document.createElement('div');
  pBarMarkers.classList.add('progress-bar-markers');
  pBarMarkers.id = 'progress-bar-markers';

  for(let i = 0; i < SP.question.length; i++) {
    let marker = document.createElement('div');
    marker.classList.add('progress-' + (i <= n ? 'active' : 'inactive'));
    pBarMarkers.appendChild(marker);
  }

  build.appendChild(pBarMarkers);

  // Obj = { HTMLNODE };

  this.build = build;
}

// Inner constructor
QuestionPanel.prototype.Inner = function(question, panel) {
  'use strict';
  question = question || 'Mangler spørsmål ...';

  var build = document.createElement('div');
  build.classList.add('valgomat-inner');

  var q = document.createElement('div');
  q.classList.add('valgomat-question');

  var qTxt = document.createElement('p');
  qTxt.textContent = question;

  q.appendChild(qTxt);
  build.appendChild(q);


  var btns = document.createElement('div');
  btns.classList.add('valgomat-buttons');

  btns.appendChild(new panel.PollButton(2, panel).build);
  btns.appendChild(new panel.PollButton(1, panel).build);
  btns.appendChild(new panel.PollButton(0, panel).build);

  build.appendChild(btns);

  this.build = build;
}

// PollButton constructor
QuestionPanel.prototype.PollButton = function(n, panel) {
  'use strict';
  var build = document.createElement('div');
  build.classList.add('poll');
  build.onclick = function() {
    panel.response = n;
    check(this, panel);
    panel.store(panel.questionNum, n);
    scoreAll();
  };

  var graphic = document.createElement('div');
  graphic.classList.add('poll-inactive');
  build.appendChild(graphic);

  var text = document.createElement('p');
  switch (n) {
    case 0: text.textContent = 'Uenig';   break;
    case 1: text.textContent = 'Nøytral'; break;
    case 2: text.textContent = 'Enig';    break;
  }
  text.classList.add('noselect')
  build.appendChild(text);

  this.build = build;
}

// Case constructor
QuestionPanel.prototype.CaseBlurb = function(text) {
  'use strict';
  text = text || '(Kunne ikke laste sakstekst)';
  var build = document.createElement('div');
  build.classList.add('case-blurb');


  var blurbInner = document.createElement('p');
  text = text
    .replace(/(\[[^\]]+\]\([^\)]+\))/g, '|$1|')
    .split(/[|]/g)
    .map(str => {
      if(str.charAt(0) === '[') {
        str = str
          .replace('[', '')
          .replace(')', '')
          .split(/\]\(/g);
        return `<a href="`+str[1]+`" target="_blank">`+str[0]+'</a>';
      } else {
        return str;
      }
    }).join('');

    blurbInner.innerHTML = text;

  build.appendChild(blurbInner);

  this.build = build;
}

function PrioritiesPanel() {
  Panel.call(this);
  this.questionNum = SP.question.length;
  var panel = this.panel;

  var header = document.createElement('h3');
  header.classList.add('priority-header');
  header.textContent = 'Hva er de viktigste sakene for deg?'
  panel.appendChild(header)

  var trackerWrap = document.createElement('div');
  trackerWrap.classList.add('progress-text', 'noselect');

  var tracker = document.createElement('p');
  tracker.textContent = 'Prioriteringer: 0';
  trackerWrap.appendChild(tracker);

  var totalPriorities = document.createElement('span');
  totalPriorities.textContent = ' / ' + 5;
  trackerWrap.appendChild(totalPriorities);

  panel.appendChild(trackerWrap);

  var priorityListWrapper = document.createElement('div');
  priorityListWrapper.classList.add('priority-slider-wrap');
  SP.question.forEach((q, i) => {
    let e = new this.PrioritySlider(q.shorthand, i, tracker, this);
    priorityListWrapper.appendChild(e.build);
  });
  panel.appendChild(priorityListWrapper);

  // Navbar
  var navbar = new this.Navbar(this, true, false);
  panel.appendChild(navbar.build);

  this.navbar = navbar;
  this.forwardError = 'Du må velge minst tre prioriteringer for å fortsette';
}
PrioritiesPanel.prototype = Object.create(Panel.prototype);

PrioritiesPanel.prototype.PrioritySlider = function (q, i, tracker, panel) {
  var build = document.createElement('div');
  build.classList.add('priority-slider')

  var text = document.createElement('p');
  text.classList.add('priority-slider--shorthand', 'noselect');
  text.textContent = q;
  build.appendChild(text);

  build.addEventListener('click', () => {
    if(build.classList.contains('priority-slider-active')) {
      if(SP.priorities.length === 5) {
        document.getElementsByClassName('priority-slider-wrap')[0].classList.remove('priority-slider-inactive');
      }
      build.classList.remove('priority-slider-active');
      let j = SP.priorities.indexOf(i);
      if(j > -1) SP.priorities.splice(j, 1);

    } else {
      if(SP.priorities.length < 5) {
        build.classList.add('priority-slider-active');
        SP.priorities.push(i);
        if(SP.priorities.length === 5) {
          document.getElementsByClassName('priority-slider-wrap')[0].classList.add('priority-slider-inactive');
        }
      }
    }

    tracker.textContent = 'Prioriteringer: ' + SP.priorities.length;

    if(SP.priorities.length >= 3) {
      panel.navbar.forward.classList.remove('navbtn-right-disabled');
    } else if(!panel.navbar.forward.classList.contains('navbtn-right-disabled')) {
      panel.navbar.forward.classList.add('navbtn-right-disabled');
    }

    scoreAll();
  });

  this.build = build;
};

function ScorePanel() {
  Panel.call(this);
  var panel = this.panel;
  panel.classList.add('score');
  this.questionNum = SP.question.length + 1;
  panel.style.paddingTop = '.7em';
  panel.style.paddingBottom = '.7em';

  var disclaimer = document.createElement('span');
  disclaimer.classList.add('score-disclaimer');
  disclaimer.innerHTML = 'Denne valgomaten gir kun en pekepinn på hvilken liste du er mest enig i basert på et utvalg av spørsmål, og bør ikke brukes som en fasit. Studvest oppfordrer deg til å undersøke listene på egenhånd for å finne ut hvem du bør stemme på. <a href="https://www.studvest.no/slik-lagde-vi-valgomat/" target="_blank">Du kan lese mer om hvordan vi lagde valgomaten her.</a>';
  panel.appendChild(disclaimer);

  scoreAll(true).forEach((party, i) => {
    panel.appendChild((new this.PartyPanel(party, i)).build);
  });

}
ScorePanel.prototype = Object.create(Panel.prototype);

ScorePanel.prototype.PartyPanel = function(party, i) {
  'use strict';
  var build = document.createElement('div');
  build.classList.add('score-list-element');
  build.style.backgroundColor = 'var(--'+party+')';

  var header = document.createElement('h3');
  header.textContent = (i + 1) + '. ' + SP.partier[party];
  header.classList.add('noselect');
  build.appendChild(header);

  if(party === 'gulliste' || party === 'internasjonalliste' || party === 'grnnliste') {
    build.style.color = 'var(--charcoal)';
    header.style.color = 'var(--charcoal)';
  }

  var prioritiesHeader = document.createElement('h4');
  prioritiesHeader.textContent = 'Dine prioriteringssaker:';
  build.appendChild(prioritiesHeader);

  SP.priorities.forEach((priority) => {
    let priorityHead = document.createElement('h5');
    priorityHead.textContent = SP.question[priority].text;
    build.appendChild(priorityHead);

    if(SP.partyPriorities[party].indexOf((priority + 1).toString()) > -1) {
      let span = document.createElement('span');
      span.classList.add('score-list-party-priority-also');
      span.textContent = SP.partier[party] + ' prioriterer dette';
      build.appendChild(span);
    }


    let compareContainer = document.createElement('div');
    compareContainer.classList.add('score-compare-wrap');
    build.appendChild(compareContainer);

    let userResponse;
    if(SP.userResponse[priority] !== undefined) {
      switch (SP.userResponse[priority]) {
        case 2: userResponse = 'Enig';    break;
        case 1: userResponse = 'Nøytral'; break;
        case 0: userResponse = 'Uenig';   break;
        default: userResponse = '(Kunne ikke hente svar)';
      }
    } else {
      userResponse = '(Kunne ikke hente svar)';
    }

    let userP = document.createElement('p');
    userP.textContent = 'Du svarte: ' + userResponse;
    compareContainer.appendChild(userP);

    let partyResponse;
    switch (SP.partyResponses[party][priority]) {
      case 2: partyResponse = 'Enig';    break;
      case 1: partyResponse = 'Nøytral'; break;
      case 0: partyResponse = 'Uenig';   break;
    }

    let partyP = document.createElement('p');
    partyP.textContent = SP.partier[party] + ' svarte: ' + partyResponse;
    compareContainer.appendChild(partyP);
  });

  this.build = build;
}
