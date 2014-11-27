$(function() {
	$(".small2").on('input',function() {
		var input = $(this);
		text = input.val().replace(/[\D]/g, "");
		input.val(text);
	});

var old_value;
	$("input").on('focusin',function() {
		var input = $(this);
		old_value = input.val();
		input.val('');
	});

	$("input").on('focusout',function() {
		var input = $(this);
		if(input.val() == '')
		{
			input.val(old_value);
		}
	});
	$("#age").on('change',function() {
		var input = $(this);
		if(input.val() < 18)
		{
			text = input.val(18);	
		}
	});

	$("#password").on('input',function() {
		var input = $(this);
		text = input.val().replace(/[^0-9A-Za-z_!@-]/g, "");
		input.val(text);
	});

	$("#email").on('input',function() {
		var input = $(this);
		text = input.val().replace(/[^0-9A-Za-z_.@-]/g, "");
		input.val(text);
	});

	$("#email").on('input',function() {
		var input = $(this);
		text = input.val().replace(/[^0-9A-Za-z_.@-]/g, "");
		input.val(text);
	});

	$(".get_user_info").on('click', function(e) {
		var user_id = $(this).text();
		$('.popup-box>.bottom').html('');
		getUserInfo(user_id);		
	});

	$("#add_users").on('click', function() {
		var email = $('#email').val();
		var age = $('#age').val();
		var task_id = $('#task_id').val();
		var gender = $('#gender').val();
		var country = $('#country').val();
		var site = $('#site').val();
		var count = $('#count').val();
		var device = $('#device').val();
		var referer = $('#referer').val();
		if(email != '' && 		
		age != '' &&
		gender != '' &&
		country != '' &&
		site != '' &&
		count != '' &&
		device != ''
		)
		{
			if(email == 'e-mail'){
			alert('Fill all fields correctly!');
				return false;
			}			
			for(c=0; c<count; c++)
			{
				for(i=0;i<$('#site').val().length;i++)
				{
					for(y=0;y<$('#country').val().length;y++)
					{
						for(x=0;x<$('#device').val().length;x++)
						{
							for(z=0;z<$('#gender').val().length;z++)
							{
								var date = new Date();
								requestId = date.getTime();				
								$('.main_table').show();
								$('.main_table').append('<tr id='+requestId+'>'
								+'<td>'+site[i]+'</td>'
								+'<td>'+country[y]+'</td>'
								+'<td>'+email+'</td>'
								+'<td>'+gender[z]+'</td>'
								+'<td>'+age+'</td>'
								+'<td>'+device[x]+'</td>'
								+'<td>'+referer+'</td>'
								+'<td>'+count+'</td>'
								+'</td>');
							}
						}
					}
				}
			}
		}
		else
		{
			alert('Fill all fields correctly!');
		}
	});

	$("#save_task").on('click', function() {
		task_id = $('#task_id').val();
		time_interval_minute = $('#time_interval_minute').val();
		time_interval_hour = $('#time_interval_hour').val();
		config = [];
		trs = $('.main_table > tbody').children();
		for(i=0; i<trs.length; i++)
		{
			tr = trs[i];   
			k = new Object();
			k.site = tr.children[0].textContent;
			k.counry = tr.children[1].textContent;
			k.email = tr.children[2].textContent;
			k.gender = tr.children[3].textContent;
			k.age = tr.children[4].textContent;
			k.device = tr.children[5].textContent;
			k.referer = tr.children[6].textContent;
			k.count = tr.children[7].textContent;
			config.push(k);
		}
		
		days = [];
		for(i=0; i<$('.schelude_day').length; i++)
		{
			if($('.schelude_day')[i].checked)
			{
				days.push(i);
			}
		}	
		if(days.length > 0 && config.length > 0)
		{
			$.post(
				"/repeat/",               
				{ 
					ajax : true,        
					save_task : true,
					config : config,
					task_id : task_id,
					days : days,
					time_interval_minute : time_interval_minute,
					time_interval_hour : time_interval_hour
				},
				function(response){
					answer = JSON.parse(response);
					/*$('#'+answer.request_id+'>td')[0].innerHTML = answer.email;
					$('#'+answer.request_id).append('<td>'+answer.data[0].id+'</a></td>');
					$('#'+answer.request_id).append('<td><a href="https://'+answer.data[0].site_domain+'/site/autologin/key/'+answer.data[0].key+'" target="_blank">https://'+answer.data[0].site_domain+'/site/autologin/key/'+answer.data[0].key+'</a></td>');*/
				}
			)
		}
		else
		{
			alert('Fill all fields correctly!');
		}
		
/*
		if(email != '' && 		
		schelude_day != '' && 
		time_interval_minute != '' && 
		time_interval_hour != '' && 
		age != '' &&
		gender != '' &&
		country != '' &&
		site != '' &&
		count != '' &&
		device != ''
		)
		{
			if(email == 'e-mail'){
			alert('Fill all fields correctly!');
				return false;
			}			
			for(c=0; c<count; c++)
			{
				for(i=0;i<$('#site').val().length;i++)
				{
					for(y=0;y<$('#country').val().length;y++)
					{
						for(x=0;x<$('#device').val().length;x++)
						{
							for(z=0;z<$('#gender').val().length;z++)
							{
								var date = new Date();
								requestId = date.getTime();				
								$('.main_table').show();
								$('.main_table').append('<tr id='+requestId+'>'
								+'<td>'+site[i]+'</td>'
								+'<td>'+country[y]+'</td>'
								+'<td>'+email+'</td>'
								+'<td>'+gender[z]+'</td>'
								+'<td>'+age+'</td>'
								+'<td>'+device[x]+'</td>'
								+'<td>'+referer+'</td>'
								+'<td>'+count+'</td>'
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
									gender : gender[z],
									country : country[y],
									site : site[i],
									device : device[x],
									referer : referer
								},
								function(response){
									answer = JSON.parse(response);
									$('#'+answer.request_id+'>td')[0].innerHTML = answer.email;   
									$('#'+answer.request_id).append('<td>'+answer.data[0].id+'</a></td>');					
									$('#'+answer.request_id).append('<td><a href="https://'+answer.data[0].site_domain+'/site/autologin/key/'+answer.data[0].key+'" target="_blank">https://'+answer.data[0].site_domain+'/site/autologin/key/'+answer.data[0].key+'</a></td>');					}
								)
							}
						}
					}
				}
			}
		}
		else
		{
			alert('Fill all fields correctly!');
		}*/
	});

});

function getUserInfo(user_id) {
	if(user_id != '')
	{			
				
		$.post(
			"/repeat/",               
			{ 
				ajax : true,        
				get_user_info : true,
				user_id : user_id
			},
			function(response){
				answer = JSON.parse(response);
				$('.popup-box>.bottom').append('<table >'
					+'<tr>'
						+'<th>site:</th><td>'+answer['data'][0].site+'('+answer['data'][0].site_id+')</td>'
					+'</tr><tr>'
						+'<th>id:</th><td>'+answer['data'][0].id+'</td>'
					+'</tr><tr>'
						+'<th>key:</th><td>'+answer['data'][0].key+'</td>'
					+'</tr><tr>'
						+'<th>login:</th><td>'+answer['data'][0].login+'</td>'
					+'</tr><tr>'
						+'<th>password:</th><td>'+answer['data'][0].password+'</td>'
					+'</tr><tr>'
						+'<th>email:</th><td>'+answer['data'][0].email+'</td>'
					+'</tr><tr>'
						+'<th>platform:</th><td>'+answer['data'][0].platform+'</td>'
					+'</tr><tr>'
						+'<th>traffic:</th><td>'+answer['data'][0].traffic+'</td>'
					+'</tr><tr>'
						+'<th>first name:</th><td>'+answer['data'][0].fname+'</td>'
					+'</tr><tr>'
						+'<th>last name:</th><td>'+answer['data'][0].lname+'</td>'
					+'</tr><tr>'
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
						+'<th>active</th><td>'+answer['data'][0].active+'</td>'
					+'</tr>');
				$('#popup-box-1').show();
				$('#blackout').show();
				$('html,body').css('overflow', 'hidden');
			}
		)			
			
	}
	else
	{
		alert('Fill all fields correctly!');
	}
}
