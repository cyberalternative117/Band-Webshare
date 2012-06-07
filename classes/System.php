<?php

class System
{
	//Base de données
	public static $db;
	public static $db2;
	public static $dbName = "";
	public static $db2Name = "";
	
	//Texte formaté
	public static $error = '<span style="color:red;">.: @lternative Framework Error :.</span> ';
	public static $title = "GeeKoEs ! - Webshare"; 
	public static $slogan= "";
	
	public static function init()
	{
		if(isset($_GET['PHPSESSID']))
		{
			$idSession = addslashes($_GET['PHPSESSID']);
			session_start($idSession);
			$_SESSION['PHPSESSID'] = $idSession;
		}
		else
		{
			session_start();
		}
		
		
		$_SESSION['fromURL'] = "band_webshare";	
		//Si la session n'est pas initialisée : demande LOGIN (SSO)
		if(!$_SESSION['loginOK'])
		{
			//$_SESSION['askedURL'] = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
			//header('Location: ./../login/index.php?PHPSESSID='.session_id().'');
			?>
			<script language="javascript" type="text/javascript">
			<!--
			window.location.replace("http://www.geekoes.com/login/index.php?PHPSESSID=<?php echo ''.session_id().''; ?>");
			-->
			</script>
            <?php
		}
		elseif($_SESSION['bwRights'] == 0)
		{
			//header('Location: ./../login/index.php?accessDenied=1');
			?>
            <script language="javascript" type="text/javascript">
			<!--
			window.location.replace("./../login/index.php?accessDenied=1");
			-->
			</script>
            <?php
		}
		
	}//FIN M(init)
	
	function addUrl_sid()
	{
		return "PHPSESSID=".$_SESSION['PHPSESSID'];
	}
	
	function phpBB_init()
	{/*
		global $phpbb_root_path, $phpEx, $user, $db, $config, $cache, $template, $auth;
		
		define('IN_PHPBB', true);
		define('IN_PHPBB', true);
		$phpbb_root_path = './../band_forum/';
		$phpEx = substr(strrchr(__FILE__, '.'), 1);
		//include($phpbb_root_path . 'common.' . $phpEx);
		include($phpbb_root_path . 'common.php');
		$_SESSION['G_phpBB_id'] = session_id();*/
	}
	
	
	public static function BDD_connect()
	{
		$db = mysql_connect('localhost', 'geeky48_mic', '');
		mysql_select_db('geeky48_BWebshare',$db);
		mysql_query("SET NAMES UTF8");
	}
	
	public static function BDD_close()
	{
		mysql_close();
	}
	
	public static function BDD_SSO_connect()
	{
		$db2 = mysql_connect('localhost', 'geeky48_mic', '');
		mysql_select_db('geeky48_base',$db2);
		mysql_query("SET NAMES UTF8");
	}
	
	public static function BDD_SSO_close()
	{
		mysql_close();
	}
		
	public static function path($path)
	{
		$result="";
		
		switch($path)
		{
			case 'upload':
				$result = "./upload/";
			break;
			
			case 'forms':
				$result = "./includes/forms/";
			break;
			
			case 'download':
				$result = "./";
			break;
		}
		
		return $result;
		
	}//Fin M(path)
	
	
	//Header principal
	public static function start_head()
	{
		?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
		<head>
		<title></title>
		<link rel="shortcut icon" href="favicon.ico" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="content-language" content="fr" />
		<link href="style.css"	title="Défaut" rel="stylesheet" type="text/css" media="screen" />
		
        <?php
	}
	
	public static function end_head()
	{
		echo '</head>';
	}
	
	public static function error($text)
	{
		echo '<div style="width:600px; margin:auto; text-align:center;" >';
			echo '<h3 style="color:red; "> .: @lternative Framework Error :. </h3>';
			echo '<span>Details :</span>';
			echo $text;
		echo '</div>';
	}//Fin M(error_forbidden)
	
	public static function error_forbidden($text)
	{
		echo '<div style="width:600px; margin:auto; text-align:center;" >';
			echo '<h3 style="color:red; ">.: @lternative Framework Security : Access Denied :.</h3>';
			echo '<span>Details :</span>';
			echo $text;
		echo '</div>';
	}//Fin M(error_forbidden)
	
	public static function error_sql($sqlError)
	{
		echo '<div style="width:600px; margin:auto; text-align:center;" >';
			echo '<h3 style="color:red; ">.: @lternative Framework Error : SQL error :.</h3>';
			echo '<span>Details :</span>';
			echo $sqlError;
		echo '</div>';
	}//Fin M(error_sql)
	
	public static function head_tinyMCE()
	{?>
		 <!-- TINY MCE -->
    	<script type="text/javascript" src="jscripts/tiny_mce/tiny_mce.js"></script>
		<script type="text/javascript">
			tinyMCE.init({				
				// General options				
				//mode : "textareas",	
				mode : "exact",
				elements : "elm1,elm2",
				theme : "advanced",				
				plugins : "style,media,paste",
// Theme options		 
				
				theme_advanced_buttons1 : "bold,italic,underline,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,undo,redo,|,link,unlink,image,|,forecolor",
theme_advanced_buttons2 : "",
theme_advanced_buttons3 : "",
paste_auto_cleanup_on_paste : true,
theme_advanced_toolbar_location : "top",			
				theme_advanced_toolbar_align : "left",				
				theme_advanced_statusbar_location : "bottom",				
				theme_advanced_resizing : true,			 
				
				// Example content CSS (should be your site CSS)				
				content_css : "./styleMCE.css",
				
				
				// Style formats
				
				style_formats : [
				
				{title : 'Gras', inline : 'b'},				
				{title : 'Rouge', inline : 'span', styles : {color : '#ff0000'}},				
				{title : 'Titre rouge', block : 'h1', styles : {color : '#ff0000'}},		
				{title : 'Tableaux'},				
				{title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}			
				],
								 
				
				formats : {				
				alignleft : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'left'},				
				aligncenter : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'center'},				
				alignright : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'right'},				
				alignfull : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'full'},				
				bold : {inline : 'span', 'classes' : 'bold'},				
				italic : {inline : 'span', 'classes' : 'italic'},				
				underline : {inline : 'span', 'classes' : 'underline', exact : true},				
				strikethrough : {inline : 'del'},				
				customformat : {inline : 'span', styles : {color : '#00ff00', fontSize : '20px'}, attributes : {title : 'My custom format'}}				
				}				
				});
		</script>
   		 <!-- Fin Tiny MCE -->
         <?php
	}//Fin M(head_TinyMCE)
	
	function current_timestamp()
	{
		return time();
	}
	
	
	
		
}//End C(System)
?>