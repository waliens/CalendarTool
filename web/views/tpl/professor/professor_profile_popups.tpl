<!-- Delete Global Event Alert -->
<div class="modal fade" id="delete_global_event_alert" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Supprimer le cours</h4>
      </div>
      <div class="modal-body">
        <p>Êtes-vous sûr de vouloir supprimer le cours <span name="global_course_deleted" class="text-bold"></span>? Le texte suivant sera envoyé à tous les étudiants actuellement inscrits à l'événement.</p>
        <div contenteditable="true" class="box"> Le cours <span name="global_course_deleted"></span> a été supprimé. </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal">Confirmer</button>
      </div>
    </div>
  </div>
</div>
<!-- Delete Independent Event Alert -->
<div class="modal fade" id="delete_indep_event_alert" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Supprimer l'événement indépendant</h4>
      </div>
      <div class="modal-body">
        <p>Êtes-vous sûr de vouloir supprimer l'événement <span name="indep_event_deleted" class="text-bold"></span>? Le texte suivant sera envoyé à tous les étudiants actuellement inscrits à l'événement.</p>
        <div contenteditable="true" class="box"> L'événement <span name="indep_event_deleted"></span> a été supprimé. </div>
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
</div>

<!-- SubEvent and Independent event info-->
<div class="modal fade" id="subevent_info" tabindex="-1" role="dialog" aria-labelledby="subevent-modal" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="subevent-title"><!--EVENT TITLE--></h4>
        </div>
        <div id="subevent-info">
                <table class="table" id="global_events">
                  <tr>
                    <td class="text-bold width-80">Détails</td>
                    <td id="subevent-details"></td>
                  </tr>
                  <tr>
                    <td class="text-bold width-80">Catégorie</td>
                    <td id="subevent-category"></td>
                  </tr>
                  <tr>
                    <td class="text-bold width-80">Place</td>
                    <td id="subevent-place"></td>
                  </tr>
                  <tr>
                    <td class="text-bold width-80">Quand</td>
                    <td id="subevent-period">
                    <table id="subevent_time">
                      <tr>
                        <td class="width-80 text-underline">Commence</td>
                        <td id="subevent_startDate">
                          <p class="marging-10-0" id="subevent_startDate"></p>
                          </td>
                      </tr>
                      <tr>
                        <td class="width-80 text-underline">Se termine</td>
                        <td id="subevent_endDate">
                          <p class="marging-10-0" id="subevent_endDate"></p>
                          </td>
                      </tr>
                    </table>
                    </td>
                  </tr>
                  <tr>
                    <td class="text-bold width-80">Recurrence</td>
                    <td id="recurrence"></td>
                  </tr>
                  <tr>
                  	<td class="width-80">Commence</td>
                    <td id="start-recurrence"></td>
                  </tr>
                  <tr>
                    <td class="width-80">Se termine</td>
                    <td id="end-recurrence"></td>
                  </tr>
                </table>
        </div>
      </div>
    </div>
</div>
