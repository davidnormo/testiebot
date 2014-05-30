<?php
require_once 'HonAPI/autoload.php';
require_once 'twitteroauth/twitteroauth.php';

$accounts = array(
	 'ImAPumpkin' => '6556316', 
	 'Testiefied' => '6725166', 
	 'AngryTestie' => '699935', 
	 'FanOfWood' => '6421092',
	 'FanOfMondi' => '6430517', 
	 'FanOfBranz' => '6216306', 
	 'Wartortle``' => '4692655', 
	 'RexxcarVroom' => '5931246', 
	 'TyAustralia' => '6582917', 
	 'FanOfHeen' => '3664904', 
	 'Testie' => '952749'	
);

$token = 'BT4UFPDFP9IXLFX7';
$client = new \HonAPI\HonClient($token);

$result = $client->getMatchHistory()->getRanked()->byNickname('imapumpkin')->fetch();
$match = json_decode($result, true);
$history = explode(",", $match[0]['history']);
$latestGame = explode("|", array_pop($history))[0];

$db = new \DB('localhost', 'dn64', 'yonnono', 'testiebot');
$result = $db->select('SELECT "yes" FROM latest WHERE matchId < ?', $latestGame)
	 ->fetch();
if($result !== false){
	//found latest match
	//sleep(10);
	$result = $client->getMatch()
		  ->setMatchId($latestGame)
		  ->getAll()
		  ->byNickname('imapumpkin')
		  ->fetch();
	$allResults = json_decode($result, true);
	$player = $allResults[2][0];
	$match = $allResults[3][0];

	 //if match has a winner and a loser
	if(!($player['wins'] === '0' && $player['losses'] === '0')){
	 	$heroJson = $client->getHero()->setId($player['hero_id'])->fetch();
		$hero = json_decode($heroJson, true)['disp_name'];
		$gameMins = $match['time_played'] / 60;
		$gpm = round($player['gold'] / $gameMins, 1);	
		$xp = round($player['exp'] / $gameMins, 1);
		$kills = $player['herokills'];
		$deaths = $player['deaths'];
		$assists = $player['heroassists'];
		$cs = $player['teamcreepkills'] + $player['neutralcreepkills'];
		$cd = $player['denies'];
		$map = $match['map'];
		$team = $player['team'] == '1' ?'Legion' : 'Hellbourne';
		$winlose = $player['wins'] == '1' ? 'won!' : 'lost.';
		$anni = $player['annihilation'] >= 1 ? ' '.$player['annihilation'].' Annihilations!' : '';
		$honbot = 'http://honbot.com/match/'.$match['match_id'];

		$tweet = $team.' '.$winlose.' '.$hero.': '.$kills.'/'.$deaths.'/'.$assists.
			 ' - '.$cs.'/'.$cd." \n ".$gpm.' GPM - '.$xp.' XP'.$anni." \n ".$honbot;

		$consumerKey = 'A1SorwuMKaYGar7KKscWAXtq1';
		$consumerSecret = 'Hcb3l7WxjFkkN4UN63LHWht4qqDGyed5Cx0n1hFc9uzq8HerMT';
		$oauthToken = '2534616121-X1ZBsLmENIGVpRL4GRcGrM4UHlhHjxjJmUgqnIZ';
		$oauthSecret = '4rnSLuZQP6zWCfpI3yOMtOPXnN5bbCibKnlTjiWzElYEl';
		$connection = new TwitterOAuth($consumerKey, $consumerSecret, $oauthToken, $oauthSecret);
		$result = $connection->post('statuses/update', array('status' => $tweet));
		var_dump($result);
	} else {	
		//match abandonded, record as latest game but don't tweet
	}
}

//Legion Win! Night Hound: 0/0/0 - 0/0 - 0 GPM - 0 XP - 1 Annihilations
//http://honbot.com/match/000
