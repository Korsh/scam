    page = new WebPage(), testindex = 0, loadInProgress = false;
    page.viewportSize = { width: 1024, height: 768 };
   
    page.onConsoleMessage = function(msg) {
    };
    userAgent = "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:29.0) Gecko/20100101 Firefox/29.0";
    page.settings.userAgent = userAgent;
    page.open('http://api.ipinfodb.com/v3/ip-city/?key=8cef7e56baa8c0bcfe5591b6624fe611bb4e58c5c8b481619dcb6a485f48aa44&format=json', function() {
            var json = page.evaluate(function(phantom){
                return document.getElementsByTagName('html')[0].textContent;
        }, phantom);
        console.log(json);
        phantom.exit();
        
    });
