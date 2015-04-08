package be.ac.ulg.myulgcalendar;

import android.graphics.drawable.Drawable;
import android.util.Log;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.StatusLine;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.conn.ConnectTimeoutException;
import org.apache.http.conn.HttpHostConnectException;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.params.BasicHttpParams;
import org.apache.http.params.HttpConnectionParams;
import org.apache.http.params.HttpParams;
import org.json.JSONException;
import org.json.JSONObject;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;

/**
 * List of categories an event can belong to.
 */
public enum EventCategory
{
    Cours_théorique(R.drawable.blue),
    Laboratoire(R.drawable.purple),
    TP(R.drawable.green),
    Conférence(R.drawable.yellow),
    Projet(R.drawable.red),
    Devoir(R.drawable.red),
    Q_R(R.drawable.yellow),
    Examen_oral(R.drawable.red),
    Examen_écrit(R.drawable.red),
    Interrogation(R.drawable.red),
    Sport(R.drawable.blue),
    Chapiteau(R.drawable.purple),
    Travail(R.drawable.green),
    Restaurant(R.drawable.purple),
    Soirée(R.drawable.purple),
    Personnel(R.drawable.blue),
    Loisirs(R.drawable.blue),
    Musique(R.drawable.green),
    Anniversaire(R.drawable.yellow),
    Autre(R.drawable.yellow);

    private int color;

    EventCategory(int color)
    {
        this.color = color;
    }

    static public EventCategory get(String name)
    {
        EventCategory[] all = EventCategory.values();

        for (EventCategory c : all)
            if (c.getName().equals(name))
                return c;

        return EventCategory.Autre;
    }

    public String getName()
    {
        if (this == EventCategory.Q_R)
            return "Q & R";

        return name().replace('_', ' ');
    }

    public int getColor()
    {
        return color;
    }
}
