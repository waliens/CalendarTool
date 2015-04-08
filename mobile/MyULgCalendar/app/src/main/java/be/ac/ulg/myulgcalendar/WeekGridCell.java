package be.ac.ulg.myulgcalendar;

import android.content.Context;
import android.graphics.Color;
import android.view.Gravity;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.LinearLayout.LayoutParams;
import android.widget.RelativeLayout;
import android.widget.TextView;

import java.util.Calendar;
import java.util.GregorianCalendar;
import java.util.Locale;

public class WeekGridCell extends RelativeLayout implements View.OnClickListener
{
    private Button button;
    private String dayOfWeek;
    private int day, month, year;
    private CalendarActivity context;

    public WeekGridCell(CalendarActivity context, String day)
    {
        super(context);
        this.context = context;
        this.dayOfWeek = day;

        button = new Button(context);
        button.setGravity(Gravity.CENTER_HORIZONTAL);
        LayoutParams layout = new LayoutParams(305, 155);
        layout.setMargins(0, 0, 6, 0);
        button.setLayoutParams(layout);
        button.setBackground(getResources().getDrawable(R.drawable.calendar_tile_small_noborder));
        button.setOnClickListener(this);
        this.addView(button);

        button.setText(day);
        //text.setTextSize(12);
        button.setTextColor(getResources().getColor(R.color.gray));

        for (int i = 0 ; i < 24 ; i++)
        {
            Button hour = new Button(context);
            hour.setBackground(getResources().getDrawable(R.drawable.calendar_tile_small_white));
            LayoutParams l = new LayoutParams(305, 100);
            l.setMargins(0, 150+i*100, 6, 0);
            hour.setLayoutParams(l);
            this.addView(hour);
        }
    }

    public void setDay(int day, int month, int year)
    {
        this.day = day;
        this.month = month;
        this.year = year;

        button.setText(dayOfWeek + "\n" + day + " " + Month.values()[month]);
        this.setTag(day + " " + Month.values()[month]);

        // The current day is highlighted
        Calendar calendar = Calendar.getInstance(Locale.FRANCE);

        if (day == calendar.get(Calendar.DAY_OF_MONTH)
         && month == calendar.get(Calendar.MONTH)
         && year == calendar.get(Calendar.YEAR))
            button.setTextColor(Color.parseColor("#e33d1b"));

        else
            button.setTextColor(getResources().getColor(R.color.gray));
    }

    @Override
    public void onClick(View v)
    {
        context.onWeekClick(day, month, year);
    }
}
