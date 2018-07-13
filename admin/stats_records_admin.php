<?php 
// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);


include_once '../includes/auth-inc.php';
require_once '../class/output.class.php';
require_once '../class/db.class.php';

$rectype = '';
if (isset($_REQUEST['recid'])) { 
    $recid = $_REQUEST['recid']; 
    switch ($recid) {
        case '1':
            $livesql = "SELECT Pilot, count(*) AS ActionsPerDay, DATE(ActivityDate) AS ActionDate FROM activity 
                GROUP BY Pilot, DATE(ActivityDate) ORDER BY ActionsPerDay DESC LIMIT 10";
            $arrFields[0] = 'Pilot';
            $arrFields[1] = 'ActionsPerDay';
            $arrFields[2] = 'ActionDate';
            break;
        case '2':
            $livesql = "SELECT Pilot, count(*) as ActionsPerDay, DATE(ActivityDate) AS ActionDate from activity 
                WHERE EntryType = 'agent' group by Pilot, DATE(ActivityDate) ORDER BY ActionsPerDay DESC";
            $arrFields[0] = 'Pilot';
            $arrFields[1] = 'ActionsPerDay';
            $arrFields[2] = 'ActionDate';
            break;
        case '3':
            $livesql = "SELECT Pilot, count(*) as ActionsPerDay, DATE(ActivityDate) AS ActionDate from activity 
                WHERE EntryType = 'sower' group by Pilot, DATE(ActivityDate) ORDER BY ActionsPerDay DESC";
            $arrFields[0] = 'Pilot';
            $arrFields[1] = 'ActionsPerDay';
            $arrFields[2] = 'ActionDate';
            break;
        case '4':
            $livesql = "SELECT Pilot, count(*) as ActionsPerDay, DATE(ActivityDate) AS ActionDate from activity 
                WHERE EntryType = 'tender' group by Pilot, DATE(ActivityDate) ORDER BY ActionsPerDay DESC";
            $arrFields[0] = 'Pilot';
            $arrFields[1] = 'ActionsPerDay';
            $arrFields[2] = 'ActionDate';
            break;
        case '5':
            $livesql = "SELECT Pilot, count(*) as ActionsPerWeek, YEARWEEK(ActivityDate) AS ActionWeek from activity 
                WHERE EntryType = 'tender' group by Pilot, YEARWEEK(ActivityDate) ORDER BY ActionsPerWeek DESC";
            $arrFields[0] = 'Pilot';
            $arrFields[1] = 'ActionsPerWeek';
            $arrFields[2] = 'ActionWeek';
            break;
        case '6':
            $livesql = "SELECT Pilot, count(*) as ActionsPerWeek, YEARWEEK(ActivityDate) AS ActionWeek from activity 
                WHERE EntryType = 'sower' group by Pilot, YEARWEEK(ActivityDate) ORDER BY ActionsPerWeek DESC";
            $arrFields[0] = 'Pilot';
            $arrFields[1] = 'ActionsPerWeek';
            $arrFields[2] = 'ActionWeek';
            break;
        case '7':
            $livesql = "SELECT Pilot, count(*) as ActionsPerWeek, YEARWEEK(ActivityDate) AS ActionWeek from activity 
                WHERE EntryType = 'agent' group by Pilot, YEARWEEK(ActivityDate) ORDER BY ActionsPerWeek DESC";
            $arrFields[0] = 'Pilot';
            $arrFields[1] = 'ActionsPerWeek';
            $arrFields[2] = 'ActionWeek';
            break;
        case '8':
            $livesql = "SELECT Pilot, count(*) as ActionsPerWeek, YEARWEEK(ActivityDate) AS ActionWeek from activity 
                group by Pilot, YEARWEEK(ActivityDate) ORDER BY ActionsPerWeek DESC LIMIT 10";
            $arrFields[0] = 'Pilot';
            $arrFields[1] = 'ActionsPerWeek';
            $arrFields[2] = 'ActionWeek';
            break;
        case '9':
            $livesql = "SELECT Pilot, count(*) as ActionsPerMonth, YEAR(ActivityDate) AS ActionYear, MONTH(ActivityDate) AS ActionMonth
                from activity group by Pilot, YEAR(ActivityDate), MONTH(ActivityDate) ORDER BY ActionsPerMonth DESC";
            $arrFields[0] = 'Pilot';
            $arrFields[1] = 'ActionsPerMonth';
            $arrFields[2] = 'ActionYear';
            $arrFields[3] = 'ActionMonth';
            break;
        case '10':
            $livesql = "SELECT Pilot, count(*) as ActionsPerMonth, YEAR(ActivityDate) AS ActionYear, MONTH(ActivityDate) AS ActionMonth
                from activity WHERE EntryType = 'agent' group by Pilot, YEAR(ActivityDate), MONTH(ActivityDate) 
                ORDER BY ActionsPerMonth DESC";
            $arrFields[0] = 'Pilot';
            $arrFields[1] = 'ActionsPerMonth';
            $arrFields[2] = 'ActionYear';
            $arrFields[3] = 'ActionMonth';
            break;
        case '11':
            $livesql = "SELECT Pilot, count(*) as ActionsPerMonth, YEAR(ActivityDate) AS ActionYear, MONTH(ActivityDate) AS ActionMonth
                from activity WHERE EntryType = 'sower' group by Pilot, YEAR(ActivityDate), MONTH(ActivityDate) 
                ORDER BY ActionsPerMonth DESC";
            $arrFields[0] = 'Pilot';
            $arrFields[1] = 'ActionsPerMonth';
            $arrFields[2] = 'ActionYear';
            $arrFields[3] = 'ActionMonth';
            break;
        case '12':
            $livesql = "SELECT Pilot, count(*) as ActionsPerMonth, YEAR(ActivityDate) AS ActionYear, MONTH(ActivityDate) AS ActionMonth
                from activity WHERE EntryType = 'tender' group by Pilot, YEAR(ActivityDate), MONTH(ActivityDate) 
                ORDER BY ActionsPerMonth DESC";
            $arrFields[0] = 'Pilot';
            $arrFields[1] = 'ActionsPerMonth';
            $arrFields[2] = 'ActionYear';
            $arrFields[3] = 'ActionMonth';
            break;
        case '13':
            $livesql = "SELECT Pilot, count(*) as ActionsOverall from activity WHERE EntryType = 'tender' group by Pilot 
                ORDER BY ActionsOverall DESC";
            $arrFields[0] = 'Pilot';
            $arrFields[1] = 'ActionsOverall';
            break;
        case '14':
            $livesql = "SELECT Pilot, count(*) as ActionsOverall from activity WHERE EntryType = 'sower' group by Pilot 
                ORDER BY ActionsOverall DESC";
            $arrFields[0] = 'Pilot';
            $arrFields[1] = 'ActionsOverall';
            break;
        case '15':
            $livesql = "SELECT Pilot, count(*) as ActionsOverall from activity WHERE EntryType = 'agent' group by Pilot 
                ORDER BY ActionsOverall DESC";
            $arrFields[0] = 'Pilot';
            $arrFields[1] = 'ActionsOverall';
            break;
        case '16':
            $livesql = "SELECT Pilot, count(*) as ActionsOverall from activity group by Pilot ORDER BY ActionsOverall DESC";
            $arrFields[0] = 'Pilot';
            $arrFields[1] = 'ActionsOverall';
            break;
        case '17':
            $livesql = "SELECT count(*) AS ActionsPerDay, DATE(ActivityDate) AS ActionDate FROM activity 
                GROUP BY DATE(ActivityDate) ORDER BY ActionsPerDay DESC";
            $arrFields[0] = 'ActionsPerDay';
            $arrFields[1] = 'ActionDate';
            break;
        case '19':
            $livesql = "SELECT count(*) as ActionsPerDay, DATE(ActivityDate) AS ActionDate from activity 
                WHERE EntryType = 'sower' group by DATE(ActivityDate) ORDER BY ActionsPerDay DESC";
            $arrFields[0] = 'ActionsPerDay';
            $arrFields[1] = 'ActionDate';
            break;
        case '20':
            $livesql = "SELECT count(*) as ActionsPerDay, DATE(ActivityDate) AS ActionDate from activity 
                WHERE EntryType = 'tender' group by DATE(ActivityDate) ORDER BY ActionsPerDay DESC";
            $arrFields[0] = 'ActionsPerDay';
            $arrFields[1] = 'ActionDate';
            break;
        case '21':
            $livesql = "SELECT count(*) as ActionsPerWeek, YEARWEEK(ActivityDate) AS ActionWeek from activity 
                WHERE EntryType = 'tender' group by YEARWEEK(ActivityDate) ORDER BY ActionsPerWeek DESC";
            $arrFields[0] = 'ActionsPerWeek';
            $arrFields[1] = 'ActionWeek';
            break;
        case '22':
            $livesql = "SELECT count(*) as ActionsPerWeek, YEARWEEK(ActivityDate) AS ActionWeek from activity 
                WHERE EntryType = 'sower' group by YEARWEEK(ActivityDate) ORDER BY ActionsPerWeek DESC";
            $arrFields[0] = 'ActionsPerWeek';
            $arrFields[1] = 'ActionWeek';
            break;
        case '23':
            $livesql = "SELECT count(*) as ActionsPerWeek, YEARWEEK(ActivityDate) AS ActionWeek from activity 
                WHERE EntryType = 'agent' group by YEARWEEK(ActivityDate) ORDER BY ActionsPerWeek DESC";
            $arrFields[0] = 'ActionsPerWeek';
            $arrFields[1] = 'ActionWeek';
            break;
        case '24':
            $livesql = "SELECT count(*) as ActionsPerWeek, YEARWEEK(ActivityDate) AS ActionWeek from activity 
                group by YEARWEEK(ActivityDate) ORDER BY ActionsPerWeek DESC";
            $arrFields[0] = 'ActionsPerWeek';
            $arrFields[1] = 'ActionWeek';
            break;
        case '25':
            $livesql = "SELECT count(*) as ActionsPerMonth, YEAR(ActivityDate) AS ActionYear, MONTH(ActivityDate) AS ActionMonth
                from activity group by YEAR(ActivityDate), MONTH(ActivityDate) ORDER BY ActionsPerMonth DESC";
            $arrFields[0] = 'ActionsPerMonth';
            $arrFields[1] = 'ActionYear';
            $arrFields[2] = 'ActionMonth';
            break;
        case '26':
            $livesql = "SELECT count(*) as ActionsPerMonth, YEAR(ActivityDate) AS ActionYear, MONTH(ActivityDate) AS ActionMonth
                from activity WHERE EntryType = 'agent' group by YEAR(ActivityDate), MONTH(ActivityDate) 
                ORDER BY ActionsPerMonth DESC";
            $arrFields[0] = 'ActionsPerMonth';
            $arrFields[1] = 'ActionYear';
            $arrFields[2] = 'ActionMonth';
            break;
        case '27':
            $livesql = "SELECT count(*) as ActionsPerMonth, YEAR(ActivityDate) AS ActionYear, MONTH(ActivityDate) AS ActionMonth
                from activity WHERE EntryType = 'sower' group by YEAR(ActivityDate), MONTH(ActivityDate) 
                ORDER BY ActionsPerMonth DESC";
            $arrFields[0] = 'ActionsPerMonth';
            $arrFields[1] = 'ActionYear';
            $arrFields[2] = 'ActionMonth';
            break;
        case '28':
            $livesql = "SELECT count(*) as ActionsPerMonth, YEAR(ActivityDate) AS ActionYear, MONTH(ActivityDate) AS ActionMonth
                from activity WHERE EntryType = 'tender' group by YEAR(ActivityDate), MONTH(ActivityDate) 
                ORDER BY ActionsPerMonth DESC";
            $arrFields[0] = 'ActionsPerMonth';
            $arrFields[1] = 'ActionYear';
            $arrFields[2] = 'ActionMonth';
            break;
        case '30':
            $livesql = "SELECT count(*) as ActionsOverall From activity WHERE EntryType = 'agent' ORDER BY ActionsOverall DESC";
            $arrFields[0] = 'ActionsOverall';
            break;
        case '33':
            $livesql = "SELECT count(DISTINCT Pilot) as ParticipantsPerDay, DATE(ActivityDate) AS ActionDate from activity 
                group by DATE(ActivityDate) ORDER BY ParticipantsPerDay DESC";
            $arrFields[0] = 'ParticipantsPerDay';
            $arrFields[1] = 'ActionDate';
            break;
        case '34':
            $livesql = "SELECT count(DISTINCT Pilot) as ParticipantsPerDay, DATE(ActivityDate) AS ActionDate from activity 
                WHERE EntryType = 'agent' group by DATE(ActivityDate) ORDER BY ParticipantsPerDay DESC";
            $arrFields[0] = 'ParticipantsPerDay';
            $arrFields[1] = 'ActionDate';
            break;
        case '35':
            $livesql = "SELECT count(DISTINCT Pilot) as ParticipantsPerDay, DATE(ActivityDate) AS ActionDate from activity 
                WHERE EntryType = 'sower' group by DATE(ActivityDate) ORDER BY ParticipantsPerDay DESC";
            $arrFields[0] = 'ParticipantsPerDay';
            $arrFields[1] = 'ActionDate';
            break;
        case '36':
        case '37':
            $livesql = "SELECT count(DISTINCT Pilot) as ParticipantsPerDay, DATE(ActivityDate) AS ActionDate from activity 
                WHERE EntryType = 'tender' group by DATE(ActivityDate) ORDER BY ParticipantsPerDay DESC";
            $arrFields[0] = 'ParticipantsPerDay';
            $arrFields[1] = 'ActionDate';
            break;
        case '38':
            $livesql = "SELECT count(DISTINCT Pilot) as ParticipantsPerWeek, YEARWEEK(ActivityDate) AS ActionWeek from activity 
                WHERE EntryType = 'tender' group by YEARWEEK(ActivityDate) ORDER BY ParticipantsPerWeek DESC";
            $arrFields[0] = 'ParticipantsPerWeek';
            $arrFields[1] = 'ActionWeek';
            break;
        case '40':
            $livesql = "SELECT count(DISTINCT Pilot) as ParticipantsPerWeek, YEARWEEK(ActivityDate) AS ActionWeek from activity 
                WHERE EntryType = 'sower' group by YEARWEEK(ActivityDate) ORDER BY ParticipantsPerWeek DESC";
            $arrFields[0] = 'ParticipantsPerWeek';
            $arrFields[1] = 'ActionWeek';
            break;
        case '41':
            $livesql = "SELECT count(DISTINCT Pilot) as ParticipantsPerWeek, YEARWEEK(ActivityDate) AS ActionWeek from activity 
                WHERE EntryType = 'agent' group by YEARWEEK(ActivityDate) ORDER BY ParticipantsPerWeek DESC";
            $arrFields[0] = 'ParticipantsPerWeek';
            $arrFields[1] = 'ActionWeek';
            break;
        case '42':
            $livesql = "SELECT count(DISTINCT Pilot) as ParticipantsPerWeek, YEARWEEK(ActivityDate) AS ActionWeek from activity 
                group by YEARWEEK(ActivityDate) ORDER BY ParticipantsPerWeek DESC";
            $arrFields[0] = 'ParticipantsPerWeek';
            $arrFields[1] = 'ActionWeek';
            break;
        case '43':
            $livesql = "SELECT count(DISTINCT Pilot) as ParticipantsPerMonth, YEAR(ActivityDate) AS ActionYear, MONTH(ActivityDate) AS ActionMonth
                from activity group by YEAR(ActivityDate), MONTH(ActivityDate) ORDER BY ParticipantsPerMonth DESC";
            $arrFields[0] = 'ParticipantsPerMonth';
            $arrFields[1] = 'ActionYear';
            $arrFields[2] = 'ActionMonth';
            break;
        case '44':
            $livesql = "SELECT count(DISTINCT Pilot) as ParticipantsPerMonth, YEAR(ActivityDate) AS ActionYear, MONTH(ActivityDate) AS ActionMonth
                from activity WHERE EntryType = 'sower' group by YEAR(ActivityDate), MONTH(ActivityDate) 
                ORDER BY ParticipantsPerMonth DESC";
            $arrFields[0] = 'ParticipantsPerMonth';
            $arrFields[1] = 'ActionYear';
            $arrFields[2] = 'ActionMonth';
            break;
        case '45':
            $livesql = "SELECT count(DISTINCT Pilot) as ParticipantsPerMonth, YEAR(ActivityDate) AS ActionYear, MONTH(ActivityDate) AS ActionMonth
                from activity WHERE EntryType = 'tender' group by YEAR(ActivityDate), MONTH(ActivityDate) 
                ORDER BY ParticipantsPerMonth DESC";
            $arrFields[0] = 'ParticipantsPerMonth';
            $arrFields[1] = 'ActionYear';
            $arrFields[2] = 'ActionMonth';
            break;
        case '46':
            $livesql = "SELECT count(DISTINCT Pilot) as ParticipantsPerMonth, YEAR(ActivityDate) AS ActionYear, MONTH(ActivityDate) AS ActionMonth
                from activity WHERE EntryType = 'agent' group by YEAR(ActivityDate), MONTH(ActivityDate) 
                ORDER BY ParticipantsPerMonth DESC";
            $arrFields[0] = 'ParticipantsPerMonth';
            $arrFields[1] = 'ActionYear';
            $arrFields[2] = 'ActionMonth';
            break;
        case '47':
            $livesql = "SELECT count(DISTINCT Pilot) as ParticipantsOverall from activity WHERE EntryType = 'agent' 
                ORDER BY ParticipantsOverall DESC";
            $arrFields[0] = 'ParticipantsOverall';
            break;
        case '48':
            $livesql = "SELECT count(DISTINCT Pilot) as ParticipantsOverall from activity WHERE EntryType = 'sower' 
                ORDER BY ParticipantsOverall DESC";
            $arrFields[0] = 'ParticipantsOverall';
            break;
        case '49':
            $livesql = "SELECT count(DISTINCT Pilot) as ParticipantsOverall from activity WHERE EntryType = 'tender' 
                ORDER BY ParticipantsOverall DESC";
            $arrFields[0] = 'ParticipantsOverall';
            break;
        case '50':
            $livesql = "SELECT count(DISTINCT Pilot) as ParticipantsOverall from activity ORDER BY ParticipantsOverall DESC";
            $arrFields[0] = 'ParticipantsOverall';
            break;
        case '51':
            $livesql = "SELECT ActiveCaches, DATE(ActivityDate) AS ActionDate FROM cache_activity ORDER BY ActiveCaches DESC";
            $arrFields[0] = 'ActiveCaches';
            $arrFields[1] = 'ActionDate';
            break;
        case '52':
            $livesql = "SELECT COUNT(*) AS cnt, startagent, DATE(requestdate) AS RequestDate FROM rescuerequest 
                GROUP BY startagent, DATE(requestdate) ORDER BY cnt desc";
            $arrFields[0] = 'cnt';
            $arrFields[1] = 'startagent';
            $arrFields[2] = 'RequestDate';
            break;
        case '53':
            $livesql = "SELECT COUNT(*) AS cnt, startagent, YEARWEEK(requestdate) AS RequestWeek FROM rescuerequest 
                GROUP BY startagent, YEARWEEK(requestdate) ORDER BY cnt desc";
            $arrFields[0] = 'cnt';
            $arrFields[1] = 'startagent';
            $arrFields[2] = 'RequestWeek';
            break;
        case '54':
            $livesql = "SELECT COUNT(*) AS cnt, startagent, MONTH(requestdate) AS RequestMonth, YEAR(requestdate) AS RequestYear
                FROM rescuerequest GROUP BY startagent, MONTH(requestdate), YEAR(requestdate) ORDER BY cnt desc";
            $arrFields[0] = 'cnt';
            $arrFields[1] = 'startagent';
            $arrFields[2] = 'RequestMonth';
            $arrFields[3] = 'RequestYear';
            break;
        case '55':
            $livesql = "SELECT COUNT(*) AS cnt, startagent FROM rescuerequest GROUP BY startagent ORDER BY cnt desc";
            $arrFields[0] = 'cnt';
            $arrFields[1] = 'startagent';
            break;
        case '56':
        case '91':
            $livesql = "SELECT COUNT(*) AS cnt, pilot, YEAR(entrytime) AS RescueYear, MONTH(entrytime) AS RescueMonth 
                FROM rescueagents GROUP BY pilot, MONTH(entrytime), YEAR(entrytime) ORDER BY cnt desc";
            $arrFields[0] = 'cnt';
            $arrFields[1] = 'pilot';
            $arrFields[2] = 'RescueYear';
            $arrFields[3] = 'RescueMonth';
            break;
        case '57':
            $livesql = "SELECT COUNT(*) AS cnt, pilot FROM rescueagents GROUP BY pilot ORDER BY cnt desc";
            $arrFields[0] = 'cnt';
            $arrFields[1] = 'pilot';
            break;
        case '58':
            $livesql = "SELECT COUNT(ra.pilot) AS cnt, ra.pilot AS Pilot, YEARWEEK(rr.LastUpdated) AS RescueWeek
                FROM rescuerequest rr, rescueagents ra WHERE rr.status = 'closed-rescued' AND rr.id=ra.reqid 
                GROUP BY ra.pilot, YEARWEEK(LastUpdated) ORDER BY cnt DESC";
            $arrFields[0] = 'cnt';
            $arrFields[1] = 'Pilot';
            $arrFields[2] = 'RescueWeek';
            break;
        case '59':
            $livesql = "SELECT COUNT(ra.pilot) AS cnt, ra.pilot AS Pilot, MONTH(rr.LastUpdated) AS RescueMonth, YEAR(rr.LastUpdated) AS RescueYear
                FROM rescuerequest rr, rescueagents ra WHERE rr.status = 'closed-rescued' AND rr.id=ra.reqid 
                GROUP BY ra.pilot, MONTH(rr.LastUpdated), YEAR(rr.LastUpdated) ORDER BY cnt DESC";
            $arrFields[0] = 'cnt';
            $arrFields[1] = 'Pilot';
            $arrFields[2] = 'RescueMonth';
            $arrFields[3] = 'RescueYear';
            break;
        case '61':
            $livesql = "SELECT COUNT(ra.pilot) AS cnt, ra.pilot AS Pilot FROM rescuerequest rr, rescueagents ra 
                WHERE rr.status = 'closed-rescued' AND rr.id=ra.reqid GROUP BY ra.pilot ORDER BY cnt DESC";
            $arrFields[0] = 'cnt';
            $arrFields[1] = 'Pilot';
            break;
        case '62':
            $livesql = "SELECT COUNT(*) AS cnt, locateagent, MONTH(LastUpdated) AS RescueMonth, YEAR(LastUpdated) AS RescueYear
                FROM rescuerequest WHERE locateagent IS NOT NULL GROUP BY locateagent, MONTH(LastUpdated), YEAR(LastUpdated) 
                ORDER BY cnt desc";
            $arrFields[0] = 'cnt';
            $arrFields[1] = 'locateagent';
            $arrFields[2] = 'RescueMonth';
            $arrFields[3] = 'RescueYear';
            break;
        case '63':
            $livesql = "SELECT COUNT(*) AS cnt, locateagent FROM rescuerequest WHERE locateagent IS NOT NULL 
                GROUP BY locateagent ORDER BY cnt desc";
            $arrFields[0] = 'cnt';
            $arrFields[1] = 'locateagent';
            break;
        case '65':
        case '66':
        case '90':
            $livesql = "SELECT COUNT(*) AS cnt, locateagent, YEAR(lastcontact) AS RescueYear, MONTH(lastcontact) AS RescueMonth 
                FROM rescuerequest WHERE locateagent IS NOT NULL AND status = 'closed-rescued' 
                GROUP BY locateagent, MONTH(LastUpdated), YEAR(LastUpdated) ORDER BY cnt desc";
            $arrFields[0] = 'cnt';
            $arrFields[1] = 'locateagent';
            $arrFields[2] = 'RescueYear';
            $arrFields[3] = 'RescueMonth';
            break;
        case '67':
        case '68':
        case '89':
            $livesql = "SELECT COUNT(*) AS cnt, locateagent, YEARWEEK(LastUpdated) AS RescueWeek FROM rescuerequest 
                WHERE locateagent IS NOT NULL AND status = 'closed-rescued' GROUP BY locateagent, YEARWEEK(LastUpdated) 
                ORDER BY cnt desc";
            $arrFields[0] = 'cnt';
            $arrFields[1] = 'locateagent';
            $arrFields[2] = 'RescueWeek';
            break;
        case '69':
            $livesql = "SELECT COUNT(*) AS cnt, locateagent FROM rescuerequest 
                WHERE locateagent IS NOT NULL AND status = 'closed-rescued' GROUP BY locateagent ORDER BY cnt desc";
            $arrFields[0] = 'cnt';
            $arrFields[1] = 'locateagent';
            break;
        case '70':
            $livesql = "SELECT COUNT(*) AS cnt, DATE(requestdate) AS RequestDate FROM rescuerequest 
                GROUP BY DATE(requestdate) ORDER BY cnt desc";
            $arrFields[0] = 'cnt';
            $arrFields[1] = 'RequestDate';
            break;
        case '71':
            $livesql = "SELECT COUNT(*) AS cnt, YEARWEEK(requestdate) AS RequestWeek FROM rescuerequest 
                GROUP BY YEARWEEK(requestdate) ORDER BY cnt desc";
            $arrFields[0] = 'cnt';
            $arrFields[1] = 'RequestWeek';
            break;
        case '72':
            $livesql = "SELECT COUNT(*) AS cnt, MONTH(requestdate) AS RequestMonth, YEAR(requestdate) AS RequestYear 
                FROM rescuerequest GROUP BY MONTH(requestdate), YEAR(requestdate) ORDER BY cnt desc";
            $arrFields[0] = 'cnt';
            $arrFields[1] = 'RequestMonth';
            $arrFields[2] = 'RequestYear';
            break;
        case '73':
            $livesql = "SELECT COUNT(DISTINCT pilot) AS cnt FROM rescuerequest";
            $arrFields[0] = 'cnt';
            break;
        case '74':
            $livesql = "SELECT COUNT(*) AS cnt, MONTH(LastUpdated) AS RescueMonth, YEAR(LastUpdated) AS RescueYear FROM rescuerequest 
                WHERE locateagent IS NOT NULL GROUP BY MONTH(LastUpdated), YEAR(LastUpdated) ORDER BY cnt desc";
            $arrFields[0] = 'cnt';
            $arrFields[1] = 'RescueMonth';
            $arrFields[2] = 'RescueYear';
            break;
        case '75':
            $livesql = "SELECT COUNT(*) AS cnt, YEARWEEK(LastUpdated) AS RescueWeek FROM rescuerequest 
                WHERE locateagent IS NOT NULL GROUP BY YEARWEEK(LastUpdated) ORDER BY cnt desc";
            $arrFields[0] = 'cnt';
            $arrFields[1] = 'RescueWeek';
            break;
        case '76':
            $livesql = "SELECT COUNT(*) AS cnt FROM rescuerequest WHERE locateagent IS NOT NULL";
            $arrFields[0] = 'cnt';
            break;
        case '77':
        case '78':
            $livesql = "SELECT COUNT(*) AS cnt, YEARWEEK(LastUpdated) AS RescueWeek FROM rescuerequest 
                WHERE locateagent IS NOT NULL AND status = 'closed-rescued' GROUP BY YEARWEEK(LastUpdated) ORDER BY cnt desc";
            $arrFields[0] = 'cnt';
            $arrFields[1] = 'RescueWeek';
            break;
        case '79':
            $livesql = "SELECT COUNT(*) AS cnt, MONTH(LastUpdated) AS RescueMonth, YEAR(LastUpdated) AS RescueYear
                FROM rescuerequest WHERE locateagent IS NOT NULL AND status = 'closed-rescued' 
                GROUP BY MONTH(LastUpdated), YEAR(LastUpdated) ORDER BY cnt desc";
            $arrFields[0] = 'cnt';
            $arrFields[1] = 'RescueMonth';
            $arrFields[2] = 'RescueYear';
            break;
        case '80':
            $livesql = "SELECT COUNT(*) AS cnt FROM rescuerequest WHERE locateagent IS NOT NULL AND status = 'closed-rescued' 
                ORDER BY cnt desc";
            $arrFields[0] = 'cnt';
            break;
        case '81':
            $livesql = "SELECT COUNT(DISTINCT reqid) AS cnt, MONTH(entrytime) AS RescueMonth, YEAR(entrytime) AS RescueYear
                FROM rescueagents GROUP BY MONTH(entrytime), YEAR(entrytime) ORDER BY cnt desc";
            $arrFields[0] = 'cnt';
            $arrFields[1] = 'RescueMonth';
            $arrFields[2] = 'RescueYear';
            break;
        case '82':
            $livesql = "SELECT COUNT(DISTINCT reqid) AS cnt, YEARWEEK(entrytime) AS RescueWeek FROM rescueagents 
                GROUP BY YEARWEEK(entrytime) ORDER BY cnt desc";
            $arrFields[0] = 'cnt';
            $arrFields[1] = 'RescueWeek';
            break;
        case '83':
            $livesql = "SELECT COUNT(DISTINCT reqid) AS cnt FROM rescueagents ORDER BY cnt desc";
            $arrFields[0] = 'cnt';
            break;
        case '84':
            $livesql = "SELECT COUNT(id) As RescuesPerDay, DATE(lastcontact) AS RescueDate 
                FROM rescuerequest WHERE status = 'closed-esrc' OR status = 'closed-rescued' 
                GROUP BY RescueDate ORDER BY RescuesPerDay DESC, RescueDate DESC";
            $arrFields[0] = 'RescuesPerDay';
            $arrFields[1] = 'RescueDate';
            break;
        case '85':
            $livesql = "SELECT COUNT(id) AS RescuesPerWeek, YEARWEEK(lastcontact) AS RescueDate FROM rescuerequest 
                WHERE status = 'closed-esrc' OR status = 'closed-rescued' GROUP BY RescueDate 
                ORDER BY RescuesPerWeek DESC, RescueDate DESC";
            $arrFields[0] = 'RescuesPerWeek';
            $arrFields[1] = 'RescueDate';
            break;
        case '86':
            $livesql = "SELECT COUNT(id) AS RescuesPerMonth, YEAR(lastcontact) AS RescueYear, MONTH(lastcontact) AS RescueMonth 
                FROM rescuerequest 
                WHERE status = 'closed-esrc' OR status = 'closed-rescued' GROUP BY  YEAR(lastcontact), MONTH(lastcontact) 
                ORDER BY RescuesPerMonth DESC";
            $arrFields[0] = 'RescuesPerMonth';
            $arrFields[1] = 'RescueYear';
            $arrFields[2] = 'RescueMonth';
            break;
        case '18':
        case '88':
            $livesql = "SELECT count(*) as ActionsPerDay, DATE(ActivityDate) AS ActionDate from activity 
                WHERE EntryType = 'agent' group by DATE(ActivityDate) ORDER BY ActionsPerDay DESC";
            $arrFields[0] = 'ActionsPerDay';
            $arrFields[1] = 'ActionDate';
            break;
    }
    // get Live Data result(s)
    $database->query($livesql);
    $arrLiveData = $database->resultset();
    $database->closeQuery();
    // set LiveData div content
    $strHTML = printLiveData($arrFields, $arrLiveData, $recid);
}
?>

<html>

<head>
	<?php
	$pgtitle = 'Stats Records Admin';
	include_once '../includes/head.php'; 
	?>
	<style>
	<!--
		table {
			table-layout: fixed;
			word-wrap: break-word;
		}
		a,
		a:visited,
		a:hover {
			color: aqua;
		}
	-->
	</style>
</head>

<body>
<div class="container">
	<div class="row" id="header" style="padding-top: 10px;">
		<?php include_once '../includes/top-left.php'; ?>
		<div class="col-sm-8" style="text-align: center; height: 100px; vertical-align: middle;">
			<span style="font-size: 125%; font-weight: bold; color: white;">Stats Records</span>
		</div>
		<?php include_once '../includes/top-right.php'; ?>
	</div>
	<div class="ws"></div>
	<?php
	// display results for the selected date range
	$db = new Database();
		
	// records categories listing
	if (!isset($_POST['details']) && $_POST['details'] != 'yes') {	
	?>
	<div class="row" id="systable">
		<div class="col-sm-8 white">
            SELECT * FROM stats_records ORDER BY Type, PilotName, Name, Period
            <br /><br />
			<table id="example" class="table display white" style="width: auto;">
				<thead>
					<tr>
                        <th class="white">ID</th>
						<th class="white">Type</th>
						<th class="white">Name</th>
						<th class="white">Period</th>
						<th class="white">Count</th>
                        <th class="white">PilotName</th>
                        <th class="white">RecordDate</th>
                        <th class="white">&nbsp;</th>
                        <th class="white"><i class="fa fa-check"></i></th>
					</tr>
				</thead>
				<tbody>
                    <?php
                    // return Current Record result(s)
                    $database->query("SELECT * FROM stats_records ORDER BY Type, PilotName, Name, Period");
                    $arrRecords = $database->resultset();
                    $database->closeQuery();

                    // loop through recordset
                    foreach ($arrRecords as $row) {
                        echo '<tr>';
                        echo '  <td>'. $row['ID'] .'</td>';
                        echo '  <td>'. $row['Type'] .'</td>';
                        echo '  <td>'. $row['Name'] .'</td>';
                        echo '  <td>'. $row['Period'] .'</td>';
                        echo '  <td>'. $row['Count'] .'</td>';
                        echo '  <td>'. $row['PilotName'] .'</td>';
                        echo '  <td>'. $row['RecordDate'] .'</td>';
                        echo '  <td>';
                        echo '      <form>
                                        <input type="hidden" name="recid" value="'. $row['ID'] .'" />
                                        <button type="submit" class="btn btn-sm black">Run</button>
                                    </form>';
                        echo '  </td>';
                        echo '  <td>';
                        if ($recid == $row['ID']) { echo '<i class="fa fa-check"></i>'; }
                        echo '  </td>';
                        echo '</tr>';
                    }
                    ?>
				</tbody>
			</table>
		</div>
        <div class="col-sm-4 white">
            <div id="LiveData">
                <?=$strHTML?>
            </div>
		</div>
    </div>
<?php
	}

function printLiveData($arrFields, $arrLiveData, $recid)
{
    $strHTML = '<div class="sechead white" style="font-weight: bold;">Live Data for #'. $recid .'</div>';
    $strHTML = $strHTML .'<table id="rectable" class="table display white" style="width: auto;">';
    $strHTML = $strHTML .'<thead>';
    $strHTML = $strHTML .'<tr>';

    // build table headers 
    $i = 0;
    $cntArray = count($arrFields);
    while ($i < $cntArray) {
        $strHTML = $strHTML .'<th class="white">'. $arrFields[$i] .'</th>';
        $i++;
    }

    $strHTML = $strHTML .'</tr>';
    $strHTML = $strHTML .'</thead>';
    $strHTML = $strHTML .'<tbody>';

    // build table rows
    foreach ($arrLiveData as $row) {
        $i = 0;
        $strHTML = $strHTML .'<tr>';
        while ($i < $cntArray) {
            $strHTML = $strHTML .'<td>'. $row[$arrFields[$i]] .'</td>';
            $i++;
        }
        $strHTML = $strHTML .'</tr>';
    }

    $strHTML = $strHTML .'</tbody>';
    $strHTML = $strHTML .'</table>';

    return $strHTML;
}
?>
</div>

<script type="text/javascript">
	function SelectAllCopy(id) {
	    document.getElementById(id).focus();
	    document.getElementById(id).select();
	    document.execCommand("Copy");
	}
</script>

</body>
</html>