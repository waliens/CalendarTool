<!-- EVENT INFO ALERT -->

<div class="modal fade" id="event_info" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="event-title"><!--EVENT TITLE--></h4>
      </div>
      <!--modal body-->
      <div> 
        <!-- Table -->
        <table class="table">
          <tr>
            <td class="text-bold width-80">Quand</td>
            <td><div id="event_time"><span id="startDate_label" class="float-left-10padright text-underline">Commence</span>
                <div id="startDate" style="float:none"></div>
                <span id="endDate_label" class="float-left-10padright text-underline">Se termine</span>
                <div id="endDate" style="float:none"></div>
              </div></td>
          </tr>
          <tr id="deadline_public_event"><td class="text-bold">Deadline</td><td><input type="checkbox" aria-label="" disabled="disabled"></td></tr>
          <tr>
            <td class="text-bold width-80">Où</td>
            <td><div id="event_place"></div></td>
          </tr>
          <tr>
            <td class="text-bold width-80">Categorie</td>
            <td><div id="event_category"></div></td>
          </tr>
          <tr>
            <td class="text-bold width-80">Professeur</td>
            <td><div id="event_owner"></div></td>
          </tr>
          <tr>
            <td class="text-bold width-80">Plus info</td>
            <td><div id="event_details"></div></td>
          </tr>
        </table>
      </div>
      <div class="modal-footer text-center" id="add_notes">
        <button type="button" class="btn btn-primary" onclick="add_note()">Ajouter note</button>
      </div>
      <div id="notes">
        <table class="table">
          <tr>
            <td class="text-bold width-80 vertical-middle">Note</td>
            <td><div id="edit_note" class="float-left-10padright"><a class="edit" onclick="edit_note()"></a></div>
              <div id="delete_note"><a tabindex="0" role="button"  class="delete" data-placement="right" data-toggle="popover" data-trigger="focus" data-title="Supprimer la note" data-content="Êtes-vous sûr de vouloir supprimer la note?"></a></div></td>
          </tr>
          <tr>
            <td colspan="2"><div id="notes_body"></div>
              <div class="modal-footer text-center hidden" id="mod_notes_btns">
                <button type="button" class="btn btn-primary" onclick="save_note()">Confirmer</button>
                <button type="button" class="btn btn-default" onclick="abort_note()">Annuler</button>
              </div></td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- PRIVATE EVENT ALERT -->
<div class="modal fade" id="private_event" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="private_event_modal_header">Nouvel événement</h4>
        <div id='edit_private_event' class="hidden float-left-10padright" disabled=false><a class='edit' onclick='edit_private_event()'></a></div>
        <div id='delete_private_event' class="hidden" disabled=false><a tabindex="0" role="button"  class="delete" data-placement="right" data-toggle="popover" data-trigger="focus" data-title="Supprimer l'événement privé" data-content="Êtes-vous sûr de vouloir supprimer cet événement?"></a></div>
      </div>
      <!--modal body-->
      <div>
        <form class="form-group">
          <!-- Table -->
          <table class="table">
            <tr>
              <td class="text-bold width-80 vertical-middle">Titre</td>
              <td><label for="private_event_title" class="sr-only">Titre</label>
                <input id="private_event_title" class="form-control" placeholder="Titre de l'événement" required autofocus></td>
            </tr>
            <tr>
              <td class="text-bold width-80">Quand</td>
              <td><table id="private_event_time">
                  <tr>
                    <td class="width-80 text-underline">Commence</td>
                    <td id="new_event_startDate"><label for="new_event_startDate_datepicker" class="sr-only">Commence</label>
                      <input id="private_event_startDate_datepicker" onclick="setSens('private_event_endDate_datepicker', 'max', 'private_event');">
                      <label for="private_event_startHour" class="sr-only">Commence Heure</label>
                      <input class="marging-10-0 time" id="private_event_startHour" placeholder="HH:MM" data-time-format="H:i"></td>
                  </tr>
                  <tr>
                    <td class="width-80 text-underline">Se termine</td>
                    <td id="private_event_endDate"><label for="private_event_endDate_datepicker" class="sr-only">Se termine</label>
                      <input class="marging-10-0" id="private_event_endDate_datepicker" onclick="setSens('private_event_startDate_datepicker', 'min','private_event');">
                      <label for="private_event_endHour" class="sr-only">Se termine Heure</label>
                      <input class="marging-10-0 time" id="private_event_endHour" placeholder="HH:MM" data-time-format="H:i"></td>
                  </tr>
                  <tr id="deadline"><td>Deadline</td><td><input type="checkbox" aria-label="" onclick="deadline();"></td></tr>
                </table></td>
                </tr>
            <tr>
            <td class="text-bold width-80 vertical-middle">Récurrence</td>
              <td>
              <div class="float-left-10padright">
                <div class="dropdown">
                  <button class="btn btn-default dropdown-toggle" type="button" id="recurrence_btn" data-toggle="dropdown" aria-expanded="true">
                    <span id="recurrence" recurrence-id="6">jamais</span>
                    <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                  	<li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="updateRecurrence()" recurrence-id="6">jamais</a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="updateRecurrence()" recurrence-id="1">tous les jours</a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="updateRecurrence()" recurrence-id="2">toutes les semaines</a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="updateRecurrence()" recurrence-id="3">toutes les deux semaines</a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="updateRecurrence()" recurrence-id="4">tous les mois</a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="updateRecurrence()" recurrence-id="5">tous les ans</a></li>
                  </ul>
                </div>
                </div>
                <div id="recurrence_end_td" class="hidden float-left-10padright">
                  <div class="col-lg-6">
                    <div class="input-group" style="width:180px">
                      <input id="recurrence_end" class="form-control border-radius-4" placeholder="Fin de la récurrence?" size="45">
                    </div>
                    <!-- /input-group --> 
                  </div>
                  <!-- /.col-lg-6 --> 
                </div>
                </td>
            </tr>
            <tr>
              <td class="text-bold width-80 vertical-middle">Categorie</td>
              <td>
              <div class="dropdown">
                  <button class="btn btn-default dropdown-toggle" type="button" id="private_event_type_btn" data-toggle="dropdown" aria-expanded="true">
                    <span id="private_event_type" category-id="11">Travail</span>
                    <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                  	<li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="changePrivateEventType()" category-id="11">Travail</a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="changePrivateEventType()" category-id="9">Sport</a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="changePrivateEventType()" category-id="10">Chapi</a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="changePrivateEventType()" category-id="12">Restaurant</a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="changePrivateEventType()" category-id="13">Soirée</a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="changePrivateEventType()" category-id="14">Personnel</a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="changePrivateEventType()" category-id="15">Loisir</a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="changePrivateEventType()" category-id="16">Musique</a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="changePrivateEventType()" category-id="17">Anniversaire</a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="changePrivateEventType()" category-id="18">Autre</a></li>
                  </ul>
                </div>
                </td>
            </tr>
            <tr>
              <td class="text-bold width-80 vertical-middle">Lieu</td>
              <td><label for="private_event_place" class="sr-only">Lieu</label>
                <input class="form-control" id="private_event_place" placeholder="Lieu de l'événement"></td>
            </tr>
            <tr>
              <td class="text-bold width-80 vertical-middle">Plus info</td>
              <td><label for="private_event_details" class="sr-only">Plus info</label>
                <input class="form-control" id="private_event_details" placeholder="Détails de l'événement"></td>
            </tr>
            <tr id="notes">
              <td class="text-bold width-80">Note</td>
              <td><label for="private_notes_body" class="sr-only">Note</label>
                <input class="form-control" id="private_notes_body" placeholder="Notes de l'événement"></td>
            </tr>
            <tr>
              <td colspan="2"><div class='text-center' id='edit_event_btns'>
                  <button type='button' class='btn btn-primary' type="submit" disabled="disabled" onclick="create_private_event();">
                  Confirmer
                  </button>
                  <button type='button' class='btn btn-default' data-dismiss="modal">Annuler</button>
                </div></td>
            </tr>
          </table>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- Filter Alert -->
<div class="modal fade" id="filter_alert" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><!--ALERT TITLE--></h4>
      </div>
      <div class="modal-body">
        <!--ALERT CONTENT-->
      </div>
      <div class="modal-footer text-center">
        <a role="button" tabindex="0" class="btn btn-primary padding-6-55" id="filter_alert_btn" data-placement="top" data-toggle="popover" data-trigger="focus" data-dismiss="modal" disabled=true>Ok</a>
      </div>
    </div>
  </div>
</div>
