package be.ac.ulg.myulgcalendar;

import android.content.Context;
import android.graphics.Typeface;
import android.text.SpannableString;
import android.text.style.StyleSpan;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;

public class Event extends RelativeLayout
{
    private LayoutParams wrap_both = new LayoutParams(LayoutParams.WRAP_CONTENT, LayoutParams.WRAP_CONTENT),
                         match_width = new LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT),
                         match_both = new LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.MATCH_PARENT);

    public Event(Context context, int colorId, String eventName, String eventDescription)
    {
        super(context);

        this.setLayoutParams(match_width);

        LinearLayout l1 = new LinearLayout(context);
        l1.setLayoutParams(match_width);
        this.addView(l1);

            Button button = new Button(context);
            button.setLayoutParams(new LayoutParams(LayoutParams.MATCH_PARENT, 180));
            l1.addView(button);

        RelativeLayout l2 = new RelativeLayout(context);
        l2.setGravity(RelativeLayout.CENTER_VERTICAL);
        LayoutParams layout = new LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT);
        layout.setMargins(29, 36, 0, 0);
        l2.setLayoutParams(layout);
        this.addView(l2);

            ImageView color = new ImageView(context);
            color.setId(1);
            color.setImageDrawable(getResources().getDrawable(colorId));
            l2.addView(color);

            TextView name = new TextView(context);
            name.setId(2);
            SpannableString string = new SpannableString(eventName);
            string.setSpan(new StyleSpan(Typeface.BOLD), 0, string.length(), 0);
            name.setText(string);
            LayoutParams layout2 = new LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT);
            layout2.setMargins(70, 0, 0, 0);
            name.setLayoutParams(layout2);
            l2.addView(name);

            TextView des = new TextView(context);
            SpannableString string2 = new SpannableString(eventDescription);
            string2.setSpan(new StyleSpan(Typeface.ITALIC), 0, string2.length(), 0);
            des.setText(string2);
            wrap_both.addRule(RelativeLayout.BELOW, 2);
            des.setLayoutParams(wrap_both);
            l2.addView(des);
    }
}
