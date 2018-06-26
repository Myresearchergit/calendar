<?php
error_reporting(E_ALL); ini_set('display_errors', 1);

$leapYearDiff = 5;
$startweekdate = "01-01-1990"; //monday
$totalMonthInYear = 13;	
$evenMonthDays = 21;
$oddMonthDays = 22;
	
if($_POST ){
	if( !empty($_POST['customDate'])){
		$customDate = $_POST['customDate']; // custom date
		$validateResult = validateDate($customDate);
		if(!$validateResult){
			echo "Invalid Date or Format.";
		}else{
			$yearTotalDays = getYearDays($evenMonthDays, $oddMonthDays, $totalMonthInYear);
			$leapYearTotalDays = ($yearTotalDays - 1);

			$day = getDaysBetweenYears($startweekdate, $customDate);
			echo "Day of date: ".$_POST['customDate']." is ".$day;
		}
	}
	else{
		echo "Date cannot be blank.";
		
	}

}

/*
* This function used to validate date as per format, leapyear day and valid range of dd,mm,yyyy 
* @param $customDate
*/
function validateDate($customDate) {
	global $evenMonthDays, $oddMonthDays;
	if(strpos($customDate, "-") == false){
		return false;
	}
	list($d,$m,$y) = explode('-', $customDate);
	$error = 0;
	$d = intval($d);
	$yearDays = 21;
	if($y%5 == 0){
		$yearDays = 20;
	}

	if($d < 0 || (strlen($d) > 2)){
		$error ++;
	}
	
	if($m < 0 || $m > 13 || (strlen($m) > 2)){
		$error ++;
	}

	$m = intval($m);
	/*Check day as per even or odd month range */

	if($m%2 == 0){
		if($d > $evenMonthDays){
			$error++;
		}
	}else{
		($d > $oddMonthDays);
		if($d > $oddMonthDays){
			$error++;
		}
	}
	if($y < 1990 || (strlen($y) != 4)){
		$error ++;
	}
	if($error > 0) return false;
    return true;
}

/*
* This function used to get odd even months count in one year 
* @param $last
*/
function getEvenOddCount($last){
	$arr = array('even' => 0, 'odd' => 0);
	$arr['even'] = intval(($last/2));
	$arr['odd'] = ($last - $arr['even']);
	return $arr;
}

/*
* This function used to get total days between one full year
* @param $evenMonthDays, $oddMonthDays, $totalMonthInYear
*/
function getYearDays($evenMonthDays, $oddMonthDays, $totalMonthInYear){

	$evenOddCount = getEvenOddCount($totalMonthInYear);
	$totalDays = 0;
	$totalDays += ($evenOddCount['even'] * $evenMonthDays);
	$totalDays += ($evenOddCount['odd'] * $oddMonthDays);
	return $totalDays;
}


/*
* This function used to get total days between start and last previous year
* @param $startweekdate, $customDate
*/
function getDaysBetweenYears($startweekdate, $customDate){
	$weekdays = array(0 => 'Sunday',1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday');
	
	$dateEntered = $customDate;
	$startweekdate = explode('-', $startweekdate);
	$startYear = $startweekdate[2];
	
	$customDate = explode('-', $customDate);
	$customDate[2] = intval($customDate[2]);
	$customDate[2] = ($customDate[2] - 1);
	$lastPrevYear = $customDate[2];
	$totalDiffDays = 0;
	/* get days between start year to last previous year */
	$totalDiffDays += getLeapAndNonLeapYearBetweenYears($startYear, $lastPrevYear);	
	/* get remaining days with current year date */
	$totalDiffDays += getLeftDaysInCurrentDate($dateEntered);	
	$remainderWeekDay = 0;
	if($totalDiffDays > 0){
		$remainderWeekDay = ($totalDiffDays%7);
	}
	if(isset($weekdays[$remainderWeekDay])){
		return $weekdays[$remainderWeekDay];
	}
	return $totalDiffDays;
	
}


/*
* This function used to get total sum days of leap and non leap year
* @param $startY, $endY
*/
function getLeapAndNonLeapYearBetweenYears($startY, $endY){
	global $leapYearDiff, $yearTotalDays, $leapYearTotalDays;
	$leapYearCount = 0;
	$nonLeapYearCount = 0;
	$totalDaysBetweenDates = 0;
	for($i=$startY;$i<=$endY;$i++){

		if(($i%$leapYearDiff) == 0){
			$leapYearCount++;
		}else{
			$nonLeapYearCount++;
		}
	}
	$totalDaysBetweenDates += ($leapYearCount * $leapYearTotalDays);
	$totalDaysBetweenDates += ($nonLeapYearCount * $yearTotalDays);
	return $totalDaysBetweenDates;

}

/*
* This function used to get remaining days within entered date year
* @param $customDate
*/
function getLeftDaysInCurrentDate($customDate){
	global $evenMonthDays, $oddMonthDays;
	$customDateArr = explode('-', $customDate);
	$day = $customDateArr[0];
	$month = $customDateArr[1];
	$year = $customDateArr[2];
	$remainingDays = 0;
	$month = intval($month);
	if($month > 1){
		$prevMonth = ($month - 1);
		$oddEvenArr = getEvenOddCount($prevMonth);
		$remainingDays += ($oddEvenArr['even'] * $evenMonthDays);
		$remainingDays += ($oddEvenArr['odd'] * $oddMonthDays);
	}
	$currentMonthRemainingDays = intval($day);
	$remainingDays += $currentMonthRemainingDays;
	return $remainingDays;
}

?>

<form action="" method="POST">
	<label>Enter Date: </label><input type="text" required name="customDate" placeholder="dd-mm-yyyy" value="<?php echo isset($_POST['customDate'])?$_POST['customDate']:'';?>" />
	<input type="submit" value="Submit">
</form>
