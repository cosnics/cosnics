<?php
require_once "src/phpfreechat.class.php"; // adjust to your own path

$params["serverid"] = md5(__FILE__); // used to identify the chat
$params["isadmin"] = true; // set wether the person is admin or not
$params["title"] = "Chamilo Chat"; // title of the chat
$params["nick"] = ""; // ask for nick at the user
$params["frozen_nick"] = true; // forbid the user to change his/her nickname later
$params["channels"] = array("Chamilo");
$params["max_channels"] = 1;
$params["theme"] = "blune";
$params["display_pfc_logo"] = false;
$params["display_ping"] = false;
$params["displaytabclosebutton"] = false;
$params["btn_sh_whosonline"] = false;
$params["btn_sh_smileys"] = false;
$chat = new phpFreeChat($params);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>phpFreeChat demo</title>
</head>
<body>
	<?php
$chat->printChat();
?>
</body>
</html>
