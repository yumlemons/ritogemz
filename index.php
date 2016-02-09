<!DOCTYPE html>
<html>
    <head>
        <title>Example Project</title>
        <meta name="author" content="Adrian Miszczuk">
        <link rel="shortcut icon" type="image/x-icon" href="http://www.lolstreams.info/wp-content/uploads/2014/07/ikonica-lol.ico" />
        <link rel="stylesheet" href="css/screen.css" media="screen" />
        <link rel="stylesheet" href="css/global.css" media="all" />
        <script type="text/javascript" src="misc/jquery-1.11.3.min.js"></script>
        <script type="text/javascript" src="misc/ajax.js"></script>
    </head>
    <body>
        <div id="maincontainer">
            <div id="content">
                <div id="errorBox">
                    Something went wrong, one of the reasons may be:<br />
                    - That user doesn't exist.<br />
                    - That player hasn't played in last 28 days.<br />
                    - I reached API limit (try again after 10 minutes).<br />
                    <br />
                    All possible things I could've repaired, I did. But I can't do anything about
                    the points above. Try later, or try on another user.
                </div>
                <div id="loadingBox">
                </div>
                <input list="predefinedPlayers" id="searchBox" onkeydown="GetData(this)" placeholder="Type in the name of player, and press [Enter]" />
                <datalist id="predefinedPlayers">
                    <option value="SirNukesAlot">
                    <option value="FNC Rekkles">
                    <option value="Cuvee">
                    <option value="ssteiww">
                    <option value="Shadow Pixelism">
                </datalist>
                <p id="introText">
                    This website is based on Riot Games API. Riot Games, maker of a well known game called League of Legends
                    delivers data of players and their careers to developers through API. This is what this website does.
                    I take the name, give it through to Riot Games, and they send me data back which I need to parse.
                    And use my knowledge to format it nicely for you. Instead of a flattening JSON array, you get to see
                    nicely colored tables and texts.<br />
                    <br />
                    In case when you can't think of a username, here's a list (autocomplete is also on, begin with typing, names will pop-up):<br />
                    <br /><br />
                    SirNukesAlot, FNC Rekkles, Cuvee, ssteiww, Shadow Pixelism.<br />
                    <br /><br />
                    Website works with user official user profile. The ones above are just examples.
                </p>
                <table id="resultsShown">
                    <tr id="toptier">
                        <td id="winloss_indic"><span>X</span>/<span>X</span><br /><span>X% winratio</span></td>
                        <td id="kda_indic">X/X/X<br /><span>Kills/Deaths/Assists</span></td>
                        <td id="cs_indic">Xcs<br /><span>Total of all visible games</span></td>
                    </tr>
                    <tr id="midtier">
                        <td rowspan="2" id="tier_indic">X<br /><span>X LP</span></td>
                        <td id="matchhistory_indic" colspan="2" rowspan="2">
                            X
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </body>
</html>