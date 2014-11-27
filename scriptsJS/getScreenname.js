// Get activity from admin area

	var system = require('system');
	var url = system.args[1];
	var page = new WebPage(), testindex = 0, loadInProgress = false;
	console.log(url);
	page.onConsoleMessage = function(msg) {
	  console.log(msg);
	};
	 
	page.onLoadStarted = function() {
	  loadInProgress = true;
	  console.log("-------------");
	  console.log("load started");
	};
	 
	page.onLoadFinished = function() {
	  loadInProgress = false;
	  console.log("load finished");
	  console.log("-------------");
	};

	var steps = [
	  function() {
		console.log("Load Login Page");
		page.settings.userAgent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_2) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.6 Safari/537.11";
		page.open(url);
	  },
	  function() {
	   console.log("Enter Credentials");
	   page.includeJs("https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js");
	   page.evaluate(function() {
		 $('#AdminLoginForm_login').val("arzhanov");
		 $('#AdminLoginForm_password').val("CiWacMadJej9");
		 console.log(document.title);
	   });
	  },
	  function() {
	   console.log('login');
	   page.evaluate(function() {
		 console.log(document.title);
		 $('#user-login-form').submit();	
	   });
	  },
	  function() {
	   console.log('go to user/find');
	   page.open("https://my.ufins.com/user/find");	
	   console.log(document.title);
	  },
	  function() {
	   console.log('Enter user id');
		page.includeJs("https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js");
	   page.evaluate(function() {
		 console.log($(location).attr('href'));
		 $('#FindUserForm_user').val("de87609cfba211e3a082d4bed9a94a8f");
	   });
	  },
	  function() {
	   console.log('Find user');
	   page.evaluate(function() {
		 console.log(document.title);
		 $('#find-user-form').submit();
	   });
	  },
	  function() {
	   console.log('Show some user info');
	   page.evaluate(function() {
		console.log($('#FindUserForm_user').val());
		 console.log($('#yw1'));
	   });
	  },
	]

interval = setInterval(function() {
  if (!loadInProgress && typeof steps[testindex] == "function") {
    console.log("step " + (testindex));
    steps[testindex]();
    //page.render((testindex) + ".png");
    testindex++;
  }
  if (typeof steps[testindex] != "function") {
    console.log("test complete!");
    phantom.exit();
  }
}, 50);
	
