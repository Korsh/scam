// Get activity from admin area
    var date = new Date();
    var uniqueAdding = date.getTime();
    var system = require('system');
    var site = system.args[1];
    var email = system.args[2].split('@')[0]+'+'+uniqueAdding+'@'+system.args[2].split('@')[1];
    var device = system.args[3];
    var platform = "www";
    var gender = system.args[4];
    var age = system.args[5];
    var ip = system.args[6];
    if(age === null) {age = 45;}
    var year = date.getFullYear()-age;
    var day = addZero(date.getDay());
    var month = addZero(date.getMonth());
    var password = '123123';

switch (device) {
    case "Nexus":
        userAgent = "Mozilla/5.0 (Linux; U; Android 4.2; en-us; Nexus 4 Build/JOP24G) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30";
        platform = "m";
        break;
    case "iPhone":
        userAgent = "Mozilla/5.0 (iPhone; CPU iPhone OS 7_1_1 like Mac OS X) AppleWebKit/537.51.2 (KHTML, like Gecko) Version/7.0 Mobile/11D201 Safari/9537.53";
        platform = "m";
        break;
    case "Chrome":
        userAgent = "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/534.14 (KHTML, like Gecko) Chrome/16.0.0 Safari/534.14";
        platform = "www";
        break;
    case "InternetExplorer":
        userAgent = "Mozilla/4.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.2; .NET4.0C)";
        platform = "www";
        break;
    case "Firefox":
        userAgent = "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:29.0) Gecko/20100101 Firefox/29.0";
        platform = "www";
        break;
    default:
        userAgent = "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:29.0) Gecko/20100101 Firefox/29.0";
        platform = "www";
        break;
}

    page = new WebPage(), testindex = 0, loadInProgress = false;
    page.viewportSize = { width: 1024, height: 768 };

    page.onConsoleMessage = function(msg) {
    };
     
page.onError = function(msg, trace) {

  var msgStack = ['ERROR: ' + msg];

  if (trace && trace.length) {
    msgStack.push('TRACE:');
    trace.forEach(function(t) {
      msgStack.push(' -> ' + t.file + ': ' + t.line + (t.function ? ' (in function "' + t.function +'")' : ''));
    });
  }

  console.error(msgStack.join('\n'));
  return;
};
    page.onLoadStarted = function() {
        loadInProgress = true;
    };
     
    page.onLoadFinished = function() {
        loadInProgress = false;
    };



    var steps = [
        function() {
            page.open('https://'+site+'/admin2/');
            phantom.addCookie({
                'name': 'ip_address',
                'value': ip,
                'domain': platform+'.'+site
            });
        },
        function() {
            page.settings.userAgent = userAgent;
            page.open('https://'+site);
        },
        function() {
            page.includeJs("https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js");
            if(platform == 'm')
            {
                mob_url = page.evaluate(function(page) {
                    mob_url = document.getElementsByClassName('btn-update')[0].href;
                    return mob_url;
                }, page);
                page.open(mob_url);
            }
        },    
        function() {
            page.includeJs("https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js");
            page.evaluate(function(gender, year, month, day, email, password) {
                $('.btn--popup').click()
                $('#UserForm_gender').val(gender).change();
                $('#UserForm_location').val('90210');
                $('#UserForm_year').val(year).change();
                $('#UserForm_month').val(month).change();
                $('#UserForm_day').val(10).change();
                $('#UserForm_email').val(email);
                $('#UserForm_just_email').val(email);
                
                $('#UserForm_password').val(password);
                $('#register-form').submit();  
                $('#register_frm').submit();

            }, gender, year, month, day, email, password);
        },
        function() {
            page.includeJs("https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js");
            page.evaluate(function(phantom) {
                if(platform == 'm') {
                    var returned_email = email;
                } else {
                    $('#resend-confirm-mail').click();
                    var returned_email = false;
                    if($('#RegistrationCompleteForm_resendEmail').val() != 'undefined' && $('#RegistrationCompleteForm_resendEmail') !== null) {
                        var returned_email =  $('#RegistrationCompleteForm_resendEmail').val();
                    } else {
                        var returned_email = email;
                    }
                }
                return returned_email;
            }, phantom);
        },
        

    ];

interval = setInterval(function() {
  if (!loadInProgress && typeof steps[testindex] == "function") {
    func = steps[testindex];
    func();
    page.render('screenshots/'+uniqueAdding+'('+(testindex) + ").png");
    testindex++;
  }
  if (typeof steps[testindex] != "function") {
    phantom.exit();
  }
}, 1000);
    
function getRandomArbitary(min, max)
{
    return parseInt(Math.random() * (max - min) + min);
}

function sleep(milliseconds) {
  var start = new Date().getTime();
  for (var i = 0; i < 1e7; i++) {
    if ((new Date().getTime() - start) > milliseconds){
      break;
    }
  }
}

function addZero(i) {
    return (i < 10)? "0" + i: i;
}
