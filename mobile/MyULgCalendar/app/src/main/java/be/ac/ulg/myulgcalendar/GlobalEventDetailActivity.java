package be.ac.ulg.myulgcalendar;

import android.content.Intent;
import android.os.Bundle;
import android.support.v7.app.ActionBarActivity;
import android.support.v4.app.NavUtils;
import android.view.MenuItem;
import android.widget.LinearLayout;
import android.widget.TextView;


/**
 * An activity representing a single GlobalEvent detail screen. This activity is only used on
 * handset devices. On tablet-size devices, item details are presented side-by-side with a list of
 * items in a {@link ProfileActivity}.
 *
 * This activity is mostly just a 'shell' activity containing nothing more than a
 * {@link GlobalEventDetailFragment}.
 */
public class GlobalEventDetailActivity extends ActionBarActivity
{
    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_globalevent_detail);

        // Show the Up button in the action bar.
        getSupportActionBar().setDisplayHomeAsUpEnabled(true);

        // savedInstanceState is non-null when there is fragment state saved from previous
        // configurations of this activity (e.g. when rotating the screen from portrait to landscape).
        // In this case, the fragment will automatically be re-added to its container so we don't
        // need to manually add it.
        if (savedInstanceState == null)
        {
            // Create the detail fragment and add it to the activity using a fragment transaction.
            Bundle arguments = new Bundle();
            arguments.putString(GlobalEventDetailFragment.ARG_ITEM_ID,
                                getIntent().getStringExtra(GlobalEventDetailFragment.ARG_ITEM_ID));

            GlobalEventDetailFragment fragment = new GlobalEventDetailFragment();
            fragment.setArguments(arguments);

            getSupportFragmentManager().beginTransaction().add(R.id.globalevent_detail_container, fragment).commit();
        }
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item)
    {
        int id = item.getItemId();

        if (id == android.R.id.home)
        {
            // This ID represents the Home or Up button. In the case of this
            // activity, the Up button is shown. Use NavUtils to allow users
            // to navigate up one level in the application structure.
            NavUtils.navigateUpTo(this, new Intent(this, ProfileActivity.class));
            return true;
        }

        return super.onOptionsItemSelected(item);
    }
}
