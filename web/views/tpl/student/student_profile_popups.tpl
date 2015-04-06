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
                    <td class="text-bold width-80">Détails</td>
                    <td id="event-details"></td>
                  </tr>
                  <tr>
                    <td class="text-bold width-80">Feedback</td>
                    <td id="event-feedback"></td>
                  </tr>
                  <tr>
                    <td class="text-bold width-80">Langue</td>
                    <td id="event-lang"></td>
                  </tr>
                  <tr>
                    <td class="text-bold width-80">Travail</td>
                    <td id="event-work"></td>
                  </tr>
                  <tr>
                    <td class="text-bold width-80">Quand</td>
                    <td id="event-period">
                    </td>
                  </tr>
                  <tr>
                    <td class="text-bold width-80">Pathway</td>
                    <td id="event-pathway"></td>
                  </tr>
                </table>
              </div>
            </div>
            <div class="panel panel-default">
              <div class="panel-heading" role="tab" id="headingThree">
                <h4 class="panel-title"> <a class="collapsed" data-toggle="collapse" data-parent="#accordion-global-event" href="#subevents_info" aria-expanded="false" aria-controls="subevents_info">Sous-événements</a> </h4>
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
</div>

<!-- SubEvent and Independent event info-->
<div class="modal fade" id="academic_event_info_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="academic_event_title">&Eacute;vénement Academic</h4>
      </div>
      <!--modal body-->
      <div class="modal-body">
        <div class="panel-group width-100 center">
          <div class="panel panel-default">
            <div class="panel-heading" style="height:42px">
              <h4 class="panel-title float-left">Info</h4>
            </div>
            <div id="academic_event_info"> 
              <!-- Table -->
              <table class="table">
                <tr>
                  <td class="text-bold width-80">Quand</td>
                  <td><table id="academic_event_time">
                      <tr>
                        <td class="width-80 text-underline">Commence</td>
                        <td id="academic_event_start"></td>
                      </tr>
                      <tr>
                        <td class="width-80 text-underline">Se termine</td>
                        <td id="academic_event_end"></td>
                      </tr>
                      <tr id="academic_event_deadline">
                        <td>Deadline</td>
                        <td><input type="checkbox" aria-label="" disabled="disabled"></td>
                      </tr>
                    </table></td>
                </tr>
                <tr>
                  <td class="text-bold width-80 vertical-middle">Récurrence</td>
                  <td id="academic_event_recurrence"></td>
                </tr>
                <tr>
                  <td class="text-bold width-80 vertical-middle">Fin de la récurrence</td>
                  <td id="academic_event_recurrence_end"></td>
                </tr>
                <tr>
                  <td class="text-bold width-80 vertical-middle">Categorie</td>
                  <td id="academic_event_category"></td>
                </tr>
                <tr>
                  <td class="text-bold width-80 vertical-middle">Lieu</td>
                  <td id="academic_event_place"></td>
                </tr>
                <tr>
                  <td class="text-bold width-80 vertical-middle">Détails</td>
                  <td id="academic_event_details"></td>
                </tr>
                <tr id="academic_event_pract_details">
                  <td class="text-bold width-80">Détails pratiques</td>
                  <td id="academic_event_pract_details_body"></td>
                </tr>
                <tr>
                    <td class="text-bold width-80 vertical-middle">Charge de Travail</td>
                    <td id="academic_event_workload">
                    </td>
                  </tr>
                  <tr id="new_soubevent_feedback">
                    <td class="text-bold width-80">Feedback</td>
                    <td id="academic_event_feedback_body"></td>
                  </tr>
                <tr>
                  <td class="text-bold width-80">Sections</td>
                  <td id="academic_event_pathways_table" class="table"><!--FILLED BY AJAX WITH LIST PATHWAYS--></td>
                </tr>
                <tr>
                  <td class="text-bold width-80">&Eacute;quipe</td>
                  <td id="academic_event_team_table" class="table"><!--FILLED BY AJAX WITH LIST TEAM MEMBERS--></td>
                </tr>
                <tr>
                <td class="text-bold width-80">Notes</td>
                <td id="notes_body"></td>
                </tr>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>