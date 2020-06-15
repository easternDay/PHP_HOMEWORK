<?php
// 开启Session
session_start();

//数据库连接部分
$mysql_server_name = 'localhost'; //改成自己的mysql数据库服务器
$mysql_username = 'root'; //改成自己的mysql数据库用户名
$mysql_password = '0311Yz1135646268'; //改成自己的mysql数据库密码
$mysql_database = '查询系统'; //改成自己的mysql数据库名

$conn = mysqli_connect($mysql_server_name, $mysql_username, $mysql_password, $mysql_database); //连接数据库

//连接数据库错误提示
if (mysqli_connect_errno($conn)) {
    die("连接 MySQL 失败: " . mysqli_connect_error());
}

mysqli_query($conn, "set names utf8"); //数据库编码格式
?>
<head>
    <title>简易教学管理系统【管理员端】</title>
</head>
<body>
<!--个人信息-->
<center style="margin: 50px">
    <h1>简易教学管理系统</h1>
    <div style="width:fit-content;border:3px dashed gray;padding:10px 100px;display: inline-block;">
        <h4>学号：1825101045 姓名：杨祉</h4>
        <h4>班级：计算机科学与技术一班</h4>
    </div>
</center>

<fieldset>
    <legend>重案组之虎曹达华</legend>
    <form method="post">
        <input type="submit" value="学生管理" name="type"/>
        <input type="submit" value="老师管理" name="type"/>
    </form>
    <?php
    if (isset($_POST['type'])) {
        ?>
        <table align="center" cellpadding="15px" border="1px">
            <?php
            switch ($_POST['type']) {
                case "学生管理":
                    ;
                    echo "<th>ID</th><th>姓名</th><th>生日</th><th>性别</th><th>操作</th><th>成绩</th></tr>";
                    $sql = "SELECT * FROM student";
                    $query = mysqli_query($conn, $sql);
                    while ($row = mysqli_fetch_array($query)) {
                        echo "<form method='post'>";
                        echo "<input type='hidden' type='text' name='select_type' value='s'/>";
                        echo "<tr>";
                        echo "<td><input readonly size='3' type='text' name='s_id' value='{$row["s_id"]}'></td>";
                        echo "<td><input size='5' type='text' name='s_name' value='{$row["s_name"]}'></td>";
                        echo "<td><input type='date' name='s_birth' value='{$row["s_birth"]}'></td>";
                        echo "<td><input size='2' type='text' name='s_sex' value='{$row["s_sex"]}'></td>";
                        echo "<td><input type='submit' name='UPDATE' VALUE='更新'>" .
                            "<input type='submit' name='DELETE' VALUE='退学'>".
                            "<input type='submit' name='WATCH' VALUE='查看'></td></form>"
                        ?>
                        <td>
                            <table align="center" cellpadding="5px" border="2px">
                                <?php
                                $sql_new = "SELECT co.c_name,sc.s_score,sc.s_id,sc.c_id FROM score sc LEFT JOIN course co ON co.c_id=sc.c_id WHERE sc.s_id='{$row["s_id"]}'";
                                $query_new = mysqli_query($conn, $sql_new);
                                while ($row_new = mysqli_fetch_array($query_new)) {
                                    echo "<tr>";
                                    echo "<form method='post'>";
                                    echo "<td>" . $row_new["c_name"] . "</td>";
                                    echo "<td><input size='2' type='text' name='s_score' value='{$row_new["s_score"]}'></td>";
                                    echo "<td><input type='submit' name='UPDATE' VALUE='打分'></td>";
                                    echo "<input type='hidden' type='text' name='s_id' value='{$row_new["s_id"]}'/>";
                                    echo "<input type='hidden' type='text' name='c_id' value='{$row_new["c_id"]}'/>";
                                    echo "<input type='hidden' type='text' name='select_type' value='sc'/>";
                                    echo "</form>";
                                    echo "</tr>";
                                }
                                ?>
                            </table>
                        </td>
                        <?php
                        echo "</tr>";
                    }
                    break;
                case "老师管理":
                    echo "<tr><th>ID</th><th>姓名</th><th>操作</th><th>课程</th></tr>";
                    $sql = "SELECT * FROM teacher";
                    $query = mysqli_query($conn, $sql);
                    while ($row = mysqli_fetch_array($query)) {
                        echo "<form method='post'>";
                        echo "<input type='hidden' type='text' name='select_type' value='t'/>";
                        echo "<tr>";
                        echo "<td><input readonly size='3' type='text' name='t_id' value='{$row["t_id"]}'></td>";
                        echo "<td><input size='5' type='text' name='t_name' value='{$row["t_name"]}'></td>";
                        echo "<td><input type='submit' name='UPDATE' VALUE='更新'>" .
                            "<input type='submit' name='DELETE' VALUE='开除'>".
                            "<input type='submit' name='WATCH' VALUE='查看'></td></form>"
                        ?>
                        <td>
                            <table align="center" cellpadding="5px" border="2px">
                                <?php
                                $sql_new = "SELECT c_id,c_name FROM course WHERE t_id='{$row["t_id"]}'";
                                $query_new = mysqli_query($conn, $sql_new);
                                while ($row_new = mysqli_fetch_array($query_new)) {
                                    echo "<tr>";
                                    echo "<form method='post'>";
                                    echo "<td>" . $row_new["c_id"] . "</td>";
                                    echo "<td>" . $row_new["c_name"] . "</td>";
                                    echo "<td><input type='submit' name='DELETE' VALUE='删除课程'></td>";
                                    echo "<input type='hidden' type='text' name='c_id' value='{$row_new["c_id"]}'/>";
                                    echo "<input type='hidden' type='text' name='t_id' value='{$row["t_id"]}'/>";
                                    echo "<input type='hidden' type='text' name='select_type' value='co'/>";
                                    echo "</form>";
                                    echo "</tr>";
                                }
                                ?>
                            </table>
                        </td>
                        <?php
                        echo "</tr>";
                    }
                    break;
            }
            ?>
        </table>
        <?php
    } elseif (isset($_POST['DELETE'])) {
        if ($_POST['select_type'] == "s") {
            $sql = "DELETE FROM student WHERE s_id='{$_POST['s_id']}'";
            $query = mysqli_query($conn, $sql);
            $sql = "DELETE FROM score WHERE s_id='{$_POST['s_id']}'";
            $query = mysqli_query($conn, $sql);
            echo "<script language=\"JavaScript\">alert(\"退学成功！\");</script>";
        } elseif ($_POST['select_type'] == "t") {
            $sql = "DELETE FROM teacher WHERE t_id='{$_POST['t_id']}'";
            $query = mysqli_query($conn, $sql);
            $sql = "SELECT c_id FROM course WHERE t_id='{$_POST['t_id']}'";
            $query = mysqli_query($conn, $sql);
            while ($row = mysqli_fetch_array($query)) {
                $sql_New = "DELETE FROM course WHERE c_id='{$row["c_id"]}'";
                $query_New = mysqli_query($conn, $sql_New);
                $sql_New = "DELETE FROM score WHERE c_id='{$row["c_id"]}'";
                $query_New = mysqli_query($conn, $sql_New);
            }
            echo "<script language=\"JavaScript\">alert(\"开除成功！\");</script>";
        } elseif ($_POST['select_type'] == "co") {
            $sql = "DELETE FROM course WHERE t_id='{$_POST['t_id']}' AND  c_id='{$_POST['c_id']}'";
            $query = mysqli_query($conn, $sql);
            $sql_New = "DELETE FROM score WHERE c_id='{$_POST['c_id']}'";
            $query_New = mysqli_query($conn, $sql_New);
            echo "<script language=\"JavaScript\">alert(\"退课成功！\");</script>";
        }
    } elseif (isset($_POST['UPDATE'])) {
        if ($_POST['select_type'] == "s") {
            $sql = "UPDATE student SET s_name='{$_POST['s_name']}',s_birth='{$_POST['s_birth']}',s_sex='{$_POST['s_sex']}' WHERE s_id='{$_POST['s_id']}'";
            $query = mysqli_query($conn, $sql);
            echo "<script language=\"JavaScript\">alert(\"更新成功！\");</script>";
        } elseif ($_POST['select_type'] == "t") {
            $sql = "UPDATE teacher SET t_name='{$_POST['t_name']}' WHERE t_id='{$_POST['t_id']}'";
            $query = mysqli_query($conn, $sql);
            echo "<script language=\"JavaScript\">alert(\"更新成功！\");</script>";
        } elseif ($_POST['select_type'] == "sc") {
            $sql = "UPDATE score SET s_score='{$_POST['s_score']}' WHERE s_id='{$_POST['s_id']}' AND c_id='{$_POST['c_id']}'";
            $query = mysqli_query($conn, $sql);
            //echo $sql;
            echo "<script language=\"JavaScript\">alert(\"打分成功！\");</script>";
        }
    }elseif (isset($_POST['WATCH'])) {
        if ($_POST['select_type'] == "s") {
            echo "student.php?id=".$_POST["s_id"];
            Header("location:student.php?id=".$_POST["s_id"]);
        } elseif ($_POST['select_type'] == "t") {
            echo "NB";
            Header("location:teacher.php?id=".$_POST["t_id"]);
        }
    }
    ?>
</fieldset>
</body>

