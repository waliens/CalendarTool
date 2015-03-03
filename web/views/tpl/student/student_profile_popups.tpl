<!-- Optional Course Alert -->
<div class="modal fade" id="optional_course_alert" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><!--ALERT TITLE--></h4>
      </div>
      <div class="modal-body">
        <p><!--ALERT CONTENT--></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal">Sauver</button>
      </div>
    </div>
  </div>
</div>

<!-- Event Info -->
<div class="modal fade" id="event_info" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title" id="event-title"><!--EVENT TITLE--></h4>
    </div>
    <div class="modal-body">
      <div class="panel-group width-70 center" id="accordion-global-event" role="tablist" aria-multiselectable="true">
        <div class="panel panel-default">
          <div class="panel-heading" role="tab" id="headingOne">
            <h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion-global-event" href="#global-event-info" aria-expanded="true" aria-controls="global-event-info">Info</a></h4>
          </div>
          <div id="global-event-info" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
            <table class="table" id="global_events">
              <tr>
                <td class="text-bold">Détails</td>
                <td id="event-details"></td>
              </tr>
              <tr>
                <td class="text-bold">Langue</td>
                <td id="event-lang"></td>
              </tr>
              <tr>
                <td class="text-bold">Travail</td>
                <td id="event-work"></td>
              </tr>
              <tr>
                <td class="text-bold">Quand</td>
                <td id="event-period"></td>
              </tr>
              <tr>
                <td class="text-bold">Pathway</td>
                <td id="event-pathway"></td>
              </tr>
            </table>
          </div>
        </div>
        <div class="panel panel-default">
          <div class="panel-heading" role="tab" id="headingThree">
            <h4 class="panel-title"> <a class="collapsed" data-toggle="collapse" data-parent="#accordion-global-event" href="#subevents_info" aria-expanded="false" aria-controls="subevents_info">Sousévénements</a> </h4>
          </div>
          <div id="subevents_info" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
              <!--FILLED BY AJAX WITH LIST SUBEVENTS-->
          </div>
        </div>
         <div class="panel panel-default">
          <div class="panel-heading" role="tab" id="headingThree">
            <h4 class="panel-title"> <a class="collapsed" data-toggle="collapse" data-parent="#accordion-global-event" href="#event_team" aria-expanded="false" aria-controls="event_team">Équipe</a> </h4>
          </div>
          <div id="event_team" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
              <!--FILLED BY AJAX WITH LIST EVENT TEAM-->
          </div>
        </div>
      </div>
    </div>
  </div>
</div>