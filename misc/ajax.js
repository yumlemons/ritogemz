var _tmp;
// Just because it is used so very often.
var _circutExecution = 1;
// Allows future execution of code, if requirements are fulfilled.
var body = document.getElementsByTagName("body")[0];
// Global grasper, so that certain visual items can be changed.
$(document).ready(function(){
    $("#resultsShown").fadeOut(0);
    $("#loadingBox").fadeOut(0);
    $("#errorBox").fadeOut(0);
});
// Nobody needs to see the empty skeleton. Same for brainless loader.

function GetData(el) {
    if (event.keyCode == 13 && el.value != "") {
        // Checking if Enter is pressed and not "empty".
        
        document.getElementById("loadingBox").style.visibility = "visible";
        document.getElementById("errorBox").style.visibility = "visible";
        document.getElementById("resultsShown").style.visibility = "visible";
        // Killed a glitch where boxes would appear for 10ms before hiding.
        
        var summonerName = el.value;
        // Assigning value of username to more phonetic variable.
        var summonerRequest = new XMLHttpRequest;
        // Creating AJAX responsive item and request.
        
        summonerRequest.onreadystatechange=function() {
            if (summonerRequest.readyState == 4 && summonerRequest.status == 200) {
                var response = summonerRequest.responseText;
                var _tmp = response.split("|!|");
                FetchMatchData(_tmp[0]);
                FetchUserData(_tmp[1]);
            }
        }
        summonerRequest.open("GET", "misc/datareq.php?name=" + summonerName, true);
        summonerRequest.send();
        LoadingBarShow();
    }
}

function FetchUserData(data) {
    var data = JSON.parse(data);
    // JSON response, converting to JavaScript arrays.
    
    if (data["name"] == undefined && data["level"] == undefined) { NotFound(); }
    if (data["league"] == "Platinum" && data["division"] == undefined) { NotFound(); }
    // Checks if the name is real and player has been found, also, player named "undefined" is detected as regular user.
    
    var _el = document.getElementById("tier_indic");
    // Tier indicator grasp.
    var toDisplay = {};
    
    if (data["league"] == "Unranked") {
        toDisplay["league"] = "Unranked";
        toDisplay["lp"] = "Level " + data["level"];
        _el.style.backgroundImage = "url('img/tiers/" + data["league"] + ".png')";
    }
    else if (data["league"] == "Challenger") {
        toDisplay["league"] = "<font color=#F1B82D>Challenger</font>";
        toDisplay["lp"] = data["lp"] + " LP";
    _el.style.backgroundImage = "url('img/tiers/" + data["league"] + ".png')";
    }
    else if (data["league"] == "Master") {
        toDisplay["league"] = "Master";
        toDisplay["lp"] = data["lp"] + " LP";
    _el.style.backgroundImage = "url('img/tiers/" + data["league"] +  ".png')";
    }
    else {
        toDisplay["league"] = data["league"] + " " + data["division"];
        toDisplay["lp"] = data["lp"] + " LP";
    _el.style.backgroundImage = "url('img/tiers/" + data["league"] + data["division"] + ".png')";
    }
    _el.innerHTML = toDisplay["league"] + "<br /><span>" + toDisplay["lp"] + "</span>";
    // League management. No reason to display the only tier in it's own league.
    // Writing league and tier data on it's place. Changing the image to tier image.
    
    var _el = document.getElementById("winloss_indic");
    var _winratio = Math.round((data["ranked_wins"] / (data["ranked_wins"] + data["ranked_losses"])) * 1000 ) / 10;
    // Getting winratio percentage, by 1 decimal precision.
    
    if (data["league"] == "Unranked") { _el.innerHTML = ""; }
    // Unranked = No Ranked Matches = No ranked Win/Loss
    else { _el.innerHTML = "<span>" + data["ranked_wins"] + "</span>/<span>" + data["ranked_losses"] + "</span><br /><span>" + _winratio + "% winratio</span>"; } 
}

function FetchMatchData(data) {
    var data = JSON.parse(data);
    // Translate JSON response into JavaScript array.    
    
    var _el = document.getElementById("matchhistory_indic");
    // Element management grasp.
    var _tmp = "";
    
    var _assistCount = 0;
    var _killsCount = 0;
    var _deathsCount = 0;
    var _csCount = 0;
    // Counters, for future values, summs up all games.
    
    for (var x = 0; x <= 4; x++) {
        if (data[x] == null) { NotFound(); break; }
        // If game doesn't have any data or doesn't exist, quit the process of for();
        if (data[x]["victory"] == true) { _statColor = "green"; _brigColor = "lightgreen"; _outText = "Victory"; } else { _statColor = "red"; _brigColor = "red"; _outText = "Defeat"; }
        
        _el.innerHTML = "<table class='match' style='float: left'><tr><td class='match_face' rowspan='4' style='background-image: url(http://ddragon.leagueoflegends.com/cdn/5.2.1/img/champion/" + data[x]["champurlname"] +".png);'></td><td style='background-color: " + _statColor + "' class='match_indicator' rowspan='4'></td><td class='match_state' style='color: " + _brigColor + ";' colspan='2'>" + _outText + "</td></tr><tr><td colspan='2' class='match_champinfo'>" + data[x]["champion"] + "</td></tr><tr><td class='match_stats'>" + data[x]["kills"] + "/" + data[x]["deaths"] + "/" + data[x]["assists"] + "</td><td class='match_stats'>" + data[x]["creeps"] + "cs</td></tr><tr><td colspan='2' class='match_map'>" + data[x]["map"] + "</td></tr></table>";
        _tmp = _tmp + _el.innerHTML;
        _assistCount = data[x]["assists"] + _assistCount;
        _killsCount = data[x]["kills"] + _killsCount;
        _deathsCount = data[x]["deaths"] + _deathsCount;
        _csCount = data[x]["creeps"] + _csCount;
        
        x++;
        if (data[x] == null) { NotFound(); break; }
        // If game doesn't have any data or doesn't exist, quit the process of for();
        if (data[x]["victory"] == true) { _statColor = "green"; _brigColor = "lightgreen"; _outText = "Victory"; } else { _statColor = "red"; _brigColor = "red"; _outText = "Defeat"; }
        _el.innerHTML = "<table class='match' style='float: right'><tr><td class='match_face' rowspan='4' style='background-image: url(http://ddragon.leagueoflegends.com/cdn/5.2.1/img/champion/" + data[x]["champurlname"] +".png);'></td><td style='background-color: " + _statColor + "' class='match_indicator' rowspan='4'></td><td class='match_state' style='color: " + _brigColor + ";' colspan='2'>" + _outText + "</td></tr><tr><td colspan='2' class='match_champinfo'>" + data[x]["champion"] + "</td></tr><tr><td class='match_stats'>" + data[x]["kills"] + "/" + data[x]["deaths"] + "/" + data[x]["assists"] + "</td><td class='match_stats'>" + data[x]["creeps"] + "cs</td></tr><tr><td colspan='2' class='match_map'>" + data[x]["map"] + "</td></tr></table>";
        _tmp = _tmp + _el.innerHTML;
        
        _assistCount = data[x]["assists"] + _assistCount;
        _killsCount = data[x]["kills"] + _killsCount;
        _deathsCount = data[x]["deaths"] + _deathsCount;
        _csCount = data[x]["creeps"] + _csCount;
    }
    // Too long to explain, comments might be longer than actual code.
    document.getElementById("kda_indic").innerHTML = _killsCount + "/" + _deathsCount + "/" + _assistCount + "<br /><span>Kills/Deaths/Assists</span>";
    document.getElementById("cs_indic").innerHTML = _csCount + " creeps<br /><span>Total of all visible games</span>";
    // Replacing default content with user data when requested.

    _el.innerHTML = _tmp;
    // Showing the matches.
    
    if (_circutExecution === 1) { Reveal(); }
}

function LoadingBarShow() {
    Hide();
    $(document).ready(function(){
        $("#loadingBox").fadeIn();
    });
}

function Reveal() {
    $(document).ready(function(){
        $("#loadingBox").fadeOut();
        $("#resultsShown").fadeIn();
    });
}

function Hide() {
    $(document).ready(function(){
        $("#introText").fadeOut();
        $("#resultsShown").fadeOut();
        $("#errorBox").fadeOut();
    });
}

function NotFound() {
    Hide();
    $(document).ready(function(){
        $("#loadingBox").fadeOut();
        $("#resultsShown").fadeOut();
        $("#errNotFound").fadeIn();
    });
    _circutExecution = 0;
    $("#errorBox").fadeIn();
}