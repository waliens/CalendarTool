package be.ac.ulg.myulgcalendar;

import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;
import android.widget.TextView;

import java.util.ArrayList;
import java.util.List;

/**
 * A fragment representing a single Global event detail screen.
 * This fragment is either contained in a {@link ProfileActivity}
 * in two-pane mode (on tablets) or a {@link GlobalEventDetailActivity}
 * on handsets.
 */
public class GlobalEventDetailFragment extends Fragment {
    /**
     * The fragment argument representing the item ID that this fragment
     * represents.
     */
    public static final String ARG_ITEM_ID = "item_id";

    /**
     * The GlobalEvent this fragment is presenting.
     */
    private GlobalEvent globalEvent;

    /**
     * Mandatory empty constructor for the fragment manager to instantiate the
     * fragment (e.g. upon screen orientation changes).
     */
    public GlobalEventDetailFragment() {
    }

    @Override
    public void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);

        if (getArguments().containsKey(ARG_ITEM_ID))
        {
            // Load the GlobalEvent specified by the fragment arguments.
            String id = getArguments().getString(ARG_ITEM_ID);
            globalEvent = GlobalEventList.ITEM_MAP.get(id);
        }
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState)
    {
        View rootView = inflater.inflate(R.layout.fragment_globalevent_detail, container, false);
        LinearLayout layout = (LinearLayout) rootView.findViewById(R.id.layout);

        // Load the GlobalEvent content.
        ((TextView) rootView.findViewById(R.id.title)).setText(globalEvent.getName());
        ((TextView) rootView.findViewById(R.id.subtitle)).setText(globalEvent.getTeam());
        ((TextView) rootView.findViewById(R.id.id_ulg)).setText(globalEvent.getCode());
        ((TextView) rootView.findViewById(R.id.descriptionContent)).setText(globalEvent.getDescription());

        if (!globalEvent.getEventList().isEmpty())
            rootView.findViewById(R.id.eventListTitle).setVisibility(View.VISIBLE);

        List<String> ids = new ArrayList<>();
        for (Subevent event : globalEvent.getEventList())
        {
            if (!ids.contains(event.getId()))
            {
                Event e = new Event(rootView.getContext(), event);
                e.setMinimalSize();
                layout.addView(e);
                ids.add(event.getId());
            }
        }

        return rootView;
    }
}
