<?php

require_once "php_3kurs/vendor/autoload.php";
spl_autoload_register(function ($className) {
    require_once 'php_3kurs/app/classes/' . $className . '.inc';
});

//$url = "http://drs.ua/rus/registrars.html";
$url = "http://www.abc.edu-net.khb.ru/";
$client = new GuzzleClient($url);
try {
    $content = $client->get($url);
} catch (UnsuccessfulRequestException $ure) {
    throw new Exception($ure->getMessage() . " Code: " . $ure->getCode() . " Http message: " . $ure->getHttpReason());
} catch (NotInitializedException $nie) {
    throw new Exception("Client not initialized for some mysterious reason");
}

$parser = new UriParser($content);
$result = $parser->getResult();

foreach ($result as $key => $item) {
    if (substr($item, 0, 1) === "#") {
        unset($result[$key]);
        continue;
    }
    if (substr($item, 0, 1) === "/") {
        unset($result[$key]);
        continue;
    }
}

$host = "localhost";
$port = 5432;
$dbname = "Url_parser";
$user = "postgres";
$password = "root";
$uridb = new UriDB($host, $port, $dbname, $user, $password);


$parent = $uridb->Add(new Uri($url));

foreach ($result as $uri) {
    $uridb->Add(new Uri($uri, null, $parent));
}

parse($uridb, $parent);

$dbresult = $uridb->GetAllUris();


echo Renderer::render('uris', ['uris' => $dbresult]);

function parse(UriDB $uridb, int $parentId, int $deep = 2)
{
    if ($deep == 1) {
        return;
    }
    $uris = $uridb->QueryById($parentId);
    foreach ($uris as $newUri) {
        $url = $newUri->getUri();

        $client = new GuzzleClient($url);
        try {
            $content = $client->get($url);
        } catch (UnsuccessfulRequestException $ure) {
            continue;
            throw new Exception($ure->getMessage() . " Code: " . $ure->getCode() . " Http message: " . $ure->getHttpReason());
        } catch (NotInitializedException $nie) {
            continue;
            throw new Exception("Client not initialized for some mysterious reason");
        }


        $parser = new UriParser($content);

        $result1 = $parser->getResult();
        $parentId = $newUri->getId();

        foreach ($result1 as $key => $item) {
            if (substr($item, 0, 1) === "#") {
                unset($result1[$key]);
                continue;
            }
            if (substr($item, 0, 1) === "/") {
                unset($result1[$key]);
                continue;
            }
//
        }

        foreach ($result1 as $uri) {
            $uridb->Add(new Uri($uri, null, $parentId));
            parse($uridb, $parentId, $deep--);
        }
    }
}


//var_dump($uridb->QueryById($parent));


/*

$conn = sprintf(
    "pgsql:host=%s;port=%s;dbname=%s;user=%s;password=%s",
    $host,
    $port,
    $dbname,
    $user,
    $password
);
$pdo = new PDO($conn);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$statement = $pdo->prepare("INSERT INTO uris(uri, parent_id) VALUES(:uri, :parent_id)");

foreach ( $parser->getResult() as $uri) {
    $statement->execute(["uri" =>$uri, "parent_id" => null]);
}


$query = $pdo->query("SELECT uri FROM uris");
*/