/**********

Funktion som anropar LDAP-api

**********/
function ajaxRequest(url) {
	$("#modaltext").text(modalactivatetext);
	$('#loadingmessage img').show();
	$('#modaltext').css("color","black");
	$.ajax({                                      
		url: url,                  
		data: $("#uguser").serialize() + '&searchuser=1',
		dataType: 'json',
		type: 'post',
		success: function(output){
			if(output.ugusers) {
				$('#myModal').hide();
				$('#loadingmessage img').hide();
				$("#myModal").focus();
				var html = 
				'<table>' +
					'<tr>' +
						'<td>Namn</td><td>' + output.ugusers.displayName + '</td>' +
					'</tr>' +
					'<tr>' +
						'<td>KTH-id</td><td>' + output.ugusers.ugKthid + '</td>' +
					'</tr>' +
					'<tr>' +
						'<td>Email</td><td>' + output.ugusers.mail + '</td>' +
					'</tr>' +
					'<tr>' +
						'<td>Title</td><td>' + output.ugusers.title + '</td>' +
					'</tr>' +
					'<tr>' +
						'<td>kthPAGroupMembership</td><td>' + output.ugusers.kthPAGroupMembership + '</td>' +
					'</tr>' +
					'<tr>' +
						'<td>Primary affiliation</td><td>' + output.ugusers.ugPrimaryAffiliation + '</td>' +
					'</tr>';
				if(output.ugusers.ugAffiliation) {
					html += 
					'<tr>' +
						'<td>Affiliations</td><td>'
					output.ugusers.ugAffiliation.forEach(element => {
						html +=  element + ', '
					});
					html += 
						'</td>' + 
					'</tr>';
				}
				html +=
				'</table>';
				
				$("#uguserinfo").html(html);
				$('#skicka').show();
				$('#uguser').show();
			}
			else {
				if(output.result) {
					$("#uguserinfo").html(output.result);
				}
			}
		},
		error: function(ajaxContext){
			$('#loadingmessage img').hide();
			$("#modaltext").html(ajaxContext.responseText + 
				"<input class=\"modalclose\" type='button' value=\"" + 
				modalclosbuttontext +
				"\" onclick=\"$('#myModal').hide();\"/></div>");
			$('#myModal').show();
		}
	});
};

/**********

Funktion som loggar ut användare

**********/
function logoutfromsaml() {
	$.ajax({                                      
			url: 'logout.php',
			dataType: 'json',
			type: 'post',
			success: function(output){
				window.location.href = "./";
			},
			error: function(ajaxContext){
				$('#loadingmessage img').hide();
				$("#modaltext").html(ajaxContext.responseText + 
					"<input class=\"modalclose\" type='button' value=\"" + 
					modalclosbuttontext +
					"\" onclick=\"$('#myModal').hide();\"/></div>");
				$('#myModal').show();
			}
		});
}

/**********

Funktion som körs vid klick på "SÖK". Validerar formulärets fält och sedan anropar ajaxRequest-funktionen(anropet till LDAP-api) om allt är OK

**********/
function sendrequest() {
	$('#id').removeClass("error");
	$('#kthaccount').removeClass("error");
	if (uguser.kthaccount.value != "") {
	}
	var validerat = Validering.form();
	if (validerat == true) {
		$('#myModal').hide();
		if ($('#iskth').val()=='true'){
			$('#almaid').val($('#id').val() + '');
		} else {
			$('#almaid').val($('#id').val())
		}
		ajaxRequest('ugusers_aj.php');
	}
}

/**********

Funktion som skapar validering för formuläret (använder jquery.validate.min.js) 

**********/
var Validering
$(document).ready(function() {
	Validering = $("#uguser").validate({
		invalidHandler: function(form, validator) {
			var errors = validator.numberOfInvalids();
			if (errors) {
				$('#loadingmessage img').hide();
				$('.modal-content').css("border","1px solid red");
				$('#modaltext').css("color","red");
				$("#modaltext").html(modalinvalidformtext);
				$('#myModal').show();
			} else {
				$('#loadingmessage img').hide();
				$('.modal-content').css("border","none");
				$('#modaltext').css("color","black");
				$("#modaltext").hide();
			}
		}
	});
});

/**********

Funktion som via en regex hanterar tillåtna tecken i användarnamn-fältet 

**********/
function isValidUserId(UserId) {
	var pattern = /^[a-zA-Z0-9@._-]+$/;
	return pattern.test(UserId);
};

/**********

Funktion som skickar användaren till logoutsidan

**********/
function logout() {
	window.location.href = "logout.php";
}		

/**********

Funktion som körs när sidan laddas. 

Ser till att placeholder(den gråa infotexten som syns inne i ett fält innan man skriver nåt) fungerar för äldre browers.

Anpassningar till språk

**********/
var language, usernamekthplaceholder, usernameotherplaceholder, modalsendrequesttext, modalclosbuttontext, modalinvalidusernamecharacterstext, modalinvalidformtext;
$(document).ready(function() {
	if ($( "#language" ).val()=='swedish') {
		language = 'swedish';
	} else {
		language = 'english';
	}
	$('input, textarea').placeholder();
});