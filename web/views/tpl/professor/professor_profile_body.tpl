<div class="container marg-top-40">
  <div class="panel panel-primary width-70 center marg-bot-40">
    <div class="panel-heading">
      <h3 class="panel-title text-center">Profile</h3>
    </div>
    <div class="panel-body">
      <p><span class="text-info text-bold">Catégorie: </span><span id="user-category">Professeur</span></p>
      <p><span class="text-info text-bold">Nom: </span><span id="user-name"><!--USERNAME--></span></p>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingOne">
      <h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">Cours</a></h4>
    </div>
    <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
      <table class="table" id="global_events">
        <tr class="text-bold">
          <td class="min-width-90">Course ID</td>
          <td>Title</td>
          <td class="width-30">Modifier</td>
          <td class="width-30">Supprimer</td>
        </tr>
        <!--FILLED BY AJAX WITH COURSES LIST-->
      </table>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingTwo">
      <h4 class="panel-title"> <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">Événements indépendants</a> </h4>
    </div>
    <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
      <table class="table" id="independent-events">
        <tr class="text-bold">
          <td>Title</td>
          <td class="width-30">Modifier</td>
          <td class="width-30">Supprimer</td>
        </tr>
        <!--FILLED BY AJAX WITH LIST INDEPENDENT EVENTS-->
      </table>
    </div>
  </div>
</div>