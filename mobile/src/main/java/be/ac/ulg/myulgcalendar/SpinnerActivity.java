package be.ac.ulg.myulgcalendar;

import android.content.Context;
import android.graphics.Color;
import android.support.v7.app.ActionBarActivity;
import android.support.v7.app.ActionBar;
import android.os.Bundle;
import android.text.format.DateFormat;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.BaseAdapter;
import android.widget.GridView;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.SpinnerAdapter;
import android.widget.TextView;

import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.GregorianCalendar;
import java.util.HashMap;
import java.util.List;
import java.util.Locale;


public class SpinnerActivity extends ActionBarActivity implements ActionBar.OnNavigationListener, View.OnClickListener
{
    private int viewMode = 0;
    private ArrayList<View>[] views = new ArrayList[4];

    private TextView currentDate;
    private ImageView prevMonth, nextMonth;
    private GridView calendarView;
    private GridCellAdapter adapter;

    private Calendar calendar = Calendar.getInstance(Locale.getDefault());
    private int day = calendar.get(Calendar.DAY_OF_MONTH),
                month = calendar.get(Calendar.MONTH) + 1,
                year = calendar.get(Calendar.YEAR);

    private static final String dateDay = "d MMMM yyyy";
    private static final String dateMonth = "MMMM yyyy";
    private final int[] daysOfMonth = {31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31};

    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.simple_calendar_view);

        //test temp
        LinearLayout listLayout = new LinearLayout(this);
        listLayout.setGravity(LinearLayout.VERTICAL);
        listLayout.setLayoutParams(new LinearLayout.LayoutParams(RelativeLayout.LayoutParams.MATCH_PARENT, RelativeLayout.LayoutParams.MATCH_PARENT));
        ((RelativeLayout)findViewById(R.id.mainLayout)).addView(listLayout);
        listLayout.addView(new Event(this, R.drawable.yellow, "Integrated project", "Review 3"), 0);
        listLayout.addView(new Event(this, R.drawable.blue, "Event example", "This is the description of the event."), 1);
        listLayout.addView(new Event(this, R.drawable.green, "Reminder to do something", "Not forget, I must."), 2);
        listLayout.addView(new Event(this, R.drawable.red, "Hello", "What's up?"));
        listLayout.addView(new Event(this, R.drawable.purple, "Purple square", "My favorite color"));
        listLayout.addView(new Event(this, R.drawable.yellow, "April's fool day (yay!)", "Prepare some fishes for the colleagues"));
        listLayout.addView(new Event(this, R.drawable.blue, "Work", "while(true) {work()}."));

        /* Spinner */
        SpinnerAdapter mSpinnerAdapter = ArrayAdapter.createFromResource(this, R.array.view_mode_list, R.layout.spinner);
        getSupportActionBar().setNavigationMode(ActionBar.NAVIGATION_MODE_LIST);
        getSupportActionBar().setListNavigationCallbacks(mSpinnerAdapter, this);

        /* View modes */
        for (int i = 0 ; i < views.length ; i++)
            views[i] = new ArrayList<>();

        currentDate = (TextView) findViewById(R.id.currentDate);
        currentDate.setText(DateFormat.format(dateDay, calendar.getTime()));

        for (int i = 0 ; i <= 2 ; i++)
            views[i].add(findViewById(R.id.orangeBar));

        //Day
        //views[0].add(findViewById(R.id.scrollLayout));

        //Week

        //Month
        prevMonth = (ImageView) findViewById(R.id.prevMonth);
        prevMonth.setOnClickListener(this);
        //views[2].add(prevMonth);

        //views[2].add(currentDate);

        nextMonth = (ImageView) findViewById(R.id.nextMonth);
        nextMonth.setOnClickListener(this);
        //views[2].add(nextMonth);

        calendarView = (GridView) findViewById(R.id.calendar);
        views[2].add(calendarView);

        adapter = new GridCellAdapter(getApplicationContext(), R.id.calendar_day_gridcell, month, year);
        adapter.notifyDataSetChanged();
        calendarView.setAdapter(adapter);

        //Agenda
        views[3].add(listLayout);

        //Visibility set on view[0]
        for (int i = 1 ; i < views.length ; i++)
            for (View v : views[i])
                v.setVisibility(View.INVISIBLE);
    }

    private void setGridCellAdapterToDate(int day, int month, int year)
    {
        adapter = new GridCellAdapter(getApplicationContext(), R.id.calendar_day_gridcell, month, year);
        calendar.set(year, month - 1, day);
        adapter.notifyDataSetChanged();
        calendarView.setAdapter(adapter);

        if (viewMode == 0)
            currentDate.setText(DateFormat.format(dateDay, calendar.getTime()));
        else if (viewMode == 2)
            currentDate.setText(DateFormat.format(dateMonth, calendar.getTime()));
    }

    @Override
    public void onClick(View v)
    {
        if (v == prevMonth)
        {
            if (viewMode == 0)
                day--;

            if (viewMode == 2 || day < 1)
            {
                if (month <= 1)
                {
                    month = 12;
                    year--;
                }
                else
                    month--;

                if (day < 1)
                    day = getNumberOfDaysOfMonth(month-1);
            }
        }

        else if (v == nextMonth)
        {
            if (viewMode == 0)
                day++;

            if (viewMode == 2 || day > getNumberOfDaysOfMonth(month-1))
            {
                // month is changing because of day overflow
                if (day > getNumberOfDaysOfMonth(month-1))
                    day = 1;

                if (month > 11)
                {
                    month = 1;
                    year++;
                }
                else
                    month++;
            }
        }

        // the current day does not exist in the new month
        // (for instance going from january 31 to february 31)
        if (day > getNumberOfDaysOfMonth(month-1))
            day = getNumberOfDaysOfMonth(month-1);

        setGridCellAdapterToDate(day, month, year);
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu)
    {
        getMenuInflater().inflate(R.menu.menu_main, menu);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item)
    {
        // Handle action bar item clicks here.
        int id = item.getItemId();

        //noinspection SimplifiableIfStatement
        if (id == R.id.action_settings)
            return true;

        return super.onOptionsItemSelected(item);
    }

    @Override
    public boolean onNavigationItemSelected(int itemPosition, long itemId)
    {
        // Create new fragment from our own Fragment class
        //Fragment newFragment = null;

        for (View v : views[viewMode])
            v.setVisibility(View.INVISIBLE);
        for (View v : views[itemPosition])
            v.setVisibility(View.VISIBLE);

        viewMode = itemPosition;

       /* if (viewMode == 0)
            setContentView(R.layout.event_in_list);
        else if (viewMode == 2)
            setContentView(R.layout.simple_calendar_view);
*/
        setGridCellAdapterToDate(day, month, year);

        //FragmentTransaction ft = getSupportFragmentManager().beginTransaction();

        // Replace whatever is in the fragment container with this fragment
        // and give the fragment a tag name equal to the string at the position
        // selected
        //ft.replace(R.id.container, newFragment, strings[itemPosition]);

        // Apply changes
        //ft.commit();
        return true;
    }

    private int getNumberOfDaysOfMonth(int i)
    {
        if (i == 1 && year%4 == 0)
            return 29;

        return daysOfMonth[i];
    }

    public class GridCellAdapter extends BaseAdapter implements View.OnClickListener
    {
        private final Context _context;

        private final List<String> list;
        private static final int DAY_OFFSET = 1;
        private final String[] weekdays = new String[]{"Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"};
        private final String[] monthName = {"January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"};
        private int daysInMonth;
        private int currentDayOfMonth;
        private TextView gridcell;
        private TextView num_events_per_day;
        private final HashMap eventsPerMonthMap;
        private final SimpleDateFormat dateFormatter = new SimpleDateFormat("dd-MMM-yyyy");

        // Days in Current Month
        public GridCellAdapter(Context context, int textViewResourceId, int month, int year)
        {
            super();
            this._context = context;
            this.list = new ArrayList<>();

            Calendar calendar = Calendar.getInstance();
            currentDayOfMonth = calendar.get(Calendar.DAY_OF_MONTH);

            printMonth(month, year);

            eventsPerMonthMap = findNumberOfEventsPerMonth(year, month);
        }

        public String getItem(int position)
        {
            return list.get(position);
        }

        @Override
        public int getCount()
        {
            return list.size();
        }

        /**
         * Prints Month
         */
        private void printMonth(int mm, int yy)
        {
            // The number of days to leave blank at
            // the start of this month.
            int trailingSpaces = 0;
            int daysInPrevMonth = 0;
            int prevMonth = 0;
            int prevYear = 0;
            int nextYear = 0;

            int currentMonth = mm - 1;
            daysInMonth = getNumberOfDaysOfMonth(currentMonth);

            GregorianCalendar cal = new GregorianCalendar(yy, currentMonth, 0);
            cal.setFirstDayOfWeek(Calendar.MONDAY);

            if (currentMonth == 11)
            {
                prevMonth = currentMonth - 1;
                daysInPrevMonth = getNumberOfDaysOfMonth(prevMonth);
                prevYear = yy;
                nextYear = yy + 1;
            }
            else if (currentMonth == 0)
            {
                prevMonth = 11;
                prevYear = yy - 1;
                nextYear = yy;
                daysInPrevMonth = getNumberOfDaysOfMonth(prevMonth);
            }
            else
            {
                prevMonth = currentMonth - 1;
                nextYear = yy;
                prevYear = yy;
                daysInPrevMonth = getNumberOfDaysOfMonth(prevMonth);
            }

            // Compute how much to leave before the first day of the month.
            // getDay() returns 0 for Sunday.
            int currentWeekDay = cal.get(Calendar.DAY_OF_WEEK) - 1;
            trailingSpaces = currentWeekDay;

            if (cal.isLeapYear(cal.get(Calendar.YEAR)) && mm == 1)
            {
                ++daysInMonth;
            }

            // Trailing Month days
            for (int i = 0 ; i < trailingSpaces ; i++)
                list.add(String.valueOf((daysInPrevMonth - trailingSpaces + DAY_OFFSET) + i) + "-GREY" + "-" + monthName[currentMonth] + "-" + prevYear);

            // Current Month Days
            for (int i = 1 ; i <= daysInMonth ; i++)
            {
                if (i == currentDayOfMonth)
                    list.add(String.valueOf(i) + "-BLUE" + "-" + monthName[currentMonth] + "-" + yy);
                else
                    list.add(String.valueOf(i) + "-WHITE" + "-" + monthName[currentMonth] + "-" + yy);
            }

            // Leading Month days
            for (int i = 0 ; i < list.size() % 7 ; i++)
                list.add(String.valueOf(i + 1) + "-GREY" + "-" + monthName[currentMonth] + "-" + nextYear);
        }

        /**
         * NOTE: WE STILL NEED TO IMPLEMENT THIS PART Given the YEAR, MONTH, retrieve
         * ALL entries from a SQLite database for that month. Iterate over the
         * List of All entries, and get the dateCreated, which is converted into
         * day.
         */
        private HashMap findNumberOfEventsPerMonth(int year, int month)
        {
            HashMap map = new HashMap<String, Integer>();

            /*String day = DateFormat.format("dd", dateCreated).toString();

            if (map.containsKey(day))
            {
                Integer val = (Integer) map.get(day) + 1;
                map.put(day, val);
            }
            else
                map.put(day, 1);
            */
            return map;
        }

        @Override
        public long getItemId(int position)
        {
            return position;
        }

        @Override
        public View getView(int position, View convertView, ViewGroup parent)
        {
            View row = convertView;
            if (row == null)
            {
                LayoutInflater inflater = (LayoutInflater) _context.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
                row = inflater.inflate(R.layout.calendar_day_gridcell, parent, false);
            }

            // Get a reference to the Day gridcell
            gridcell = (TextView) row.findViewById(R.id.calendar_day_gridcell);
            gridcell.setOnClickListener(this);

            // ACCOUNT FOR SPACING
            String[] day_color = list.get(position).split("-");
            String theday = day_color[0];
            String themonth = day_color[2];
            String theyear = day_color[3];

            if (eventsPerMonthMap != null && !eventsPerMonthMap.isEmpty())
            {
                if (eventsPerMonthMap.containsKey(theday))
                {
                    num_events_per_day = (TextView) row.findViewById(R.id.num_events_per_day);
                    Integer numEvents = (Integer) eventsPerMonthMap.get(theday);
                    num_events_per_day.setText(numEvents.toString());
                }
            }

            // Set the Day GridCell
            gridcell.setText(theday);
            gridcell.setTag(theday + "-" + themonth + "-" + theyear);

            if (day_color[1].equals("GREY"))
                gridcell.setTextColor(Color.LTGRAY);

            else if (day_color[1].equals("WHITE"))
                gridcell.setTextColor(Color.GRAY);

            else if (day_color[1].equals("BLUE"))
                gridcell.setTextColor(Color.parseColor("#e33d1b"));

            if (theday.equals("12"))
                row.findViewById(R.id.event2).setVisibility(View.VISIBLE);

            return row;
        }

        @Override
        public void onClick(View view)
        {
            String date_month_year = (String) view.getTag();

            try
            {
                Date parsedDate = dateFormatter.parse(date_month_year);
                Log.d("onClick():", "Parsed Date= " + parsedDate.toString());

            }
            catch (ParseException e)
            {
                e.printStackTrace();
            }
        }
    }
}
