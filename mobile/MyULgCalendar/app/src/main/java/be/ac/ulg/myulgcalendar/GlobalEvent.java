package be.ac.ulg.myulgcalendar;

import android.util.Log;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.Calendar;
import java.util.GregorianCalendar;

public class GlobalEvent
{
    private String id, code, name, team, period, description, feedback, language, year,
                   workload;

    private ArrayList<String> pathways = new ArrayList<>();

    private ArrayList<Subevent> eventList = new ArrayList<>();

    public GlobalEvent(JSONObject json) throws JSONException
    {
        if (json == null)
            return;

        id = json.getString("id");
        code = json.getString("id_ulg");
        name = json.getString("name_short");

        team = json.getString("name");
        int index =  Math.max(team.indexOf("Th,"), Math.max(team.indexOf("Pr,"), Math.max(team.indexOf("j.,"), team.indexOf("o.,"))));
        team = team.substring(index+4);

        period = json.getString("period");
        description = json.getString("description");
        feedback = json.getString("feedback");
        language = json.getString("language");
        year = json.getString("acad_year");
        workload = json.getJSONObject("workload").toString();

        JSONArray pathArray = json.getJSONArray("pathways");
        for (int i = 0 ; i < pathArray.length() ; i++)
            pathways.add(pathArray.getJSONObject(i).getString("name_short"));

        JSONArray eventArray = json.getJSONArray("subevents");
        for (int i = 0 ; i < eventArray.length() ; i++)
        {
            JSONObject event = eventArray.getJSONObject(i);
            String eventId = event.getString("id");
            String recurrence = event.getString("recurrence_type");

            JSONObject view = Request.VIEW_SUB_EVENT.get("&event="+eventId);
            if (view == null)
                return;

            boolean deadline = view.getString("deadline").equals("true");

            if (recurrence.equals("6"))
                eventList.add(new Subevent(view));
            else
            {
                String[] startRec = view.getString("start_recurrence").split(" ");
                String[] startDate = startRec[0].split("-");

                int startYear = Integer.valueOf(startDate[0]);
                int startMonth = Integer.valueOf(startDate[1]);
                int startDay = Integer.valueOf(startDate[2]);

                GregorianCalendar curEnd = null;
                if (!deadline)
                {
                    String[] endDate = view.getString("endDay").split("-");

                    int endYear = Integer.valueOf(endDate[0]);
                    int endMonth = Integer.valueOf(endDate[1]);
                    int endDay = Integer.valueOf(endDate[2]);

                    curEnd = new GregorianCalendar(endYear, endMonth - 1, endDay);
                }

                String[] endRec = view.getString("end_recurrence").split(" ");
                String[] endDate = endRec[0].split("-");

                int endYear = Integer.valueOf(endDate[0]);
                int endMonth = Integer.valueOf(endDate[1]);
                int endDay = Integer.valueOf(endDate[2]);

                GregorianCalendar curStart = new GregorianCalendar(startYear, startMonth-1, startDay);
                GregorianCalendar end = new GregorianCalendar(endYear, endMonth-1, endDay);
                GregorianCalendar firstStart = (GregorianCalendar) curStart.clone();

                while (curStart.compareTo(end) <= 0)
                {
                    eventList.add(new Subevent(view, curStart, curEnd, firstStart));

                    switch(recurrence)
                    {
                        case "1": curStart.add(Calendar.DATE, 1); curEnd.add(Calendar.DATE, 1); break;
                        case "2": curStart.add(Calendar.DATE, 7); curEnd.add(Calendar.DATE, 7); break;
                        case "3": curStart.add(Calendar.DATE, 14); curEnd.add(Calendar.DATE, 14); break;
                        case "4": curStart.add(Calendar.MONTH, 1); curEnd.add(Calendar.MONTH, 1); break;
                        case "5": curStart.add(Calendar.YEAR, 1); curEnd.add(Calendar.YEAR, 1); break;
                    }
                }
            }
        }
    }

    public String getId()
    {
        return id;
    }

    public String getCode()
    {
        return code;
    }

    public String getName()
    {
        return name;
    }

    public String getTeam()
    {
        return team;
    }

    public String getDescription()
    {
        return description.isEmpty() ? "Aucune description disponible."
                                     : description;
    }

    public ArrayList<Subevent> getEventList()
    {
        return eventList;
    }

    @Override
    public String toString()
    {
        return name;
    }
}
