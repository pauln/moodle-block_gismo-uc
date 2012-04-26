===================================================================================
                                      G I S M O
                    
                              Version 2.0 - May 7, 2010 
	
		
		       Università della svizzera italiana, Lugano, Switzerland
                    
                                       README
===================================================================================

0)  Requirements

    Gismo is primarily developed on the LAMP platform (Linux, Apache, MySQL and 
    PHP), we didn't test it in Windows environments.

    The requirements for Gismo are:
    - PHP v. 5.2 (it should work with previous versions of php if you install
      PECL JSON >=1.2.0, but we didn't test it).
    - Mysql v.4
    - Moodle 1.9+

1)  Setup
    
    a) INSTALL FILES
    
    Please follow the standard procedure to install Moodle plugins:
    - copy the gismo folder into the moodle blocks directory (MOODLE_DOCUMENT_ROOT/blocks)
    - logon to moodle as administrator and visit the Notifications page
    
    b) LAUNCH DATA EXPORTER FOR THE FIRST TIME
    
    Gismo needs to perform some computation on regular basis. The purpose of this 
    computation is to extract data from logs and derive aggregated information. 
    
    This computation is done daily by a script that, depending on the setting of your 
    Moodle cron script, runs during the night between 02.00 and 04.00 (server time).
    When you install the Gismo software the fist time, if you don't want to wait 
    the next day to see the result, you can lunch this script by hand 
    at this address:

    MOODLE_MAIN_URL/blocks/gismo/lib/gismo/server_side/export_data.php

2)  Configuration

    Gismo doesn't need any specific configuration. Everything runs out of the box.

3)  Use
    
    Please access your course as teacher and add a Gismo block instance, 
    then follows instructions.  

4)  Further information
    
    Please visit http://gismo.sourceforge.net/ for further information
    
5)  Gismo is released under GPL License
    
    
The eLab Team
    
===================================================================================
