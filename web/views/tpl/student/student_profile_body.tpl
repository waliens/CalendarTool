<div class="container marg-top-40">
  <div class="panel panel-primary width-70 center marg-bot-40">
    <div class="panel-heading">
      <h3 class="panel-title text-center">Profile</h3>
    </div>
    <div class="panel-body">
      <p><span class="text-info text-bold">Catégorie: </span><span id="user-category">Student</span></p>
      <p><span class="text-info text-bold">Nom: </span><span id="user-name"><!--USERNAME--></span></p>
      <p><span class="text-info text-bold">Pathway: </span><span id="user-pathway"><!--PATHWAY--></span></p>
    </div>
  </div>
  <div class="panel-group width-70 center" id="accordion" role="tablist" aria-multiselectable="true">
    <div class="panel panel-default">
      <div class="panel-heading" role="tab" id="headingOne">
        <h4 class="panel-title"> <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne"> Mes cours </a> </h4>
      </div>
      <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
        <table class="table" id="user-mandatory-courses">
        <tr><td class="min-width-80">Course ID</td><td>Title</td></tr>
          <!--FILLED BY AJAX WITH LIST MANDATORY COURSES-->
        </table>
      </div>
    </div>
    <div class="panel panel-default">
      <div class="panel-heading" role="tab" id="headingTwo">
        <h4 class="panel-title"> <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo"> Cours Optionnels </a> </h4>
      </div>
      <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
        <table class="table" id="user-optional-courses">
        <tr><td class="min-width-80">Course ID</td><td>Title</td><td class="text-center">Sélectionner</td></tr>
          <!--FILLED BY AJAX WITH LIST OPTIONAL COURSES-->
        </table>
      </div>
    </div>
  </div>
</div>