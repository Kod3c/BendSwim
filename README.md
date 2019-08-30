<img src='https://img.shields.io/badge/Build-Passing-brightgreen'> <img src='https://img.shields.io/github/license/Kod3c/BendSwim'> <img src='https://img.shields.io/github/issues/Kod3c/BendSwim'>


# Swim Lesson Appointment System v.2.2

## CHANGELOG v.2.2

1. Got rid of mentions of "Appointments"
     - Replaced with "Lessons"

2. Repaired Email bug by removing emails entirely. Admin now creates/manages accounts.

3. Cleaned up code regarding Users and account creation.


## CHANGELOG v.2.1

- [FIXED] user last_failed_login count and set last_failed_login date
- [FIXED] appointment date cannot be blank - Appointment Form @ Dashboard
- [UPDATED] language array twig parser
- [FIXED] check existence for translation lang file, from session or DEFAULT_LANGUAGE constant value
- [UPDATED] added Base Twig Layout for Auth modules (login, signup, register, forgot, reset)
- [FIXED] MySQL 5.7.5+ changed the way GROUP BY behaved in order to be SQL99 compliant (where in previous versions it was not). [SettingModel : getBrowserStatistics]
- [FIXED] Trying to get property of non-object
- [IMPROVED] Auth security

## I've used the following framework, icons, favicon, file manager, Calendar or other files as listed.

- Thank's to comestoarra-labs - https://codecanyon.net/item/dental-clinic-appointment-system/17619098
- Thank's to BOOTSTRAP - www.getbootstrap.com
- Thank's to slim PHP FRAMEWORK - www.slimframework.com
- Thank's to TWIG TEMPLATING ENGINE - www.twig.sensiolabs.org/
- Thank's to SMVC PHP FRAMEWORK - www.simplemvcframework.com
- Thank's to Clock Face - www.vitalets.github.io/clockface
- Thank's to Font Awesome - www.fontawesome.io
- Thank's to Icon Archive - www.iconarchive.com
- Thank's to Favicon - www.favicon.cc
- Thank's to Highchart - www.highcharts.com
- Thank's to Full Calendar - www.fullcalendar.io/
- Thank's to Responsive File Manager - www.responsivefilemanager.com/
