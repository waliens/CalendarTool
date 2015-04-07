package be.ac.ulg.myulgcalendar;

import android.content.Intent;
import android.graphics.Color;
import android.os.Bundle;
import android.support.v7.app.ActionBarActivity;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AbsListView;
import android.widget.FrameLayout;
import android.widget.LinearLayout;
import android.widget.LinearLayout.LayoutParams;
import android.widget.ListAdapter;
import android.widget.TextView;

import java.util.ArrayList;
import java.util.Calendar;
import java.util.List;
import java.util.Locale;

/**
 * Activity representing the profile page, including user information and a list of Global events.
 * This activity has different presentations for handset and tablet-size devices. On handsets, the
 * activity presents a list of {@link GlobalEvent}, which when touched, lead to a
 * {@link GlobalEventDetailActivity} representing event details. On tablets, the activity presents
 * the list of Global events and details side-by-side using two vertical panes.
 *
 * <p/>
 * The activity makes heavy use of fragments. The list of items is a {@link GlobalEventList}
 * and the item details (if present) is a {@link GlobalEventDetailFragment}.
 * <p/>
 *
 * This activity also implements the required {@link GlobalEventList.Callbacks} interface to
 * listen for item selections.
 */
public class ProfileActivity extends ActionBarActivity implements GlobalEventList.Callbacks
{
    /**
     * Whether or not the activity is in two-pane mode, i.e. running on a tablet device.
     */
    private boolean mTwoPane;

    public static View list;

    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_profile);

        // Show the Up button in the action bar.
        getSupportActionBar().setDisplayHomeAsUpEnabled(true);

        /*if (findViewById(R.id.globalevent_detail_container) != null)
        {
            // The detail container view will be present only in the large-screen layouts
            // (res/values-large and res/values-sw600dp). If this view is present, then the
            // activity should be in two-pane mode.
            mTwoPane = true;

            // In two-pane mode, list items should be given the 'activated' state when touched.
            ((GlobalEventList) getSupportFragmentManager()
                    .findFragmentById(R.id.globalevent_list))
                    .setActivateOnItemClick(true);
        }*/

        LinearLayout layout = (LinearLayout) findViewById(R.id.layout);
        ((TextView)findViewById(R.id.nameContent)).setText(Profile.getName());

        if (Profile.isProfessor())
        {
            ((TextView)findViewById(R.id.ulgIdContent)).setText("u013317");
            ((TextView)findViewById(R.id.categoryContent)).setText("Professeur");

            ((TextView)findViewById(R.id.other)).setText("Evénements indépendants");
        }
        else
        {
            ((TextView)findViewById(R.id.ulgIdContent)).setText("s060934");
            ((TextView)findViewById(R.id.categoryContent)).setText("Etudiant");

            TextView pathwayTitle = new TextView(this);
            pathwayTitle.setText("Section");
            pathwayTitle.setTextColor(Color.BLACK);
            layout.addView(pathwayTitle, 1);

            TextView pathwayContent = new TextView(this);
            LayoutParams param = new LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT);
            param.setMargins(0, 0, 0, 30);
            pathwayContent.setLayoutParams(param);
            pathwayContent.setText(Profile.getPathway());
            layout.addView(pathwayContent, 2);

            ((TextView)findViewById(R.id.other)).setText("Evénements privés");
        }

        boolean hide = true;
        List<String> ids = new ArrayList<>();
        for (Subevent s : Profile.getPrivateEvents())
        {
            // Past events are not displayed.
            if (s.getStart().compareTo(Calendar.getInstance(Locale.getDefault())) < 0)
                continue;

            // Recurrent events are only displayed once.
            if (!ids.contains(s.getId()))
            {
                Event e = new Event(this, s);
                e.setMinimalSize();
                layout.addView(e);

                ids.add(s.getId());
                hide = false;
            }
        }

        // Hide the list title if there is no event.
        if (hide)
            ((TextView)findViewById(R.id.other)).setText("");

        setListViewHeightBasedOnChildren();
    }

    /**
     * Method for Setting the Height of the ListView dynamically.
     * Hack to fix the issue of not showing all the items of the ListView
     * when placed inside a ScrollView
     */
    public void setListViewHeightBasedOnChildren()
    {
        FrameLayout listView = (FrameLayout) findViewById(R.id.globalevent_list);
        ListAdapter listAdapter = GlobalEventList.adapter;

        if (listAdapter == null)
            return;

        int desiredWidth = View.MeasureSpec.makeMeasureSpec(listView.getWidth(), View.MeasureSpec.UNSPECIFIED);
        int totalHeight = 0;
        View view = null;
        for (int i = 0; i < listAdapter.getCount(); i++) {
            view = listAdapter.getView(i, view, listView);
            if (i == 0)
                view.setLayoutParams(new ViewGroup.LayoutParams(desiredWidth, AbsListView.LayoutParams.WRAP_CONTENT));

            view.measure(desiredWidth, View.MeasureSpec.UNSPECIFIED);
            totalHeight += view.getMeasuredHeight();
        }
        ViewGroup.LayoutParams params = listView.getLayoutParams();
        params.height = totalHeight + (42 * (listAdapter.getCount() - 1));
        listView.setLayoutParams(params);
        listView.requestLayout();
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item)
    {
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
     * Callback method from {@link GlobalEventList.Callbacks}
     * indicating that the item with the given ID was selected.
     */
    @Override
    public void onItemSelected(String id)
    {
        /*if (mTwoPane)
        {
            // In two-pane mode, show the detail view in this activity by
            // adding or replacing the detail fragment using a
            // fragment transaction.
            Bundle arguments = new Bundle();
            arguments.putString(GlobalEventDetailFragment.ARG_ITEM_ID, id);
            GlobalEventDetailFragment fragment = new GlobalEventDetailFragment();
            fragment.setArguments(arguments);
            getSupportFragmentManager().beginTransaction()
                    .replace(R.id.globalevent_detail_container, fragment)
                    .commit();

        }
        else*/
        {
            // In single-pane mode, simply start the detail activity for the selected item ID.
            Intent detailIntent = new Intent(this, GlobalEventDetailActivity.class);
            detailIntent.putExtra(GlobalEventDetailFragment.ARG_ITEM_ID, id);
            startActivity(detailIntent);
        }
    }
}
