fuTelldus Mod
=============

Installation:
-------------
Upgrade from CSOM fork
  Upgrade instructions if you already have CSOMs fuTelldus fork installed:
  - Run db_update.php and replace all files.

Intall instructions different from Dicos original fuTelldus instructions when installing from scratch:
  Use at least 5 min interval for cron_schedule.php (needs to get eventalerts on device state changes right).
  Make sure you have Curl for PHP installed to get pushmessages to work.

Synology user instructions for full installation:
  A full installation description is created in xxxxx.txt
	Big part of it could be used for installations on other servers as well.

News in this fork (androidemil):
----------------------------------
2015, August 9

1. More advanced schemas/event handling for devices
2. Sensor statistics (min, avg, max) for many different periods (12hours, 24hours, today, this/last month, this/last year, custom)
3. Click on sensor in first page to get sensor statistics and information
4. Possible to select to use CSOMs blue or DICOs grey layout.
5. Added favicon 
6. Added shell scripts for setting up cron jobs on synology through synology control panel
7. Added synology installation instructions


News in CSOMs fork used as a base for this fork.
------------------------------------------------
1. Theme/design changes.
2. More info on each sensor by clicking sensor table-row on sensors page.
3. Ability to use combined highcharts instead of rGraph (choose in user-settings).
4. List Telldus Events/Schedules and ability to activate/deactivate.
5. Show device history by clicking table-row on device-page.
6. Set alerts on device change state (schedule page).
7. Option to get push-alerts on schedules (uses PushOver and Curl "Server needs to have Curl for PHP installed").
8. Ignore and UnIgnore Telldus sensors under settings/sensors page, posibility to delete unwanted sensor from database.
9. Public page-chart uses highcharts instead of rGraph.
10. Log users last activity (activate on general settings-page).
11. Mobile-device webapp login-link on user settings-page (needed to autologin on Iphones webapp).
12. Minor bugfixes from initial release.
