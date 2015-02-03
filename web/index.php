<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">

<!-- Added css -->
<link rel="stylesheet" href="css\stylesheet.css">

<!-- Custom styles for this template -->
<link href="css\jumbotron.css" rel="stylesheet">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="icon" href="img/myulg.ico">
<title>MyULG Calendar Tool</title>
</head>

<body>
<nav class="navbar navbar-inverse navbar-fixed-top">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
    </div>
    <div id="navbar" class="collapse navbar-collapse">
      <ul class="nav navbar-nav">
        <li><a href="#">Calendrier</a></li>
        <li class="active"><a href="#profile">Profile</a></li>
        <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Menu <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="#">Demandes d'accès au calendrier</a></li>
            <li class="divider"></li>
            <li><a href="#">Export statique</a></li>
            <li><a href="#">Export dynamique</a></li>
            <li class="divider"></li>
            <li><a href="#">&Eacute;venements privés</a></li>
            <li class="divider"></li>
            <li><a href="#">Déconnexion</a></li>
          </ul>
        </li>
      </ul>
    </div>
    <!--/.nav-collapse --> 
  </div>
</nav>
  <div class="container marg-top-40">
    <div class="panel panel-primary width-70 center marg-bot-40">
      <div class="panel-heading">
        <h3 class="panel-title text-center">Profile</h3>
      </div>
      <div class="panel-body">
        <p><span class="text-info text-bold">Catégorie: </span><span id="user-category">Student</span></p>
        <p><span class="text-info text-bold">Nom: </span><span id="user-name">Carlo Rossi</span></p>
        <p><span class="text-info text-bold">Pathway: </span><span id="user-pathway">Ingénierie Informatique - 1ère année</span></p>
      </div>
    </div>
    <div class="panel-group width-70 center" id="accordion" role="tablist" aria-multiselectable="true">
      <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingOne">
          <h4 class="panel-title"> <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne"> Mes cours </a> </h4>
        </div>
        <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
          <div class="panel-body">
          <div class="list-group" id="user-mandatory-courses"></div>
          </div>
        </div>
      </div>
      <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingTwo">
          <h4 class="panel-title"> <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo"> Cours Optionnels </a> </h4>
        </div>
        <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
          <div class="panel-body">
           	<div class="list-group" id="user-optional-courses"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /.container --> 
<div id="footer"> Copyright &copy; <script>document.write(new Date().getFullYear())</script> SEGI - Université de Liège </div>
<!-- Bootstrap core JavaScript
    ================================================== --> 
<!-- Placed at the end of the document so the pages load faster --> 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script> 
<!-- Latest compiled and minified JavaScript --> 
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
<!-- added js -->
<script src="js\mycalendar.js"></script>
</body>
</html>