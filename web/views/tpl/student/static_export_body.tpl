<div class="container marg-top-40">
  <div class="panel panel-primary width-70 center marg-bot-40">
    <div class="panel-heading">
      <h3 class="panel-title text-center">Export Statique</h3>
    </div>
    <div class="panel-body">
      <table class="table" id="filters">
        <tr><td colspan="2">Filtrer par:</td></tr>
         <tr><td class="width-30"><input type="checkbox" id="all_events_filter"></td><td>Tous les événements</td></tr> 
         <tr><td class="width-30"><input type="checkbox" id="date_filter" data-toggle="modal" data-target="#filter_alert"></td><td>Par date</td></tr>
         <tr><td class="width-30"><input type="checkbox" id="course_filter" data-toggle="modal" data-target="#filter_alert"></td><td>Par course</td></tr>
         <tr><td class="width-30"><input type="checkbox" id="event_type_filter" data-toggle="modal" data-target="#filter_alert"></td><td>Par type d'événement</td></tr>
         <tr><td class="width-30"><input type="checkbox" id="session_filter" data-toggle="modal" data-target="#filter_alert"></td><td>Par session</td></tr>
         <tr><td class="width-30"><input type="checkbox" id="professor_filter" data-toggle="modal" data-target="#filter_alert"></td><td>Par professeur</td></tr>     
        </table>
        <div class="text-center">
        <a href="#" class="btn btn-primary padding-6-55" role="button" id="static_export" data-toggle="popover" data-trigger="focus">Ok</a>
        </div>
    </div>
  </div>
</div>