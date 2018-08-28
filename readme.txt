=== CF7 to Spreadsheet ===
Contributors: brainstormforce, abhishilawat
Donate link: https://www.brainstormforce.com/payment/
Tags: google, sheets, spreadsheets, google sheets, google spreadsheets,  cf7, contact form 7, data, form, form data
Requires at least: 4.4
Tested up to: 4.8.1
Stable tag: 1.1
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Send your Contact Form 7 data directly to your Google Spreadsheet.

== Description ==

This plugin connects your Contact Form 7 and Google Spreadsheet.

When a visitor submits his/her data on your website via a Contact Form 7 form, on form submission, such data are added into Google Spreadsheet.

This is a simple way to maintain your contact form 7 data backup.

= How to Use this Plugin =

*In Google Spreadsheet* 
 
* Log into your Google Account and visit Google Spreadsheet.  
* Create a new Sheet and name it.  
* Rename the tab on which you want to capture the data.
* In the Google sheets tab, provide column names in row 1. The first column should be "date" if you want to add date. For each further column, copy paste mail tags from the Contact Form 7 form (e.g. "your-name", "your-email", "your-subject", "your-message", etc). Also, you can add required mail tags (e.g "your-name", "your-email").

*In WordPress Admin*
  
* Create or Edit the Contact Form 7 form from which you want to capture the data. Set up the form as usual in the Form and Mail etc tabs. Thereafter, go to the new "CF7 to Spreadsheet" tab.  
* On the "CF7 to Spreadsheet" tab, copy-paste the Google Spreadsheets sheet name and tab name into respective positions, and hit "Save".

= Important Notes = 

* You must pay very careful attention to your naming. This plugin will have unpredictable results if names and spellings do not match between your Google Spreadsheets and form settings. Also, naming is not case sensitive.

== Installation ==

1. Upload `contact-form-to-excel-addon` to the `/wp-content/plugins/` directory and Install`.  
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the `Admin Panel > Setting > CF7 To spreadsheet` screen to connect to `Google Sheets Account` by entering the Access Code. You can get the Access Code by clicking the "Get Code" button. 
4. Use the `Admin Panel > Contact form 7 > Select Contact form > On editor window tab - CF7 To spreadsheet` Add your Sheet Name And Tab Name.

== Screenshots ==

1. Plugin Settings and How to Configure your Google Spreadsheet and your Contact Form 7.

== Changelog ==

= 1.0.0 =
* Initial release
= 1.1 =
* Configuration API key updated
