package be.ac.ulg.myulgcalendar;

import android.content.Context;
import android.view.ViewGroup.LayoutParams;
import android.widget.TextView;

public class Title extends TextView
{
    public Title(Context context, String text)
    {
        super(context);
        this.setLayoutParams(new LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT));
        this.setPadding(20, 0, 0, 0);
        this.setText(text);
    }
}
