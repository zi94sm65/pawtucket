Installation Instructions
-------------------------
1) Make sure your CollectiveAccess Providence/Pawtucket setup.php file is configured with Email settings appropriate for your webserver: __CA_SMTP_SERVER__ = server to use for outgoing mail.

2) Upload formContact directory to your installation of Pawtucket plugins directory.
Usually located at /pawtucket/app/plugins/

3) Open the file, formContact.conf in the /formContact/conf/ directory and enable the plugin by setting:
enabled = 1 
To disable plugin set:
enabled = 0

Then decide if you want to require the user to send you an email with text in the "Message" section of the form by setting:
require_message_text = 1
If you do not want to require any message set:
require_message_text = 0

Then decide where you want the "Contact" menu item to appear in the Pawtucket navigation menu bar:
Default setting is blank, so it will appear to be the last (right most) item in the navigation menu bar:
position_top_level_nav_item_before =
If you want it to appear before the "About" item, set it like:
position_top_level_nav_item_before = about

4) Open the file, FormcontactController.php, in the /formContact/controllers/ directory
Look around line 62-67 for:

$ps_to_email = "somebody@yourorganization.com";

Change this to the email address of whoever you want to receive the info submitted by a user in the contact form.

Thats it! If you followed all the steps the contact menu item should appear in the Pawtucket navigation menu bar and when clicked the contact form should load in Pawtucket and a user can send you an email via the form.

Note: Depending on your server set-up sometimes you might get a "invalid controller path" error. You may have to adjust the capitalization of the controller file name: sometimes formcontactController will work better than FormcontactController or vice-versa. If you still get a "invalid controller path" error try changing the capitalization of the file, formContactPlugin.php, to formcontactPlugin.php
