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

	$("#add_users").on('click', function() {
		var email = $('#email').val();
		var age = $('#age').val();
		var gender = $('#gender').val();
		var country = $('#country').val();
		var site = $('#site').val();
		var count = $('#count').val();
		var device = $('#device').val();
		var referer = $('#referer').val();
		
		registerUser(email, password, age, gender, country, site, count, device);		
	});

	$("#get_user_info").on('click', function() {
		var user_id = $("#user_info").text();
		alert(1);
		$('#user_info').html('');
		getUserInfo(user_id);		
	});

	$("#get_activity").on('click', function() {
		var user_id = $('#activity_user_id').val();
		$('#activity_result').html('');

		getActivity(user_id);		
	});
});

function registerUser(email, age, gender, country, site, count, device) {
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
		for(i=0; i<count; i++)
		{
			var date = new Date();
			requestId = date.getTime();			
	
			$('.main_table').append('<tr id='+requestId+'>'
			+'<td><img src="/img/wait.gif"></td>'
			+'<td>'+age+'</td>'
			+'<td>'+gender+'</td>'
			+'<td>'+country+'</td>'
			+'<td>'+site+'</td>'
			+'<td>'+referer+'</td>'
			+'</td>');
			$('.main_table').show();			
			
			
			$.post(
				"/register/",               
				{ 
					ajax : true,        
					register : true,
					email : email,
					age : age,
					request_id : requestId,
					gender : gender,
					country : country,
					site : site,
					device : device,
					referer : referer
				},
				function(response){
					answer = JSON.parse(response);
					$('#'+answer.request_id+'>td')[0].innerHTML = answer.email;   
					$('#'+answer.request_id).append('<td>'+answer.data[0].id+'</a></td>');					
					$('#'+answer.request_id).append('<td><a href="https://'+site+'/site/autologin/key/'+answer.data[0].key+'" target="_blank">https://'+site+'/site/autologin/key/'+answer.data[0].key+'</a></td>');					}
			)			
			}
	}
	else
	{
		alert('Fill all fields correctly!');
	}
}

function getActivity(user_id) {
	if(user_id != '')
	{			
				
		$.post(
			"/register/",               
			{ 
				ajax : true,        
				activity : true,
				user_id : user_id
			},
			function(response){
				answer = JSON.parse(response);
				$('#activity_result').append('<table>');
				if(!!answer)
				{
					$('#activity_result').append('<tr><th>id</th><th>email</th><th>99</th><th>ll</th></tr>');
					for(i=0;i<answer.length;i++)
					{
						$('#activity_result').append('<tr><td>'+answer[i].user["id"]+'</td><td>'
						+answer[i].user["mail"]+'</td><td>'
						+answer[i].user["99"]+'</td><td>'
						+answer[i].user["ll"]+'</td><td>'
						+answer[i].message["time"]+'</td><td>'
						+answer[i].message["text"]+'</td>');							
					}
				}
				else
				{
					$('#activity_result').append('<tr><td>No activity</td></tr>');
				}
				$('#activity_result').append('</table>');
			}
		)			
			
	}
	else
	{
		alert('Fill all fields correctly!');
	}
}

function getUserInfo(user_id) {
	if(user_id != '')
	{			
				
		$.post(
			"/register/",               
			{ 
				ajax : true,        
				user_info : true,
				user_id : user_id
			},
			function(response){
				answer = JSON.parse(response);
				/*$('#activity_result').append('<table>');
				if(!!answer)
				{
					$('#activity_result').append('<tr><th>id</th><th>email</th><th>99</th><th>ll</th></tr>');
					for(i=0;i<answer.length;i++)
					{
						$('#activity_result').append('<tr><td>'+answer[i].user["id"]+'</td><td>'
						+answer[i].user["mail"]+'</td><td>'
						+answer[i].user["99"]+'</td><td>'
						+answer[i].user["ll"]+'</td><td>'
						+answer[i].message["time"]+'</td><td>'
						+answer[i].message["text"]+'</td>');							
					}
				}
				else
				{
					$('#activity_result').append('<tr><td>No activity</td></tr>');
				}
				$('#activity_result').append('</table>');*/
			}
		)			
			
	}
	else
	{
		alert('Fill all fields correctly!');
	}
}

