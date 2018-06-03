<?php
@session_start();

date_default_timezone_set('UTC');
 
require_once('general.php');

if (isset($_GET['logout'])) {
	@session_start();
	session_destroy();
	header("Location: index.php");
	return;
}

 //invalid login attempts - kick out!
if (isset($_SESSION['invalid_login']) && $_SESSION['invalid_login'] > 3)
	exit;
    		
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password_string = md5($_POST['upassword']); //convert plain text to md5
  
		$db = new dbase();
		$db->connect_sqlite();
 
    //get the dbase password for this mail
    $r = $db->getRow('select user_id,user_level from users where user_mail=? and user_password=?',array($_POST['umail'], $password_string));
 
    //^if record exists
    if ($r){
            $_SESSION['id'] = $r['user_id'];
            $_SESSION['level'] = $r['user_level'];
            $_SESSION['login_expiration'] = date('Y-m-d');
            
            header('Location: index.php');
    }
    else {
    	if (isset($_SESSION['invalid_login']))
    		$_SESSION['invalid_login']+=1;
    	else 
				$_SESSION['invalid_login']=1;
				
				$r = $db->getScalar('select count(*) from users', null);

				if ($r == 0) { //delete this, once you create the admin

						//user doesnt exist - create new
						$sql = 'INSERT INTO users (user_mail, user_password, user_level) VALUES (:user_mail, :user_password, :user_level)';
						$stmt = $db->getConnection()->prepare($sql);

						$stmt->bindValue(':user_mail' , $_POST['umail']);
						$stmt->bindValue(':user_password' , $password_string);
						$stmt->bindValue(':user_level' , 1);

						$stmt->execute();

						$res = $stmt->rowCount();

						if($res == 1)
								echo 'User created successfully!';
						else
								echo 'error';
				}
						
    }
} 

//auto go to portal when loggedin
if (isset($_SESSION['id'])) {
	
	if ($_SESSION["login_expiration"] == date('Y-m-d'))
	{	
		header('Location: index.php');
		exit;
	} else {
		session_destroy();
	}
}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
		
		<title>Login</title>
		
		<link href="assets/bootstrap.min.css" rel="stylesheet">

		<style>
			body {
			  padding-top: 40px;
			  padding-bottom: 40px;
			  background-color: #eee;
			}
		 
			.form-signin {
			  max-width: 330px;
			  padding: 15px;
			  margin: 0 auto;
			}
			.form-signin .form-signin-heading,
			.form-signin .checkbox {
			  margin-bottom: 10px;
			}
			.form-signin .checkbox {
			  font-weight: normal;
			}
			.form-signin .form-control {
			  position: relative;
			  height: auto;
			  -webkit-box-sizing: border-box;
				 -moz-box-sizing: border-box;
					  box-sizing: border-box;
			  padding: 10px;
			  font-size: 16px;
			}
			.form-signin .form-control:focus {
			  z-index: 2;
			}
			.form-signin input[type="email"] {
			  margin-bottom: -1px;
			  border-bottom-right-radius: 0;
			  border-bottom-left-radius: 0;
			}
			.form-signin input[type="password"] {
			  margin-bottom: 10px;
			  border-top-left-radius: 0;
			  border-top-right-radius: 0;
			}
		</style>

	</head>
	
	<body>
		
    <div class="container">
 
      <form class="form-signin" method="POST" action="">
        <h2 class="form-signin-heading">Please sign in</h2>
        <label for="umail" class="sr-only">Email address</label>
        <input type="email" name="umail" class="form-control" placeholder="Email address" required autofocus>
        <label for="upassword" class="sr-only">Password</label>
        <input type="password" name="upassword" id="upassword" class="form-control" placeholder="Password" required>
 
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
      </form>
 
    </div> <!-- /container -->
		
	</body>
</html>