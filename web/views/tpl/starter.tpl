<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">

  <!-- Optional theme -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">

  <!-- Added css -->
  <link rel="stylesheet" href="css/stylesheet.css">

  <title>MyULG Calendar Tool {if !empty($title)}- {$title}{/if}</title>

  <!-- Custom styles for this template -->
  {$includes}
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="icon" href="imgs/myulg.ico">
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