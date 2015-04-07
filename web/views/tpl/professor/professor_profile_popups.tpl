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
<!-- Delete SUBEVENT Alert -->

<div class="modal fade" id="delete_academic_event_alert" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Supprimer l'événement</h4>
      </div>
      <div class="modal-body">
        <p>Êtes-vous sûr de vouloir supprimer l'événement <span name="academic_event_deleted" class="text-bold"></span>? Le texte suivant sera envoyé à tous les étudiants actuellement inscrits à l'événement.</p>
        <div contenteditable="true" class="box"> L'événement <span name="academic_event_deleted"></span> a été supprimé. </div>
      </div>
      <div class="modal-footer" id="delete_academic_event_norecurr_btns">
        <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="confirm_delete_academic_event('false')">Confirmer</button>
      </div>
      <div class="modal-footer" id="delete_academic_event_recurr_btns">
        <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="confirm_delete_academic_event('false')">Seulement cet événement</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="confirm_delete_academic_event('true')">Tous les événements</button>
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

<!-- GLOBAL EVENT INFO-->
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
              <div class="text-center marg-bot-10"> <a href="#" class="btn btn-primary padding-6-55" role="button" id="add_member">Ajouter équipe</a> </div>
              <div class="modal-footer hidden text-center" id="add_member_conf_abort_buttons">
                <button type="button" class="btn btn-default" id="add_member_abort">Annuler</button>
                <button type="button" class="btn btn-primary" id="add_member_confirm" disabled="disabled">Confirmer</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer hidden text-center" id="edit-global-event-buttons">
        <button type="button" class="btn btn-default" id="edit-global-event-abort" onclick="edit_global_event_abort()">Annuler</button>
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
        <h4 class="modal-title" id="new_subevent_modal_header">Nouveau sous-événement</h4>
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
                            <input class="date-picker form-control" id="new_subevent_startDate_datepicker" onclick="setSens('new_subevent_endDate_datepicker', 'max', 'new_subevent_dates');">
                            <label for="new_subevent_startHour" class="sr-only">Commence Heure</label>
                            <input class="time form-control" id="new_subevent_startHour" placeholder="HH:MM" data-time-format="H:i"></td>
                        </tr>
                        <tr>
                          <td class="width-80 text-underline">Se termine</td>
                          <td id="new_subevent_endDate"><label for="new_subevent_endDate_datepicker" class="sr-only">Se termine</label>
                            <input class="marging-10-0 form-control date-picker" id="new_subevent_endDate_datepicker" onclick="setSens('new_subevent_startDate_datepicker', 'min','new_subevent_dates');">
                            <label for="new_subevent_endHour" class="sr-only">Se termine Heure</label>
                            <input class="marging-10-0 time form-control" id="new_subevent_endHour" placeholder="HH:MM" data-time-format="H:i"></td>
                        </tr>
                        <tr id="new_subevent_deadline">
                          <td>Deadline</td>
                          <td><input type="checkbox" aria-label="" onclick="deadline('#new_subevent');"></td>
                        </tr>
                      </table></td>
                  </tr>
                  <tr>
                    <td class="text-bold width-80 vertical-middle">Récurrence</td>
                    <td><div class="float-left-10padright">
                        <div class="dropdown">
                          <button class="btn btn-default dropdown-toggle" type="button" id="new_subevent_recurrence_btn" data-toggle="dropdown" aria-expanded="true"> <span id="new_subevent_recurrence" recurrence-id="6">jamais</span> <span class="caret"></span> </button>
                          <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="updateRecurrence('new_subevent')" recurrence-id="6">jamais</a></li>
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="updateRecurrence('new_subevent')" recurrence-id="1">tous les jours</a></li>
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="updateRecurrence('new_subevent')" recurrence-id="2">toutes les semaines</a></li>
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="updateRecurrence('new_subevent')" recurrence-id="3">toutes les deux semaines</a></li>
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="updateRecurrence('new_subevent')" recurrence-id="4">tous les mois</a></li>
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="updateRecurrence('new_subevent')" recurrence-id="5">tous les ans</a></li>
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
              <div class="modal-footer hidden text-center" id="add_member_conf_abort_buttons">
                <button type="button" class="btn btn-default" id="add_member_abort">Annuler</button>
                <button type="button" class="btn btn-primary" id="add_member_confirm" disabled="disabled">Confirmer</button>
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

<!--ADD INDEP EVENT ALERT-->
<div class="modal fade" id="new_indepevent" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="new_indepevent_modal_header">Nouveau événement indépendant</h4>
      </div>
      <!--modal body-->
      <div class="modal-body">
        <div class="panel-group width-100 center" id="accordion-indepevent" role="tablist" aria-multiselectable="true">
          <div class="panel panel-default">
            <div class="panel-heading" style="height:42px" role="tab" id="headingOne">
              <h4 class="panel-title float-left"><a data-toggle="collapse" data-parent="#accordion-indepevent" href="#indepevent-info" aria-expanded="true" aria-controls="indepevent-info">Info</a></h4>
            </div>
            <div id="indepevent-info" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
              <form class="form-group">
                <!-- Table -->
                <table class="table">
                  <tr>
                    <td class="text-bold width-80 vertical-middle">Titre</td>
                    <td><label for="new_indepevent_title" class="sr-only">Titre</label>
                      <input id="new_indepevent_title" class="form-control" placeholder="Titre de l'événement" required autofocus></td>
                  </tr>
                  <tr>
                    <td class="text-bold width-80">Quand</td>
                    <td><table id="new_indepevent_time">
                        <tr>
                          <td class="width-80 text-underline">Commence</td>
                          <td id="new_indepevent_startDate"><label for="new_indepevent_startDate_datepicker" class="sr-only">Commence</label>
                            <input class="form-control date-picker"id="new_indepevent_startDate_datepicker" onclick="setSens('new_indepevent_endDate_datepicker', 'max', 'new_indepevent_dates');">
                            <label for="new_indepevent_startHour" class="sr-only">Commence Heure</label>
                            <input class="form-control time" id="new_indepevent_startHour" placeholder="HH:MM" data-time-format="H:i"></td>
                        </tr>
                        <tr>
                          <td class="width-80 text-underline">Se termine</td>
                          <td id="new_indepevent_endDate"><label for="new_indepevent_endDate_datepicker" class="sr-only">Se termine</label>
                            <input class="marging-10-0 form-control date-picker" id="new_indepevent_endDate_datepicker" onclick="setSens('new_indepevent_startDate_datepicker', 'min','new_indepevent_dates');">
                            <label for="new_indepevent_endHour" class="sr-only">Se termine Heure</label>
                            <input class="marging-10-0 time form-control" id="new_indepevent_endHour" placeholder="HH:MM" data-time-format="H:i"></td>
                        </tr>
                        <tr id="new_indepevent_deadline">
                          <td>Deadline</td>
                          <td><input type="checkbox" aria-label="" onclick="deadline('#new_indepevent');"></td>
                        </tr>
                      </table></td>
                  </tr>
                  <tr>
                    <td class="text-bold width-80 vertical-middle">Récurrence</td>
                    <td><div class="float-left-10padright">
                        <div class="dropdown">
                          <button class="btn btn-default dropdown-toggle" type="button" id="new_indepevent_recurrence_btn" data-toggle="dropdown" aria-expanded="true"> <span id="new_indepevent_recurrence" recurrence-id="6">jamais</span> <span class="caret"></span> </button>
                          <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="updateRecurrence('new_indepevent')" recurrence-id="6">Jamais</a></li>
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="updateRecurrence('new_indepevent')" recurrence-id="1">Tous les jours</a></li>
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="updateRecurrence('new_indepevent')" recurrence-id="2">Toutes les semaines</a></li>
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="updateRecurrence('new_indepevent')" recurrence-id="3">Toutes les deux semaines</a></li>
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="updateRecurrence('new_indepevent')" recurrence-id="4">Tous les mois</a></li>
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="updateRecurrence('new_indepevent')" recurrence-id="5">Tous les ans</a></li>
                          </ul>
                        </div>
                      </div>
                      <div id="new_indepevent_recurrence_end_td" class="hidden float-left-10padright">
                        <div class="col-lg-6">
                          <div class="input-group" style="width:180px">
                            <input id="new_indepevent_recurrence_end" class="form-control border-radius-4" placeholder="Fin de la récurrence?" size="45">
                          </div>
                          <!-- /input-group --> 
                        </div>
                        <!-- /.col-lg-6 --> 
                      </div></td>
                  </tr>
                  <tr>
                    <td class="text-bold width-80 vertical-middle">Categorie</td>
                    <td><div class="dropdown">
                        <button class="btn btn-default dropdown-toggle" type="button" id="new_indepevent_type_btn" data-toggle="dropdown" aria-expanded="true"> <span id="new_indepevent_type" category-id="1">Cours théorique</span> <span class="caret"></span> </button>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1" id="new_indepevent_categories">
                          <!-- FILLED WITH ACADEMIC EVENTS CATEGORIES THROUGH AJAX -->
                        </ul>
                      </div></td>
                  </tr>
                  <tr>
                    <td class="text-bold width-80 vertical-middle">Charge de Travail</td>
                    <td><label for="new_indepevent_workload" class="sr-only">Charge de Travail</label>
                      <input type="number" name="points" min="0" max="1000" step="1" value="30" class="form-control" id="new_indepevent_workload" placeholder="new_indepevent_workload"></td>
                  </tr>
                  <tr>
                    <td class="text-bold width-80 vertical-middle">Lieu</td>
                    <td><label for="new_indepevent_place" class="sr-only">Lieu</label>
                      <input class="form-control" id="new_indepevent_place" placeholder="Lieu de l'événement"></td>
                  </tr>
                  <tr>
                    <td class="text-bold width-80 vertical-middle">Détails</td>
                    <td><label for="new_indepevent_details" class="sr-only">Détails</label>
                      <input class="form-control" id="new_indepevent_details" placeholder="Détails de l'événement"></td>
                  </tr>
                  <tr id="new_indepevent_feedback">
                    <td class="text-bold width-80">Feedback</td>
                    <td><label for="new_indepevent_feedback_body" class="sr-only">Feedback</label>
                      <input class="form-control" id="new_indepevent_feedback_body" placeholder="Feedback pour l'événement"></td>
                  </tr>
                  <tr id="new_indepevent_pract_details">
                    <td class="text-bold width-80">Détails pratiques</td>
                    <td><label for="new_indepevent_pract_details_body" class="sr-only">Détails pratiques</label>
                      <input class="form-control" id="new_indepevent_pract_details_body" placeholder="Détails pratiques pour l'étudiants"></td>
                  </tr>
                </table>
              </form>
            </div>
          </div>
          <div class="panel panel-default">
            <div class="panel-heading" role="tab">
              <h4 class="panel-title"> <a class="collapsed" data-toggle="collapse" data-parent="#accordion-indepevent" href="#indepevents_pathways" aria-expanded="false" aria-controls="indepevents_pathways">Sections</a> </h4>
            </div>
            <div class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree" id="indepevents_pathways">
              <div id="new_indepevents_pathways">
                <table id="new_indepevent_pathways_table" class="table">
                  <!--FILLED BY AJAX WITH LIST AVAILABLE PATHWAYS-->
                </table>
                <div class="dropdown text-center marg-bot-10">
                  <button class="btn btn-primary dropdown-toggle padding-6-55" type="button" id="add_indepevent_pathway_dropdown" data-toggle="dropdown" aria-expanded="true" >Ajouter Section <span class="caret"></span> </button>
                  <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1" id="new_indepevent_pathways_list">
                  </ul>
                </div>
              </div>
            </div>
          </div>
          <div class="panel panel-default" style="margin-bottom: 10px;">
            <div class="panel-heading" role="tab">
              <h4 class="panel-title"> <a class="collapsed" data-toggle="collapse" data-parent="#accordion-indepevent" href="#indepevent_team_accordion" aria-expanded="false" aria-controls="indepevent_team_accordion">Équipe</a> </h4>
            </div>
            <div class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo" id="indepevent_team_accordion">
              <div id="new_indepevent_team" style="padding-top: 10px;">
                <table id="new_indepevent_team_table" class="table">
                  <!--FILLED BY AJAX WITH LIST EVENT TEAM-->
                </table>
                <div class="dropdown" style="margin-left: 10px;margin-bottom: 10px;">
                  <button class="btn btn-default dropdown-toggle" type="button" id="add_indepevent_team_member_dropdown" data-toggle="dropdown" aria-expanded="true" member-id="">Ajouter un membre de l'équipe <span class="caret"></span> </button>
                  <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1" id="new_indepevent_team_members_list">
                  </ul>
                </div>
                <div class="dropdown" style="margin-left: 10px;margin-bottom: 10px;">
                  <button class="btn btn-default dropdown-toggle" type="button" id="add_indepevent_team_member_role_dropdown" data-toggle="dropdown" aria-expanded="true" member-role-id="">Sélectionner un role <span class="caret"></span> </button>
                  <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1" id="new_indepevent_team_members_role_list">
                  </ul>
                </div>
              </div>
              <div class="modal-footer hidden text-center" id="add_indepevent_member_conf_abort_buttons">
                <button type="button" class="btn btn-default" id="add_indepevent_member_abort">Annuler</button>
                <button type="button" class="btn btn-primary" id="add_indepevent_member_confirm" disabled="disabled">Confirmer</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <div class='text-center' id='new_indepevent_btns'>
            <button type='button' class='btn btn-primary' type="submit" disabled="disabled" id="new_indepevent_creation_confirm">
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
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!---EDIT ACADEMIC EVENT-->
<div class="modal fade" id="edit_academic_event" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="edit_academic_modal_header">Nouveau sous-événement</h4>
      </div>
      <!--modal body-->
      <div class="modal-body">
        <div class="panel-group width-100 center" id="accordion-edit-academic-event" role="tablist" aria-multiselectable="true">
          <div class="panel panel-default">
            <div class="panel-heading" style="height:42px" role="tab" id="headingOne">
              <h4 class="panel-title float-left"><a data-toggle="collapse" data-parent="#accordion-edit-academic-event" href="#edit_academic_event_info" aria-expanded="true" aria-controls="edit_academic_event_info">Info</a></h4>
            </div>
            <div id="edit_academic_event_info" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
              <form class="form-group">
                <!-- Table -->
                <table class="table">
                  <tr>
                    <td class="text-bold width-80 vertical-middle">Titre</td>
                    <td><label for="edit_academic_event_title" class="sr-only">Titre</label>
                      <input id="edit_academic_event_title" class="form-control" placeholder="Titre de l'événement" required autofocus></td>
                  </tr>
                  <tr>
                    <td class="text-bold width-80">Quand</td>
                    <td><table id="edit_academic_event_time">
                        <tr>
                          <td class="width-80 text-underline">Commence</td>
                          <td id="edit_academic_event_startDate"><label for="edit_academic_event_startDate_datepicker" class="sr-only">Commence</label>
                            <input class="form-control date-picker" id="edit_academic_event_startDate_datepicker" onclick="setSens('edit_academic_event_endDate_datepicker', 'max', 'edit_academic_event_dates');">
                            <label for="edit_academic_event_startHour" class="sr-only">Commence Heure</label>
                            <input class="time form-control" id="edit_academic_event_startHour" placeholder="HH:MM" data-time-format="H:i"></td>
                        </tr>
                        <tr>
                          <td class="width-80 text-underline">Se termine</td>
                          <td id="edit_academic_event_endDate"><label for="edit_academic_event_endDate_datepicker" class="sr-only">Se termine</label>
                            <input class="marging-10-0 form-control date-picker" id="edit_academic_event_endDate_datepicker" onclick="setSens('edit_academic_event_startDate_datepicker', 'min','edit_academic_event_dates');">
                            <label for="edit_academic_event_endHour" class="sr-only">Se termine Heure</label>
                            <input class="marging-10-0 time form-control" id="edit_academic_event_endHour" placeholder="HH:MM" data-time-format="H:i"></td>
                        </tr>
                        <tr id="edit_academic_event_deadline">
                          <td>Deadline</td>
                          <td><input type="checkbox" aria-label="" onclick="deadline('#edit_academic_event');"></td>
                        </tr>
                      </table></td>
                  </tr>
                  <tr>
                    <td class="text-bold width-80 vertical-middle">Récurrence</td>
                    <td>
                          <p id="edit_academic_event_recurrence" recurrence-id="6">jamais</p>
                            <p><span class="text-bold">Fin:</span> <span id="edit_academic_event_recurrence_end"></span></p></td>
                  </tr>
                  <tr>
                    <td class="text-bold width-80 vertical-middle">Categorie</td>
                    <td><div class="dropdown">
                        <button class="btn btn-default dropdown-toggle" type="button" id="edit_academic_event_type_btn" data-toggle="dropdown" aria-expanded="true"> <span id="edit_academic_event_type" category-id="1">Cours théorique</span> <span class="caret"></span> </button>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1" id="edit_academic_event_categories">
                          <!-- FILLED WITH ACADEMIC EVENTS CATEGORIES THROUGH AJAX -->
                        </ul>
                      </div></td>
                  </tr>
                  <tr>
                    <td class="text-bold width-80 vertical-middle">Charge de Travail</td>
                    <td><label for="edit_academic_event_workload" class="sr-only">Charge de Travail</label>
                      <input type="number" name="points" min="0" max="1000" step="1" value="30" class="form-control" id="edit_academic_event_workload" placeholder="edit_academic_event_workload"></td>
                  </tr>
                  <tr>
                    <td class="text-bold width-80 vertical-middle">Lieu</td>
                    <td><label for="edit_academic_event_place" class="sr-only">Lieu</label>
                      <input class="form-control" id="edit_academic_event_place" placeholder="Lieu de l'événement"></td>
                  </tr>
                  <tr>
                    <td class="text-bold width-80 vertical-middle">Détails</td>
                    <td><label for="edit_academic_event_details" class="sr-only">Détails</label>
                      <input class="form-control" id="edit_academic_event_details" placeholder="Détails de l'événement"></td>
                  </tr>
                  <tr id="edit_academic_event_feedback">
                    <td class="text-bold width-80">Feedback</td>
                    <td><label for="edit_academic_event_feedback_body" class="sr-only">Feedback</label>
                      <input class="form-control" id="edit_academic_event_feedback_body" placeholder="Feedback pour l'événement"></td>
                  </tr>
                  <tr id="edit_academic_event_pract_details">
                    <td class="text-bold width-80">Détails pratiques</td>
                    <td><label for="edit_academic_event_pract_details_body" class="sr-only">Détails pratiques</label>
                      <input class="form-control" id="edit_academic_event_pract_details_body" placeholder="Détails pratiques pour l'étudiants"></td>
                  </tr>
                </table>
              </form>
            </div>
          </div>
          <div class="panel panel-default">
            <div class="panel-heading" role="tab">
              <h4 class="panel-title"> <a class="collapsed" data-toggle="collapse" data-parent="#accordion-edit-academic-event" href="#edit_academic_event_pathways" aria-expanded="false" aria-controls="edit_academic_event_pathways">Sections</a> </h4>
            </div>
            <div class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree" id="edit_academic_event_pathways">
              <table id="edit_academic_event_pathways_table" class="table">
                <!--FILLED BY AJAX WITH LIST PATHWAYS OF GLOBAL EVENT-->
              </table>
            </div>
          </div>
          <div class="panel panel-default" style="margin-bottom: 10px;">
            <div class="panel-heading" role="tab">
              <h4 class="panel-title"> <a class="collapsed" data-toggle="collapse" data-parent="#accordion-edit-academic-event" href="#edit_academic_team" aria-expanded="false" aria-controls="edit_academic_team">Équipe</a> </h4>
            </div>
            <div class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo" id="edit_academic_team">
              <table id="edit_academic_event_team_table" class="table">
                <!--FILLED BY AJAX WITH LIST EVENT TEAM-->
              </table>
              <div class="modal-footer hidden text-center" id="add_member_conf_abort_buttons">
                <button type="button" class="btn btn-default" id="add_member_abort">Annuler</button>
                <button type="button" class="btn btn-primary" id="add_member_confirm" disabled="disabled">Confirmer</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <div class='text-center' id='edit_academic_event_btns'>
            <button tabindex="0" type='button' class='btn btn-primary' type="submit" id="edit_academic_event_creation_confirm_recursion" data-placement="left" data-toggle="popover" data-trigger="focus" title="Mis à jour événement récurrent" data-content="Cet événement est récurrent.">Confirmer</button>
            <button tabindex="0" type='button' class='btn btn-primary hidden' type="submit" id="edit_academic_event_creation_confirm_norecursion" onclick="edit_academic_event(false)">
            Confirmer
            </button>
            <button type='button' class='btn btn-default' data-dismiss="modal">Annuler</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
