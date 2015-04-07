package be.ac.ulg.myulgcalendar;

import android.content.Context;
import android.content.Intent;
import android.graphics.Typeface;
import android.text.SpannableString;
import android.text.style.StyleSpan;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;

import java.util.ArrayList;
import java.util.Calendar;

public class Event extends RelativeLayout implements View.OnClickListener
{
    private LayoutParams wrap_both = new LayoutParams(LayoutParams.WRAP_CONTENT, LayoutParams.WRAP_CONTENT),
                         match_width = new LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT),
                         match_both = new LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.MATCH_PARENT);

    private Context context;

    protected Button button;
    private TextView name, des;

    private RelativeLayout content;

    public Subevent subevent;
    public boolean allDay = false, endOfEvent = false;

    public Event(Context context, int colorId, String eventName, String eventDescription)
    {
        super(context);
        this.context = context;
        this.setLayoutParams(match_width);

        LinearLayout l1 = new LinearLayout(context);
        l1.setLayoutParams(match_width);
        this.addView(l1);

            button = new Button(context);
            button.setLayoutParams(new LayoutParams(LayoutParams.MATCH_PARENT, 180));
            button.setOnClickListener(this);
            l1.addView(button);

        content = new RelativeLayout(context);
        content.setGravity(RelativeLayout.CENTER_VERTICAL);
        LayoutParams layout = new LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT);
        layout.setMargins(29, 36, 0, 0);
        content.setLayoutParams(layout);
        this.addView(content);

            ImageView color = new ImageView(context);
            color.setId(1);
            color.setImageDrawable(getResources().getDrawable(colorId));
            content.addView(color);

            name = new TextView(context);
            name.setId(2);
            SpannableString string = new SpannableString("       " + eventName);
            string.setSpan(new StyleSpan(Typeface.BOLD), 0, string.length(), 0);
            name.setText(string);
            LayoutParams layout2 = new LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT);
            //layout2.setMargins(70, 0, 0, 0);
            name.setLayoutParams(layout2);
            content.addView(name);

            des = new TextView(context);
            SpannableString string2 = new SpannableString(eventDescription);
            string2.setSpan(new StyleSpan(Typeface.ITALIC), 0, string2.length(), 0);
            des.setText(string2);
            wrap_both.addRule(RelativeLayout.BELOW, 2);
            des.setLayoutParams(wrap_both);
            content.addView(des);
    }

    public Event(Context context, Subevent event)
    {
        this(context, event.getColor(), event.getName(), event.getDescription());
        this.subevent = event;

        /*TextView globalName = new TextView(context);
        SpannableString string = new SpannableString("Blabla bla");
        string.setSpan(new StyleSpan(Typeface.BOLD), 0, string.length(), 0);
        globalName.setText(string);
        content.addView(globalName, 0);
        */
    }

    public void setSize(int newWidth, int newHeight)
    {
        button.setLayoutParams(new LinearLayout.LayoutParams(newWidth, newHeight));

        LayoutParams param = new LayoutParams(newWidth-50, newHeight-65);
        param.setMargins(29, 36, 0, 0);
        content.setLayoutParams(param);
    }

    public void setMinimalSize()
    {
        name.setMaxLines(1);
        des.setMaxLines(1);
    }

    public void removeText()
    {
        name.setText(null);
        des.setText(null);
    }

    @Override
    public void onClick(View v)
    {
        Intent detailIntent = new Intent(context, SubEventActivity.class);
        detailIntent.putExtra("id", subevent.getId());
        context.startActivity(detailIntent);
    }
}
