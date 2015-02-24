BEGIN:VCALENDAR
PRODID:{$prod_id}
VERSION:2.0
{if isset($timezone)}VTIMEZONE:{$timezone}
{foreach from=$events item=event}
BEGIN:VEVENT
UID:{$event.uid} // unique identifier, required, id@ct.com
DTSTAMP:{$event.dtstamp} // last update time, required
DTSTART:{$event.dtstart} // start time of the event, required if method no defined
DTEND:{$event.dtend} // event end
LOCATION:{$event.location} // event location
DESCRIPTION:{$event.description} // text description
COMMENT:{$event.comment} // event comment
URL:{$event.url} // url
CATEGORIES:{$event.category} // event category
END:VEVENT
{/foreach}
END:VCALENDAR