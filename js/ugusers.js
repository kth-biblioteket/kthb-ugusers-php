/**********

Funktion som anropar LDAP-api

**********/
function ajaxRequest(url, userinfotype) {
	$("#modaltext").hide();
	$("#modaltext").text('');
	$('#loadingmessage img').show();
	$('#modaltext').css("color","black");
	$.ajax({                                      
		url: url,                  
		data: $("#uguser").serialize() + '&searchuser=1&type=' + userinfotype,
		dataType: 'json',
		type: 'post',
		success: function(output){
				//$('#myModal').hide();
				$('#loadingmessage img').hide();
				$("#myModal").focus();

				if(userinfotype == 'ldap') {
					if(output.ugusers) {
						ugusersarr = []
						if (typeof output.ugusers.length === 'undefined') {
							ugusersarr.push(output.ugusers)
						} else {
							ugusersarr = output.ugusers
						}
						var html = ''
						//console.log(ugusersarr)
						for (x in ugusersarr) {
							//console.log(ugusersarr[x])
							if (typeof ugusersarr[x].ugKthid !== 'undefined') {
								buttonhtml = ugusersarr[x].displayName + '&nbsp<button onclick="getKthProfileByKthid(\'ugusers_aj.php\', \'' + ugusersarr[x].ugKthid + '\')" class="btn btn-success" type="button" aria-label="Profiledetails">KTH Profile</button>'
							} else {
								buttonhtml = ugusersarr[x].displayName;
							}
							html +=
							'<div style="padding-bottom: 10px">' + 
								'<table>' +
									'<tr>' +
										'<td style="font-size:20px" colspan="2">UG-information</td>' +
									'</tr>' +
									'<tr>' +
										'<td class="left">Namn</td><td>' + buttonhtml + '</td>' +
									'</tr>' +
									'<tr>' +
										'<td class="left">UG Username</td><td>' + ugusersarr[x].ugUsername + '</td>' +
									'</tr>' +
									'<tr>' +
										'<td class="left">KTH-id</td><td>' + ugusersarr[x].ugKthid + '</td>' +
									'</tr>' +
									'<tr>' +
										'<td class="left">Email</td><td>' + ugusersarr[x].mail + '</td>' +
									'</tr>' +
									'<tr>' +
										'<td class="left">Title</td><td>' + ugusersarr[x].title + '</td>' +
									'</tr>' +
									'<tr>' +
										'<td class="left">kth PA Group Membership</td><td>' + ugusersarr[x].kthPAGroupMembership + '</td>' +
									'</tr>' +
									'<tr>' +
										'<td class="left">Primary affiliation</td><td>' + ugusersarr[x].ugPrimaryAffiliation + '</td>' +
									'</tr>';
							if(ugusersarr[x].ugAffiliation) {
								html += 
									'<tr>' +
										'<td>Affiliations</td><td>'
										if(Array.isArray(ugusersarr[x].ugAffiliation)) {
											ugusersarr[x].ugAffiliation.forEach(element => {
												html +=  element + ', '
											});
										} else 
										{
											html +=  ugusersarr[x].ugAffiliation;
										}
									html += 
										'</td>' + 
									'</tr>';
							}
							html +=
								'</table>' +
							'</div>';
						
							
						}
						$("#uguserinfo").html(html);
					}
					else {
						if(output.result) {
							$("#uguserinfo").html(output.result);
						}
					}
					//ajaxRequest('ugusers_aj.php','kthprofile');
				} else {
					//console.log(output)
					var html = 
					'<table>' +
						'<tr>' +
							'<td style="font-size:20px" colspan="2">KTH-Profiles</td>' +
						'</tr>' +
						'<tr>' +
							'<td>Name</td><td>' + output.firstName + ' ' + output.lastName + '</td>' +
						'</tr>' +
						'<tr>' +
							'<td>Profile page</td><td><a target="_blank" href="https://www.kth.se/profile/' + output.username + '">' + 'https://www.kth.se/profile/' + output.username + '</a></td>' +
						'</tr>' +
						'<tr>' +
							'<td>KTH-id</td><td>' + output.kthId + '</td>' +
						'</tr>' +
						'<tr>' +
							'<td>Email</td><td>' + output.emailAddress + '</td>' +
						'</tr>' +
						'<tr>' +
							'<td>Title</td><td>' + output.title.sv + '</td>' +
						'</tr>'+
						'<tr>' +
							'<td>Created at</td><td>' + output.createdAt + '</td>' +
						'</tr>'+
						'<tr>' +
							'<td>Works for</td><td>' + output.worksFor.items[0].name + ', ' + output.worksFor.items[1].name +'</td>' +
						'</tr>'+
						'<tr>' +
							'<td>Organisation groups</td><td>' + output.worksFor.items[0].key + ', ' + output.worksFor.items[1].key +'</td>' +
						'</tr>'+
						'<tr>' +
							'<td>Orcid</td><td>' + output.researcher.orcid + '</td>' +
						'</tr>';
					html +=
					'</table>';
					$("#kthprofileinfo").html(html);
				}
				
				
				$('#skicka').show();
				$('#uguserinfo').show();
				$('#kthprofileinfo').show();
		},
		error: function(ajaxContext){
			$('#loadingmessage img').hide();
			$("#modaltext").html(ajaxContext.responseText + 
				"<input class=\"modalclose\" type='button' value=\"" + 
				modalclosbuttontext +
				"\" onclick=\"$('#myModal').hide();\"/></div>");
			$("#modaltext").show();
			$('#myModal').show();
		}
	});
};

/**********

Funktion som anropar LDAP-api

**********/
function getKthProfileByKthid(url, kthid) {
	$("#modaltext").hide();
	$("#modaltext").text('');
	//$('#loadingmessage img').show();
	$('#modaltext').css("color","black");
	$('#kthprofileinfo').hide();
	$.ajax({                                      
		url: url,
		data: '&searchuser=1&type=kthprofilebykthid&kthid=' + kthid,                  
		dataType: 'json',
		type: 'post',
		success: function(output) {
				//$('#myModal').hide();
				$('#loadingmessage img').hide();
				$("#myModal").focus();
					//console.log('profiles')
					var html = 
					'<table>' +
						'<tr>' +
							'<td style="font-size:20px" colspan="2">KTH-Profiles</td>' +
						'</tr>' +
						'<tr>' +
							'<td>Name</td><td>' + output.firstName + ' ' + output.lastName + '</td>' +
						'</tr>' +
						'<tr>' +
							'<td>Profile page</td><td><a target="_blank" href="https://www.kth.se/profile/' + output.username + '">' + 'https://www.kth.se/profile/' + output.username + '</a></td>' +
						'</tr>' +
						'<tr>' +
							'<td>KTH-id</td><td>' + output.kthId + '</td>' +
						'</tr>' +
						'<tr>' +
							'<td>Email</td><td>' + output.emailAddress + '</td>' +
						'</tr>' +
						'<tr>' +
							'<td>Title</td><td>' + output.title.sv + '</td>' +
						'</tr>';
						html += '<tr>' +
							'<td>Is staff</td><td>' + output.isStaff + '</td>' +
						'</tr>';
						html += '<tr>' +
							'<td>Created at</td><td>' + output.createdAt + '</td>' +
						'</tr>';
						if(typeof output.worksFor.items.length !== 'undefined' && output.worksFor.items.length > 0) {
							html += '<tr>' +
								'<td>Works for</td><td>' + output.worksFor.items[0].name + ', ' + output.worksFor.items[1].name +'</td>' +
							'</tr>'+
							'<tr>' +
								'<td>Organisation groups</td><td>' + output.worksFor.items[0].key + ', ' + output.worksFor.items[1].key +'</td>' +
							'</tr>';
						}
						if(output.researcher.orcid !== "") {
							html += '<tr>' +
								'<td>Orcid</td><td>' + output.researcher.orcid + '</td>' +
							'</tr>';
						}
						if(output.researcher.researcherId !== "") {
							html += '<tr>' +
								'<td>Resaercher ID</td><td>' + output.researcher.researcherId + '</td>' +
							'</tr>';
						}
						if(output.researcher.researchGate !== "") {
							html += '<tr>' +
								'<td>Research gate</td><td>' + output.researcher.researchGate + '</td>' +
							'</tr>';
						}
						if(output.researcher.scopusId !== "") {
							html += '<tr>' +
								'<td>Scopus ID</td><td>' + output.researcher.scopusId + '</td>' +
							'</tr>';
						}
						if(output.researcher.googleScholarId !== "") {
							html += '<tr>' +
								'<td>Google scholar ID</td><td>' + output.researcher.googleScholarId + '</td>' +
							'</tr>';
						}
					html +=
					'</table>';
					$("#kthprofileinfo").html(html);
				
				$('#skicka').show();
				$('#kthprofileinfo').show();
		},
		error: function(ajaxContext){
			$('#loadingmessage img').hide();
			$("#modaltext").html(ajaxContext.responseText + 
				"<input class=\"modalclose\" type='button' value=\"" + 
				modalclosbuttontext +
				"\" onclick=\"$('#myModal').hide();\"/></div>");
			$("#modaltext").show();
			$('#myModal').show();
		}
	});
};

/**********

Funktion som körs vid klick på "SÖK". Validerar formulärets fält och sedan anropar ajaxRequest-funktionen(anropet till LDAP-api) om allt är OK

**********/
function sendrequest() {
	$('#uguserinfo').html('');
	$('#kthprofileinfo').html('');
	$('#id').removeClass("error");
	$('#kthaccount').removeClass("error");
	if (uguser.kthaccount.value != "") {
	}
	var validerat = Validering.form();
	if (validerat == true) {
		//$('#myModal').hide();
		if ($('#iskth').val()=='true'){
			$('#almaid').val($('#id').val() + '');
		} else {
			$('#almaid').val($('#id').val())
		}
		ajaxRequest('ugusers_aj.php','ldap');
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

function clearfields() {
	$('#firstname').val("");
	$('#lastname').val("");
	$('#kthaccount').val("");
}

/**********

Funktion som körs när sidan laddas. 

Ser till att placeholder(den gråa infotexten som syns inne i ett fält innan man skriver nåt) fungerar för äldre browers.

Anpassningar till språk

**********/
var language, 
usernamekthplaceholder, 
usernameotherplaceholder, 
modalsendrequesttext, 
modalclosbuttontext, 
modalinvalidusernamecharacterstext, 
modalinvalidformtext;

$(document).ready(function() {
	if ($( "#language" ).val()=='sv') {
		language = 'sv';
	} else {
		language = 'en';
	}
	$('input, textarea').placeholder();
});