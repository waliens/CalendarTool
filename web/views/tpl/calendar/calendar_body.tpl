<div class="container marg-top-40">
  <div id="upcoming_deadlines" style="width:50%;margin:0 auto;margin-top:20px">
    <div class="panel-group" id="accordion-upcoming-deadlines" role="tablist" aria-multiselectable="true">
      <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="heading-upcoming-deadlines">
          <h4 class="panel-title"> <img  src="imgs/warning_red.png"/><a data-toggle="collapse" data-parent="#accordion-upcoming-deadlines" href="#collapseDeadlines" aria-expanded="false" aria-controls="collapseOne" class="collapsed">Deadlines à venir</a> </h4>
        </div>
        <div id="collapseDeadlines" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-upcoming-deadlines" aria-expanded="false" style="height: 0px;">
          <div class="panel-body">
            <table class="table" id="deadlines">
             <!--FILLED THROUGH AJAX CALL WITH UPCOMING DEADLINES-->
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div id='calendar'></div>

  <div id="filters" style="width:50%;margin:0 auto;margin-top:20px">
    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
      <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingOne">
          <h4 class="panel-title"> <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne" class="collapsed"> Filtres </a> </h4>
        </div>
        <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne" aria-expanded="false" style="height: 0px;">
          <div class="panel-body">
            <table class="table" id="filters">
              <tr>
                <td colspan="2">Filtrer par:</td>
              </tr>
              <tr>
                <td class="width-30"><input type="checkbox" id="all_events_filter" checked></td>
                <td>Tous les événements</td>
              </tr>
              {if !$student}
              <tr>
                <td class="width-30"><input type="checkbox" id="pathway_filter" data-toggle="modal" data-target="#filter_alert" disabled="disabled"></td>
                <td>Par section</td>
              </tr>
              {/if}
              <tr>
                <td class="width-30"><input type="checkbox" id="course_filter" data-toggle="modal" data-target="#filter_alert" disabled="disabled"></td>
                <td>Par cours</td>
              </tr>
              <tr>
                <td class="width-30"><input type="checkbox" id="event_type_filter" data-toggle="modal" data-target="#filter_alert" disabled="disabled"></td>
                <td>Par type d'événement</td>
              </tr>
              <tr>
                <td class="width-30"><input type="checkbox" id="event_category_filter" data-toggle="modal" data-target="#filter_alert" disabled="disabled"></td>
                <td>Par catégorie d'événement</td>
              </tr>
              <tr>
                <td class="width-30"><input type="checkbox" id="professor_filter" data-toggle="modal" data-target="#filter_alert" disabled="disabled"></td>
                <td>Par professeur</td>
              </tr>
            </table>
            <div class="text-center">
              <button type="button" class="btn btn-primary" onclick="addEvents()" disabled="true" id="submit_filters">Ok</button>
              <button type="button" class="btn btn-default" onclick="reset_filters()">Réinitialiser</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
