<?php
require('config.php'); //innehåller API-KEY + error reporting. Byt från Sandbox vid prodsättning

/********** 

Funktion som kollar via LDAP om användaren är behörig

**********/
function checkAuth($kth_id) {
	global $apikey_ldap;
	$ch = curl_init();
	$url = 'https://lib.kth.se/ldap/api/v1/kthid/' . $kth_id;
	$queryParams = '?token=' . $apikey_ldap;
	curl_setopt($ch, CURLOPT_URL, $url . $queryParams);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	$response = curl_exec($ch);
	curl_close($ch);
	$respsonseobj = json_decode($response, true);
	foreach($respsonseobj['ugusers'][0]['memberOf'] as $ug_group)
	{
		if (strpos($ug_group,'app.katalog3.T.TR,OU') != false ) {
			return true;
		}
	}
	return false;
}

session_start();
if (!isset($_SESSION['token']))
{
	$_SESSION['token'] = md5(uniqid(rand(), TRUE));
	$_SESSION['token_time'] = time();
}

if (!empty($_GET["language"])) {
			$language = $_GET["language"];
		} else {
			$language = "en";
		}
?>


<!DOCTYPE html>
<html>
	<head>
		<?php if ($language == "sv") { ?>
		<title>Sök KTH-användare</title>
		<?php } else { ?>
		<title>Search KTH-users</title>
		<?php } ?>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
		<!-- KTH Styles -->
		<!--link href="<?php echo $kth_www?>/css/kth-22cebcf8c708b27ad9c875474470c18b.css" rel="stylesheet"-->
    <link type="text/css" href="/kthstyle/kth.css" rel="stylesheet" />
		<link type="text/css" href="css/ugusers.css?version=2" rel="stylesheet" />
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
		<script type="text/javascript" src="js/jquery.validate.min.js"></script>
		<script type="text/javascript" src="js/jquery.placeholder.js"></script>
		<script type="text/javascript" src="js/ugusers.js?version=2.2"></script>
	</head>
	<body>
<?php
	if (!isset($_SESSION)) {
		session_start();
	}
	//logout
	if (isset($_REQUEST['logout'])) {
		session_unset();
		session_destroy();
		header("location: https://login.kth.se/logout");
		exit;
	}

	if(!isset($_SESSION['kth_id'])) { //logga in via KTH-CAS
		header("location: login.php?language=" . $language) ;
	}

	if(isset($_SESSION['kth_id'])) {
		//Check if authorized for the app (belongs to "app.katalog3.T.TR" KTH BIBLIOTEKET)
		$isauth = checkAuth($_SESSION['kth_id']);
		if ($isauth) {

?>	
		<?php 
			if ($language == "sv") {
				$formiamlable = 'Inloggad som&nbsp';
				$kthaccounttext = 'KTH-konto</i>';
				$kthidtext = 'KTH-id</i>';
				$searchbuttontext = 'Sök';
				$ingress = 'Searching for a KTH-id or KTH-account takes precedence. There is an automatic truncation in the first and last name fields, so it is possible to enter only one or a few letters in each field. By clicking on the green "KTH Profile" button, more information is obtained from KTH\'s profile pages, e.g. where the person works and possibly ORCiD or other author-IDs.';
			} else {
				$formiamlable = 'Logged in as&nbsp';
				$kthaccounttext = 'KTH-account</i>';
				$kthidtext = 'KTH-id</i>';
				$searchbuttontext = 'Search';
				$ingress = 'Searching for a KTH-id eller KTH-account takes precedence. There is an automatic truncation in the first and last name fields, so it is possible to enter only one or a few letters in each field. By clicking on the green "KTH Profile" button, more information is obtained from KTH\'s profile pages, e.g. where the person works and possibly ORCiD or other author-IDs.';
			}
	?>
	<div class="content">
		<header>
		<div class="container-fluid">
			<div class="container">      
					<div class="header-container__top">
						<figure class="block figure defaultTheme mainLogo" data-cid="1.77257" lang="sv-SE">
							<a href="<?php echo $kth_www?>/"><img class="figure-img img-fluid" src="/polopoly/KTH_Logotyp.svg" alt="KTH:s logotyp" height="70" width="70"></a>
						</figure>
						<h1 class="block siteName" data-cid="1.260060">
							<?php if ($language == "sv") { ?>
								<a data-cid="1.260060" href="<?php echo $kth_www?>/biblioteket">KTH Biblioteket</a>
							<?php } else {?>
								<a data-cid="1.260060" href="<?php echo $kth_www?>/en/biblioteket">KTH Library</a>
							<?php }?>
						</h1>
						<div class="block list links secondaryMenu" data-cid="1.864801" lang="sv-SE">
							<ul>
								<?php if ($language == "sv") { ?>
								<li><a href="index.php?language=en" hreflang="en-UK">English</a>
								</li>
								<?php } else {?>
								<li><a href="index.php?language=sv" hreflang="en-UK">Svenska</a>
								</li>
								<?php }?>
							</ul>
						</div>
					</div>
					<div class="header-container__bottom">
						<nav style="height: 53px;" class="block megaMenu navbar navbar-expand-lg navbar-light" data-cid="1.855134" lang="sv-SE">
							<span id="propertiesMegaMenu"></span>
							<div class="collapse navbar-collapse" id="megaMenuContent">
								<ul class="menu navbar-nav mr-auto" id="megaMenu">
									<!--  size-${policy.size} -->
									<li class="item nav-item megaItem homeItem" data-content-id="1.863181" data-id="menu-group-1-855134-27000830">
										<div class="headerItem true showLabel">
											<?php if ($language == "sv") { ?>
												<a class="nav-link null true" href="<?php echo $kth_www?>/"> Hem</a>
											<?php } else {?>
												<a class="nav-link null true" href="<?php echo $kth_www?>/en"> Home</a>
											<?php }?>
										</div>
										<div class="menuItemContent" id="dropdownmenu-group-1-855134-27000830">
										<div class="megaMenuBody">
											<button class="closeButton" type="button" aria-label="Stäng"></button>
											<div class="megaMenuBodyInner">
											<div id="dropdown-placeholdermenu-group-1-855134-27000830"></div>
											</div>
										</div>
										</div>
									</li>
									<li class="item nav-item megaItem" data-content-id="1.202243" data-id="menu-group-1-855134-418323064">
										<div class="headerItem false">
										<?php if ($language == "sv") { ?>
											<a class="nav-link null true" href="<?php echo $kth_www?>/utbildning"> Utbildning</a>
										<?php } else {?>
											<a class="nav-link null true" href="<?php echo $kth_www?>/en/studies"> Studies</a>
										<?php }?>
										</div>
										<div class="menuItemContent" id="dropdownmenu-group-1-855134-418323064">
										<div class="megaMenuBody">
											<button class="closeButton" type="button" aria-label="Stäng"></button>
											<div class="megaMenuBodyInner">
											<div id="dropdown-placeholdermenu-group-1-855134-418323064"></div>
											</div>
										</div>
										</div>
									</li>
									<li class="item nav-item megaItem" data-content-id="1.202244" data-id="menu-group-1-855134-62723924">
										<div class="headerItem false">
										<?php if ($language == "sv") { ?>
											<a class="nav-link null true" href="<?php echo $kth_www?>/forskning"> Forskning</a>
										<?php } else {?>
											<a class="nav-link null true" href="<?php echo $kth_www?>/en/forskning"> Research</a>
										<?php }?>
										</div>
										<div class="menuItemContent" id="dropdownmenu-group-1-855134-62723924">
										<div class="megaMenuBody">
											<button class="closeButton" type="button" aria-label="Stäng"></button>
											<div class="megaMenuBodyInner">
											<div id="dropdown-placeholdermenu-group-1-855134-62723924"></div>
											</div>
										</div>
										</div>
									</li>
									<li class="item nav-item megaItem" data-content-id="1.202245" data-id="menu-group-1-855134-210762362">
										<div class="headerItem false">
										<?php if ($language == "sv") { ?>
											<a class="nav-link null true" href="<?php echo $kth_www?>/samverkan"> Samverkan</a>
										<?php } else {?>
											<a class="nav-link null true" href="<?php echo $kth_www?>/en/samverkan"> Co-operation</a>
										<?php }?>
										</div>
									</li>
									<li class="item nav-item megaItem" data-content-id="1.863186" data-id="menu-group-1-855134-1026005456">
										<div class="headerItem false">
										<?php if ($language == "sv") { ?>
											<a class="nav-link null true" href="<?php echo $kth_www?>/om"> Om KTH</a>
										<?php } else {?>
											<a class="nav-link null true" href="<?php echo $kth_www?>/en/om"> About KTH</a>
										<?php }?>
										</div>
										<div class="menuItemContent" id="dropdownmenu-group-1-855134-1026005456">
										<div class="megaMenuBody">
											<button class="closeButton" type="button" aria-label="Stäng"></button>
											<div class="megaMenuBodyInner">
											<div id="dropdown-placeholdermenu-group-1-855134-1026005456"></div>
											</div>
										</div>
										</div>
									</li>
									<li class="item nav-item megaItem" data-content-id="1.853601" data-id="menu-group-1-855134-315160002">
										<div class="headerItem true">
										<?php if ($language == "sv") { ?>
											<a class="nav-link null true" href="<?php echo $kth_www?>/biblioteket"> Biblioteket</a>
										<?php } else {?>
											<a class="nav-link null true" href="<?php echo $kth_www?>/en/biblioteket"> Library</a>
										<?php }?>
										</div>
										<div class="menuItemContent" id="dropdownmenu-group-1-855134-315160002">
										<div class="megaMenuBody">
											<button class="closeButton" type="button" aria-label="Stäng"></button>
											<div class="megaMenuBodyInner">
											<div id="dropdown-placeholdermenu-group-1-855134-315160002"></div>
											</div>
										</div>
										</div>
									</li>
								</ul>
							</div>
						</nav>
					</div>
				</div>
			</div>
		<div id="gradientBorder"></div>
		</header>
		<div class="container start noMainMenu">
			<div class="row">
				<div class="col">
					<article class="article block teaser">
						<div id="" class="">
							<div>
								<?php 
								if ($language == "sv") {
								?>
								<div class="preArticleParagraphs">
									<h1>Sök KTH-användare</h1>
									<div class="teaserBody">
										<div class="lead ">
											<p><?php echo $ingress?></p>
										</div>
									</div>
								</div>
								<?php 
								} else {
								?>
								<div class="preArticleParagraphs">
									<h1>Search KTH-users</h1>
									<div class="teaserBody">
										<div class="lead ">
											<p><?php echo $ingress?></p>
										</div>
									</div>
								</div>
								<?php 
								}
								?>
							</div>
							<form onsubmit="return sendrequest();" method="post" action="javascript:;" name="uguser" id="uguser">
								<input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>" />
								<input id="language" name="language" type="hidden" value="<?php echo ($language)?>">
								<input id="id" name="id" type="hidden" value="<?php echo $_SESSION['kth_id'] ?>" class="required" placeholder="">
								<input id="almaid" name="almaid" type="hidden" value="">
								<input id="isactivepatron" name="isactivepatron" type="hidden" value="">
								<div id="searchfields">
									<label style="" class="bib_label" for="kthaccount">
										<?php echo $kthaccounttext;?>
									</label>
									<div>
										<input class="" type="text" name="kthaccount" id="kthaccount">
									</div>
									<label style="" class="bib_label" for="kthid">
										<?php echo $kthidtext;?>
									</label>
									<div>
										<input class="" type="text" name="kthid" id="kthid">
									</div>
									<label style="" class="bib_label" for="firstname">
										<?php echo 'First name';?>
									</label>
									<div>
										<input class="" type="text" name="firstname" id="firstname">
									</div>
									<label style="" class="bib_label" for="lastname">
										<?php echo 'Last name';?>
									</label>
									<div>
										<input class="" type="text" name="lastname" id="lastname">
									</div>
								</div>
								<div style="padding-top:10px">
									<input id="skicka" name="skicka" type="submit" value="<?php echo $searchbuttontext ?>" >
									<button onclick="clearfields()" class="btn" type="button" aria-label="clear">Clear</button>
								</div>
								
							</form>
							<div id="myModal" class="" tabindex="-1">
								<div class="">
									<div id="loadingmessage">
										<img src="images/ajax_loader_blue_512.gif"/>
										<div id="modaltext" class="alert alert-danger"></div>
									</div>
								</div>
							</div>
							<div style="display:flex">
								<div style="flex:1;padding:10px 10px 0px 0px" id="uguserinfo" class="" tabindex="-1">
								</div>
								<div style="flex:1;padding:10px 10px 0px 0px" id="kthprofileinfo" class="" tabindex="-1">
								</div>
							</div>
						</div>
					</article>
				</div>
			</div>
		</div>
	</div>
	<?php
		}
		else {
			?>
			<div>Not authorized for this app.</div>
			<?php
		}

	}
?>
	</body>
<style>
</style>
</html>
