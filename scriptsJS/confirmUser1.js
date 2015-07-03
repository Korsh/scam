// Get activity from admin area
    var system = require('system');
    var date = new Date();
    var uniqueAdding = date.getTime();
    var autologin = system.args[1];
    var ip = system.args[2];
    var site = system.args[3];
    
    page = new WebPage(), testindex = 0, loadInProgress = false;
    page.viewportSize = { width: 1024, height: 768 };
   
    page.onConsoleMessage = function(msg) {
        WriteToFile(msg);
       };  
    page.onLoadStarted = function() {
        loadInProgress = true;
    };
    page.onLoadFinished = function() {
        loadInProgress = false;
    };

    phantom.addCookie({
        'name': 'ip_address',
        'value': ip,
        'domain': 'https://www.'+site
    });
    phantom.addCookie({
        'name': 'ip_address',
        'value': ip,
        'domain': 'https://m.'+site
    });
    phantom.addCookie({
        'name': 'ip_address',
        'value': ip,
        'domain': 'https://'+site
    });
    
    var steps = [
        function() {
            page.open('https://m.'+site+'/admin2/');
            phantom.addCookie({
                'name': 'ip_address',
                'value': ip,
                'domain': 'm.'+site
            });
        },
        function() {
            page.open('https://www.'+site+'/admin2/');
            phantom.addCookie({
                'name': 'ip_address',
                'value': ip,
                'domain': 'www.'+site
            });
        },
        function() {
            page.open('https://'+site+'/admin2/');
            phantom.addCookie({
                'name': 'ip_address',
                'value': ip,
                'domain': +site
            });
        },
        function() {
            page.open(autologin);
        },
        function() {
            page.open('/funnel');
        },
        function() {
            page.open('/site/logout');
        },
    ];

interval = setInterval(function() {
  if (!loadInProgress && typeof steps[testindex] == "function") {
    steps[testindex]();
    page.render('screenshots/'+uniqueAdding+'('+(testindex) + ").png");
    testindex++;
  }
  if (typeof steps[testindex] != "function") {
    phantom.exit();
  }
}, 1500);

function WriteToFile(msg) {
    set fso = CreateObject("Scripting.FileSystemObject");  
    set s = fso.CreateTextFile('screenshots/'+uniqueAdding+'('+(testindex) + ").txt", True);
    s.writeline(msg);
    s.Close();
 }
