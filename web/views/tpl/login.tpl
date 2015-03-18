<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">
<!-- Added css -->
<link rel="stylesheet" href="css\stylesheet.css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="icon" href="imgs/myulg.ico">
<title>MyULG Calendar Tool - Admin Login</title>
</head>

<body>
<div class="container">
	<div class="header">
        <img alt="My ULG Calendar" id="logo" src="imgs/myulg.png"/>
    </div>
      <form class="form-signin">
        <h2 class="form-signin-heading text-center">Admin Login</h2>
        <label for="inputUserName" class="sr-only">Nom d'utilisateur</label>
        <input id="inputUserName" class="form-control" placeholder="Utilisateur" required autofocus>
        <label for="inputPassword" class="sr-only">Mot de passe</label>
        <input type="password" id="inputPassword" class="form-control" placeholder="Mot de passe" required>
        <div class="checkbox">
          <label>
            <input type="checkbox" value="remember-me">Garder ma session active
          </label>
        </div>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Se connecter</button>
      </form>

    </div> <!-- /container -->
    <div id="footer">
           Copyright &copy; <script>document.write(new Date().getFullYear())</script> SEGI - Université de Liège
      </div>
<!-- Bootstrap core JavaScript
    ================================================== --> 
<!-- Placed at the end of the document so the pages load faster --> 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script> 
<!-- Latest compiled and minified JavaScript --> 
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script> 
</body>
</html>