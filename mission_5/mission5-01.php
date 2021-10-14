<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>mission_5-01</title>
    </head>
    <body>
        <?php
        createTable();
        $datetime = date("Y-m-d H:i:s");
        $get_name = "";
        $get_comment = "";
        $get_id = "";
        $get_pass = "";
        $delete_id = "";
        $edit_id = "";
        $error_message = array(); // エラーメッセージを格納する配列

///////////////////////メインの処理//////////////////////////////

        //新規投稿が投稿フォームから入力送信された場合
        if( !empty( $_POST["comment"] ) && !empty( $_POST["name"] ) && empty( $_POST["get_num"]) && !empty($_POST["pass"] ) ) {
            $name = $_POST["name"];
            $comment = $_POST["comment"];
            $new_pass = $_POST["pass"];
            newPost( $name, $comment, $datetime, $new_pass );
            showPost();
            //echo "newPost<br>";

        //編集投稿が投稿フォームから入力送信された場合
        } elseif( !empty( $_POST["get_num"] ) ) {
            $edit_num = $_POST["get_num"];
            $name = $_POST["name"];
            $comment = $_POST["comment"];
            $edit_pass = $_POST["pass"];
            editPost( $edit_num, $name, $comment, $datetime, $edit_pass );
            showPost();

        //削除フォームに入力送信された場合
        } elseif( !empty( $_POST["delete_no"]) && !empty( $_POST["delete_pass"]) ) {
            $delete_num = $_POST["delete_no"];
            $delete_pass = $_POST["delete_pass"];
            deletePost( $delete_num, $delete_pass );
            showPost();
            //echo "deletePost";

        //編集フォームに入力送信され該当投稿をフォームに表示させる場合    
        } elseif( !empty( $_POST["edit_no"] ) && !empty( $_POST["edit_pass"] ) ) {
            $edit_num = $_POST["edit_no"];
            $edit_pass = $_POST["edit_pass"];
            getPost( $edit_num, $edit_pass );
            showPost();
            //echo "getPost";
        } else {
            showPost(); 
        }
        
        //エラーメッセージの表示
        if( !empty( $error_message ) ) {
            foreach ( $error_message as $message ) {
                echo $message."<br>";
            }
        }

////////////////////////メインの処理ここまで/////////////////////////////

        //DB接続設定
        function dbConnect():PDO {
            $dsn = 'データベース名';
            $user = 'ユーザー名';
            $password = 'パスワード';
            $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));//Php Data Object
            return $pdo;
        }
        
        //テーブルを作成する関数
        function createTable() {
            $pdo = dbConnect(); //Php Data Object
            $sql = "CREATE TABLE IF NOT EXISTS tb_5"
            ." ("
            . "id INT AUTO_INCREMENT PRIMARY KEY," //idという項目を整数の型で作る。
            . "name char(32)," //nameという項目をcharacter（文字列）で入れる。上限は半角英数32文字。
            . "comment TEXT," //commentという項目をTEXTの型で作る。
            . "dt datetime," //dt datetime型
            . "pass char(32)" //passという項目をcharacterで作る。
            .");";
            $stmt = $pdo->query($sql);
        }
        //新規投稿を処理する関数
        function newPost( $name, $comment, $datetime, $pass ){
            $pdo = dbConnect();
            global $get_name;
            global $get_comment;
            global $error_message;

            // 名前が入力されていない場合
            if ( empty( $_POST["name"] ) && $_POST["name"] != "0" ) {
                $error_message[] = "名前を入力してください";
                $get_comment = $_POST["comment"];
            }
        
            // コメントが入力されていない場合
            if ( empty( $_POST["comment"] ) && $_POST["comment"] != "0" ) {
                $error_message[] = "コメントを入力してください";
                $get_name = $_POST["name"];
            } 
        
            // パスワードが入力されていない場合
            if ( empty( $_POST["pass"] ) && $_POST["pass"] != "0" ) {
                $error_message[] = "パスワードを入力してください";
                $get_name = $_POST["name"];
                $get_comment = $_POST["comment"];
            }
        
            // 名前もコメントもパスワードも入力されている場合
            if ( empty( $error_message ) ) {
                    //dbのtableにデータを登録
                    $sql = $pdo -> prepare("INSERT INTO tb_5(name, comment, dt, pass) VALUES (:name, :comment, :dt, :pass)");
                    $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                    $sql -> bindParam(":dt", $datetime, PDO::PARAM_STR);
                    $sql -> bindParam(":pass", $pass, PDO::PARAM_STR);
                    $sql -> execute();
            }
        }

        //投稿削除を処理する関数
        function deletePost( $id, $pass ){
            global $delete_id;
            global $error_message;

            // 編集対象番号が入力されていない場合
            if ( empty( $_POST["delete_no"] ) ) {
                $error_message[] = "削除対象番号を入力してください";
            }
        
            // パスワードが入力されていない場合
            if ( empty( $_POST["delete_pass"] ) ) {
                $error_message[] = "パスワードを入力してください";
                $delete_id = $_POST["delete_no"];
            }
        
            // 編集対象番号もパスワードも入力されている場合
            if ( empty( $error_message ) ) {
                $pdo = dbConnect();
                $sql = 'SELECT * FROM tb_5';
                $stmt = $pdo -> query($sql);
                // 入力されたものが1以上の整数かどうかを判定
                if ( !ctype_digit( $id ) && (int)$id < 1 ) {   // ctype_digit: 文字列数値を判定する関数
                echo "削除番号には1以上の整数を指定してください<br><br>";
                    
                } else {
                // 指定された投稿番号のパスワードを取り出す
                $stmt = $pdo -> prepare('SELECT pass FROM tb_5 WHERE id=:id');
                $stmt -> bindParam(":id", $id, PDO::PARAM_INT);
                $stmt -> execute();
                $results = $stmt -> fetchAll();
                }
        
                // 指定された番号の投稿(パスワード)があるかの確認
                if ( !empty( $results[0][0] ) ) {
                    $saved_pass = $results[0][0];
                    // パスワードが一致する場合
                    if ( $pass == $saved_pass ) {
                        $delete_pass = $_POST["delete_pass"];
                        $pdo = dbConnect();                
                        $sql = 'delete from tb_5 where id=:id';
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                        $stmt->execute();
                    } else {
                        echo "パスワードが間違っています。<br>";
                    }
                }
            }
        }
        //編集番号に対応する投稿を入力フォームに表示させる関数
        function getPost( $id, $pass ){
            $pdo = dbConnect();
            global $get_name;
            global $get_comment;
            global $get_id;
            global $get_pass;
            global $edit_id;
            // 編集対象番号が入力されていない場合
            if ( empty( $_POST["edit_no"] ) ) {
                $error_message[] = "編集対象番号を入力してください";
            }
            
            // 入力されたものが1以上の整数でない場合
            if ( !ctype_digit( $id ) && (int)$id < 1 ) {   // ctype_digit: 文字列数値を判定する関数
                $error_message[] = "編集番号には1以上の整数を指定してください";
            } 
        
            // パスワードが入力されていない場合
            if ( empty( $_POST["edit_pass"] ) ) {
                $error_message[] = "パスワードを入力してください";
                $edit_id = $_POST["edit_num"];
            }
        
            // 編集対象番号もパスワードも入力されている場合
            if ( empty( $error_message ) ) {
                // 指定された投稿番号のパスワードを取り出す
                $pdo = dbConnect(); 
                $stmt = $pdo -> prepare('SELECT * FROM tb_5 WHERE id=:id');
                $stmt -> bindParam(":id", $id, PDO::PARAM_INT);
                $stmt -> execute();
                $results = $stmt -> fetchAll();
        
                // 指定された番号があるかどうかの判定
                if ( !empty( $results[0][4] ) ) {
                // 指定された番号が存在する場合
                $saved_pass = $results[0][4];
                // パスワードが一致する場合
                if ( $pass == $saved_pass ) {
                    $get_id = $results[0][0];
                    $get_name = $results[0][1];
                    $get_comment = $results[0][2];
                    $get_pass = $results[0][4];
        
                // パスワードが正しくない場合
                } else {
                    echo "パスワードが正しくありません<br><br>";
                }
        
                // 指定された番号が存在しない場合
                } else {
                echo "指定された番号の投稿はありません<br><br>";
                }
            }
        }

        //投稿編集を処理する関数
        function editPost( $id, $name, $comment, $datetime, $pass){
            global $get_name;
            global $get_comment;
            global $get_id;
            // 名前が入力されていない場合
            if ( empty( $_POST["name"] ) && $_POST["name"] != "0" ) {
                $error_message[] = "名前を入力してください";
                $get_comment = $_POST["comment"];
                $get_id = $_POST["get_num"];
            }
            
            // コメントが入力されていない場合
            if ( empty( $_POST["comment"] ) && $_POST["comment"] != "0" ) {
                $error_message[] = "コメントを入力してください";
                $get_name = $_POST["name"];
                $get_id = $_POST["get_num"];
            } 
        
            // パスワードが入力されていない場合
            if ( empty( $_POST["pass"] ) && $_POST["pass"] != "0" ) {
                $error_message[] = "パスワードを入力してください";
                $get_name = $_POST["name"];
                $get_comment = $_POST["comment"];
                $get_id = $_POST["get_num"];
            }
            
            // 名前もコメントもパスワードも入力されている場合
            if ( empty( $error_message ) ) {
                $pdo = dbConnect();
                $sql = 'UPDATE tb_5 SET name=:name,comment=:comment, dt=:dt, pass=:pass WHERE id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
                $stmt->bindParam(':dt', $datetime, PDO::PARAM_STR);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();    
            }
        }

        //投稿の中身を出力する関数
        function showPost() {
            $pdo = dbConnect();
            //SELECT文で、テーブルに登録されたデータを取得し表示
            $sql = 'SELECT * FROM tb_5';
            $stmt = $pdo -> query($sql); 
            $results = $stmt -> fetchAll();
            foreach ($results as $row){
                //$rowの中にはテーブルのカラム名が入る
                echo $row['id'].',';
                echo $row['name'].',';
                echo $row['comment'].',';
                echo $row['dt'].'<br>';
                echo "<hr>";
            }
            echo "<br>";
        }
        ?>

        <form action=""method="post">
            <p>【新規投稿】</p>
            <input type="text" name="name" value= "<?php echo $get_name; ?>" placeholder="名前を入力">
            <input type="text" name="comment"  placeholder="コメントを入力" value="<?php echo $get_comment; ?>">
            <input type="hidden" name="get_num" value= "<?php echo $get_id; ?>" >
            <input type="password" name="pass" value="<?php echo $get_pass; ?>" placeholder="パスワード">
            <br />
            <input type="submit" name="送信">
            <P>【削除】</P>
            <input type="number" name="delete_no" placeholder="削除対象番号を入力" value="<?php echo $delete_id?>">
            <input type="password" name="delete_pass" placeholder ="パスワード" >
            <input type="submit" name="d_submit" value="削除">
            <p>【編集】</p>
            <input type="number" name="edit_no" placeholder="編集対象番号を入力" value="<?php echo $edit_id?>">
            <input type="password" name="edit_pass" placeholder="パスワード">
            <input type="submit" name="e_submit" value="編集">
        </form>
    </body>
</html>