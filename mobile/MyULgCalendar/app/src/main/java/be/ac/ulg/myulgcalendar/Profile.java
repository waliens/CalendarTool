package be.ac.ulg.myulgcalendar;

import android.util.Log;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.Calendar;
import java.util.GregorianCalendar;

public class Profile
{
    private static boolean isProfessor;

    private static String firstName = "", lastName = "", pathway;

    private static ArrayList<GlobalEvent> mandatoryCourses = new ArrayList<>();
    private static ArrayList<GlobalEvent> optionalCourses = new ArrayList<>();
    private static ArrayList<Subevent> privateEvents = new ArrayList<>();

    public Profile()
    {
        ArrayList<GlobalEvent> mandatoryCourses_new = new ArrayList<>();
        ArrayList<GlobalEvent> optionalCourses_new = new ArrayList<>();
        ArrayList<Subevent> privateEvents_new = new ArrayList<>();

        //isProfessor = (id.charAt(0) == 'u');

        JSONObject json = Request.GET_PROFESSOR_PROFILE.get();

        isProfessor = !(json == null || json.toString().contains("Access denied"));
        if (!isProfessor)
            json = Request.GET_STUDENT_PROFILE.get();

        if (json == null)
            return;

        try {
            firstName = json.getString("firstName");
            lastName = json.getString("lastName");

            if (isProfessor())
            {
                JSONArray courses = json.getJSONArray("courses");

                for (int i = 0; i < courses.length(); i++)
                {
                    String courseId = courses.getJSONObject(i).getString("id");
                    mandatoryCourses_new.add(new GlobalEvent(Request.VIEW_GLOBAL_EVENT.get("&event=" + courseId)));
                }
            } else
            {
                pathway = json.getJSONObject("pathway").getString("nameLong");

                JSONObject courses = json.getJSONObject("courses");

                JSONArray mandatory = courses.getJSONArray("mandatory");
                for (int i = 0; i < mandatory.length(); i++)
                {
                    String courseId = mandatory.getJSONObject(i).getString("id");
                    mandatoryCourses_new.add(new GlobalEvent(Request.VIEW_GLOBAL_EVENT.get("&event=" + courseId)));
                }

                /*JSONArray optional = courses.getJSONArray("optional");
                for (int i = 0; i < optional.length(); i++)
                {
                    String courseId = optional.getJSONObject(i).getString("id");
                    optionalCourses.add(new GlobalEvent(Request.VIEW_GLOBAL_EVENT.get("&event=" + courseId)));
                }*/
            }

            if (isProfessor)
                privateEvents_new.addAll(academicEvents(json.getJSONArray("indep_events"), false));
            else
                privateEvents_new.addAll(academicEvents(Request.GET_PRIVATE_EVENTS.get().getJSONArray("events"), true));
        }
        catch (JSONException | NullPointerException e)
        {
            e.printStackTrace();
        }

        GlobalEventList.clear();
        GlobalEventList.addItem(mandatoryCourses_new);
        GlobalEventList.addItem(optionalCourses_new);

        // Update
        if (Request.dataBase)
        {
            mandatoryCourses = mandatoryCourses_new;
            optionalCourses = optionalCourses_new;
            privateEvents = privateEvents_new;
        }
    }

    public ArrayList<Subevent> academicEvents(JSONArray events, boolean privat) throws JSONException
    {
        ArrayList<Subevent> list = new ArrayList<>();

        for (int i = 0; i < events.length(); i++)
        {
            String eventId = events.getJSONObject(i).getString("id");
            JSONObject event = privat ? Request.VIEW_PRIVATE_EVENT.get("&event=" + eventId)
                                      : Request.VIEW_INDEP_EVENT.get("&event=" + eventId);

            String recurrence = event.getString("recurrence");
            boolean deadline = event.getString("deadline").equals("true");

            // Single event
            if (recurrence.equals("6"))
                list.add(new Subevent(event));

            // Recurrent event
            else
            {
                String[] startRec = event.getString("start_recurrence").split(" ");
                String[] startDate = startRec[0].split("-");

                int startYear = Integer.valueOf(startDate[0]);
                int startMonth = Integer.valueOf(startDate[1]);
                int startDay = Integer.valueOf(startDate[2]);

                GregorianCalendar curEnd = null;
                if (!deadline)
                {
                    String[] endDate = event.getString("endDay").split("-");

                    int endYear = Integer.valueOf(endDate[0]);
                    int endMonth = Integer.valueOf(endDate[1]);
                    int endDay = Integer.valueOf(endDate[2]);

                    curEnd = new GregorianCalendar(endYear, endMonth - 1, endDay);
                }

                String[] endRec = event.getString("end_recurrence").split(" ");
                String[] endDate = endRec[0].split("-");

                int endYear = Integer.valueOf(endDate[0]);
                int endMonth = Integer.valueOf(endDate[1]);
                int endDay = Integer.valueOf(endDate[2]);

                GregorianCalendar curStart = new GregorianCalendar(startYear, startMonth-1, startDay);
                GregorianCalendar end = new GregorianCalendar(endYear, endMonth-1, endDay);
                GregorianCalendar firstStart = (GregorianCalendar) curStart.clone();

                while (curStart.compareTo(end) <= 0)
                {
                    list.add(new Subevent(event, curStart, curEnd, firstStart));

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

        return list;
    }

    public static boolean isProfessor()
    {
        return isProfessor;
    }

    public static String getName()
    {
        return firstName.isEmpty() ? "Marco Rossi"
                                   : firstName + " " + lastName;
    }

    public static String getPathway()
    {
        return pathway;
    }

    public static ArrayList<Subevent> getPrivateEvents()
    {
        return privateEvents;
    }

    public static ArrayList<Subevent> getAllSubevents()
    {
        ArrayList<Subevent> allEvents = new ArrayList<>();

        if (Request.dataBase)
        {
            for (GlobalEvent g : mandatoryCourses)
                allEvents.addAll(g.getEventList());

            for (GlobalEvent g : optionalCourses)
                allEvents.addAll(g.getEventList());

            allEvents.addAll(privateEvents);
        }

        return allEvents;
    }
}
