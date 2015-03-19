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
        <button type="button" class="btn btn-primary" data-dismiss="modal" id="global_event_delete_confirm">Confirmer</button>
      </div>
    </div>
  </div>
</div>
<!-- Add Global Event Alert -->
<div class="modal fade" id="add_global_event_alert" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Ajouter un cours</h4>
      </div>
      <!--BODY-->
      <div>
        <table class="table" id="global_events">
          <tr>
            <td class="text-bold width-80">Année</td>
            <td id="year-dropdown"><div class="dropdown">
                <button class="btn btn-default dropdown-toggle" type="button" id="selected_year" data-toggle="dropdown" aria-expanded="true"> Année <span class="caret"></span> </button>
                <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1" id="years_list">
                </ul>
              </div></td>
          </tr>
          <tr>
            <td class="text-bold width-80">Cours</td>
            <td id="cours-dropdown"><div class="dropdown">
                <button class="btn btn-default dropdown-toggle" type="button" id="cours_to_add" data-toggle="dropdown" aria-expanded="true"> Sélectionner cours <span class="caret"></span> </button>
                <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1" id="global_course_list">
                </ul>
              </div></td>
          </tr>
          <tr>
            <td class="text-bold width-80">Détails</td>
            <td><input type="text" class="form-control" placeholder="Détails" aria-describedby="sizing-addon1" id="new_global_cours_details"></td>
          </tr>
          <tr>
            <td class="text-bold width-80">Feedback</td>
            <td><input type="text" class="form-control" placeholder="Feedback" aria-describedby="sizing-addon1" id="new_global_cours_feedback"></td>
          </tr>
          <tr>
            <td class="text-bold width-80">Langue</td>
            <td><div class="dropdown">
                <button class="btn btn-default dropdown-toggle" type="button" id="cours_language" data-toggle="dropdown" aria-expanded="true" language=""> Sélectionner langue <span class="caret"></span> </button>
                <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1" id="languages_list">
                  <li role="presentation"><a role="menuitem" tabindex="-1" href="#" language="FR">Français</a></li>
                  <li role="presentation"><a role="menuitem" tabindex="-1" href="#" language="EN">Anglais</a></li>
                </ul>
              </div></td>
          </tr>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal" id="global_event_add_confirm" disabled="disabled">Confirmer</button>
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
        <button type="button" class="btn btn-primary" data-dismiss="modal" id="indep_event_delete_confirm">Confirmer</button>
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
        <div class="panel-group width-100 center" id="accordion-global-event" role="tablist" aria-multiselectable="true">
          <div class="panel panel-default">
            <div class="panel-heading" style="height:42px" role="tab" id="headingOne">
              <h4 class="panel-title float-left"><a data-toggle="collapse" data-parent="#accordion-global-event" href="#global-event-info" aria-expanded="true" aria-controls="global-event-info">Info</a></h4>
              <span id="edit_global_event" class="float-right" style="margin-top: -4px;"><a class="edit" onclick="edit_global_event()"></a></span> </div>
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
                  <td id="event-period"></td>
                </tr>
                <tr>
                  <td class="text-bold width-80">Section</td>
                  <td id="event-pathway"></td>
                </tr>
              </table>
            </div>
          </div>
          <div class="panel panel-default">
            <div class="panel-heading" role="tab">
              <h4 class="panel-title"> <a class="collapsed" data-toggle="collapse" data-parent="#accordion-global-event" href="#subevents_info_accordion" aria-expanded="false" aria-controls="subevents_info_accordion">Sous-événements</a> </h4>
            </div>
            <div class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree" id="subevents_info_accordion">
              <div id="subevents_info"> 
                <!--FILLED BY AJAX WITH LIST SUBEVENTS--> 
              </div>
              <div class="text-center marg-bot-10"> <a href="#" class="btn btn-primary padding-6-55" role="button" id="add-subevent" data-toggle="modal" data-dismiss="modal" data-target="#new_subevent">Ajouter sous-événement</a> </div>
            </div>
          </div>
          <div class="panel panel-default">
            <div class="panel-heading" role="tab">
              <h4 class="panel-title"> <a class="collapsed" data-toggle="collapse" data-parent="#accordion-global-event" href="#event_team_accordion" aria-expanded="false" aria-controls="event_team_accordion">Équipe</a> </h4>
            </div>
            <div class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo" id="event_team_accordion">
              <div id="event_team"> 
                <!--FILLED BY AJAX WITH LIST EVENT TEAM--> 
              </div>
              <div class="text-center marg-bot-10"> <a href="#" class="btn btn-primary padding-6-55" role="button" id="add-event-member">Ajouter équipe</a> </div>
              <div class="modal-footer hidden text-center" id="add-event-member-conf-abort-buttons">
                <button type="button" class="btn btn-default" id="add-event-member-abort">Annuler</button>
                <button type="button" class="btn btn-primary" id="add-event-member-confirm" disabled="disabled">Confirmer</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer hidden text-center" id="edit-global-event-buttons">
        <button type="button" class="btn btn-default" data-dismiss="modal" id="edit-global-event-abort" onclick="edit_global_event_abort()">Annuler</button>
        <button type="button" class="btn btn-primary" id="edit-global-event-confirm" onclick="edit_global_event_confirm()">Confirmer</button>
      </div>
    </div>
  </div>
</div>

<!--ADD SUBEVENT ALERT-->
<div class="modal fade" id="new_subevent" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="new_subevent_modal_header">Nouvel sous-événement</h4>
      </div>
      <!--modal body-->
      <div class="modal-body">
        <div class="panel-group width-100 center" id="accordion-subevent" role="tablist" aria-multiselectable="true">
          <div class="panel panel-default">
            <div class="panel-heading" style="height:42px" role="tab" id="headingOne">
              <h4 class="panel-title float-left"><a data-toggle="collapse" data-parent="#accordion-subevent" href="#subevent-info" aria-expanded="true" aria-controls="subevent-info">Info</a></h4>
            </div>
            <div id="subevent-info" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
              <form class="form-group">
                <!-- Table -->
                <table class="table">
                <tr>
                  <td class="text-bold width-80 vertical-middle">Titre</td>
                  <td><label for="new_subevent_title" class="sr-only">Titre</label>
                    <input id="new_subevent_title" class="form-control" placeholder="Titre de l'événement" required autofocus></td>
                </tr>
                <tr>
                  <td class="text-bold width-80">Quand</td>
                  <td><table id="new_subevent_time">
                      <tr>
                        <td class="width-80 text-underline">Commence</td>
                        <td id="new_subevent_startDate"><label for="new_subevent_startDate_datepicker" class="sr-only">Commence</label>
                          <input id="new_subevent_startDate_datepicker" onclick="setSens('new_subevent_endDate_datepicker', 'max', 'new_subevent_dates');">
                          <label for="new_subevent_startHour" class="sr-only">Commence Heure</label>
                          <input class="marging-10-0 time" id="new_subevent_startHour" placeholder="HH:MM" data-time-format="H:i"></td>
                      </tr>
                      <tr>
                        <td class="width-80 text-underline">Se termine</td>
                        <td id="new_subevent_endDate"><label for="new_subevent_endDate_datepicker" class="sr-only">Se termine</label>
                          <input class="marging-10-0" id="new_subevent_endDate_datepicker" onclick="setSens('new_subevent_startDate_datepicker', 'min','new_subevent_dates');">
                          <label for="new_subevent_endHour" class="sr-only">Se termine Heure</label>
                          <input class="marging-10-0 time" id="new_subevent_endHour" placeholder="HH:MM" data-time-format="H:i"></td>
                      </tr>
                      <tr id="new_subevent_deadline">
                        <td>Deadline</td>
                        <td><input type="checkbox" aria-label="" onclick="deadline();"></td>
                      </tr>
                    </table></td>
                </tr>
                <tr>
                  <td class="text-bold width-80 vertical-middle">Récurrence</td>
                  <td><div class="float-left-10padright">
                      <div class="dropdown">
                        <button class="btn btn-default dropdown-toggle" type="button" id="new_subevent_recurrence_btn" data-toggle="dropdown" aria-expanded="true"> <span id="new_subevent_recurrence" recurrence-id="0">jamais</span> <span class="caret"></span> </button>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                          <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="updateRecurrence()" recurrence-id="0">jamais</a></li>
                          <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="updateRecurrence()" recurrence-id="1">tous les jours</a></li>
                          <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="updateRecurrence()" recurrence-id="2">toutes les semaines</a></li>
                          <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="updateRecurrence()" recurrence-id="3">toutes les deux semaines</a></li>
                          <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="updateRecurrence()" recurrence-id="4">tous les mois</a></li>
                          <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="updateRecurrence()" recurrence-id="5">tous les ans</a></li>
                        </ul>
                      </div>
                    </div>
                    <div id="new_subevent_recurrence_end_td" class="hidden float-left-10padright">
                      <div class="col-lg-6">
                        <div class="input-group" style="width:180px">
                          <input id="new_subevent_recurrence_end" class="form-control border-radius-4" placeholder="Fin de la récurrence?" size="45">
                        </div>
                        <!-- /input-group --> 
                      </div>
                      <!-- /.col-lg-6 --> 
                    </div></td>
                </tr>
                <tr>
                  <td class="text-bold width-80 vertical-middle">Categorie</td>
                  <td><div class="dropdown">
                      <button class="btn btn-default dropdown-toggle" type="button" id="new_subevent_type_btn" data-toggle="dropdown" aria-expanded="true"> <span id="new_subevent_type" category-id="1">Cours théorique</span> <span class="caret"></span> </button>
                      <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1" id="new_subevent_categories">
                        <!-- FILLED WITH ACADEMIC EVENTS CATEGORIES THROUGH AJAX -->
                      </ul>
                    </div></td>
                </tr>
                <tr>
                  <td class="text-bold width-80 vertical-middle">Charge de Travail</td>
                  <td><label for="new_subevent_workload" class="sr-only">Charge de Travail</label>
                    <input type="number" name="points" min="0" max="1000" step="1" value="30" class="form-control" id="new_subevent_workload" placeholder="new_subevent_workload"></td>
                </tr>
                <tr>
                  <td class="text-bold width-80 vertical-middle">Lieu</td>
                  <td><label for="new_subevent_place" class="sr-only">Lieu</label>
                    <input class="form-control" id="new_subevent_place" placeholder="Lieu de l'événement"></td>
                </tr>
                <tr>
                  <td class="text-bold width-80 vertical-middle">Détails</td>
                  <td><label for="new_subevent_details" class="sr-only">Détails</label>
                    <input class="form-control" id="new_subevent_details" placeholder="Détails de l'événement"></td>
                </tr>
                <tr id="new_soubevent_feedback">
                  <td class="text-bold width-80">Feedback</td>
                  <td><label for="new_subevent_feedback_body" class="sr-only">Feedback</label>
                    <input class="form-control" id="new_subevent_feedback_body" placeholder="Feedback pour l'événement"></td>
                </tr>
                <tr id="new_soubevent_pract_details">
                  <td class="text-bold width-80">Détails pratiques</td>
                  <td><label for="new_soubevent_pract_details_body" class="sr-only">Détails pratiques</label>
                    <input class="form-control" id="new_soubevent_pract_details_body" placeholder="Détails pratiques pour l'étudiants"></td>
                </tr>
                </table>
              </form>
            </div>
          </div>
          <div class="panel panel-default">
            <div class="panel-heading" role="tab">
              <h4 class="panel-title"> <a class="collapsed" data-toggle="collapse" data-parent="#accordion-subevent" href="#subevents_pathways" aria-expanded="false" aria-controls="subevents_pathways">Sections</a> </h4>
            </div>
            <div class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree" id="subevents_pathways"> 
              <div id="new_subevents_pathways"> 
              <table id="new_subevent_pathways_table" class="table">
              <!--FILLED BY AJAX WITH LIST PATHWAYS OF GLOBAL EVENT--> 
              </table>
            </div>
            </div>
            
          </div>
          <div class="panel panel-default" style="margin-bottom: 10px;">
            <div class="panel-heading" role="tab">
              <h4 class="panel-title"> <a class="collapsed" data-toggle="collapse" data-parent="#accordion-subevent" href="#subevent_team_accordion" aria-expanded="false" aria-controls="subevent_team_accordion">Équipe</a> </h4>
            </div>
            <div class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo" id="subevent_team_accordion"> 
              	<div id="new_subevent_team"> 
                <table id="new_subevent_team_table" class="table">
              <!--FILLED BY AJAX WITH LIST EVENT TEAM--> 
              </table>
            	</div>
            <div class="modal-footer hidden text-center" id="add-event-member-conf-abort-buttons">
                <button type="button" class="btn btn-default" id="add-event-member-abort">Annuler</button>
                <button type="button" class="btn btn-primary" id="add-event-member-confirm" disabled="disabled">Confirmer</button>
              </div>
            </div>
            
          </div>
        </div>
        <div class="modal-footer">
        <div class='text-center' id='new_subevent_btns'>
                <button type='button' class='btn btn-primary' type="submit" disabled="disabled" id="new_subevent_creation_confirm">
                Confirmer
                </button>
                <button type='button' class='btn btn-default' data-dismiss="modal">Annuler</button>
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
            <td class="text-bold width-80">Lieu</td>
            <td id="subevent-place"></td>
          </tr>
          <tr>
            <td class="text-bold width-80">Quand</td>
            <td id="subevent-period"><table id="subevent_time">
                <tr>
                  <td class="width-80 text-underline">Commence</td>
                  <td><p class="marging-10-0" id="subevent_startDate"></p></td>
                </tr>
                <tr>
                  <td class="width-80 text-underline">Se termine</td>
                  <td><p class="marging-10-0" id="subevent_endDate"></p></td>
                </tr>
              </table></td>
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

<!-- NEW AND EDIT INDEPENDENT EVENT MODAL -->
<div class="modal fade" id="new_indep_event_info" tabindex="-1" role="dialog" aria-labelledby="new_indep_event_modal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="new_indep_event_title"><!--EVENT TITLE--></h4>
      </div>
      <div id="new_indep_event_info">
        <table class="table" id="new_indep_event_info_table">
          <tr>
            <td class="text-bold width-80">Détails</td>
            <td id="new-indep-event-details"></td>
          </tr>
          <tr>
            <td class="text-bold width-80">Catégorie</td>
            <td id="new-indep-event-category"></td>
          </tr>
          <tr>
            <td class="text-bold width-80">Lieu</td>
            <td id="new-indep-event-place"></td>
          </tr>
          <tr>
            <td class="text-bold width-80">Quand</td>
            <td id="new-indep-event-period"><table id="new-indep-event_time">
                <tr>
                  <td class="width-80 text-underline">Commence</td>
                  <td><p class="marging-10-0" id="new_indep_event_startDate"></p></td>
                </tr>
                <tr>
                  <td class="width-80 text-underline">Se termine</td>
                  <td><p class="marging-10-0" id="new_indep_event_endDate"></p></td>
                </tr>
              </table></td>
          </tr>
          <tr>
            <td class="text-bold width-80">Recurrence</td>
            <td id="new_indep_event_recurrence"></td>
          </tr>
          <tr>
            <td class="width-80">Commence</td>
            <td id="new_indep_event_start_recurrence"></td>
          </tr>
          <tr>
            <td class="width-80">Se termine</td>
            <td id="new_indep_event_end_recurrence"></td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>
>>>>>>> 1cf6be534201903db994a3c26cb0dc4f3786775a
