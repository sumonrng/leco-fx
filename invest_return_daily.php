<?php
	session_start();
	$timing_start = explode(' ', microtime());
	error_reporting(-1);
	set_time_limit(0);
	ini_set('memory_limit','4096M');
	$_SESSION['token']='12345gh';	
	require 'db.php';
	//require 'functions.php';
	$timezone = "Asia/Dacca"; // 
	if(function_exists('date_default_timezone_set')) date_default_timezone_set($timezone);	
	
	function ReturnDateGap($date){
		$datetime1 = date_create($date);
		$recenDate=date("Y-m-d");
		$datetime2 = date_create($recenDate);
		$interval = date_diff($datetime1, $datetime2);
		$differDate=$interval->days;
		return $differDate;
	}
	
	function InvestReturng($date){
		global $mysqli;
		$dateR=date("Y-m-d", strtotime("-1 day"));
		$returnLimit=range(7.8,8.7, 0.05);
		shuffle($returnLimit);
		$thisMonth=$returnLimit[array_rand($returnLimit)];
		$retMonth=date("m", strtotime($date));
		$checkEntryDatr=mysqli_num_rows($mysqli->query("SELECT `serial` FROM `daily_return_per` WHERE `entry_date`='".$date."'"));
		if($checkEntryDatr<1){
			$mysqli->query("INSERT INTO `daily_return_per`(`return_percent`, `return_month`, `entry_date`) VALUES ('".$thisMonth."','".$retMonth."','".$date."')");
		}
		
		$datE=$mysqli->query("SELECT * FROM `upgrade` WHERE DATE(`date`)<='".$date."' AND `auto_id`='0'");
		while($allInvest=mysqli_fetch_assoc($datE)){
			$AutoInvestf=mysqli_fetch_assoc($mysqli->query("SELECT SUM(amount) AS totak FROM `upgrade` WHERE `auto_id`='".$allInvest['serial']."'"));
			$totalInvest=$allInvest['amount']+$AutoInvestf['totak'];
			$returnMonth=(($totalInvest*$thisMonth)/100);
			$dayMonth=date("t", strtotime($date));
			$returnDay=number_format($returnMonth/$dayMonth,2);
			
			if($returnDay>0){
				$ChekcEntry=mysqli_num_rows($mysqli->query("SELECT `serial` FROM `game_return_test` WHERE `user`='".$allInvest['user']."' AND `play_id`='".$allInvest['serial']."' AND `count_date`='".$date."'"));
				if($ChekcEntry<1){
					$mysqli->query("INSERT INTO `game_return_test`( `user`, `play_id`, `curent_bal`,`bonus_bal`,`count_date`) VALUES ('".$allInvest['user']."','".$allInvest['serial']."','".$returnDay."','".$thisMonth."','".$date."')");
				}
			}
		}
	}
	
	function SponsorCommission($date){
		global $mysqli;
		$todaysD=date("Y-m-d", strtotime($date ."-1 day"));
		$datES=$mysqli->query("SELECT * FROM `upgrade` WHERE DATE(`date`)<='".$todaysD."' AND `auto_id`='0'");
		$limitIncome=12;
		while($allInvest=mysqli_fetch_assoc($datES)){
			$totalInvest=0;
			$AutoInvestf=mysqli_fetch_assoc($mysqli->query("SELECT SUM(amount) AS totak FROM `upgrade` WHERE `auto_id`='".$allInvest['serial']."' AND DATE(`date`)<'".$todaysD."'"));
			$totalInvest=$allInvest['amount']+$AutoInvestf['totak'];
			
			$returnMonth=(($totalInvest*10)/100);
			$DateG=date("t", strtotime($date));
			$returnDay=number_format((($returnMonth/$limitIncome)/$DateG),2);
			$dateM=date("Y-m-d", strtotime($date));
			if($checkInc<$limitIncome){
				$CheckPrevEntry=$mysqli->query("SELECT `amount`,`total_invest` FROM `sponsor_com_daily` WHERE `user`='".$allInvest['sponsor']."' AND `from_user`='".$allInvest['user']."' AND `com_month`='".$dateM."'");
				$EntryCount=mysqli_num_rows($CheckPrevEntry);
				if($EntryCount>0){
					$prevIncome=mysqli_fetch_assoc($CheckPrevEntry);
					$todayTotal=$prevIncome['amount']+$returnDay;
					$TodayInvest=$prevIncome['total_invest']+$totalInvest;
					$mysqli->query("UPDATE `sponsor_com_daily` SET `total_invest`='".$TodayInvest."', `amount`='".$todayTotal."' WHERE `user`='".$allInvest['sponsor']."' AND `from_user`='".$allInvest['user']."' AND `com_month`='".$dateM."'");
				}else{
					$mysqli->query("INSERT INTO `sponsor_com_daily`( `user`, `from_user`, `total_invest`, `play_id`, `amount`, `com_month`) VALUES ('".$allInvest['sponsor']."','".$allInvest['user']."','".$totalInvest."','".$allInvest['serial']."','".$returnDay."','".$dateM."')");
				}
			}
		}
	}
	
	function PremiumBonus($date){
		global $mysqli;
		$Comsd=range(2,9,1);
		shuffle($Comsd);
		$thisMonth="0.000".$Comsd[array_rand($Comsd)];
		$datE=$mysqli->query("SELECT DISTINCT `user` FROM `upgrade` WHERE DATE(`date`)<='".$date."' AND `auto_id`='0'");
		while($allInvest=mysqli_fetch_assoc($datE)){
			$selfDeposit=mysqli_fetch_assoc($mysqli->query("SELECT SUM(amount) AS totals FROM `upgrade` WHERE `user`='".$allInvest['user']."'"));
			if($selfDeposit['totals']>=3000){
				$depoSitCount=$selfDeposit['totals'];
				$CommissionThisMonth=($depoSitCount*$thisMonth);
				if($CommissionThisMonth>0){
					$checlPrev=mysqli_num_rows($mysqli->query("SELECT `user` FROM `leader_premium_daily` WHERE `user`='".$allInvest['user']."' AND `com_date`='".$date."'"));
					if($checlPrev<1){
						$mysqli->query("INSERT INTO `leader_premium_daily`( `user`, `amount`, `com_date`) VALUES ('".$allInvest['user']."','".$CommissionThisMonth."','".$date."')");
					}
				}
			}
		}
	}
	if($tsponsor['tspon']<4.5){
		$SponsorBonus=0;
	}elseif($tsponsor['tspon']==4.5){
		$SponsorBonus=((($require_amn*$PackAInfo['direct_com'])/100)*3)-$tsponsor['tspon'];
	}elseif($tsponsor['tspon']>4.5){
		$SponsorBonus=(($require_amn*$PackAInfo['direct_com'])/100);
	}
	function daily_in($userlist,$amount,$date){
		global $mysqli;
		$iiiin=implode("' OR DATE(`count_date`)='".$date."' AND `user`='", $userlist);
		$rrett=" DATE(`count_date`)='".$date."' AND `user`='" . $iiiin ."'";
		$ttyy=mysqli_fetch_assoc($mysqli->query("SELECT SUM(`curent_bal`) AS total FROM `game_return_test` WHERE  $rrett"));
		$totalGET=$ttyy['total']*0.10;
		return number_format((($totalGET*$amount)/100),2);
	}
	
	function downUser($users,$table){
		global $mysqli;
		$fff=array();
		if(is_array($users)){
			foreach($users as $user){
				$uu=$mysqli->query("select `sponsor`,`user` from `$table` where sponsor='".$user."'");
				while($spp=mysqli_fetch_assoc($uu)){
					array_push($fff, $spp['user']);
				}
			}
		}else{
			$uu=$mysqli->query("select `sponsor`,`user` from `$table` where sponsor='".$users."'");
			while($spp=mysqli_fetch_assoc($uu)){
				array_push($fff, $spp['user']);
			}
		}
		return $fff;
	}
	
	
	function GenerationCommission($dateE){
		global $mysqli;
		$urt=array(50,30,20);
		$date=date("Y-m-d", strtotime($dateE));
		$SelctRank=$mysqli->query("SELECT DISTINCT `user` FROM `bcpp` WHERE DATE(`date`)<='".$date."'");
		while($allUsd=mysqli_fetch_assoc($SelctRank)){
			$memberid=$allUsd['user'];
			$yyy=array();
			$ttt2=downUser($memberid,'member');
			
			$yyy[0]=daily_in($ttt2,$urt[0],$date);
			for($i=1;$i<=2;$i++){
				$ttt2=downUser($ttt2,'member');
				$yyy[$i]=daily_in($ttt2,$urt[0],$date);
			}
			$levelData=json_encode($yyy);
			$genIncome=array_sum($yyy);
			
			if($genIncome>0){
				$CheckPrev=mysqli_num_rows($mysqli->query("SELECT `user` FROM `generation_income_daily` WHERE `user`='".$allUsd['user']."' AND DATE(`com_date`)='".$date."'"));
				if($CheckPrev<1){
					$mysqli->query("INSERT INTO `generation_income_daily`( `user`, `amount`, `level_data`, `com_date`) VALUES ('".$allUsd['user']."','".$genIncome."','".$levelData."','".$date."')");
				}
			}
		}
	}
	
	function SpreadCommission($date){
		global $mysqli;
		$ranks=array("i","ib","oib","sib","tib","mib");
		$deposits=array(3000,10000,30000,100000,500000,2500000);
		$Comsd=range(2.50,3.50,0.05);
		shuffle($Comsd);
		$thisMonth=$Comsd[array_rand($Comsd)];
		
		$SelctRank=$mysqli->query("SELECT DISTINCT `user` FROM `bcpp` WHERE DATE(`date`)<='".$date."'");
		while($allUsd=mysqli_fetch_assoc($SelctRank)){
			$track=array();
			$SelcRt=$mysqli->query("SELECT `mood` FROM `bcpp` WHERE `user`='".$allUsd['user']."'");
			while($allRank=mysqli_fetch_assoc($SelcRt)){
				$SetrY=array_search($allRank['mood'],$ranks);
				array_push($track,$SetrY);
			}
			$recentRank=max($track);
			$depoSitCount=$deposits[$recentRank];
			$monthCount=date("t", strtotime($date));
			$CommissionThisMonth=((($depoSitCount*$thisMonth)/100)/$monthCount);
			if($CommissionThisMonth>0){
				$checlPrev=mysqli_num_rows($mysqli->query("SELECT `user` FROM `leader_support_daily` WHERE `user`='".$allUsd['user']."' AND `com_date`='".$date."'"));
				if($checlPrev<1){
					$mysqli->query("INSERT INTO `leader_support_daily`( `user`, `amount`, `rank`, `com_date`) VALUES ('".$allUsd['user']."','".$CommissionThisMonth."','".$ranks[$recentRank]."','".$date."')");
				}
			}
		}
	}
	
	
	function SpreadCommissionPremium($date){
		global $mysqli;
		$ranks=array("i","ib","oib","sib","tib","mib");
		$deposits=array(3000,10000,30000,100000,500000,2500000);
		$Comsd=range(0.90,1.1,0.01);
		shuffle($Comsd);
		$thisMonth=$Comsd[array_rand($Comsd)];
		$SelctRank=$mysqli->query("SELECT DISTINCT `user` FROM `bcpp` WHERE DATE(`date`)<='".$date."'");
		while($allUsd=mysqli_fetch_assoc($SelctRank)){
			$selfDeposit=mysqli_fetch_assoc($mysqli->query("SELECT SUM(amount) AS totals FROM `upgrade` WHERE `user`='".$allUsd['user']."'"));
			if($selfDeposit['totals']>=3000){
				$track=array();
				$SelcRt=$mysqli->query("SELECT `mood` FROM `bcpp` WHERE `user`='".$allUsd['user']."'");
				while($allRank=mysqli_fetch_assoc($SelcRt)){
					$SetrY=array_search($allRank['mood'],$ranks);
					array_push($track,$SetrY);
				}
				$recentRank=max($track);
				$depoSitCount=$deposits[$recentRank];
				$monthCount=date("t", strtotime($date));
				$CommissionThisMonth=((($depoSitCount*$thisMonth)/100)/$monthCount);
				
				if($CommissionThisMonth>0){
					$checlPrev=mysqli_num_rows($mysqli->query("SELECT `user` FROM `leader_support2_daily` WHERE `user`='".$allUsd['user']."' AND `com_date`='".$date."'"));
					if($checlPrev<1){
						$mysqli->query("INSERT INTO `leader_support2_daily`( `user`, `amount`, `rank`, `com_date`) VALUES ('".$allUsd['user']."','".$CommissionThisMonth."','".$ranks[$recentRank]."','".$date."')");
					}
				}
			}
		}
	}
	
	
	
	$dateRE=date('H');
	if($dateRE==0){
		$StartDate=date("Y-m-01",strtotime("-2 days"));
		$gapDate=ReturnDateGap($StartDate);
		for($i=0;$i<=$gapDate; $i++){
			$CountableDate=date("Y-m-d", strtotime($StartDate ."+$i day"));
			$checkEntryDatr=mysqli_num_rows($mysqli->query("SELECT `serial` FROM `daily_return_per` WHERE `entry_date`='".$CountableDate."'"));
			if($checkEntryDatr<1){
				InvestReturng($CountableDate);
			}
			
			$checlPrev1=mysqli_num_rows($mysqli->query("SELECT `user` FROM `sponsor_com_daily` WHERE `com_month`='".$CountableDate."'"));
			if($checlPrev1<1){
				SponsorCommission($CountableDate);
			}
			
			$checlPrev2=mysqli_num_rows($mysqli->query("SELECT `user` FROM `leader_premium_daily` WHERE `com_date`='".$CountableDate."'"));
			if($checlPrev2<1){
				PremiumBonus($CountableDate);
			}
			
			$CheckPrev3=mysqli_num_rows($mysqli->query("SELECT `user` FROM `generation_income_daily` WHERE DATE(`com_date`)='".$CountableDate."'"));
			if($CheckPrev3<1){
				GenerationCommission($CountableDate);
			}
			
			$checlPrev4=mysqli_num_rows($mysqli->query("SELECT `user` FROM `leader_support_daily` WHERE `com_date`='".$CountableDate."'"));
			if($checlPrev4<1){
				SpreadCommission($CountableDate);
			}
			
			$checlPrev5=mysqli_num_rows($mysqli->query("SELECT `user` FROM `leader_support2_daily` WHERE `com_date`='".$CountableDate."'"));
			if($checlPrev5<1){
				SpreadCommissionPremium($CountableDate);
			}
			
		}
		
		//
	}
?>