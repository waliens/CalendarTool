<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="css/bootstrap.css">

  <!-- Optional theme -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">

  <!-- Added css -->
  <link rel="stylesheet" href="css/stylesheet.css">

  <title>MyULG Calendar Tool {if !empty($title)}- {$title}{/if}</title>

  <script>
    var student = {$is_student_str};
  </script>
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
          <li id="calendar_nav" class="active"><a href="index.php">Calendrier</a></li>
          <li id="profile_nav"><a href="?page=profile">Profil</a></li>
          <li id="static_export_page"><a href="?page=static_export">Export statique</a></li>
          {if $is_student}<li id="private_events"><a href="?page=private_events">&Eacute;venements privés</a></li>{/if}
          <li><a href="?page=disonnect">Déconnexion</a></li>
        </ul>
      </div>
      <!--/.nav-collapse --> 
    </div>
  </nav>