<?php
    
    error_reporting(0);
    $apikey = "e6f522a4-e934-471a-aa0a-0995aabcd58b";
    // API key is requirement to have access to data, instead of typing it over and over again. I can just use variable.

    $summonerData = array("name" => $_GET["name"], "urlname" => preg_replace("/ /", "%20", $_GET["name"]), "capsname" => strtolower(preg_replace("/ /", "", $_GET["name"])));
    // Name translated three times, one is the original full name, second is name that I pass to the server, third is name in the received array.

    $resp = json_decode(file_get_contents("https://euw.api.pvp.net/api/lol/euw/v1.4/summoner/by-name/" . $summonerData["urlname"] . "?api_key=" . $apikey), true);
    // Before anything, data must be retrieved, this does just that, it seeks player by name, shows his/her basic information.
    // Level, ID (important), name, server and some other stuff.
    if ($http_response_header[0] == "HTTP/1.1 404 Not Found") { die("{}|!|{}"); }
    // Faulty Jack, checks if there is such user before wasting more bandwidth.
    $summonerData = array_merge($summonerData, array("level" => $resp[$summonerData["capsname"]]["summonerLevel"], "id" => $resp[$summonerData["capsname"]]["id"]));
    // Adds received response to already collected data.
    $resp = json_decode(file_get_contents("https://euw.api.pvp.net/api/lol/euw/v2.5/league/by-summoner/" . $summonerData["id"] . "?api_key=" . $apikey), true);
    // Get ranked data. The only one that matter in tier promotions or something else. This shows the placements.
    
    foreach ($resp[$summonerData["id"]][0]["entries"] as $_tmp) {
        // Just skipping an amount of arrays and bruteforcing through them to find wanted result.
        if ($_tmp["playerOrTeamName"] == $summonerData["name"]) {
            // If bruteforced result, has the same name as the user requested, that means it's the target.
            $summonerData = array_merge($summonerData, 
                                        array("division" => $_tmp["division"],
                                              "lp" => $_tmp["leaguePoints"],
                                              "ranked_wins" => $_tmp["wins"],
                                              "ranked_losses" => $_tmp["losses"]
                                             )
                                       );
            // Extracting and saving useful information into temporary variables, later to be translated into new ones.
        }
    }
    if ($http_response_header[0] == "HTTP/1.1 404 Not Found" || count($resp[$summonerData["id"]][0]) == 0) { $summonerData = array_merge($summonerData, array("league" => "Unranked")); }
    else { $summonerData = array_merge($summonerData, array("league" => ucfirst(strtolower($resp[$summonerData["id"]][0]["tier"])))); }
    // Merging temporal information from arrays above, into "user information" array, alongside his/her name, level and ID.

    $resp = json_decode(file_get_contents("https://euw.api.pvp.net/api/lol/euw/v1.3/game/by-summoner/" . $summonerData["id"] . "/recent?api_key=" . $apikey), true);
    // Getting last 10 matches played.

    $x = 0;
    foreach ($resp["games"] as $_tmp) {
        // Skipping unwanted results, and proceeding to ID of our user.
        if ($_tmp["stats"]["championsKilled"] == null) { $_tmp["stats"]["championsKilled"] = 0; }
        if (array_key_exists("championsKilled", $_tmp)) { $_tmp["stats"]["championsKilled"] = 0; }
        // Somewhat common, when player made no kills, an error pops up about missing key, this should fix majority of them.        
        if ($_tmp["stats"]["win"] == "") { $_tmp["stats"]["win"] = false; }
        if ($_tmp["stats"]["assists"] == null) { $_tmp["stats"]["assists"] = 0; }
        // Happens as well, if player won, the result is "true", but when lost result is nothing, just plain "", this enforces it for easier use.
        
        if ($_tmp["mapId"] == 1) { $_tmp["mapId"] = "Summoner's Rift"; }
        if ($_tmp["mapId"] == 10) { $_tmp["mapId"] = "Twisted Treeline"; }
        if ($_tmp["mapId"] == 11) { $_tmp["mapId"] = "Summoner's Rift"; }
        if ($_tmp["mapId"] == 12) { $_tmp["mapId"] = "Howling Abyss"; }
        if ($_tmp["mapId"] == 14) { $_tmp["mapId"] = "Butcher's Bridge"; }
        // This translates MapID which noone cares about into phonetic name of the map.
        
        if ($_tmp["stats"]["numDeaths"] == null) { $_tmp["stats"]["numDeaths"] = 0; }
        if ($_tmp["stats"]["championsKilled"] == null) { $_tmp["stats"]["championsKilled"] = 0; }
        if ($_tmp["stats"]["assists"] == null) { $_tmp["stats"]["assists"] = 0; }
        if ($_tmp["stats"]["minionsKilled"] == null) { $_tmp["stats"]["minionsKilled"] = 0; }
        
        
        $idToNameCoversion = array(
            '429' => 'Kalista','421' => 'RekSai','412' => 'Thresh','268' => 'Azir','267' => 'Nami','266' => 'Aatrox','254' => 'Vi','238' => 'Zed','236' => 'Lucian','222' => 'Jinx','201' => 'Braum','161' => 'Velkoz','157' => 'Yasuo','154' => 'Zac','150' => 'Gnar','143' => 'Zyra','134' => 'Syndra','133' => 'Quinn','131' => 'Diana','127' => 'Lissandra','126' => 'Jayce','122' => 'Darius','121' => 'Khazix','120' => 'Hecarim','119' => 'Draven','117' => 'Lulu','115' => 'Ziggs','114' => 'Fiora','113' => 'Sejuani','112' => 'Viktor','111' => 'Nautilus','110' => 'Varus','107' => 'Rengar','106' => 'Volibear','105' => 'Fizz','104' => 'Graves','103' => 'Ahri','102' => 'Shyvana','101' => 'Xerath','99' => 'Lux','98' => 'Shen','96' => 'KogMaw','92' => 'Riven','91' => 'Talon','90' => 'Malzahar','89' => 'Leona','86' => 'Garen','85' => 'Kennen','84' => 'Akali','83' => 'Yorick','82' => 'Mordekaiser','81' => 'Ezreal','80' => 'Pantheon','79' => 'Gragas','78' => 'Poppy','77' => 'Udyr','76' => 'Nidalee','75' => 'Nasus','74' => 'Heimerdinger','72' => 'Skarner','69' => 'Cassiopeia','68' => 'Rumble','67' => 'Vayne','64' => 'LeeSin','63' => 'Brand','62' => 'MonkeyKing','61' => 'Orianna','60' => 'Elise','59' => 'JarvanIV','58' => 'Renekton','57' => 'Maokai','56' => 'Nocturne','55' => 'Katarina','54' => 'Malphite','53' => 'Blitzcrank','51' => 'Caitlyn','50' => 'Swain','48' => 'Trundle','45' => 'Veigar','44' => 'Taric','43' => 'Karma','42' => 'Corki','41' => 'Gangplank','40' => 'Janna','39' => 'Irelia','38' => 'Kassadin','37' => 'Sona','36' => 'DrMundo','35' => 'Shaco','34' => 'Anivia','33' => 'Rammus','32' => 'Amumu','31' => 'Chogath','30' => 'Karthus','29' => 'Twitch','28' => 'Evelynn','27' => 'Singed','26' => 'Zilean','25' => 'Morgana','24' => 'Jax','23' => 'Tryndamere','22' => 'Ashe','21' => 'MissFortune','20' => 'Nunu','19' => 'Warwick','18' => 'Tristana','17' => 'Teemo','16' => 'Soraka','15' => 'Sivir','14' => 'Sion','13' => 'Ryze','12' => 'Alistar','11' => 'MasterYi','10' => 'Kayle','9' => 'FiddleSticks','8' => 'Vladimir','7' => 'Leblanc','6' => 'Urgot','5' => 'XinZhao','4' => 'TwistedFate','3' => 'Galio','2' => 'Olaf','1' => 'Annie'
        );
        // Array I generated using PHP to save myself lots of time. Sorted by descending key, to avoid future problems with str_replace().
        
        $resp = json_decode(file_get_contents("https://global.api.pvp.net/api/lol/static-data/euw/v1.2/champion/" . $_tmp["championId"] . "?api_key=" . $apikey), true);
        $_tmp["championTitle"] = $resp["title"];
        $_tmp["championName"] = $resp["name"];
        
        $_tmp["championUrlName"] = str_replace(array_keys($idToNameCoversion), $idToNameCoversion, $_tmp["championId"]);
        // This one takes the championID, which is again useless, instead I just made a list above, which will seek out the
        // related ID and replace it with name of the champion. So number "268", changes into "Thresh".

        $summonerMatches[$x]["mode"] = $_tmp["gameMode"];
        $summonerMatches[$x]["map"] = $_tmp["mapId"];
        $summonerMatches[$x]["team"] = $_tmp["teamId"];
        $summonerMatches[$x]["champion"] = $_tmp["championName"];
        $summonerMatches[$x]["champtitle"] = $_tmp["championTitle"];
        $summonerMatches[$x]["champurlname"] = $_tmp["championUrlName"];
        $summonerMatches[$x]["ipEarned"] = $_tmp["ipEarned"];
        $summonerMatches[$x]["victory"] = $_tmp["stats"]["win"];
        $summonerMatches[$x]["creeps"] = $_tmp["stats"]["minionsKilled"];
        $summonerMatches[$x]["kills"] = $_tmp["stats"]["championsKilled"];
        $summonerMatches[$x]["deaths"] = $_tmp["stats"]["numDeaths"];
        $summonerMatches[$x]["assists"] = $_tmp["stats"]["assists"];
        // echo $x;
        // Placing results of one's matches in retrieveable multi-dimensional array. Calling up the matches should be super easy now.
        // This will be popped out at the end for JavaScript to "back-parse" it.
        
        $x++;
    }

    $x = null;
    $i = null;
    $_tmp = null;
    // Cleaning the results O_O, FBI might be spying.
    // echo str_replace("world","Peter","Hello world!");
    print_r(json_encode($summonerMatches));
    echo "|!|";
    print_r(json_encode($summonerData));
    // Now smack everything into JavaScript's face... oh lord...
?>	