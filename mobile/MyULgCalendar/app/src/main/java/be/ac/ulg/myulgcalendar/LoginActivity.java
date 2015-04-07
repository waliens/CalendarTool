package be.ac.ulg.myulgcalendar;

import android.animation.Animator;
import android.animation.AnimatorListenerAdapter;
import android.annotation.TargetApi;
import android.app.Activity;
import android.content.Intent;
import android.os.AsyncTask;

import android.os.Build;
import android.os.Bundle;
import android.text.TextUtils;
import android.view.KeyEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.inputmethod.EditorInfo;
import android.widget.AutoCompleteTextView;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;

/**
 * A login screen that offers login via id/password.
 */
public class LoginActivity extends Activity
{
    // Keep track of the login task to ensure we can cancel it if requested.
    private UserLoginTask authTask = null;

    // UI references.
    private AutoCompleteTextView idView;
    private EditText passwordView;
    private View progressView, loginFormView;

    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login);

        // Set up the login form.
        idView = (AutoCompleteTextView) findViewById(R.id.ulg_id);

        passwordView = (EditText) findViewById(R.id.password);
        passwordView.setOnEditorActionListener(new TextView.OnEditorActionListener()
        {
            @Override
            public boolean onEditorAction(TextView textView, int id, KeyEvent keyEvent)
            {
                if (id == R.id.login || id == EditorInfo.IME_NULL)
                {
                    attemptLogin();
                    return true;
                }
                return false;
            }
        });

        Button emailSignInButton = (Button) findViewById(R.id.sign_in_button);
        emailSignInButton.setOnClickListener(new OnClickListener()
        {
            @Override
            public void onClick(View view)
            {
                attemptLogin();
            }
        });

        loginFormView = findViewById(R.id.login_form);
        progressView = findViewById(R.id.login_progress);
    }

    /**
     * Attempts to sign in the account specified by the login form.
     * If there are form errors (invalid id, missing fields, etc.), the
     * errors are presented and no actual login attempt is made.
     */
    public void attemptLogin()
    {
        if (authTask != null)
        {
            return;
        }

        // Reset errors.
        idView.setError(null);
        passwordView.setError(null);

        // Store values at the time of the login attempt.
        String id = idView.getText().toString();
        String password = passwordView.getText().toString();

        // Check for a valid Ulg id.
        if (TextUtils.isEmpty(id))
        {
            idView.setError(getString(R.string.error_field_required));
            idView.requestFocus();
            return;
        }
        if (!isUlgIdValid(id))
        {
            idView.setError(getString(R.string.error_invalid_id));
            idView.requestFocus();
            return;
        }

        // Check for a valid password.
        /* TODO: uncomment after connecting to the authentication system.
        if (TextUtils.isEmpty(password))
        {
            passwordView.setError(getString(R.string.error_field_required));
            passwordView.requestFocus();
            return;
        }
        if (!isPasswordValid(password))
        {
            passwordView.setError(getString(R.string.error_invalid_password));
            passwordView.requestFocus();
            return;
        } */

        // Show a progress spinner, and kick off a background task to perform the user login attempt.
        showProgress(true);
        authTask = new UserLoginTask(this, id, password);
        authTask.execute((Void) null);
    }

    /**
     * Return true if the given id starts by the letter 'u' or 's' and is of length 7.
     * False otherwise.
     */
    private boolean isUlgIdValid(String id)
    {
        char c = id.charAt(0);
        return (c == 'u' || c == 's');// && id.length() == 7;
    }

    /**
     * Return true if the given password is long enough, or false if it is too short.
     */
    private boolean isPasswordValid(String password)
    {
        return password.length() > 2;
    }

    /**
     * Shows the progress UI and hides the login form.
     */
    @TargetApi(Build.VERSION_CODES.HONEYCOMB_MR2)
    public void showProgress(final boolean show)
    {
        // On Honeycomb MR2 we have the ViewPropertyAnimator APIs, which allow
        // for very easy animations. If available, use these APIs to fade-in
        // the progress spinner.
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.HONEYCOMB_MR2)
        {
            int shortAnimTime = getResources().getInteger(android.R.integer.config_shortAnimTime);

            loginFormView.setVisibility(show ? View.GONE : View.VISIBLE);
            loginFormView.animate().setDuration(shortAnimTime).alpha(show ? 0 : 1)
                         .setListener(new AnimatorListenerAdapter()
            {
                @Override
                public void onAnimationEnd(Animator animation)
                {
                    loginFormView.setVisibility(show ? View.GONE : View.VISIBLE);
                }
            });

            progressView.setVisibility(show ? View.VISIBLE : View.GONE);
            progressView.animate().setDuration(shortAnimTime).alpha(show ? 1 : 0)
                        .setListener(new AnimatorListenerAdapter()
            {
                @Override
                public void onAnimationEnd(Animator animation)
                {
                    progressView.setVisibility(show ? View.VISIBLE : View.GONE);
                }
            });
        }
        else
        {
            // The ViewPropertyAnimator APIs are not available, so simply show
            // and hide the relevant UI components.
            progressView.setVisibility(show ? View.VISIBLE : View.GONE);
            loginFormView.setVisibility(show ? View.GONE : View.VISIBLE);
        }
    }

    /**
     * Represents an asynchronous login/registration task used to authenticate the user.
     */
    public class UserLoginTask extends AsyncTask<Void, Void, Boolean>
    {
        private Activity activity;
        private final String id;
        private final String password;

        UserLoginTask(Activity activity, String id, String password)
        {
            this.activity = activity;
            this.id = id;
            this.password = password;
        }

        @Override
        protected Boolean doInBackground(Void... params)
        {
            // TODO: attempt authentication against the Ulg front-end server.

            new Profile();
            return true;
        }

        @Override
        protected void onPostExecute(final Boolean success)
       {
            authTask = null;
            showProgress(false);

            if (success)
            {
                startActivity(new Intent(activity, CalendarActivity.class));
            }
            else
            {
                passwordView.setError(getString(R.string.error_incorrect_password));
                passwordView.requestFocus();
            }
        }

        @Override
        protected void onCancelled()
        {
            authTask = null;
            showProgress(false);
        }
    }
}
