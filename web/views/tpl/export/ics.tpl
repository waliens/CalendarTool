BEGIN:VCALENDAR
PRODID:{$prod_id}
VERSION:2.0
{foreach from=$events item=event}BEGIN:VEVENT
UID:{$event.uid}
DTSTAMP:{$event.dtstamp}
DTSTART:{$event.dtstart}
{if isset($event.dtend)}DTEND:{$event.dtend}
{/if}{if isset($event.summary)}SUMMARY:{$event.summary}
{/if}{if isset($event.location)}LOCATION:{$event.location}
{/if}{if isset($event.description)}DESCRIPTION:{$event.description}
{/if}{if isset($event.comment)}COMMENT:{$event.comment}
{/if}{if isset($event.url)}URL:{$event.url}
{/if}{if isset($event.categories)}CATEGORIES:{$event.categories}
{/if}END:VEVENT
{/foreach}
END:VCALENDAR