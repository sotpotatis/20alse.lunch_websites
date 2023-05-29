<?php
// Initialize
declare(strict_types=1);
require_once("vendor/autoload.php");
// Other constants
const LUNCH_MENU_BASE_URL  = "https://lunchmeny.albins.website";
// HTTP client
use GuzzleHttp\Client;
use GuzzleHttp\psr7\Request;
$client = new Client([
    "base_uri" => LUNCH_MENU_BASE_URL,
    "timeout" => 10
]);
// Provide initial page content
echo <<<EOL
<head>
<meta charset=\"UTF-8\">
<meta name=\"description\" content=\"Hitta veckans lunchmeny för Eatery Kista Nod här, precis som det hade sett ut på den gamla goda tiden!\">
<title>Veckans lunchmeny | 20alse</title>
<style>
body {
background-image: url(bg.gif);
color: white;
}
h1 {
  background-color: gray;
  border: 3px solid;
  padding: 0.5%;
}
h2 {
  background-color:darkblue;
  padding-left: 0.5%
}
.container {
    background-color: gray;
}
.container hr {
    border-color: black;
}
#footer a {
    color: white;
}
p {
    padding: 1%;
}
</style>
</head>
<body>
EOL;
// Create some functions
function generateDishElement($dishText){
    $hotImg = "<img src=\"http://www.fillster.com/images/icons/13f.gif\"></img>";
    $highlights = [
        [
            "Sweet Tuesday",
            "$hotImg <b>Sweet Tuesday</b>"
        ],
        [
            "Pancake Thursday",
            "$hotImg <b>Pancake Thursday</b>"
        ],
        [
            "Burger Friday",
            "$hotImg <b>Burger Friday</b>"
        ]
    ];
    // Check if dish should be highlighted
    foreach ($highlights as $highlight){
        list($phraseToLookFor, $phraseToReplaceWith) = $highlight;
        if (str_contains($dishText, $phraseToLookFor)){
            $dishText = str_replace($phraseToLookFor, $phraseToReplaceWith, $dishText);
        }
    }
    return <<<EOL
    <p>$dishText</p>
    <hr></hr>
    EOL;
}
function generateDayElement($dayName, $dishes){
    $output = "<div class=\"container\">";
    $output .= <<<EOL
    <h2>$dayName</h2>
    EOL;// Add day name
    foreach($dishes as $dish){ // Generate text for each dish
        $output .= generateDishElement($dish);
    }
    $output .= "</div>"; // Add horizontal divider
    return $output;
}
// Make the request
$error = false;
try {
    $request = $client->get("/api");
    $body = json_decode((string) $request->getBody());
}
catch(Exception $e) {
    $error = true;
}
// Validate that the request succeeded
if (!$error and $body->status === "success"){
    // Add heading
    $menu = $body->menu;
    echo "<h1>$menu->title</h1>";
    // Get and render menu items
    foreach($menu->days as $dayId=>$dayData){
        echo generateDayElement($dayData->day_name->swedish, $dayData->dishes); // Generate data for each day
    }
}
else {
    echo <<<EOL
    <center>
        <div class="container">
        <img src="http://www.fillster.com/images/graphics/12a.gif"></img>
        <h2>Meny ej tillgänglig</h2>
        <p>Tyvärr, ingen meny verkar finnas tillgänglig just nu. Testa att komma tillbaka om ett tag.
        </div>
    </center>
    EOL;
    // Add information if set
    if (isset($body->message)){
        echo "(meddelande från servern: $body->message)";
    }
    echo "</p>"; // CLose p-tag
}
// Add footer and close body
echo <<<EOL
<center>
<div id="footer">
<p>Drivs av <a href="https://lunchmeny.albins.website">mitt egna API</a>.</p>
</div>
</center>
</body>
EOL;
?>
