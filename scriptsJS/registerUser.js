// Get activity from admin area
    var date = new Date();
    var uniqueAdding = date.getTime();
    var system = require('system');
    var site = system.args[1];
    var email = system.args[2].split('@')[0]+'+'+uniqueAdding+'@'+system.args[2].split('@')[1];
    var device = system.args[3];
    var platform = "www";
    var gender = system.args[4];
    var orientation = system.args[5];
    var age = (system.args[6] === null) ? 45 : system.args[6];
    var ip = system.args[7];
    var city = system.args[8];
    city = city.replace(/\+/g, ' ');
    var referer = (system.args[9] != undefined || system.args[9] != null) ? system.args[9] : '';
    var debug = system.args[10] != null ? system.args[10] : false;
    console.log(system.args[10]);
    console.log(debug);
    var year = date.getFullYear()-age;
    var day = 10;//addZero(date.getDay());
    var month = addZero(date.getMonth()+1);
    var password = '123123';
    var alphabet = ["a","b","c","d","e","f","g","h","i","k","l","m","n","o","p","q","r","s","t","v","x","y","z"];

    var screenname_adding = date.getTime().toString().slice(-5);
    var screenname = '';
    for(i=0; i<getRandomArbitary(3, 7);i++)
    {
      screenname += alphabet[getRandomArbitary(0,22)];
    }
    var screenname = screenname+screenname_adding;
    
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
    page.settings.userAgent = userAgent;


    page.viewportSize = { width: 1024, height: 768 };

    page.onConsoleMessage = function(msg) {
    console.log(msg);
    };
     
phantom.onError = function(msg, trace) {
    console.log(msg+'/ trace: '+trace);
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
    
    var steps = [
        function() {
            page.open('https://'+platform+'.'+site+'/admin2/', function(status) {
            });
            phantom.addCookie({
                'name': 'ip_address',
                'value': ip,
                'domain': platform+'.'+site
            });
        },
        function() {
            page.open('https://'+site+'/admin2/', function(status) {
            });
            phantom.addCookie({
                'name': 'ip_address',
                'value': ip,
                'domain': site
            });
        },
        function() {
            page.open('https://'+platform+'.'+site+referer, function() {
            });
        },
        /*function() {
            page.includeJs("https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js");
            if(platform == 'm' && document.getElementsByClassName('btn-update')[0].href)
            {
                mob_url = page.evaluate(function(page) {
                    mob_url = document.getElementsByClassName('btn-update')[0].href;
                    return mob_url;
                }, page);
                page.open(mob_url);
            }
        },*/
        function() {
            page.includeJs("https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js");
            page.evaluate(function(phantom) {
                $('#switcher2RegForm').click();  
                return;
            }, phantom);
        },
        
        function() {
            page.includeJs("https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js");
            page.evaluate(function(phantom, gender, orientation, year, month, day, email, password, city, screenname, debug) {
                $('.btn--popup').click();
                console.log(debug);
                if(debug) console.log("$('.btn--popup').click();");
                $('#UserForm_gender').val(gender).removeAttr('disabled');
                $('#formRegGender').val(gender).removeAttr('disabled');
                if(debug) console.log("$('#UserForm_gender').val("+gender+").removeAttr('disabled');");
                $('#UserForm_sexual_orientation').val(orientation).removeAttr('disabled');
                if(debug) console.log("$('#UserForm_sexual_orientation').val("+orientation+").removeAttr('disabled');");
                $('#UserForm_year').val(year).change().removeAttr('disabled');
                if(debug) console.log("$('#UserForm_year').val("+year+").change().removeAttr('disabled');");
                $('#UserForm_month').val(month).change().removeAttr('disabled');
                if(debug) console.log("$('#UserForm_month').val("+month+").change().removeAttr('disabled');");
                $('#UserForm_day').val(day).change().removeAttr('disabled');
                if(debug) console.log("$('#UserForm_day').val("+day+").change().removeAttr('disabled');");
                $('#UserForm_login').val(screenname).change().removeAttr('disabled');
                if(debug) console.log("$('#UserForm_login').val("+screenname+").change().removeAttr('disabled');");
                postCode = $('#UserForm_location').val();
                if(debug) console.log("postCode = $('#UserForm_location').val();");
                if(postCode) {
                    postCode = postCode.replace(/ /g, '');
                }                
                if(debug && postCode) console.log("postCode = postCode.replace(/ /g, '');");
                if(postCode == '') {
                    $('#UserForm_location').val(city).removeAttr('disabled');
                    if(debug) console.log("$('#UserForm_location').val("+city+").removeAttr('disabled');");
                }
                $('#UserForm_email').val(email).removeAttr('disabled');
                if(debug) console.log("$('#UserForm_email').val("+email+").removeAttr('disabled');");
                $('#UserForm_just_email').val(email).removeAttr('disabled');
                if(debug) console.log("$('#UserForm_just_email').val("+email+").removeAttr('disabled');");
                $('#UserForm_password').val(password).removeAttr('disabled');
                if(debug) console.log("$('#UserForm_password').val("+password+").removeAttr('disabled');");
            }, phantom, gender, orientation, year, month, day, email, password, city, screenname, debug);
        },
        function() {
            page.includeJs("https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js");
            page.evaluate(function(phantom) {
                $('#register-form').submit();  
                $('#register_frm').submit();
                $('#reg-form').submit();                
                return;
            }, phantom);
        },
        function() {
            page.includeJs("https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js");
            page.evaluate(function(phantom, email) {
                if(document.location.pathname == '/verify/cardAuthorization')
                {
                    card_number = 4012888888881881;
                    card_name = 'Mark Shelton';
                    card_month = 10;
                    card_year = 2016;
                    cvv = 521;
                    $('#CardAuthorizationModel_cardNumber').val(card_number);
                    $('#CardAuthorizationModel_cardHolder').val(card_name);
                    element = document.getElementById('CardAuthorizationModel_expiration_date_m');
                    var options = element.options;
                    for (var i = 0; i < options.length; i++)
                    {
                      if(card_month == false)
                      {
                        if(i == options.length-1)
                        {
                          options[i].selected = true;
                        }          else {
                          options[i].selected = false;
                        }
                      }  else 
                      {      
                        if(options[i].getAttribute('value') == card_month)
                        {    
                          options[i].selected = true;   
                          break;    
                        }       else if(options[i].getAttribute('value') >= card_month)   
                        {     
                          options[i].selected = true;   
                        }    else 
                        {          
                          options[i].selected = false; 
                        }   
                      } 
                    } 
                    document.getElementById('CardAuthorizationModel_expiration_date_m').parentNode.getElementsByTagName('span')[0].innerHTML = card_month;

                    year = 2016;
                    element = document.getElementById('CardAuthorizationModel_expiration_date_y');
                    var options = element.options;
                    for (var i = 0; i < options.length; i++)
                    {       
                      if(card_year == false)
                      {
                        if(i == options.length-1)
                        {
                          options[i].selected = true;
                        } else {
                          options[i].selected = false;
                        }
                      }  else {
                        if(options[i].getAttribute('value') == card_year)
                        {    
                          options[i].selected = true;   
                          break;    
                        }       else if(options[i].getAttribute('value') >= card_year)   
                        {     
                          options[i].selected = true;   
                        }    else 
                        {          
                          options[i].selected = false; 
                        }   
                      } 
                    } 
                    document.getElementById('CardAuthorizationModel_expiration_date_y').parentNode.getElementsByTagName('span')[0].innerHTML = card_year;
                    $('#CardAuthorizationModel_securityNumber').val(cvv);
                    $('#AgeVerificationPageModel_terms').click();
                    console.log(email);
                    $('#avp').submit();
                    phantom.exit();
                } else if(document.location.pathname == '/verify/ageVerificationPage')
                {
                    card_number = 4012888888881881;
                    card_name = 'Mark Shelton';
                    card_month = 10;
                    card_year = 2016;
                    cvv = 521;
                    $('#AgeVerificationPageModel_cardNumber').val(card_number);
                    $('#AgeVerificationPageModel_cardHolder').val(card_name);
                    element = document.getElementById('AgeVerificationPageModel_expiration_date_m');
                    var options = element.options;
                    for (var i = 0; i < options.length; i++)
                    {
                      if(card_month == false)
                      {
                        if(i == options.length-1)
                        {
                          options[i].selected = true;
                        }          else {
                          options[i].selected = false;
                        }
                      }  else {
                        if(options[i].getAttribute('value') == card_month)
                        {    
                          options[i].selected = true;   
                          break;    
                        }       else if(options[i].getAttribute('value') >= card_month)   
                        {     
                          options[i].selected = true;   
                        }    else {
                          options[i].selected = false; 
                        }
                      } 
                    } 
                    document.getElementById('AgeVerificationPageModel_expiration_date_m').parentNode.getElementsByTagName('span')[0].innerHTML = card_month;

                    year = 2016;
                    element = document.getElementById('AgeVerificationPageModel_expiration_date_y');
                    var options = element.options;
                    for (var i = 0; i < options.length; i++)
                    {       
                      if(card_year == false)
                      {
                        if(i == options.length-1)
                        {
                          options[i].selected = true;
                        }          else {
                          options[i].selected = false;
                        }
                      }  else 
                      {      
                        if(options[i].getAttribute('value') == card_year)
                        {    
                          options[i].selected = true;   
                          break;    
                        }       else if(options[i].getAttribute('value') >= card_year)   
                        {     
                          options[i].selected = true;   
                        }    else 
                        {          
                          options[i].selected = false; 
                        }   
                      } 
                    } 
                    document.getElementById('AgeVerificationPageModel_expiration_date_y').parentNode.getElementsByTagName('span')[0].innerHTML = card_year;
                    $('#AgeVerificationPageModel_securityNumber').val(cvv);
                    $('#AgeVerificationPageModel_terms').click();
                    console.log(email);
                    $('#avp').submit();
                    phantom.exit();
                }
            }, phantom, email);
        },
/*        function() {
            page.includeJs("https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js");
            page.evaluate(function(phantom) {
                if (document.location.pathname == '/verify/ageVerificationPage' || document.location.pathname == '/verify/cardAuthorization') {
                    
                }
            }, phantom);
        },*/
        
        function() {
            page.includeJs("https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js");
            page.evaluate(function(phantom) {
                if(document.location.pathname == '/pay/features') {
                    $('#subscription').submit();
                }
            }, phantom);
        },
        function() {
            page.includeJs("https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js");
            page.evaluate(function(phantom) {
                if(document.location.pathname == '/pay/liveCamCredits') {
                    $('#liveCamCreditForm').submit();
                }
            }, phantom);
        },
        function() {
            page.includeJs("https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js");
            page.evaluate(function(phantom, email) {
                console.log(email);
            }, phantom, email);
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


