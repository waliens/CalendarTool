package be.ac.ulg.myulgcalendar;

import android.content.Context;
import android.graphics.PorterDuff;
import android.view.Gravity;
import android.widget.Button;
import android.widget.LinearLayout.LayoutParams;

public class DayGridcell extends Button
{

    public DayGridcell(Context context, String hour)
    {
        super(context);

        this.setGravity(Gravity.CENTER_VERTICAL);
        LayoutParams layout = new LayoutParams(LayoutParams.MATCH_PARENT, 100);
        layout.setMargins(0, 0, 0, 0);
        this.setLayoutParams(layout);
        this.setBackground(getResources().getDrawable(R.drawable.calendar_button_selector));
        //this.getBackground().setColorFilter(0xffffff, PorterDuff.Mode.CLEAR);
        //light grey 0xE7E7E7

        this.setText(hour);
        this.setTextSize(12);
        this.setTextColor(getResources().getColor(R.color.gray));
    }
}
