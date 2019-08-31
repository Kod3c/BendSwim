<img src='https://img.shields.io/badge/Build-Passing-brightgreen'> <img src='https://img.shields.io/badge/Release-v2.2-blue'> <img src='https://img.shields.io/github/license/Kod3c/BendSwim?color=yellow'> <a href='https://github.com/Kod3c/BendSwim/issues'><img src='https://img.shields.io/github/issues/Kod3c/BendSwim'></a> <img src='https://img.shields.io/github/forks/Kod3c/BendSwim'> <img src='https://img.shields.io/badge/Code%20Size-51.85%20MB-blue'> <a href='https://github.com/Kod3c/BendSwim/blob/master/CODE_OF_CONDUCT.md'><img  src='https://camo.githubusercontent.com/ee50e87026b615a0348ce5f77bd088e3ea160b3d/68747470733a2f2f696d672e736869656c64732e696f2f62616467652f2545322539442541342d636f64652532306f66253230636f6e647563742d626c75652e7376673f7374796c653d666c6174'></a><br>
<img src='https://forthebadge.com/images/badges/does-not-contain-msg.svg'> <img src='https://forthebadge.com/images/badges/powered-by-netflix.svg'>



# Swim Lesson Appointment System v.2.2
A system to help instructors and pool managers regulate and delegate swim lessons and other tasks.

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
