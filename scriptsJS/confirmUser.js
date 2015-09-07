// Get activity from admin area
    var system = require('system');
    var date = new Date();
    var uniqueAdding = date.getTime();
    var autologin = system.args[1];
    var ip = system.args[2];
    var site = system.args[3];
    var device = system.args[5];
    var requestId = system.args[6];
    var platform = "www";
    var payFor = system.args[4] == 1 ? true : false;
    var cardNumber = ['4012','8888','8888','1881'];
    var cardMonth = '10';
    var cardYear = '2016';
    var cardNameFirst = 'Mark';
    var cardNameLast = 'Shelton';
    var cardCV2 = '521';
    var cpfValue = '08077385575';
    var cardAddress = 'Beverly Hills,123';
    var cardZip = '90210';    
    var alphabet = ["a","b","c","d","e","f","g","h","i","k","l","m","n","o","p","q","r","s","t","v","x","y","z"];

    page = new WebPage(), testindex = 0, loadInProgress = false;
    page.viewportSize = { width: 1024, height: 768 };
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
        userAgent = "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; en-us; rv:29.0) Gecko/20100101 Firefox/29.0";
        platform = "www";
        break;
    default:
        userAgent = "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; en-us; rv:29.0) Gecko/20100101 Firefox/29.0";
        platform = "www";
        break;
}

page.onError = function (msg, trace) {
    console.log(msg);
    trace.forEach(function(item) {
        console.log('  ', item.file, ':', item.line);
    });

};

    page.onConsoleMessage = function(msg) {
    console.log(msg);
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
        'domain': platform+'.'+site
    });
    var steps = [
        function() {
            console.log('https://'+platform+'.'+autologin);
            page.open('https://'+platform+'.'+autologin, function(status) {
                console.log(status);
            });
        },
        function() {
            page.includeJs("https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js");
            page.evaluate(function(phantom, screenname) {
                if(document.location.pathname == '/funnel')
                {
                    $('#funnelScreenname').val(screenname);
                    

                }
            }, phantom, screenname);
        },
        function() {
            page.includeJs("https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js");
            page.evaluate(function(phantom) {
                if(document.location.pathname == '/funnel') {
                    $('#FunnelForm').submit();
                }
            }, phantom);
        },
        function() {
            if(payFor) {
                page.open('https://'+platform+'.'+site+'/pay/membership', function(status) {
                });
            }            
        },
        function() {
            page.includeJs("https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js");
            if(payFor) {
                page.evaluate(function(phantom, cardNumber, cardMonth, cardYear, cardNameFirst, cardNameLast, cardCV2, cardAddress, cardZip, payFor) {
                    $('#continueButton').click();
                    page.render('../screenshots/'+site+'/'+requestId+'/confirm_'+uniqueAdding+'('+page.url+'_'+(testindex) + ").png");
                }, phantom, cardNumber, cardMonth, cardYear, cardNameFirst, cardNameLast, cardCV2, cardAddress, cardZip, payFor);
            }
        },
        function() {
            page.includeJs("https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js");
            if(payFor) {
                page.evaluate(function(phantom, cardNumber, cardMonth, cardYear, cardNameFirst, cardNameLast, cardCV2, cardAddress, cardZip, payFor) {
                        cardFull = cardNumber[0]+cardNumber[1]+cardNumber[2]+cardNumber[3];
                      if(document.getElementById('CreditCardPaymentForm_card_number')) {
                          setInputValue(document.getElementById('CreditCardPaymentForm_card_number'), cardFull);
                      }
                      if(document.getElementById('CreditCardPaymentForm_card_number').parentElement.children[0].children.length > 1) {
                        for(i=0;i<4;i++) {
                            
                            setInputValue(document.getElementById('CreditCardPaymentForm_card_number').parentElement.children[0].children[i], cardNumber[i]);
                        }
                      }

                        if(document.getElementById('CreditCardPaymentForm_cpf'))setInputValue(document.getElementById('CreditCardPaymentForm_cpf'), cpfValue);
                        if(document.getElementById('CreditCardPaymentForm_expiration_date_m')){
                            setInputValue(document.getElementById('CreditCardPaymentForm_expiration_date_m'), cardMonth);
                            document.getElementById('CreditCardPaymentForm_expiration_date_m').parentNode.getElementsByTagName('span')[0].innerHTML = cardMonth;
                        }

                      if(document.getElementById('CreditCardPaymentForm_expiration_date_y')){
                        setInputValue(document.getElementById('CreditCardPaymentForm_expiration_date_y'), cardYear);
                        document.getElementById('CreditCardPaymentForm_expiration_date_y').parentNode.getElementsByTagName('span')[0].innerHTML = cardYear;
                      }

                      if(document.getElementById('CreditCardPaymentForm_card_holder'))setInputValue(document.getElementById('CreditCardPaymentForm_card_holder'), cardNameFirst+' '+cardNameLast);

                      if(document.getElementById('CreditCardPaymentForm_security_number'))setInputValue(document.getElementById('CreditCardPaymentForm_security_number'), cardCV2);

                      if(document.getElementById('CreditCardPaymentForm_name_first'))setInputValue(document.getElementById('CreditCardPaymentForm_name_first'), cardNameFirst);

                      if(document.getElementById('CreditCardPaymentForm_name_last'))setInputValue(document.getElementById('CreditCardPaymentForm_name_last'), cardNameLast);
                      if(document.getElementById('CreditCardPaymentForm_address'))setInputValue(document.getElementById('CreditCardPaymentForm_address'), cardNameLast);

                      if(document.getElementById('CreditCardPaymentForm_city'))setInputValue(document.getElementById('CreditCardPaymentForm_city'), cardAddress);

                      if(document.getElementById('CreditCardPaymentForm_postal_code'))setInputValue(document.getElementById('CreditCardPaymentForm_postal_code'), cardZip);


                        document.getElementById('CreditCardPaymentForm_card_holder').click();

                    function setInputValue(element, value){
                      if(element.type == 'select-one'){
                        var options = element.options;
                         for (var i = 0; i < options.length; i++) {
                           if(value == false){
                             if(i == options.length-1){
                               options[i].selected = true;
                             }
                             else{
                               options[i].selected = false;
                             }
                           }
                           else{
                             if(options[i].getAttribute('value') == value){
                               options[i].selected = true;
                               break;
                             }
                             else if(options[i].getAttribute('value') >= value){
                               options[i].selected = true;
                             }
                             else{
                               options[i].selected = false;
                             }
                           }

                         }
                      }
                      else {
                        element.value = value;
                      }
                    }
                    page.render('../screenshots/'+site+'/'+requestId+'/confirm_'+uniqueAdding+'('+page.url+'_'+(testindex) + ").png");
                }, phantom, cardNumber, cardMonth, cardYear, cardNameFirst, cardNameLast, cardCV2, cardAddress, cardZip, payFor);
            }
        },
        function() {
            if(payFor) {
                page.evaluate(function(phantom, payFor, platform) {
                        $('#subscription').submit();
                        $('#creditCardPaymentForm').submit();
                    page.render('../screenshots/'+site+'/'+requestId+'/confirm_'+uniqueAdding+'('+page.url+'_'+(_testindex) + ").png");
                }, phantom, payFor, platform);
            }
        },
        function() {
            page.open('/site/logout');
        },
    ];

interval = setInterval(function() {
  if (!loadInProgress && typeof steps[testindex] == "function") {
    steps[testindex]();
    page.render('../screenshots/'+site+'/'+requestId+'/confirm_'+uniqueAdding+'('+page.url+'_'+(testindex) + ").png");
    testindex++;
  }
  if (typeof steps[testindex] != "function") {
    phantom.exit();
  }
}, 800);
/*
function WriteToFile(msg, uniqueAdding, testindex) {
    set fso = CreateObject("Scripting.FileSystemObject");  
    set s = fso.CreateTextFile('screenshots\\logs.txt', true);
    s.writeline(uniqueAdding + ':' + testindex + ':' + msg);
    s.writeline("-----------------------------");
    s.Close();
}*/

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
