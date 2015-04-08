package be.ac.ulg.myulgcalendar;

import android.content.Context;
import android.os.AsyncTask;
import android.support.v4.app.NavUtils;
import android.support.v7.app.ActionBarActivity;
import android.os.Bundle;
import android.view.KeyEvent;
import android.view.Menu;
import android.view.MenuItem;
import android.view.inputmethod.EditorInfo;
import android.view.inputmethod.InputMethodManager;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.TextView;

import java.util.HashMap;

/**
 * An activity representing a single SubEvent detail screen.
 */
public class SubEventActivity extends ActionBarActivity
{
    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_sub_event);

        // Show the Up button in the action bar.
        getSupportActionBar().setDisplayHomeAsUpEnabled(true);

        // Load the Subevent content
        String id = getIntent().getStringExtra("id");
        final Subevent event = Subevent.all.get(id);

        ((ImageView)findViewById(R.id.color)).setImageDrawable(getResources().getDrawable(event.getColor()));
        ((TextView)findViewById(R.id.title)).setText(event.getName());
        ((TextView)findViewById(R.id.date)).setText(event.getDate());
        ((TextView)findViewById(R.id.type)).setText(event.getCategory());
        ((TextView)findViewById(R.id.descriptionContent)).setText(event.getDescription());
        ((TextView)findViewById(R.id.noteContent)).setHint(event.getNote());

        // Handle note changes
        final EditText editText = (EditText) findViewById(R.id.noteContent);
        editText.setOnEditorActionListener(new TextView.OnEditorActionListener()
        {
            @Override
            public boolean onEditorAction(TextView v, int actionId, KeyEvent kevent)
            {
                HashMap params = new HashMap();
                params.put(new String("id_event"), event.getId());
                params.put(new String("note"), v.getText());
                new NoteTask(params).execute();
                event.note = v.getText().toString();
                ((TextView)findViewById(R.id.noteContent)).setHint(v.getText());
                return false;
            }
        });
    }

    /*@Override
    public boolean onCreateOptionsMenu(Menu menu)
    {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.menu_sub_event, menu);
        return true;
    }*/

    @Override
    public boolean onOptionsItemSelected(MenuItem item)
    {
        // Handle action bar item clicks here.
        int id = item.getItemId();

        if (id == android.R.id.home)
        {
            // This ID represents the Home or Up button. Navigate up one level in the activities
            // stack to allow the user to come back to the previous activity.
            finish();
            return true;
        }

        return super.onOptionsItemSelected(item);
    }

    /**
     * Represents an asynchronous task used to send note updates.
     */
    public class NoteTask extends AsyncTask<Void, Void, Boolean>
    {
        HashMap param;

        public NoteTask(HashMap param)
        {
            this.param = param;
        }

        @Override
        protected Boolean doInBackground(Void... params)
        {
            Request.EDIT_NOTE.post(param);
            return true;
        }
    }
}
