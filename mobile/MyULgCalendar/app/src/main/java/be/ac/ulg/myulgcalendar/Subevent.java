package be.ac.ulg.myulgcalendar;

import android.util.Log;

import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.Calendar;
import java.util.GregorianCalendar;
import java.util.HashMap;
import java.util.Locale;
import java.util.Map;

public class Subevent
{
    public static Map<String, Subevent> all = new HashMap<String, Subevent>();

    private String id, name, description, place, recurrence, type;
    public String note;
    private EventCategory category;
    private boolean deadline;

    private Calendar start = Calendar.getInstance(Locale.FRANCE),
                     end = Calendar.getInstance(Locale.FRANCE),
                     first;

    public ArrayList<Subevent> collidingEvents;
    public int collisions = 1, position = 0;

    public boolean oneDay;

    public Subevent(JSONObject json) throws JSONException
    {
        this(json, null, null, null);
    }

    public Subevent(JSONObject json, Calendar recStart, Calendar recEnd, Calendar first) throws JSONException
    {
        if (json == null)
            return;

        id = json.getString("id");
        all.put(id, this);

        name = json.getString("name");
        description = json.getString("description");
        place = json.getString("place");
        deadline = json.getString("deadline").equals("true");
        recurrence = json.getString("recurrence");
        type = json.getString("type");

        String[] startDay = json.getString("startDay").split("-");
        String[] startTime = json.getString("startTime").split(":");
        String[] endDay = json.getString("endDay").split("-");
        String[] endTime = json.getString("endTime").split(":");

        // Single event
        if (recStart == null)
        {
            start.set(Integer.valueOf(startDay[0]), Integer.valueOf(startDay[1]) - 1, Integer.valueOf(startDay[2]),
                      Integer.valueOf(startTime[0]), Integer.valueOf(startTime[1]));

            if (!deadline)
                end.set(Integer.valueOf(endDay[0]), Integer.valueOf(endDay[1])-1, Integer.valueOf(endDay[2]),
                        Integer.valueOf(endTime[0]), Integer.valueOf(endTime[1]));
        }
        // Recurrent event
        else
        {
            start = (Calendar) recStart.clone();
            start.set(Calendar.HOUR_OF_DAY, Integer.valueOf(startTime[0]));
            start.set(Calendar.MINUTE, Integer.valueOf(startTime[1]));

            end = (Calendar) recEnd.clone();
            end.set(Calendar.HOUR_OF_DAY, Integer.valueOf(endTime[0]));
            end.set(Calendar.MINUTE, Integer.valueOf(endTime[1]));

            this.first = first;
        }

        category = EventCategory.get(json.getString("category_name"));
        note = json.getString("annotation");

        oneDay = !recurrence.equals("6")
              || (start.get(Calendar.DAY_OF_MONTH) == end.get(Calendar.DAY_OF_MONTH)
              && start.get(Calendar.MONTH) == end.get(Calendar.MONTH)
              && start.get(Calendar.YEAR) == end.get(Calendar.YEAR));
    }

    public String getId()
    {
        return id;
    }

    public String getName()
    {
        return name;
    }

    public String getDescription()
    {
        return description.isEmpty() ? "Aucune description disponible"
                                     : description;
    }

    public String getCategory()
    {
        return category.getName();
    }

    public boolean isDateRange()
    {
        return type.equals("date_range");
    }

    public int getColor()
    {
        return category.getColor();
    }

    public String getNote()
    {
        return note.isEmpty() ? "Ajouter une note"
                              : note;
    }

    public String getPlace()
    {
        return place;
    }

    public Calendar getStart()
    {
        return start;
    }

    public Calendar getEnd() { return end; }

    public String getDate()
    {
        String s = "";
        switch(recurrence)
        {
            case "1": s += "Tous les jours "; break;
            case "2": s += "Toutes les semaines "; break;
            case "3": s += "Toutes les deux semaines "; break;
            case "4": s += "Chaque mois "; break;
            case "5": s += "Chaque année "; break;
        }

        int min = start.get(Calendar.MINUTE);

        // Start day
        if (!deadline && !recurrence.equals("6"))
            s += "du " + first.get(Calendar.DAY_OF_MONTH) + " " + Month.values()[first.get(Calendar.MONTH)]
               + " " + first.get(Calendar.YEAR);
        else
            s += start.get(Calendar.DAY_OF_MONTH) + " " + Month.values()[start.get(Calendar.MONTH)]
               + " " + start.get(Calendar.YEAR);

        // End day
        if (!deadline && (!oneDay || !recurrence.equals("6")))
            s += " au " + end.get(Calendar.DAY_OF_MONTH) + " " + Month.values()[end.get(Calendar.MONTH)]
                    + " " + end.get(Calendar.YEAR);

        if (isDateRange())
            return s;

        // Start hour
        s += ", " + start.get(Calendar.HOUR_OF_DAY) + ":" + (min < 10 ? "0"+min : min);

        // End hour
        min = end.get(Calendar.MINUTE);
        if (!deadline)
            return s + " à " + end.get(Calendar.HOUR_OF_DAY) + ":" + (min < 10 ? "0"+min : min);

        return s;
    }

    public String getAgendaDate()
    {
        if ((isDateRange() && recurrence.equals("6")) || !oneDay)
            return getDate();

        int min = start.get(Calendar.MINUTE);

        String s = start.get(Calendar.DAY_OF_MONTH) + " " + Month.values()[start.get(Calendar.MONTH)]
                 + " " + start.get(Calendar.YEAR);

        if (deadline || isDateRange())
            return s;

        s += ", " + start.get(Calendar.HOUR_OF_DAY) + ":" + (min < 10 ? "0"+min : min);

        min = end.get(Calendar.MINUTE);

        return s + " à " + end.get(Calendar.HOUR_OF_DAY) + ":" + (min < 10 ? "0"+min : min);
    }

    public boolean isDate(int day, int month, int year)
    {
        // One day events
        if (day == start.get(Calendar.DAY_OF_MONTH)
            && month == start.get(Calendar.MONTH)
            && year == start.get(Calendar.YEAR))
            return true;

        if (deadline)
            return false;

        // Date-range events
        Calendar date = (Calendar) end.clone();
        date.set(Calendar.YEAR, year);
        date.set(Calendar.MONTH, month);
        date.set(Calendar.DAY_OF_MONTH, day);

        if (!oneDay)
            return date.compareTo(start) >= 0 && date.compareTo(end) <= 0;

        return false;
    }

    public int getDayOfWeek()
    {
        int day = start.get(Calendar.DAY_OF_WEEK) - 2;
        return day == -1 ? 6 : day;
    }

    public boolean isDeadline()
    {
        return deadline;
    }

    public int getStartingMinutes()
    {
        return start.get(Calendar.HOUR_OF_DAY) * 60 + start.get(Calendar.MINUTE);
    }

    public int getEndingMinutes()
    {
        return end.get(Calendar.HOUR_OF_DAY) * 60 + end.get(Calendar.MINUTE);
    }

    public int getDuration()
    {
        if (!recurrence.equals("6"))
        {
            Calendar endSameDay = (Calendar) start.clone();
            endSameDay.set(Calendar.HOUR_OF_DAY, end.get(Calendar.HOUR_OF_DAY));
            endSameDay.set(Calendar.MINUTE, end.get(Calendar.MINUTE));

            return (int) (endSameDay.getTimeInMillis() - start.getTimeInMillis());
        }

        return (int) (end.getTimeInMillis() - start.getTimeInMillis());
    }

    public boolean isColliding(Subevent other)
    {
        int thisStart = getStartingMinutes(), thisEnd = deadline ? thisStart+90 : getEndingMinutes();
        int otherStart = other.getStartingMinutes(), otherEnd = other.deadline ? otherStart+90 : other.getEndingMinutes();

        return (otherStart > thisStart  && otherStart < thisEnd)
            || (otherEnd > thisStart && otherEnd < thisEnd)
            || (otherStart < thisStart && otherEnd > thisEnd);
    }

    public void updateCollisions(int c, int pos)
    {
        if (collisions < c)
        {
            collisions = c;
            position = pos;

            for (Subevent e : collidingEvents)
                e.updateCollisions(c, pos+1);
        }
    }
}
