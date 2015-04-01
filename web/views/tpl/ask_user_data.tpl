<div class="container marg-top-40">
  <div class="panel panel-primary width-70 center marg-bot-40">
    <div class="panel-heading">
      <h3 class="panel-title text-center">L'équipe du CalendarTool vous souhaite la bienvenue!</h3>
    </div>
    <div class="panel-body">
      <div><p> Avant que vous ne commenciez à utiliser notre plateforme et afin de vous assurer un confort d'utilisation maximum, nous aurions besoin de quelques informations à votre sujet. </p></div><br>
      <form class="form-ask-data">
        <label for="surname" class="sr-only">Votre nom : </label>
        <input type="text" name="surname" id="surname" class="form-control" placeholder="Nom" required autofocus {if isset($surname)}value="{$surname}"{/if}><br>
        <label for="name" class="sr-only">Votre prénom : </label>
        <input type="text" name="name" id="name" class="form-control" placeholder="Prénom" required {if isset($name)}value="{$name}"{/if}><br>
        <label for="email" class="sr-only">Votre email ULg : </label>
        <input type="text" name="email" id="email" class="form-control" placeholder="Email" required {if isset($email)}value="{$email}"{/if}><br>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Se connecter</button>
      </form>
    </div>
  </div>
</div>