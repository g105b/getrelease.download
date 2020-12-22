<?php
require("vendor/autoload.php");
define("ERROR", [
	"no-matching-version" => "No matching version could be found using this path:",
	"no-matching-assets" => "No matching assets could be found using this path:",
]);

$auth = file_get_contents("auth");
$uri = $_SERVER["REQUEST_URI"];
$path = trim(parse_url($uri, PHP_URL_PATH), "/");
[$organisation, $repository, $version, $file] = explode(
	"/",
	$path
);

if($path === ""
|| isset($_GET["error"])) {
	goto website;
}
else {
	if(empty($organisation) || empty($repository)) {
		header("Location: /");
		exit;
	}
}
$apiUri = "https://api.github.com/repos/$organisation/$repository/releases";
$ch = curl_init($apiUri);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt(
	$ch,
	CURLOPT_HTTPHEADER,
	[
		"User-agent: getrelease.download",
		"Accept: application/vnd.github.v3+json",
		"Authorization: Basic " . base64_encode("$auth"),
	]
);
$response = curl_exec($ch);
if(trim($response) === "[]") {
	curl_setopt(
		$ch,
		CURLOPT_URL,
		"https://api.github.com/repos/$organisation/$repository/tags"
	);
	$response = curl_exec($ch);
}
$json = json_decode($response);

if(!$response || !$json) {
	http_response_code(500);
	exit(500);
}

$releaseVersionNumberObjectLookup = array();
$releaseVersionNumberList = array();
foreach($json as $i => $release) {
	$fullVersion = strtolower($release->tag_name ?? $release->name);
	$versionNumber = trim($fullVersion, " \n\r\t\v\0abcdefghijklmnopqrstuvwxyz");
	$releaseVersionNumberObjectLookup[$versionNumber] = $release;
	array_push($releaseVersionNumberList, $versionNumber);
}

$releaseVersionNumberList = array_filter($releaseVersionNumberList, function($release):bool {
	$parser = new \Composer\Semver\VersionParser();
	try {
		$parser->normalize($release);
	}
	catch(Exception) {
		return false;
	}

	return true;
});

$version = urldecode($version);
$releaseVersionNumberList = \Composer\Semver\Semver::rsort($releaseVersionNumberList);

if(!$version) {
	header("Content-type: application/json");
	$json = (object)[
		"releasedVersionList" => $releaseVersionNumberList,
	];
	echo json_encode($json);
	exit;
}

$releaseVersionNumberList = \Composer\Semver\Semver::satisfiedBy($releaseVersionNumberList, $version);

if(empty($releaseVersionNumberList)) {
	header("Location: $uri?error=no-matching-version");
	exit;
}

$latestMatchingRelease = $releaseVersionNumberObjectLookup[$releaseVersionNumberList[0]];
$absoluteVersion = $latestMatchingRelease->tag_name ?? $latestMatchingRelease->name;

if($version !== $absoluteVersion) {
	header("Location: /$organisation/$repository/$absoluteVersion/$file");
	exit;
}

if(!$file) {
	header("Content-type: application/json");
	$assetList = array();
	foreach($latestMatchingRelease->assets as $asset) {
		array_push($assetList, $asset->name);
	}

	if($latestMatchingRelease->zipball_url) {
		array_push($assetList, "$absoluteVersion.zip");
	}
	if($latestMatchingRelease->tarball_url) {
		array_push($assetList, "$absoluteVersion.tar.gz");
	}

	$json = (object)[
		"availableAssetList" => $assetList,
	];
	echo json_encode($json);
	exit;
}

foreach($latestMatchingRelease->assets as $asset) {
	$assetFile = $asset->name;
	if(!str_ends_with($assetFile, $file)) {
		continue;
	}

	header("Location: $asset->browser_download_url");
	exit;
}

if($file === "zip") {
//	$latestMatchingRelease->zipball_url;
	header("Location: https://github.com/$organisation/$repository/archive/$absoluteVersion.zip");
	exit;
}
if($file === "tar" || $file === "targz" || $file === "tar.gz") {
//	$latestMatchingRelease->tarball_url;
	header("Location: https://github.com/$organisation/$repository/archive/$absoluteVersion.tar.gz");
	exit;
}

header("Location: $uri?error=no-matching-assets");
exit;

website:
?><!doctype html>
<html lang="en-GB">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>Get Release Download!</title>

	<style>
	html {
		font-size: 18px;
	}
	body {
		font-family: monospace;
		line-height: 1.5;
		max-width: 50rem;
	}
	h1,h2,h3,h4,h5,h6 {
		font-size: 1rem;
		font-weight: bold;
	}
	.error {
		background: red;
		color: white;
	}
	.error i {
		display: block;
	}
	.error::before {
		content: "ERROR: ";
		font-weight: bold;
	}
	@media(min-width: 72rem) {
		html {
			font-size: 24px;
			padding-left: 8rem;
			padding-top: 4rem;
			padding-bottom: 4rem;
		}
	}
	</style>
</head>
<body>

<?php if(isset($_GET["error"])): ?>
	<header class="error">
		<?php echo ERROR[$_GET["error"]]; ?>
		<i><?php echo $path; ?></i>
	</header>
<?php endif ?>

	<h1>Get Release Download!</h1>
	<h2>by <a href="https://github.com/g105b">Greg Bowler</a>.</h2>

	<hr />
	<h3>Download a release from Github using a single fuzzy-matched URL.</h3>

	<p>Examples:</p>
	<ul>
		<li>
			sloria's doitlive, zip archive, the latest version
			<a href="/sloria/doitlive/*/zip">
				getrelease.download/sloria/doitlive/*/zip
			</a>
		</li>
		<li>
			PHPStan, phar archive, >=v0.12
			<a href="/phpstan/phpstan/>=0.12/phar">
				getrelease.download/phpstan/phpstan/>=0.12/phar
			</a>
		</li>
		<li>
			Forty Seven Effect's Arduino MIDI library, zip archive, v5.*
			<a href="/FortySevenEffects/arduino_midi_library/5.*/zip">
				getrelease.download/FortySevenEffects/arduino_midi_library/5.*/zip
			</a>
		</li>
		<li>
			Pixi.js, minified JavaScript, ^5.3
			<a href="/pixijs/pixi.js/^5.3/pixi.min.js">
				getrelease.download/pixijs/pixi.js/^5.3/pixi.min.js
			</a>
		</li>
	</ul>

	<p>Usage:</p>
	<p>
		The URL takes the following form:
	</p>
	<ul>
		<li>
			https://getrelease.download/
		</li>
		<li>
			Github organisation
		</li>
		<li>
			Github repository
		</li>
		<li>
			Version constraint
		</li>
		<li>
			Asset filename or extension
		</li>
	</ul>

	<p>Extras:</p>
	<p>
		End the URL with the organisation/repository (without version or asset filename) to see a list of available versions.
	</p>
	<p>
		Example: <a href="/phpstan/phpstan">getrelease.download/phpstan/phpstan</a>
	</p>
	<p>
		End the URL with the version constraint (without asset filename) to see a list of available assets.
	</p>
	<p>
		Example: <a href="/phpstan/phpstan/*">getrelease.download/phpstan/phpstan/0.12.64</a>
	</p>

	<hr />

	<p>If you find this tool useful, consider <a href="https://github.com/sponsors/g105b">sponsoring the developer</a>.</p>
</body>
</html>