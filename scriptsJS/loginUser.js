// Get activity from admin area
    var date = new Date();
    var uniqueAdding = date.getTime();
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
    func = steps[testindex];
    func();
    page.render('../screenshots/'+uniqueAdding+'('+(testindex) + ")2.png");
    testindex++;
  }
  if (typeof steps[testindex] != "function") {
    phantom.exit();
  }
}, 5000);
