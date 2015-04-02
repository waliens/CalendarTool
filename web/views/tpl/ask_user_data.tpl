<div class="container marg-top-40">
  <div class="panel panel-primary width-70 center marg-bot-40">
    <div class="panel-heading">
      <h3 class="panel-title text-center">L'équipe du MyULG Calendar vous souhaite la bienvenue!</h3>
    </div>
    <div class="panel-body">
      <div><p> Avant que vous ne commenciez à utiliser notre plateforme et afin de vous assurer un confort d'utilisation maximum, nous aurions besoin de quelques informations à votre sujet. </p></div>
      {if isset($error) && !empty($error)} <div><p><strong>Attention</strong> : {$error}</p></div> {/if}
      <form class="form-ask-data form-group" action="index.php?page=ask_data" method="POST">
        <label for="surname">Nom</label>
        <input type="text" name="surname" id="surname" class="form-control" placeholder="Nom" required autofocus {if isset($surname) && !empty($surname)}value="{$surname}"{/if}><br>
        <label for="name" >Prénom</label>
        <input type="text" name="name" id="name" class="form-control" placeholder="Prénom" required {if isset($name) && !empty($name)}value="{$name}"{/if}><br>
        <label for="email">Email ULg</label>
        <input type="text" name="email" id="email" class="form-control" placeholder="Votre email ULg" required {if isset($email) && !empty($mail)}value="{$email}"{/if}><br>
        
        <button class="btn btn-lg btn-primary btn-block" type="submit">Envoyer</button>
      </form>
    </div>
  </div>
</div>