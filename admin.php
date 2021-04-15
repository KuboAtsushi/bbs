<?php
// 管理ページのログインパスワード
define('PASSWORD', 'admin');

$view_name = null;
$message = null;
$date_time = null;
$text = null;
$fp = null;
$dataArr = null;
$d = null;
$text = null;
$arr = null;
$success_message = null;
$error_message = array();

$sql = null;
$res = null;
$dbh = null;

$user = 'root';
$pass = 'atsushi';

// セッションスタート
session_start();

if (!empty($_POST['btn_submit'])) {

    if (!empty($_POST['admin_password']) && $_POST['admin_password'] === PASSWORD) {
        $_SESSION['admin_login'] = true;
    } else {
        $error_message[] = 'ログインに失敗しました。';
    }
}

try {
    // DBへ接続
    $dbh = new PDO("mysql:host=localhost; dbname=board; charset=utf8", $user, $pass);

    // SQL作成
    $sql = "SELECT * FROM message ORDER BY date_time desc";

    // SQL実行
    $dataArr = $dbh->query($sql);
} catch (PDOException $e) {
    echo $e->getMessage();
    die();
}

// 接続を閉じる
$dbh = null;

// ログアウト処理
if (!empty($_GET['btn_logout'])) {
    unset($_SESSION['admin_login']);
    $_GET = array();
    header("Location: ./admin.php");
}

?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <title>ひと言掲示板　管理ページ</title>
    <style>
        /*------------------------------
 Reset Style
 
------------------------------*/
        html,
        body,
        div,
        span,
        object,
        iframe,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        p,
        blockquote,
        pre,
        abbr,
        address,
        cite,
        code,
        del,
        dfn,
        em,
        img,
        ins,
        kbd,
        q,
        samp,
        small,
        strong,
        sub,
        sup,
        var,
        b,
        i,
        dl,
        dt,
        dd,
        ol,
        ul,
        li,
        fieldset,
        form,
        label,
        legend,
        table,
        caption,
        tbody,
        tfoot,
        thead,
        tr,
        th,
        td,
        article,
        aside,
        canvas,
        details,
        figcaption,
        figure,
        footer,
        header,
        hgroup,
        menu,
        nav,
        section,
        summary,
        time,
        mark,
        audio,
        video {
            margin: 0;
            padding: 0;
            border: 0;
            outline: 0;
            font-size: 100%;
            vertical-align: baseline;
            background: transparent;
        }

        body {
            line-height: 1;
        }

        article,
        aside,
        details,
        figcaption,
        figure,
        footer,
        header,
        hgroup,
        menu,
        nav,
        section {
            display: block;
        }

        nav ul {
            list-style: none;
        }

        blockquote,
        q {
            quotes: none;
        }

        blockquote:before,
        blockquote:after,
        q:before,
        q:after {
            content: '';
            content: none;
        }

        a {
            margin: 0;
            padding: 0;
            font-size: 100%;
            vertical-align: baseline;
            background: transparent;
        }

        /* change colours to suit your needs */
        ins {
            background-color: #ff9;
            color: #000;
            text-decoration: none;
        }

        /* change colours to suit your needs */
        mark {
            background-color: #ff9;
            color: #000;
            font-style: italic;
            font-weight: bold;
        }

        del {
            text-decoration: line-through;
        }

        abbr[title],
        dfn[title] {
            border-bottom: 1px dotted;
            cursor: help;
        }

        table {
            border-collapse: collapse;
            border-spacing: 0;
        }

        hr {
            display: block;
            height: 1px;
            border: 0;
            border-top: 1px solid #cccccc;
            margin: 1em 0;
            padding: 0;
        }

        input,
        select {
            vertical-align: middle;
        }

        /*------------------------------
Common Style
------------------------------*/
        body {
            padding: 50px;
            font-size: 100%;
            font-family: 'ヒラギノ角ゴ Pro W3', 'Hiragino Kaku Gothic Pro', 'メイリオ', Meiryo, 'ＭＳ Ｐゴシック', sans-serif;
            color: #222;
            background: #f7f7f7;
        }

        a {
            color: #007edf;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        h1 {
            margin-bottom: 30px;
            font-size: 100%;
            color: #222;
            text-align: center;
        }

        /*-----------------------------------
入力エリア
-----------------------------------*/
        label {
            display: block;
            margin-bottom: 7px;
            font-size: 86%;
        }

        input[type="text"],
        input[type="password"],
        textarea {
            margin-bottom: 20px;
            padding: 10px;
            font-size: 86%;
            border: 1px solid #ddd;
            border-radius: 3px;
            background: #fff;
        }

        input[type="text"],
        input[type="password"] {
            width: 200px;
        }

        textarea {
            width: 50%;
            max-width: 50%;
            height: 70px;
        }

        input[type="submit"] {
            appearance: none;
            -webkit-appearance: none;
            padding: 10px 20px;
            color: #fff;
            font-size: 86%;
            line-height: 1.0em;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            background-color: #37a1e5;
        }

        input[type=submit]:hover,
        button:hover {
            background-color: #2392d8;
        }

        hr {
            margin: 20px 0;
            padding: 0;
        }

        .success_message {
            margin-bottom: 20px;
            padding: 10px;
            color: #48b400;
            border-radius: 10px;
            border: 1px solid #4dc100;
        }

        .error_message {
            margin-bottom: 20px;
            padding: 10px;
            color: #ef072d;
            list-style-type: none;
            border-radius: 10px;
            border: 1px solid #ff5f79;
        }

        .success_message,
        .error_message li {
            font-size: 86%;
            line-height: 1.6em;
        }

        /*-----------------------------------
掲示板エリア
-----------------------------------*/
        article {
            margin-top: 20px;
            padding: 20px;
            border-radius: 10px;
            background: #fff;
        }

        article.reply {
            position: relative;
            margin-top: 15px;
            margin-left: 30px;
        }

        article.reply::before {
            position: absolute;
            top: -10px;
            left: 20px;
            display: block;
            content: "";
            border-top: none;
            border-left: 7px solid #f7f7f7;
            border-right: 7px solid #f7f7f7;
            border-bottom: 10px solid #fff;
        }

        .info {
            margin-bottom: 10px;
        }

        .info h2 {
            display: inline-block;
            margin-right: 10px;
            color: #222;
            line-height: 1.6em;
            font-size: 86%;
        }

        .info time {
            color: #999;
            line-height: 1.6em;
            font-size: 72%;
        }

        article p {
            color: #555;
            font-size: 86%;
            line-height: 1.6em;
        }

        @media only screen and (max-width: 1000px) {
            body {
                padding: 30px 5%;
            }

            input[type="text"] {
                width: 100%;
            }

            textarea {
                width: 100%;
                max-width: 100%;
                height: 70px;
            }
        }

        input[name=btn_logout] {
            margin-top: 40px;
            background-color: #666;
        }

        input[name=btn_logout]:hover {
            background-color: #777;
        }
    </style>
</head>

<body>
    <h1>ひと言掲示板　管理ページ</h1>
    <section>
        <!--　アドミンログインされていたら投稿データを表示 -->
        <?php if (!empty($_SESSION["admin_login"]) && $_SESSION["admin_login"] === true) : ?>
            <form method="get" action="./download.php">
                <label for="btn_download">CSVダウンロード</label>
                <select name="limit">
                    <option value="">全て</option>
                    <option value="2">2件</option>
                    <option value="30">30件</option>
                </select>
                <input type="submit" name="btn_download" value="保存">
            </form>
            <!-- ここに投稿されたメッセージを表示 -->
            <?php if (!empty($dataArr)) : ?>
                <?php foreach ($dataArr as $data) : ?>
                    <article>
                        <div class="info">
                            <form method="get" action="./edit.php">
                                <input type="hidden" name="id" value="<?php echo $data["id"]; ?>">
                                <h2><?php echo $data["view_name"]; ?></h2>:<span><?php echo $data["date_time"]; ?> </span><a href="edit.php?id=<?php echo $data["id"] ?>">編集</a> <a href="delete.php?id=<?php echo $data["id"] ?>">削除</a></p>
                            </form>
                        </div>
                        <p><?php echo nl2br($data["message"]); ?></p>
                    </article>
            <?php endforeach;
            endif; ?>
            <form method="get" action="">
                <input type="submit" name="btn_logout" value="ログアウト">
            </form>
        <?php else : ?>
            <!--　アドミンログインされてなかったら、ログインフォームを表示　-->
            <form method="post">
                <div>
                    <label for="admin_password">ログインパスワード</label>
                    <input id="admin_password" type="password" name="admin_password" value="">
                </div>
                <input type="submit" name="btn_submit" value="ログイン">
            </form>
        <?php endif; ?>
    </section>
</body>

</html>