fuTelldus csoM Mod
=========

PHP Telldus temperatur monitoring and controll
------------------------------------------------
Modifications from dicos initial release.
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


Instructions other than dicos

1. use fuTelldus_csom.sql for correct database setup.
2. use 5 min interval for cronjob in cron_schedule.php (needs to get eventalerts on device state changes right).
3. Make sure you have Curl for PHP installed to get pushmessages to work.
