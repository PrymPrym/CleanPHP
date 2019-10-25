<?php
session_start();
date_default_timezone_set('Europe/Moscow');
$db['user']='root';
$db['pass']='12345';
$db['name']='UrlBaseNew';
$db['host']='localhost';

function SaveUser($db)
{
	$mysqli = new mysqli($db['host'], $db['user'], $db['pass'], $db['name']);
	if ($mysqli->connect_errno) {
		echo "Не удалось подключиться к MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	$login=$_GET['login'];
	$pass=$_GET['password'];
	$mail=$_GET['mail'];
	$_GET=[];
	$login = $mysqli->real_escape_string($login);
	$pass = $mysqli->real_escape_string($pass);
	$mail = $mysqli->real_escape_string($mail);
	$sql_str="INSERT into User (id,login,pass,mail) VALUES (DEFAULT,('$login'),('$pass'),('$mail'))";
	$res = $mysqli->query($sql_str);
	if ( $res === false ) {
		echo "Something went wrong, handle it";
	  }
	$mysqli->close(); 
}

function TestUser($db)
{
	$testData=[];
	$mysqli = new mysqli($db['host'], $db['user'], $db['pass'], $db['name']);
	if ($mysqli->connect_errno) {
		echo "Не удалось подключиться к MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	$login=$_GET['login'];
	$pass=$_GET['pass'];
	$_GET=[];
	$login = $mysqli->real_escape_string($login);
	$pass = $mysqli->real_escape_string($pass);
	$sql_str="SELECT * FROM User  WHERE pass=('$pass') AND login=('$login')";
	$res = $mysqli->query($sql_str);
	if ( $res === false ) {
	echo "Something went wrong, handle it";
	}
	$item=mysqli_fetch_assoc($res);
	$t=mysqli_num_rows($res);
	if ($t==0)
		{
			$testData[0]=false;
			}
	else
		{
			$testData[0]=true;
		
		}		
	
	if ( $res === false ) {
		echo "Something went wrong, handle it";
	  }
	$mysqli->close(); 
	return $testData;	
}

function CreateDBforUrl($db)
{
	
	$mysqli = new mysqli($db['host'], $db['user'], $db['pass'], $db['name']);
	if ($mysqli->connect_errno) {
		echo "Не удалось подключиться к MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	$sql_strOne="TRUNCATE OldUrl";
	$res = $mysqli->query($sql_strOne);
	if ( $res === false ) {
	echo "Something went wrong, handle it";
	}
	$sql_strOne="TRUNCATE NewUrl";
	$res = $mysqli->query($sql_strOne);
	if ( $res === false ) {
	echo "Something went wrong, handle it";
	}
	$CharArr=['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
	$hash = "";
	for ($i=0;$i<26;$i++)
		for ($j=0;$j<26;$j++)
			{	
				$hash=$CharArr[$i].$CharArr[$j];
				$hash=$mysqli->real_escape_string($hash);
				$sql_str="INSERT into OldUrl (id,hash) VALUES (DEFAULT,('$hash'))";
				$res = $mysqli->query($sql_str);		
			}
	if ( $res === false ) {
		echo "Something went wrong, handle it";
	  }
	$mysqli->close(); 
}

function GetUrlTable($db)
{
	$mysqli = new mysqli($db['host'], $db['user'], $db['pass'], $db['name']);;
	if ($mysqli->connect_errno) {
		echo "Не удалось подключиться к MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	$_GET=[];
	$sql_str="SELECT * FROM NewUrl";
	$result = $mysqli->query($sql_str);
	while ($row = $result->fetch_assoc()) {
		$Urls[]=$row;
    }
    $result->free();
	$mysqli->close(); 
	return $Urls;
}
function HandleLongUrl($db)
{
	$mysqli = new mysqli($db['host'], $db['user'], $db['pass'], $db['name']);
	if ($mysqli->connect_errno) {
		echo "Не удалось подключиться к MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	$sql_str="SELECT * FROM OldUrl";
	$result = $mysqli->query($sql_str);
	while ($row = $result->fetch_assoc()) {
		$Urls[]=$row;
    }
    $UrlCount=count($Urls);
    $i=rand(0,$UrlCount);
    $longurl=$mysqli->real_escape_string($_GET['BigUrl']);
    $hashurl=$mysqli->real_escape_string($Urls[$i]['hash']);
    $sql_strOne="INSERT into NewUrl (id,longurl,shorturl) VALUES (DEFAULT,('$longurl'),('$hashurl'))";
    $res = $mysqli->query($sql_strOne);
	if ( $res === false ) {
		echo "Something went wrong, handle it";
	  }
	$id=$mysqli->real_escape_string($Urls[$i]['id']);  
    $sql_strOne="DELETE FROM OldUrl WHere id=('$id')";
    $res = $mysqli->query($sql_strOne);
	if ( $res === false ) {
		echo "Something went wrong, handle it";
	  }
	$mysqli->close(); 
	return $hashurl;
}

function DeleteUrlFromDB($db,$id,$hashurl)
{
	$mysqli = new mysqli($db['host'], $db['user'], $db['pass'], $db['name']);
	if ($mysqli->connect_errno) {
		echo "Не удалось подключиться к MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	$id=$mysqli->real_escape_string($id);  
    $sql_strOne="DELETE FROM NewUrl WHere id=('$id')";
    $res = $mysqli->query($sql_strOne);
	if ( $res === false ) {
		echo "Something went wrong, handle it";
	  }
    $hashurl=$mysqli->real_escape_string($hashurl);
    $sql_strOne="INSERT into OldUrl (id,hash) VALUES (DEFAULT,('$hashurl'))";
    $res = $mysqli->query($sql_strOne);
	if ( $res === false ) {
		echo "Something went wrong, handle it";
	  }
	$mysqli->close(); 
	return true;
}
$ArrGetA=['makebase','delete'];
$ArrGetB=['adminpanel','makebase','delete'];
$ArrGet=['mainpage','loginpanel','register','sendurl','sendregdata'];	
//handle_redirect
if  (strlen($_SERVER['PATH_INFO'])<4)
	{
	 $Path=explode("/",$_SERVER['PATH_INFO']);
	 $HashItem=$Path[1];
	 $mysqli = new mysqli($db['host'], $db['user'], $db['pass'], $db['name']);
	 $HashItem=$mysqli->real_escape_string($HashItem);
	 $sql_str="SELECT * FROM NewUrl where shorturl=('$HashItem')";
	 $res = $mysqli->query($sql_str);
	 if ( $res === false ) {
		echo "Something went wrong, handle it";
	  }
	 if (mysqli_num_rows($res)==1)
		{
			$row = $res->fetch_assoc();
			header('Location: '.$row['longurl']);
		}
	}
	
//data_reset		
if 	($_GET['action']=="logout")
	{
		$_GET['action']="mainpage";	
		$_SESSION['UserRole']='guest';
	}	
		
//data_none_set	
if (!isset($_SESSION['UserRole']))
	{
	$_SESSION['UserRole']='guest';
	$_GET['action']="mainpage";	
	}
if 	(!isset($_GET['action']))
	$_GET['action']='mainpage';	
	
//data set		
if ($_SESSION['UserRole']=='admin')
	{
	if (in_array($_GET['action'], $ArrGetA)==false)
		$_GET['action']='adminpanel';
	}	
if ($_SESSION['UserRole']=='guest')
	{
	if ($_GET['action']!='adminpanel')	
		{
			if (in_array($_GET['action'], $ArrGetA))
				$_GET['action']='mainpage';
			if (in_array($_GET['action'], $ArrGet)==false)
				$_GET['action']='mainpage';
		}
	}	
	
//handle_routes	for admin
if 	($_GET['action']=="makebase")
	{
		CreateDBforUrl($db);
		$_GET['action']="adminpanel";	
	}	
if 	($_GET['action']=="delete")
	{
		DeleteUrlFromDB($db,$_GET['index'],$_GET['hash']);
		$_GET=[];
		$_GET['action']="adminpanel";		
	}	
if 	($_GET['action']=="adminpanel")
	{
		$t=[];
		if ($_SESSION['UserRole']=='admin') 
				{
				$s=1500;
				$t[0]=true;	
				}
			else
				{
				$t=TestUser($db);	
				}
		if ($t[0]==true)
		 {
			$_SESSION['UserRole']='admin'; 
			$p="<td>&nbsp&nbsp&nbsp</td>"; 
			echo "<table style='border:2px solid green'><tr><td><form method='' action='/'><label>Удаление всех текущих данных и создание базы данных хеш</label></td></tr>";
			echo "<input type='hidden' name='action' value='makebase' >";
			echo "<tr><td><button type='submit'>Выполнить действие</button></form></td><td></td></tr></table>";
			echo "<br><br><br><table style='border:2px solid green'><tr><td><form method='' action='/'><input type='hidden' name='action' value='logout' ><label>Переход на главную страницу</label></td><td><button type='submit'>LogOut</button></form></td></tr>";
			echo "<tr><td></td><td></form><span>&nbsp&nbsp&nbsp&nbsp&nbsp</span></td></tr></table>";
			echo "<br><br><br><table style='border:2px solid green'><tr>Список работающих урлов</tr>";
			echo "<tr><td>Индекс</td>".$p."<td>Длинный Url</td>".$p."<td>Короткий Url</td>".$p."<td>Удалить</td></tr>";	
			$UserTable=GetUrlTable($db);
			$m=count($UserTable);
			for ($i=0;$i<$m;$i++)
				echo "<tr><td><form method='' action='/'><input type='hidden' name='action' value='delete'><input type='hidden' name='hash' value='".$UserTable[$i]['shorturl']."'><input type='hidden' name='index' value='".$UserTable[$i]['id']."'>".$i."</td>".$p."<td>".$UserTable[$i]['longurl']."</td>".$p."<td>".$UserTable[$i]['shorturl']."</td>".$p."<td><button type='submit'>Удалить Url из БД</button></form></td></tr>";
			echo "</table>";
		}
		else
		{
			echo "<p>Логин и Пароль не верны</p>";
			$_GET['action']="mainpage";
		}
		
	}	
//handle_routes	for user
	
if 	($_GET['action']=="mainpage")
	{
	if ($_SESSION['UserRole']=='admin')	
	echo "<div style='float:right'><form method='POST'><input type='hidden' name='register' value='doit' ><button type='submit'>Logout</button></form><span>&nbsp&nbsp&nbsp&nbsp&nbsp</span></div>";
	if ($_SESSION['UserRole']=='guest')	
	echo "<div style='float:right'><form><input type='hidden' name='action' value='loginpanel' ><button type='submit'>Login</button></form><span>&nbsp&nbsp&nbsp&nbsp&nbsp</span><form><input type='hidden' name='action' value='register' ><button type='submit'>Register</button></form></div><br>";
	echo "<div style='width:100%,margin:auto'><table><tr><td><form><input type='hidden' name='action' value='sendurl'><label>Введите свой урл</label></td></tr>";
	echo "<tr><td><input type='text' name='BigUrl' value='' required ><button type='submit'>Обработать</button></td></tr></table></div>";
	}
if 	($_GET['action']=="loginpanel")
	{
	echo "<table><tr><td><form method='' action='/'><label>Вход в панель администратора</label></td></tr>";
	echo "<input type='hidden' name='action' value='adminpanel' >";
	echo "<tr><td><label>Логин</label></td><td><input type='text' name='login' value='' required></td>";
	echo "<tr><td><label>Пароль</label></td><td><input type='text' name='pass' value='' required></td>";
	echo "<tr><td></td></td><td><button type='submit'>Войти</button></form></td></tr></table>";
	//
	echo "<br><br><br><table><tr><td><form method='' action='/'><label>Переход на главную страницу</label></td><td><button type='submit'>Главная страница</button></td></tr>";
	echo "<tr><td><input type='hidden' name='action' value='mainpage' ></td><td></form><span>&nbsp&nbsp&nbsp&nbsp&nbsp</span></td></tr></table>";
	}		
if 	($_GET['action']=="register")
{
	echo "<table><tr><td><form method='' action='/'><label>Введите Ваш Логин</label></td><td><input type='text' name='login' value='' required ></td></tr>";
	echo "<tr><td><label>Введите Ваш пароль</label></td><td><input type='text' name='password' value='' required ></td></tr>";
	echo "<tr><td><label>Введите Ваш адрес электронной почты</label></td><td><input type='text' name='mail' value='' required ></td></tr>";
	echo "<input type='hidden' name='action' value='sendregdata' >";
	echo "<tr><td></td><td><button type='submit'>Register</button></form><span>&nbsp&nbsp&nbsp&nbsp&nbsp</span></td></tr></table>";	
	}

if 	($_GET['action']=="sendurl")
	{
	$ShortUrl=HandleLongUrl($db);
	echo "<div style='float:right'><form><input type='hidden' name='action' value='loginpanel' ><button type='submit'>Login</button></form><span>&nbsp&nbsp&nbsp&nbsp&nbsp</span><form><input type='hidden' name='action' value='register' ><button type='submit'>Register</button></form></div><br>";
	echo "<div style='width:100%,margin:auto'><form><input type='hidden' name='action' value='mainpage' ><label>Ваша короткая ссылка</label><input type='text' name='BigUrl' value='".$ShortUrl."' required ><button type='submit'>Перейти на главную</button></div>";
	}	
if 	($_GET['action']=="sendregdata")
	{
	SaveUser($db);	
	echo "<p>Успешная регистрация</p>";
	echo "<table><tr><td><form method='' action='/'><label>Вход в панель администратора</label></td></tr>";
	echo "<input type='hidden' name='action' value='adminpanel' >";
	echo "<tr><td><label>Логин</label></td><td><input type='text' name='login' value='' required ></td>";
	echo "<tr><td><label>Пароль</label></td><td><input type='text' name='pass' value='' required ></td>";
	echo "<tr><td></td></td><td><button type='submit'>Войти</button></form></td></tr></table>";
	//
	echo "<br><br><br><table><tr><td><form method='' action='/'><label>Переход на главную страницу</label></td><td><button type='submit'>Главная страница</button></td></tr>";
	echo "<tr><td><input type='hidden' name='action' value='mainpage' ></td><td></form><span>&nbsp&nbsp&nbsp&nbsp&nbsp</span></td></tr></table>";
	}

?>
