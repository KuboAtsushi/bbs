<?php
$row_count = null;
//ファイル名
$filename = 'data.csv';

if (!$fp = fopen($filename, 'w')) {
    echo "Cannot open file ($filename)";
    exit;
}

$head = '"id","名前","メッセージ","時刻"';

// head書き込み
fwrite($fp, mb_convert_encoding($head . "\n", "SJIS"));


//DB接続情報
$dsn = 'mysql:host=localhost;dbname=board';
$id = 'root';
$pw = 'atsushi';

// 件数取得
if (isset($_GET['limit']) === TRUE) {
    $row_count = $_GET['limit'];
}

try {
    //DB検索処理
    $pdo = new PDO(
        $dsn,
        $id,
        $pw,
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
    if (empty($row_count)) {
        $sql = 'SELECT * FROM message ORDER BY date_time Desc';
    } else {
        $sql = 'SELECT * FROM message ORDER BY date_time Desc Limit ' . $row_count;
        var_dump($sql);

    }
    $sql = $pdo->prepare($sql);
    $sql->execute();

    while ($data = $sql->fetch()) {

        // 出力用
        $output_text  = '"';
        $output_text .= $data['id'];
        $output_text .= '","' . $data['view_name'];
        $output_text .= '","' . $data['message'];
        $output_text .= '","' . $data['date_time'];
        $output_text .= '"';
        $output_text .= "\n";

        if (fwrite($fp, mb_convert_encoding($output_text, "SJIS")) === FALSE) {
            break;
        }
    }


    // close mysql
    $pdo = null;
} catch (PDOException $e) {

    print "[ERROR] {{$e->getMessage()}}\n";
    die();
}

// close mysql
$pdo = null;


/* download_file関数実行 */
download_file($filename);

function download_file($path_file)
{
    /* ファイルの存在確認 */
    if (!file_exists($path_file)) {
        die("Error: File(" . $path_file . ") does not exist");
    }

    /* オープンできるか確認 */
    if (!($fp = fopen($path_file, "r"))) {
        die("Error: Cannot open the file(" . $path_file . ")");
    }
    fclose($fp);

    /* ファイルサイズの確認 */
    if (($content_length = filesize($path_file)) == 0) {
        die("Error: File size is 0.(" . $path_file . ")");
    }

    /* ダウンロード用のHTTPヘッダー送信 */
    header("Cache-Control: private");
    header("Pragma: private");
    header('Content-Description: File Transfer');
    header("Content-Disposition: inline; filename=\"" . basename($path_file) . "\"");
    header("Content-Length: " . $content_length);
    header("Content-Type: application/octet-stream");
    header('Content-Transfer-Encoding: binary');

    /* ファイルを読んで出力 */
    if (!readfile($path_file)) {
        die("Cannot read the file(" . $path_file . ")");
    }
}
