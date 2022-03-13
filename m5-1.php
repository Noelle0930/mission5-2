<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission5-1</title>
</head>

<body>
<?php
  $dsn = 'データベース名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
 
  $sql = "CREATE TABLE IF NOT EXISTS mission5"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32),"
    . "comment TEXT"
    .");";
    $stmt = $pdo->query($sql);
    
   $sql = $pdo -> prepare("INSERT INTO tbtest (name, comment) VALUES (:name, :comment)");
    $sql -> bindParam(':name', $name, PDO::PARAM_STR);
    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
    $name = '$_POST["str1"]';
    $comment = '$_POST["str2"]'; //好きな名前、好きな言葉は自分で決めること
    $sql -> execute();
    //bindParamの引数名（:name など）はテーブルのカラム名に併せるとミスが少なくなります。最適なものを適宜決めよう。
    
  $sql = 'SELECT * FROM mission5';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        //$rowの中にはテーブルのカラム名が入る
        echo $row['id'].',';
        echo $row['name'].',';
        echo $row['comment'].'<br>';
    echo "<hr>";
    }
    

 $date=date("Y年m月d日 H時i分s秒");
 $filename="mission5";
  if(file_exists($filename)){
    $num=count(file($filename))+1;
    }else{
        $num=1;
    }
 
 //名前、コメント、パスワードがあるときの処理
 if(!empty($_POST["str1"])&&!empty($_POST["str2"])&&!empty($_POST["pass1"])){//名前、コメント、パスワードがあるとき
     $str1=$_POST["str1"];//名前
     $str2=$_POST["str2"];//コメント
     $pass1=$_POST["pass1"];//パスワード
     $new=$num."<>".$str1."<>".$str2."<>".$date."<>".$pass1."<>";

    //編集 
    if(!empty($_POST["hide"])){//編集隠し番号が入ったとき
     $edit=$_POST["hide"];//隠し番号
     $editdata=$edit."<>".$str1."<>".$str2."<>".$date."<>".$pass1."<>";//編集の内容
     $lines=file($filename,FILE_IGNORE_NEW_LINES);//ファイルでの処理
     $fp=fopen($filename,"w+");//読み込みと書き込みモードで開く
     echo "投稿番号".$edit."が編集されました。<br>";
     
      for($i=0;$i<count($lines);$i++){//一行ずつ下記の操作を行う、ループ処理
           $datas=explode("<>",$lines[$i]);
           
           if($datas[0]==$edit){//投稿番号と隠し番号が同じとき
           fwrite($fp, $editdata.PHP_EOL);
           }
           else{//「投稿番号と隠し番号が同じ」以外のとき
           fwrite($fp,$lines[$i].PHP_EOL);
           }
       }fclose($fp);
       
     //新規投稿
     }else{//編集番号がないとき
         $lines=file($filename,FILE_IGNORE_NEW_LINES); 
         $fp=fopen($filename,"a");//書き込みモードでファイル開く
         fwrite($fp, $new. PHP_EOL);
         fclose($fp);
         echo "投稿を受け付けました<br>";
     }
    }elseif(empty($_POST["str1"])&&!empty($_POST["str2"])&&!empty($_POST["pass1"])){
         echo"名前が入力されていません！";
    }elseif(!empty($_POST["str1"])&&empty($_POST["str2"])&&!empty($_POST["pass1"])){
         echo"コメントが入力されていません！";
    }elseif(!empty($_POST["str1"])&&!empty($_POST["str2"])&&empty($_POST["pass1"])){
         echo"パスワードが入力されていません！";
         
    //削除番号が送信されたとき
    }elseif(!empty($_POST["delete"])&&!empty($_POST["pass2"])){//削除番号とパスワードがあるとき
        $delete=$_POST["delete"];//削除番号
        $lines=file($filename,FILE_IGNORE_NEW_LINES);
        $fp=fopen($filename,"w+");
        $pass2=$_POST["pass2"];//削除のときのパスワード
        echo"投稿番号".$delete."が削除されました<br>";

           for($i=0;$i<count($lines);$i++){//一行ずつ下記の操作を行う
           $datas=explode("<>",$lines[$i]);
           
           if($datas[0]==$delete&&$datas[4]==$pass2){//投稿番号と削除番号、一番目のパスワードと削除時のパスワードが同じとき
              if($datas[0]!=$delete){
           fwrite($fp, $lines[$i].PHP_EOL);
           }}else{fwrite($fp,$lines[$i].PHP_EOL);
           }}fclose($fp);
           
    }elseif(empty($_POST["delete"])&&!empty($_POST["pass2"])){
        echo"削除番号が入力されていません";
    }elseif(!empty($_POST["delete"])&&empty($_POST["pass2"])){
        echo"パスワードが入力されていません";
    
       //編集番号が送信されたとき
    }else{
        if(!empty($_POST["edit"])&&!empty($_POST["pass3"])){//編集指定番号とパスワードがあるとき
        $edit=$_POST["edit"];//編集指定番号
        $lines=file($filename,FILE_IGNORE_NEW_LINES);
        $pass3=$_POST["pass3"];//編集時のパスワード
       
            if(file_exists($filename)){
               for($i=0;$i<count($lines);$i++){//一行ずつ下記の操作を行う
               $datas=explode("<>",$lines[$i]);
                   if($datas[0]==$edit&&$datas[4]==$pass3){//投稿番号と編集指定番号、最初のパスワードと編集時のパスワードが同じとき
                   $newname=$datas[1];
                   $newcomment=$datas[2];
                   $newpass=$datas[4];
                   echo"投稿番号".$edit."を編集します<br>";
                   }
               }
            }
        }elseif(empty($_POST["edit"])&&!empty($_POST["pass3"])){
            echo"編集番号が入力されていません。";
        }elseif(!empty($_POST["edit"])&&empty($_POST["pass3"])){
            echo"パスワードが入力されていません。";
        }
    }
?>      
    <b>「投稿→編集→削除」お願いします！</b><br>
            <form action ="" method ="post">
      <input type="text" name="str1" placeholder="名前"
      value="<?php if(!empty($newname)){echo $newname;}?>"><br>
      
      <input type="text" name="str2" placeholder="コメント"
      value="<?php if(!empty($newcomment)){echo $newcomment;}?>"><br>
      
      <input type="password" name="pass1" placeholder="パスワード"
      value="<?php if(!empty($pass3)){echo $pass3;}?>">
      
      <input type="hidden" name="hide" placeholder="編集する番号"
      value="<?php if(!empty($edit)){echo $edit;}?>">
      
      <button type = "submit" name ="submit1" value ="送信" >送信</button>
      </form>
  <br>
   <form action="" method="post">
   <input type="number" name="delete" placeholder="削除番号"><br>
   <input type="password" name="pass2" placeholder="パスワード">
   <button type= "submit" name="submit2" value="削除">削除</button> 
   </form>
  <br>
   <form action="" method="post">
   <input type="number" name="edit" placeholder="編集する投稿番号"><br>
   <input type="password" name="pass3" placeholder="パスワード">
   <button type="submit" name="submit3" value="編集">編集</button>
   </form>

<?php  
    //ファイル内処理
    if(file_exists($filename)){//ファイルが存在するときの処理
      $lines=file($filename,FILE_IGNORE_NEW_LINES);
           for($i=0;$i<count($lines);$i++){//forは繰り返す数が定まっているときに使う
           $datas=explode("<>",$lines[$i]);
           echo $datas[0]." ".$datas[1]." ".$datas[2]." ".$datas[3];
           echo "<br>";
           }}
?>

</body>
</html>