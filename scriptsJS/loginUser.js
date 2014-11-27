// Get activity from admin area
    var system = require('system');
    var autologin = system.args[1];

    page = new WebPage(), testindex = 0, loadInProgress = false;
	page.viewportSize = { width: 1024, height: 768 };
   
	page.onConsoleMessage = function(msg) {
   	};  
    page.onLoadStarted = function() {
		loadInProgress = true;
    };
    page.onLoadFinished = function() {
		loadInProgress = false;
    };

    var steps = [
		function() {
		    page.open(autologin);
		},
		function() {
			page.open('/site/logout');
		},
	];

interval = setInterval(function() {
  if (!loadInProgress && typeof steps[testindex] == "function") {
    steps[testindex]();
    testindex++;
  }
  if (typeof steps[testindex] != "function") {
    phantom.exit();
  }
}, 800);
