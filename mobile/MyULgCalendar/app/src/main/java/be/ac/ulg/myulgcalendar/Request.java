package be.ac.ulg.myulgcalendar;

import android.util.Log;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.StatusLine;
import org.apache.http.client.HttpClient;
import org.apache.http.client.ResponseHandler;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.conn.ConnectTimeoutException;
import org.apache.http.conn.HttpHostConnectException;
import org.apache.http.entity.StringEntity;
import org.apache.http.impl.client.BasicResponseHandler;
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
import java.io.UnsupportedEncodingException;
import java.util.HashMap;

/**
 * List of all the requests available.
 */
public enum Request
{
    /* Student */
    GET_STUDENT_PROFILE("011"),

    /* Professor */
    GET_ALL_PROFESSORS("021"),
    GET_PROFESSOR_PROFILE("022"),

    /* Global Event */
    GET_GLOBAL_EVENTS("031"),
    VIEW_GLOBAL_EVENT("032"),
    DELETE_GLOBAL_EVENT("033"),
    EDIT_GLOBAL_EVENT("034"),
    CREATE_GLOBAL_EVENT("035"),
    GET_PROFESSOR_AVAILABLE_COURSE("036"),

    /* Event */
    EVENT_TYPES("041"),
    ADD_NOTE("042"),
    EDIT_NOTE("043"),
    DELETE_NOTE("044"),
    ADD_TO_FAVORITE("045"),
    DELETE_FROM_FAVORITE("046"),
    EVENT_CATEGORIES("047"),

    /* Sub event */
    VIEW_SUB_EVENT("051"),
    GET_SUB_EVENTS("052"),
    ADD_SUB_EVENT("053"),
    EDIT_SUB_EVENT("054"),
    DELETE_SUB_EVENT("055"),
    VIEW_SUB_EVENT2("056"),

    /* Private event */
    CREATE_PRIVATE_EVENT("061"),
    GET_PRIVATE_EVENTS("062"),
    DELETE_PRIVATE_EVENT("063"),
    VIEW_PRIVATE_EVENT("064"),
    EDIT_PRIVATE_EVENT("065"),
    VIEW_PRIVATE_EVENT2("066"),

    /* Teaching team */
    GET_TEACHING_TEAM("071"),
    ADD_TEACHING_TEAM_MEMBER("072"),
    DELETE_TEACHING_TEAM_MEMBER("073"),
    GET_TEACHING_ROLES("074"),
    GET_ADDABLE_USERS("075"),

    /* Independent event */
    CREATE_INDEP_EVENT("081"),
    GET_INDEP_EVENTS("082"),
    DELETE_INDEP_EVENT("083"),
    VIEW_INDEP_EVENT("084"),
    EDIT_INDEP_EVENT("085"),
    VIEW_INDEP_EVENT2("086"),

    /* Export */
    STATIC_EXPORT("091"),

    /* Calendar */
    CALENDAR_BASE_DATA("101"),
    CALENDAR_VIEW("102"),

    /* Section */
    GET_ALL_SECTIONS("111");

    // Activate this to connect to the montefiore database.
    private static final String ip = "ms803.montefiore.ulg.ac.be:3081";
    // Or activate this to connect to a custom database (change the ip).
    //private static final String ip = "192.168.1.7:80";

    public static boolean dataBase = true;

    private String id;

    Request(String id)
    {
        this.id = id;
    }

    public JSONObject get()
    {
        return get("");
    }

    public JSONObject get(String param)
    {
        /*if (!dataBase)
            return null;
        */
        HttpParams httpParams = new BasicHttpParams();
        HttpConnectionParams.setConnectionTimeout(httpParams, 3000);
        HttpClient client = new DefaultHttpClient(httpParams);
        HttpGet httpGet = new HttpGet("http://" + ip + "/ct/index.php?src=ajax&req=" + id + param);

        StringBuilder builder = new StringBuilder();
        JSONObject json = null;

        try
        {
            HttpResponse response = client.execute(httpGet);
            StatusLine statusLine = response.getStatusLine();
            int statusCode = statusLine.getStatusCode();

            if (statusCode == 200)
            {
                HttpEntity entity = response.getEntity();
                InputStream content = entity.getContent();
                BufferedReader reader = new BufferedReader(new InputStreamReader(content));
                String line;

                while ((line = reader.readLine()) != null)
                    builder.append(line);

                //Log.e(this.name(), builder.toString());
                json = new JSONObject(builder.toString());
            }
            else
            {
                Log.e(Request.class.toString(), "Http request failed: status code = " + statusCode);
                dataBase = false;
            }
        }
        catch(HttpHostConnectException | ConnectTimeoutException e)
        {
            Log.e(Request.class.toString(), "Failed to contact " + ip + ".");
            dataBase = false;
        }
        catch (IOException | JSONException e)
        {
            e.printStackTrace();
        }

        return json;
    }

    public JSONObject post(HashMap params)
    {
        HttpParams httpParams = new BasicHttpParams();
        HttpConnectionParams.setConnectionTimeout(httpParams, 2000);
        HttpClient client = new DefaultHttpClient(httpParams);

        StringBuilder builder = new StringBuilder();
        JSONObject json = null;

        try
        {
            HttpPost httpPost = new HttpPost("http://" + ip + "/ct/index.php?src=ajax&req=" + id);
            JSONObject holder = new JSONObject(params);
            StringEntity se = new StringEntity(holder.toString());
            httpPost.setEntity(se);
            httpPost.setHeader("Accept", "application/json");
            httpPost.setHeader("Content-type", "application/json");

            HttpResponse response = client.execute(httpPost);
            StatusLine statusLine = response.getStatusLine();
            int statusCode = statusLine.getStatusCode();

            if (statusCode == 200)
            {
                HttpEntity entity = response.getEntity();
                InputStream content = entity.getContent();
                BufferedReader reader = new BufferedReader(new InputStreamReader(content));
                String line;

                while ((line = reader.readLine()) != null)
                    builder.append(line);

                //Log.e(this.name(), builder.toString());
                json = new JSONObject(builder.toString());
            }
            else
            {
                Log.e(Request.class.toString(), "Http request failed: status code = " + statusCode);
                dataBase = false;
            }
        }
        catch(HttpHostConnectException | ConnectTimeoutException e)
        {
            Log.e(Request.class.toString(), "Database not found at " + ip + " . Be sure the ip field is correctly set.");
            dataBase = false;
        }
        catch (IOException | JSONException e)
        {
            e.printStackTrace();
        }

        return json;
    }
}
