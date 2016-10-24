<?PHP
require_once("./include/fg_membersite.php");

$fgmembersite = new FGMembersite();

$fgmembersite->SetWebsiteName('http://www.deenysoft.com/');

$fgmembersite->SetAdminEmail('deenysoft@gmail.com');

$fgmembersite->InitDB(/*hostname*/'127.0.0.1',
                      /*username*/'root',
                      /*password*/'1234567890',
                      /*database name*/'ehospital',
                      /*table name*/'patient');

$fgmembersite->SetRandomKey('hRB0GS3RGS3W65ev');

?>