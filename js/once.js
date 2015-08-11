$(function()
{
    $(".small2").on('input',function()
    {
        var input = $(this);
        text = input.val().replace(/[\D]/g, "");
        input.val(text);
    });

var old_value;
    $("input").on('focusin',function()
    {
        var input = $(this);
        old_value = input.val();
        input.val('');
    });

    $("input").on('focusout',function()
    {
        var input = $(this);
        if(input.val() == '')
        {
            input.val(old_value);
        }
    });
    $("#age").on('change',function()
    {
        var input = $(this);
        if(input.val() < 18) {
            text = input.val(18);
        }
    });

    $("#password").on('input',function()
    {
        var input = $(this);
        text = input.val().replace(/[^0-9A-Za-z_!@-]/g, "");
        input.val(text);
    });

    $("#email").on('input',function()
    {
        var input = $(this);
        text = input.val().replace(/[^0-9A-Za-z_.@-]/g, "");
        input.val(text);
    });

    $(".get_user_info").on('click', function(e)
    {
        var user_id = $(this).text();
        $('.popup-box>.bottom').html('');
        getUserInfo(user_id);
    });

    $("#update_activity").on('click', function(e)
    {
        var success = 0;
        var error = 0;
        var count = $('.get_user_info').length;
        $('#loading').show();
        setTimeout(function(){for(i=0;i<count;i++)
        {
            var user_id = $('.get_user_info')[i].text;
            getUserActivity(user_id, i)
        }},1000);
    });

    $("#sync_users").on('click', function(e)
    {
        var success = 0;
        var error = 0;
        var count = $('.get_user_info').length;
        $('#loading').show();
        setTimeout(function(){for(i=0;i<count;i++)
        {
            var user_id = $('.get_user_info')[i].text;
            syncUser(user_id, i)
        }},1000);
    });

//location.search

    $("#add_users").on('click', function()
    {
        var email = $('#email').val();
        var age = $('#age').val();
        var task_id = $('#task_id').val();
        var gender = $('#gender').val();
        var country = $('#country').val();
        var site = $('#site').val();
        var count = $('#count').val();
        var device = $('#device').val();
        var referer = $('#referer').val();
        var time_count = 0;
        if(email != '' && age != '' && gender != '' && country != '' &&
        site != '' && count != '' && device != '') {

            if(email == 'e-mail') {
                alert('Fill all fields correctly!');
                return false;
            }
            for(c=0;c<count;c++) {
                for(i=0;i<$('#site').val().length;i++) {
                    for(y=0;y<$('#country').val().length;y++) {
                        for(x=0;x<$('#device').val().length;x++) {
                            for(z=0;z<$('#gender').val().length;z++) {
                                curr_site = site[i];
                                curr_device = device[x];
                                curr_gender = gender[z];
                                curr_country = country[y];
                                setTimeout(function(curr_site, curr_device, curr_gender, curr_country){
                                var date = new Date();
                                requestId = date.getTime();
                                $('.main_table').show();
                                $('.main_table').append('<tr id='+requestId+'>'
                                +'<td><img src="/img/wait.gif"></td>'
                                +'<td>'+email+'</td>'
                                +'<td>'+curr_site+'</td>'
                                +'<td>'+curr_country+'</td>'
                                +'<td>'+age+'</td>'
                                +'<td>'+curr_gender+'</td>'
                                +'<td>'+curr_device+'</td>'
                                +'<td>'+referer+'</td>'
                                +'</td>');
                                $.post(
                                    "/once/", 
                                    {
                                        ajax : true,
                                        register : true,
                                        task_id : task_id,
                                        email : email,
                                        age : age,
                                        request_id : requestId,
                                        gender : curr_gender,
                                        country : curr_country,
                                        site : curr_site,
                                        device : curr_device,
                                        referer : referer
                                    },
                                    function(response)
                                    {
                                        answer = JSON.parse(response);
                                        if(answer.result) {
                                            $('#'+answer.request_id+'>td')[0].innerHTML = 'success';
                                            $('#'+answer.request_id+'>td')[1].innerHTML = answer.data[0].email;
                                            $('#'+answer.request_id).append('<td>'+answer.data[0].id+'</td>');
                                            $('#'+answer.request_id).append('<td><a href="https://'+answer.data[0].siteDomain+'/site/autologin/key/'+answer.data[0].key+'" target="_blank">https://'+answer.data[0].siteDomain+'/site/autologin/key/'+answer.data[0].key+'</a></td>');
                                            $('#'+answer.request_id).append('<td>'+answer.data[0].splitGroup+'</td>');
                                        } else {
                                            $('#'+answer.request_id+'>td')[0].innerHTML = '<input type="button" id="resend" value="resend" onClick="resend(this)" data="'+answer.request_id+'">';
                                        }
                                        
                                    }
                                );
                                },time_count,curr_site, curr_device, curr_gender, curr_country);
                                time_count = time_count + 1000;
                            }
                        }
                    }
                }
            }
        }
        else {
            alert('Fill all fields correctly!');
        }
    });

/*
    $("#add_users").on('click', function()
    {
            var time_count = 0;
                        for(x=0;x<tableContent.length;x++) {
                            row = tableContent[x];
                                curr_site = row[2];
                                curr_mail = row[1];
                                curr_age = row[4];
                                curr_device = row[6];
                                curr_gender = row[5];
                                curr_country = row[3];
                                requestId = row[0]
                                setTimeout(function(requestId, curr_site, curr_mail, curr_age, curr_device, curr_gender, curr_country){
                                $('.main_table').show();
                                $('.main_table').append('<tr id='+requestId+'>'
                                +'<td><img src="/img/wait.gif"></td>'
                                +'<td>'+curr_mail+'</td>'
                                +'<td>'+curr_site+'</td>'
                                +'<td>'+curr_country+'</td>'
                                +'<td>'+curr_age+'</td>'
                                +'<td>'+curr_gender+'</td>'
                                +'<td>'+curr_device+'</td>'
                                +'<td></td>'
                                +'</td>');
                                $.post(
                                    "/once/", 
                                    {
                                        ajax : true,
                                        register : true,
                                        task_id : 1416471036,
                                        email : curr_mail,
                                        age : curr_age,
                                        request_id : requestId,
                                        gender : curr_gender,
                                        country : curr_country,
                                        site : curr_site,
                                        device : curr_device,
                                        referer : ''
                                    },
                                    function(response)
                                    {
                                        answer = JSON.parse(response);
                                        if(answer.result) {
                                            $('#'+answer.request_id+'>td')[0].innerHTML = 'success';
                                            $('#'+answer.request_id+'>td')[1].innerHTML = answer.data[0].email;
                                            $('#'+answer.request_id).append('<td>'+answer.data[0].id+'</a></td>');
                                            $('#'+answer.request_id).append('<td><a href="https://'+answer.data[0].siteDomain+'/site/autologin/key/'+answer.data[0].key+'" target="_blank">https://'+answer.data[0].siteDomain+'/site/autologin/key/'+answer.data[0].key+'</a></td>');
                                        } else {
                                            $('#'+answer.request_id+'>td')[0].innerHTML = '<input type="button" id="resend" value="resend" onClick="resend(this)" data="'+answer.request_id+'">';
                                        }
                                        
                                    }
                                );
                                },time_count, requestId, curr_site, curr_mail, curr_age, curr_device, curr_gender, curr_country);
                                time_count = time_count + 1000;
            }
    });
    */
});


function getUserInfo(user_id)
{
    if(user_id != '') {
        $.post(
            "/once/", 
            { 
                ajax : true,
                get_user_info : true,
                user_id : user_id
            },
            function(response)
            {
                answer = JSON.parse(response);
                $('.popup-box>.bottom').append('<table >'
                    +'<script>'
                    +'$("a#get_user_activity").on("click", function(e) {'
                    +'var user_id = $("#get_user_activity").text();'
                    +'getUserActivity(user_id);'        
                    +'});'
                    +'</script>'
                    +'<tr>'
                    +'<th>site:</th><td>'+answer['data'][0].site+'('+answer['data'][0].site_id+')</td>'
                    +'</tr><tr>'
                    +'<th>id:</th><td><a class="link" id="get_user_activity">'+answer['data'][0].id+'</a></td>'
                    +'</tr><tr>'
                    +'<th>key:</th><td>'+answer['data'][0].key+'</td>'
                    +'</tr><tr>'
                    +'<th>login:</th><td>'+answer['data'][0].login+'</td>'
                    +'</tr><tr>'
                    +'<th>password:</th><td>'+answer['data'][0].password+'</td>'
                    +'</tr><tr>'
                    +'<th>email:</th><td>'+answer['data'][0].email+'</td>'
                    +'</tr>'
                    +'<tr><td rowspan="12"><table><tr>'
                    +'<th>platform:</th><td>'+answer['data'][0].platform+'</td>'
                    +'</tr><tr>'
                    +'<th>traffic:</th><td>'+answer['data'][0].traffic+'</td>'
                    +'</tr>'
                    +'<tr>'
                    +'<th>country:</th><td>'+answer['data'][0].country+'</td>'
                    +'</tr><tr>'
                    +'<th>registered:</th><td>'+answer['data'][0].reg_time+'</td>'
                    +'</tr><tr>'
                    +'<th>gender:</th><td>'+answer['data'][0].gender+'</td>'
                    +'</tr><tr>'
                    +'<th>birthday:</th><td>'+answer['data'][0].birthday+'</td>'
                    +'</tr><tr>'
                    +'<th>orientation:</th><td>'+answer['data'][0].orientation+'</td>'
                    +'</tr><tr>'
                    +'<th>ll</th><td>'+answer['data'][0].ll+'</td>'
                    +'</tr><tr>'
                    +'<th>location</th><td>'+answer['data'][0].location+'</td>'
                    +'</tr><tr>'
                    +'<th>searchable</th><td>'+answer['data'][0].searchable+'</td>'
                    +'</tr><tr>'
                    +'<th>confirmed</th><td>'+answer['data'][0].confirmed+'</td>'
                    +'</tr><tr>'
                    +'<th>active</th><td>'+answer['data'][0].active+'</td>'
                    +'</tr></table></td>'
                    +'<td><img src="http://maps.googleapis.com/maps/api/staticmap?center='+answer['data'][0].ll+'&zoom=2&size=200x200&sensor=true&markers=color:blue%7C'+answer['data'][0].ll+'"></td>'
                    +'</tr>'
                );
                $('#popup-box-1').show();
                $('#blackout').show();
                $('html,body').css('overflow', 'hidden');
            }
        )
    }
    else {
        alert('Fill all fields correctly!');
    }
}

function syncUser(user_id, i)
{
    $("#loading").show();
    $.get(
        "/sync_by_createria/"+user_id, 
        { 
            ajax : true, 
            activity : true,
            id : user_id
        }, 
        function()
        {
            $(document).ajaxStop(function(e)
            {
                $('#loading').hide()
                $('#informer').show().delay(2000, function(){location.search = '';});
            });
        }
    )
}

function getUserActivity(user_id, i)
{
    $("#loading").show();
    $.get(
        "/once/", 
        { 
            ajax : true, 
            activity : true,
            id : user_id
        }, 
        function()
        {
            $(document).ajaxStop(function(e)
            {
                $('#loading').hide()
                $('#informer').show().delay(2000, function(){location.search = '';});
            });
        }
    )
}

/*resend
arrayCount = $('.main_table').children().children().length
for(i=0;i<arrayCount;i++) {
    item = $('.main_table').children().children()[250];
    id = item.getAttribute('id');
    content = $('#'+id+'>td')[0].innerHTML
    if(content == '<img src="/img/wait.gif">') {
        $('#'+id+'>td')[0].innerHTML = '<input type="button" id="resend" value="resend" onClick="resend(this)" data="'+id+'">'
    }
}
*/
function resend(event)
{
    var request_id = event.getAttribute('data')
    var params  = $('#'+request_id+'>td');
    var task_id = $('#task_id').val();
    var email   = params[1].innerHTML;
    var site    = params[2].innerHTML;
    var country = params[3].innerHTML;
    var age     = params[4].innerHTML;
    var gender  = params[5].innerHTML;
    var device  = params[6].innerHTML;
    var referer = params[7].innerHTML;
    $('#'+request_id+'>td')[0].innerHTML = '<img src="/img/wait.gif">';
   
    $.post(
        "/once/", 
        {
            ajax : true,
            register : true,
            task_id : task_id,
            email : email,
            age : age,
            request_id : request_id,
            gender : gender,
            country : country,
            site : site,
            device : device,
            referer : referer
        },
        function(response)
        {
            answer = JSON.parse(response);
            if(answer.result) {
                $('#'+answer.request_id+'>td')[0].innerHTML = 'success';
                $('#'+answer.request_id+'>td')[1].innerHTML = answer.data[0].email;
                $('#'+answer.request_id).append('<td>'+answer.data[0].id+'</a></td>');
                $('#'+answer.request_id).append('<td><a href="https://'+answer.data[0].siteDomain+'/site/autologin/key/'+answer.data[0].key+'" target="_blank">https://'+answer.data[0].siteDomain+'/site/autologin/key/'+answer.data[0].key+'</a></td>');
            } else {
                $('#'+answer.request_id+'>td')[0].innerHTML = '<input type="button" id="resend" value="resend" onClick="resend(this)" data="'+answer.request_id+'">';
            }
        }
    );
}
