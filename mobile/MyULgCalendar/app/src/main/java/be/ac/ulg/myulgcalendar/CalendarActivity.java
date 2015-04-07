package be.ac.ulg.myulgcalendar;

import android.app.Activity;
import android.app.SearchManager;
import android.content.ComponentName;
import android.content.Context;
import android.content.Intent;
import android.graphics.Color;
import android.os.AsyncTask;
import android.support.v4.app.NavUtils;
import android.support.v7.app.ActionBarActivity;
import android.support.v7.app.ActionBar;
import android.os.Bundle;
import android.text.format.DateFormat;
import android.util.Log;
import android.view.Display;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.BaseAdapter;
import android.widget.Button;
import android.widget.GridView;
import android.widget.HorizontalScrollView;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.support.v7.widget.SearchView;
import android.widget.SpinnerAdapter;
import android.widget.TextView;

import java.lang.reflect.Array;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.GregorianCalendar;
import java.util.List;
import java.util.Locale;
import java.util.Timer;
import java.util.TimerTask;

/**
 * Main activity containing the four view modes.
 * It implements a single drop-down menu integrated in the action bar.
 */
public class CalendarActivity extends ActionBarActivity implements ActionBar.OnNavigationListener, View.OnClickListener
{
    // The time (in ms) between two automatic refresh of the calendar.
    private static final int REFRESH_TIME = 1*60*1000;

    private static final int DAY = 0, WEEK = 1, MONTH = 2, AGENDA = 3;
    private static int viewMode = DAY;
    private ArrayList<View>[] views = new ArrayList[4];
    private boolean portrait;

    // Orange bar references.
    private TextView currentDate;
    private ImageView previous, next;

    private GridCellAdapter adapter, adapterWeek;

    private static Calendar calendar = Calendar.getInstance(Locale.getDefault());
    private int day = calendar.get(Calendar.DAY_OF_MONTH),
            month = calendar.get(Calendar.MONTH),
            year = calendar.get(Calendar.YEAR);

    private final int[] daysOfMonth = {31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31};
    private LinearLayout daysOfWeek;

    @Override
    public boolean onCreateOptionsMenu(Menu menu)
    {
        getMenuInflater().inflate(R.menu.menu_main, menu);

        SearchManager searchManager = (SearchManager) getSystemService(Context.SEARCH_SERVICE);
        SearchView searchView = (SearchView) menu.findItem(R.id.action_search).getActionView();

        // Link the SearchActivity with the current activity.
        searchView.setSearchableInfo(searchManager.getSearchableInfo(new ComponentName(this, SearchActivity.class)));
        // Do not iconify the widget; expand it by default.
        searchView.setIconifiedByDefault(true);

        return true;
    }

    @Override
    /**
     * Called when an action bar item is selected.
     */
    public boolean onOptionsItemSelected(MenuItem item)
    {
        switch(item.getItemId())
        {
            case R.id.action_today:
                setGridCellAdapterToDate();
                return true;

            case R.id.action_profile:
                startActivity(new Intent(this, ProfileActivity.class));
                return true;

            /*
            case R.id.action_filters:
                startActivity(new Intent(this, FiltersActivity.class));
                return true;

            case R.id.action_settings:
                startActivity(new Intent(this, SettingsActivity.class));
                return true;
            */

            case R.id.action_refresh:
                new RefreshTask().execute((Void) null);
                return true;

            case R.id.action_logout:
                Request.dataBase = true;
                NavUtils.navigateUpFromSameTask(this);
                return true;

            default:
                return super.onOptionsItemSelected(item);
        }
    }

    @Override
    /**
     * Called when an item of the drop-down menu is selected.
     */
    public boolean onNavigationItemSelected(int itemPosition, long itemId)
    {
        // The previous view mode is hidden.
        for (View v : views[viewMode])
            v.setVisibility(View.INVISIBLE);

        LinearLayout eventLayout = (LinearLayout) findViewById(R.id.topLayout);
        while(eventLayout.getChildCount() > 1)
            eventLayout.removeViewAt(1);

        // The new view mode is shown.
        for (View v : views[itemPosition])
            v.setVisibility(View.VISIBLE);

        findViewById(R.id.orangeBar).setVisibility(itemPosition == AGENDA ? View.INVISIBLE: View.VISIBLE);
        viewMode = itemPosition;

        // The current date is maintained when changing the view mode.
        setGridCellAdapterToDate(day, month, year);

        return true;
    }

    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_calendar);
        getSupportActionBar().setDisplayHomeAsUpEnabled(false);

        /* Refresh task */
        Timer timer = new Timer();
        timer.scheduleAtFixedRate(new TimerTask()
        {
            @Override
            public void run()
            {
                new RefreshTask().execute((Void) null);
            }
        }, REFRESH_TIME, REFRESH_TIME);

        /* Screen orientation */
        Display d = getWindowManager().getDefaultDisplay();
        portrait = d.getWidth() < d.getHeight();

        /* Drop-down menu */
        SpinnerAdapter mSpinnerAdapter = ArrayAdapter.createFromResource(this, R.array.view_mode_list, R.layout.spinner);
        getSupportActionBar().setNavigationMode(ActionBar.NAVIGATION_MODE_LIST);
        getSupportActionBar().setListNavigationCallbacks(mSpinnerAdapter, this);

        /* View modes */
        for (int i = 0; i < views.length; i++)
            views[i] = new ArrayList<>();

        /* Orange bar */
        currentDate = (TextView) findViewById(R.id.currentDate);

        // Left arrow
        previous = (ImageView) findViewById(R.id.previous);
        previous.setOnClickListener(this);

        // Right arrow
        next = (ImageView) findViewById(R.id.next);
        next.setOnClickListener(this);

        /* Day */
        views[DAY].add(findViewById(R.id.dayScrollLayout));
        LinearLayout hourLayout = (LinearLayout) findViewById(R.id.dayLinearLayout);

        for (int hour = 0; hour < 24; hour++)
            hourLayout.addView(new DayGridcell(this, String.valueOf(hour) + ":00"));

        /* Week */
        views[WEEK].add(findViewById(R.id.weekScroll));

        LinearLayout hours = (LinearLayout) findViewById(R.id.weekHours);
        views[WEEK].add(hours);
        for (int i = -1 ; i < 24 ; i++)
        {
            Button hour = new Button(this);
            hour.setBackground(getResources().getDrawable(R.drawable.calendar_tile_small));
            RelativeLayout.LayoutParams l = new RelativeLayout.LayoutParams(200, i == -1 ? 150 : 100);
            l.setMargins(0, 0, 0, 0);
            hour.setLayoutParams(l);
            hour.setGravity(Gravity.LEFT);
            hour.setText(i == -1 ? "" : i + ":00");
            hour.setTextSize(12);
            hour.setTextColor(getResources().getColor(R.color.gray));
            hours.addView(hour);
        }

        daysOfWeek = (LinearLayout) findViewById(R.id.weekLayout);
        daysOfWeek.addView(new WeekGridCell(this, "Lundi"));
        daysOfWeek.addView(new WeekGridCell(this, "Mardi"));
        daysOfWeek.addView(new WeekGridCell(this, "Mercredi"));
        daysOfWeek.addView(new WeekGridCell(this, "Jeudi"));
        daysOfWeek.addView(new WeekGridCell(this, "Vendredi"));
        daysOfWeek.addView(new WeekGridCell(this, "Samedi"));
        daysOfWeek.addView(new WeekGridCell(this, "Dimanche"));

        /* Month */
        GridView calendarMonth = (GridView) findViewById(R.id.calendarMonth);
        views[MONTH].add(calendarMonth);

        adapter = new GridCellAdapter(getApplicationContext(), month, year);
        adapter.notifyDataSetChanged();
        calendarMonth.setAdapter(adapter);

        /* Agenda */
        views[AGENDA].add(findViewById(R.id.agendaLayout));

        /* Hide unselected view modes */
        for (int i = 0; i < views.length; i++)
            if (i != viewMode)
                for (View v : views[i])
                    v.setVisibility(View.INVISIBLE);

        findViewById(R.id.orangeBar).setVisibility(viewMode == AGENDA ? View.INVISIBLE: View.VISIBLE);

        /* Show selected view mode */
        getSupportActionBar().setSelectedNavigationItem(viewMode);

        switch(viewMode)
        {
            case DAY: currentDate.setText(day + " " + Month.values()[month] + " " + year); break;
            case WEEK: break;
            case MONTH: currentDate.setText(Month.values()[month] + " " + year); break;
            case AGENDA: break;
        }
    }

    @Override
    public void onClick(View v)
    {
        /* Left arrow in orange bar */
        if (v == previous)
        {
            switch(viewMode)
            {
                case DAY: previousDay(); break;
                case WEEK: for(int i=0;i<7;i++) previousDay(); break;
                case MONTH: previousMonth(); break;
                case AGENDA: break;
            }
        }

        /* Right arrow in orange bar */
        else if (v == next)
        {
            switch(viewMode)
            {
                case DAY: nextDay(); break;
                case WEEK: for(int i=0;i<7;i++) nextDay(); break;
                case MONTH: nextMonth(); break;
                case AGENDA: break;
            }
        }

        // the current day does not exist in the new month
        // (for instance going from january 31 to february 31)
        if (day > daysInMonth(month))
            day = daysInMonth(month);

        setGridCellAdapterToDate(day, month, year);
    }

    /**
     * Decrement the day. If the current day is 1 or less, the month is also decremented.
     * Return the new day.
     */
    private int previousDay()
    {
        if (day <= 1)
            return day = daysInMonth(previousMonth());

        return --day;
    }

    /**
     * Decrement the month. If the current month is January, the year is also decremented.
     * Return the new month.
     */
    private int previousMonth()
    {
        if (month <= 0)
        {
            year--;
            return month = 11;
        }

        return --month;
    }

    /**
     * Increment the day. If the current day is already the last day of the current month,
     * the month is also incremented. Return the new day.
     */
    private int nextDay()
    {
        if (day >= daysInMonth(month))
        {
            nextMonth();
            return day = 1;
        }

        return ++day;
    }

    /**
     * Increment the month. If the current month is December, the year is also incremented.
     * Return the new month.
     */
    private int nextMonth()
    {
        if (month >= 11)
        {
            year++;
            return month = 0;
        }

        return ++month;
    }

    /**
     * Return the number of days in the given month (0-11). Leap years are taken into account.
     */
    private int daysInMonth(int mm)
    {
        if (new GregorianCalendar(day, month, year).isLeapYear(year) && mm == 1)
            return 29;

        return daysOfMonth[mm];
    }

    /**
     * Set the date of the calendar to the current date.
     */
    private void setGridCellAdapterToDate()
    {
        Calendar c = Calendar.getInstance(Locale.getDefault());

        day = c.get(Calendar.DAY_OF_MONTH);
        month = c.get(Calendar.MONTH);
        year = c.get(Calendar.YEAR);

        setGridCellAdapterToDate(day, month, year);
    }

    /**
     * Set the date of the calendar to the given date
     */
    private void setGridCellAdapterToDate(int day, int month, int year)
    {
        calendar.set(year, month, day);

        switch(viewMode)
        {
            case DAY: updateDay(day, month, year); break;
            case WEEK: updateWeek(day, month, year); break;
            case MONTH: updateMonth(month, year); break;
            case AGENDA: updateAgenda(); break;
        }
    }

    /**
     * Update the set of events displayed by the Day view.
     */
    private void updateDay(int day, int month, int year)
    {
        currentDate.setText(day + " " + Month.values()[month] + " " + year);

        // Remove the events currently displayed
        LinearLayout topEvents = (LinearLayout) findViewById(R.id.topLayout);
        while(topEvents.getChildCount() > 1)
            topEvents.removeViewAt(1);

        RelativeLayout eventLayout = (RelativeLayout) findViewById(R.id.dayRelativeLayout);
        while(eventLayout.getChildCount() > 1)
            eventLayout.removeViewAt(1);

        ArrayList<Event> todayEvent = new ArrayList<>();

        // Display the event of the selected day.
        for (Subevent s : Profile.getAllSubevents())
        {
            // Date range events are displayed at the top
            if (s.isDateRange() && s.isDate(day, month, year))
            {
                Event e = new Event(this, s);
                e.setMinimalSize();
                topEvents.addView(e);
            }

            // Time range events are displayed inside the layout
            else if (s.isDate(day, month, year))
            {
                Event event = new Event(this, s);
                if (!s.isDeadline() && !s.oneDay && !s.isDateRange())
                {
                    Calendar end = s.getEnd(), start = s.getStart();

                    if (start.get(Calendar.YEAR) == year
                     && start.get(Calendar.MONTH) == month
                     && start.get(Calendar.DAY_OF_MONTH) == day)
                        event.setY((float) (Math.min(s.getStartingMinutes() * 1.63, 2225)));

                    else if (end.get(Calendar.YEAR) == year
                          && end.get(Calendar.MONTH) == month
                          && end.get(Calendar.DAY_OF_MONTH) == day)
                    {
                        event.setY(0);
                        event.endOfEvent = true;
                    }

                    else // middle
                    {
                        event.setY(0);
                        event.allDay = true;
                    }
                }
                else
                    event.setY((float) (Math.min(s.getStartingMinutes() * 1.63, 2225)));

                eventLayout.addView(event);
                todayEvent.add(event);
            }
        }

        detectCollisions(todayEvent);

        // Move and resize events to avoid collisions;
        for (Event e : todayEvent)
        {
            int width = (portrait ? 940 : 1645) / e.subevent.collisions;
            e.setX(120 + e.subevent.position*width);

            int height = e.subevent.isDeadline() ? 180 : e.subevent.getDuration() / 32000; //33200
            if (e.allDay) height = 24*60*2;
            else if (e.endOfEvent)
                height = (e.subevent.getEnd().get(Calendar.HOUR_OF_DAY) * 60 + e.subevent.getEnd().get(Calendar.MINUTE)) *1000*60/32000;

            e.setSize(width, (int) Math.min(height, 2400 - e.getY()));
        }
    }

    private void detectCollisions(ArrayList<Event> allEvent)
    {
        if (allEvent.isEmpty() || allEvent.get(0).subevent.collidingEvents != null)
            return;

        for (Event e1 : allEvent)
        {
            e1.subevent.collidingEvents = new ArrayList<>();

            for (Event e2 : allEvent)
                if (e1.subevent.isColliding(e2.subevent))
                    e1.subevent.collidingEvents.add(e2.subevent);
        }

        for (Event e : allEvent)
            e.subevent.updateCollisions(e.subevent.collidingEvents.size()+1, 0);
    }

    /**
     * Update the days and the set of events displayed by the week view.
     */
    private void updateWeek(int day, int month, int year)
    {
        // The position of the day in the week.
        Calendar c = new GregorianCalendar(year, month, day);
        int trailingSpaces = c.get(Calendar.DAY_OF_WEEK) - 2;

        // Put Sunday at the end of the week
        if (trailingSpaces == -1)
            trailingSpaces = 6;

        int prevMonth = month == 0 ? 11 : month-1, nextMonth = month == 11 ? 0 : month+1;
        int prevYear = month == 0 ? year-1 : year, nextYear = month == 11 ? year+1 : year;

        // From Monday to Sunday
        int theYear = year;
        for (int d = 0 ; d < 7 ; d++)
        {
            int theDay = day-trailingSpaces+d;
            int theMonth = month;
            theYear = year;

            // This day belongs to the previous month
            if (theDay <= 0)
            {
                theDay += daysInMonth(prevMonth);
                theMonth = prevMonth;
                theYear = prevYear;
            }
            // This day belongs to the next month
            else if (theDay > daysInMonth(theMonth))
            {
                theDay -= daysInMonth(theMonth);
                theMonth = nextMonth;
                theYear = nextYear;
            }

            ((WeekGridCell)daysOfWeek.getChildAt(d)).setDay(theDay, theMonth, theYear);
        }

        String monday = daysOfWeek.getChildAt(0).getTag().toString();
        String sunday = daysOfWeek.getChildAt(6).getTag().toString();
        currentDate.setText(monday + " - " + sunday + " " + String.valueOf(theYear));

        /* Clean the previous events */
        RelativeLayout events = (RelativeLayout) findViewById(R.id.weekEvents);
        while(events.getChildCount()>1)
            events.removeViewAt(1);

        /* Collect the new events */
        ArrayList<ArrayList<Event>> weekEvent = new ArrayList<>();
        for (int d = 0 ; d < 7 ; d++)
            weekEvent.add(new ArrayList<Event>());

        for (Subevent s : Profile.getAllSubevents())
            if (s.getStart().get(Calendar.WEEK_OF_YEAR) == c.get(Calendar.WEEK_OF_YEAR)
             && s.getStart().get(Calendar.YEAR) == c.get(Calendar.YEAR))
                weekEvent.get(s.getDayOfWeek()).add(new Event(this, s));

        /* Create the new events */
        for (ArrayList<Event> a : weekEvent)
        {
            detectCollisions(a);
            for (Event e : a)
            {
                int width = 305 / e.subevent.collisions;
                e.setX(e.subevent.getDayOfWeek() * 311 + e.subevent.position * width);
                e.setY(130 + (float) (Math.min(e.subevent.getStartingMinutes() * 1.67, 2225)));

                int duration = e.subevent.getDuration();
                int height = e.subevent.isDeadline()? 180 : duration / 32000; //32900

                // Several days events
                if (!e.subevent.isDeadline() && height > 2550 - e.getY())
                {
                    // first day
                    e.setSize(width, (int) (2550 - e.getY()));
                    duration /= 32000;
                    duration -= 2550 - e.getY();

                    // Middle days
                    int i = 1;
                    while (duration > 2550-130)
                    {
                        Event middle = new Event(this, e.subevent);
                        middle.setX((e.subevent.getDayOfWeek()+i) * 311 + e.subevent.position * width);
                        middle.setY(130+10);
                        middle.setSize(width, 2550 - 130);

                        if (middle.subevent.collisions > 1)
                            middle.removeText();
                        events.addView(middle);

                        duration -= 2550+130+10;
                        i++;
                    }

                    // End day
                    Event end = new Event(this, e.subevent);
                    end.setX((e.subevent.getDayOfWeek()+i) * 311 + e.subevent.position * width);
                    end.setY(130+10);
                    end.setSize(width, duration-(130/2));
                    if (end.subevent.collisions > 1)
                        end.removeText();
                    events.addView(end);
                }
                else
                    e.setSize(width, height);

                if (e.subevent.collisions > 1)
                    e.removeText();
                events.addView(e);
            }
        }
    }

    /**
     * Update the date written in the orange bar with the given month and year.
     * The events displayed by the Month view are already updated by the GridCellAdapter.
     */
    private void updateMonth(int month, int year)
    {
        currentDate.setText(Month.values()[month] + " " + year);

        adapter.setDate(month, year);
        adapter.notifyDataSetChanged();
    }

    /**
     * Update the set events displayed by the Agenda view
     */
    private void updateAgenda()
    {
        LinearLayout agenda = ((LinearLayout) findViewById(R.id.agendaList));
        agenda.removeAllViews();

        for (Subevent s : Profile.getAllSubevents())
        {
            // Past events are not displayed
            if (s.getStart().compareTo(calendar) < 0)
                continue;

            // Position the event in chronological order
            int index = 0;
            while(index < agenda.getChildCount())
                if (((Event)agenda.getChildAt(index+1)).subevent.getStart().compareTo(s.getStart()) > 0)
                    break;
                else
                    index += 2;

            // Add the event
            agenda.addView((new Title(this, s.getAgendaDate())), index);
            Event e = new Event(this, s);
            e.setMinimalSize();
            agenda.addView(e, index+1);
        }
    }

    /**
     * Adapter of the month grid
     */
    public class GridCellAdapter extends BaseAdapter implements View.OnClickListener
    {
        protected final Context context;
        protected final List<String> cellList = new ArrayList<>();
        protected final List<List<Integer>> eventList = new ArrayList<>();
        protected int currentDayOfMonth, currentMonth, currentYear;

        public GridCellAdapter(Context context, int month, int year)
        {
            super();
            this.context = context;

            Calendar c = Calendar.getInstance(Locale.FRANCE);
            this.currentDayOfMonth = c.get(Calendar.DAY_OF_MONTH);
            this.currentMonth = c.get(Calendar.MONTH);
            this.currentYear = c.get(Calendar.YEAR);

            for (int day = 1 ; day <= 42 ; day++)
                eventList.add(new ArrayList<Integer>());

            //setDate(month, year);
        }

        @Override
        public int getCount()
        {
            return cellList.size();
        }

        @Override
        public String getItem(int position)
        {
            return cellList.get(position);
        }

        @Override
        public long getItemId(int position)
        {
            return position;
        }

        /**
         * Fill the grid with the days of the given month. The first and last weeks are
         * completed with the days of the previous and next month respectively.
         */
        public void setDate(int mm, int yy)
        {
            cellList.clear();
            for (List l : eventList)
                l.clear();

            // The number of days to leave blank at the start of this month.
            int trailingSpaces = new GregorianCalendar(yy, mm, 0).get(Calendar.DAY_OF_WEEK) - 1;
            int prevMonth = mm == 0 ? 11 : mm-1, nextMonth = mm == 11 ? 0 : mm+1;
            int prevYear = mm == 0 ? yy-1 : yy, nextYear = mm == 11 ? yy+1 : yy;

            // Trailing month days
            for (int i = 1 ; i <= trailingSpaces ; i++)
                cellList.add(String.valueOf(daysInMonth(prevMonth) - trailingSpaces + i) +'-'+prevMonth+'-'+prevYear+ "-lightgray");

            // Current month days
            for (int i = 1; i <= daysInMonth(mm); i++)
            {
                if (i == currentDayOfMonth && mm == currentMonth && yy == currentYear)
                    cellList.add(String.valueOf(i) + '-'+mm+'-'+yy+ "-#e33d1b");
                else
                    cellList.add(String.valueOf(i) + '-'+mm+'-'+yy+ "-gray");
            }

            // Leading month days
            for (int i = 0 ; i < cellList.size() % 7 ; i++)
                cellList.add(String.valueOf(i + 1) +'-'+nextMonth+'-'+nextYear + "-lightgray");

            // Update events
             for (Subevent s : Profile.getAllSubevents())
             {
                 for (int i = 0; i < trailingSpaces; i++)
                     if (s.isDate(daysInMonth(prevMonth) - trailingSpaces +i+1, prevMonth, prevYear))
                         eventList.get(i).add(s.getColor());

                 for (int day = 0; day < daysInMonth(mm); day++)
                     if (s.isDate(day+1, mm, yy))
                         eventList.get(day + trailingSpaces).add(s.getColor());

                 for (int i = 1; i + daysInMonth(mm) + trailingSpaces -1 < eventList.size(); i++)
                 {
                     if (s.isDate(i, nextMonth, nextYear))
                         eventList.get(daysInMonth(mm) + trailingSpaces + i-1).add(s.getColor());
                 }
             }
        }

        @Override
        public View getView(int position, View convertView, ViewGroup parent)
        {
            View row = convertView;
            if (row == null)
            {
                LayoutInflater inflater = (LayoutInflater) context.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
                row = inflater.inflate(R.layout.gridcell_month, parent, false);
            }

            // Get a reference to the gridcell.
            LinearLayout gridcell = (LinearLayout) row.findViewById(R.id.gridcell);
            gridcell.setOnClickListener(this);

            // Set the gridCell text and color.
            TextView text = (TextView) row.findViewById(R.id.text);
            String[] day_color = cellList.get(position).split("-");
            text.setText(day_color[0]);
            gridcell.setTag(day_color[0] + '-' + day_color[1] + '-' + day_color[2]);
            text.setTextColor(Color.parseColor(day_color[3]));

            // Set the events
            LinearLayout slot = (LinearLayout) row.findViewById(R.id.slot);
            slot.removeAllViews();

            for (int color : eventList.get(position))
            {
                ImageView view = new ImageView(context);
                view.setImageDrawable(getResources().getDrawable(color));
                slot.addView(view);
            }

            return row;
        }

        /**
         * Called when a grid cell is selected.
         */
        @Override
        public void onClick(View view)
        {
            // Open the Day view centered on the selected day.
            String[] dayInfo = view.getTag().toString().split("-");
            day = Integer.valueOf(dayInfo[0]);
            month = Integer.valueOf(dayInfo[1]);
            year = Integer.valueOf(dayInfo[2]);
            getSupportActionBar().setSelectedNavigationItem(DAY);
        }
    }

    /* Called when a WeekGridCell is selected */
    public void onWeekClick(int day, int month, int year)
    {
        this.day = day;
        this.month = month;
        this.year = year;
        getSupportActionBar().setSelectedNavigationItem(DAY);
    }

    /**
     * Represents an asynchronous task used to refresh the calendar.
     */
    public class RefreshTask extends AsyncTask<Void, Void, Boolean>
    {
        @Override
        protected Boolean doInBackground(Void... params)
        {
            Request.dataBase = true;
            new Profile();

            /*switch(viewMode)
            {
                case DAY: updateDay(day, month, year); break;
                case WEEK: updateWeek(day, month, year); break;
                case MONTH: updateMonth(month, year); break;
                case AGENDA: break;
            }*/

            return true;
        }
    }
}
