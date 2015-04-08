package be.ac.ulg.myulgcalendar;

import android.app.SearchManager;
import android.content.Context;
import android.content.Intent;
import android.support.v7.app.ActionBarActivity;
import android.os.Bundle;
import android.view.Menu;
import android.view.MenuItem;
import android.widget.LinearLayout;
import android.support.v7.widget.SearchView;


public class SearchActivity extends ActionBarActivity
{
    @Override
    public void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_search);

        // Show the Up button in the action bar.
        getSupportActionBar().setDisplayHomeAsUpEnabled(true);

        // Get the intent, verify the action and get the query
        Intent intent = getIntent();
        if (Intent.ACTION_SEARCH.equals(intent.getAction())) {
            String query = intent.getStringExtra(SearchManager.QUERY);
            doMySearch(query);
        }
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu)
    {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.menu_search, menu);

        // Get the SearchView and set the searchable configuration
        //SearchManager searchManager = (SearchManager) getSystemService(Context.SEARCH_SERVICE);
        //SearchView searchView = (SearchView) menu.findItem(R.id.action_search).getActionView();
        // Assumes current activity is the searchable activity
        //searchView.setSearchableInfo(searchManager.getSearchableInfo(getComponentName()));
        //searchView.setIconifiedByDefault(false); // Do not iconify the widget; expand it by default

        return true;
    }

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

    private void doMySearch(String query)
    {
        LinearLayout searchResults = (LinearLayout) findViewById(R.id.searchResults);

        for (Subevent s : Profile.getAllSubevents())
            if (s.getName().toLowerCase().contains(query) || s.getDescription().toLowerCase().contains(query))
            {
                searchResults.addView((new Title(this, s.getAgendaDate())));
                Event e = new Event(this, s);
                e.setMinimalSize();
                searchResults.addView(e);
            }
    }
}
