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

//开课
if (isset($_GET["Open"]) and $_GET["open_course"]) {
    $sql = "SELECT COUNT(DISTINCT c_id) AS count FROM course";
    $query = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($query);
    $course_num = $row["count"] + 1;
    $sql = "INSERT INTO course VALUES('{$course_num}','{$_GET["open_course"]}','{$_GET["id"]}')";
    $query = mysqli_query($conn, $sql);
    //echo $sql;
    Header('location:teacher.php?id=' . $_GET["id"]);
}

//处理打分
if (isset($_POST['setGrade'])) {
    $sql = "UPDATE score SET s_score='{$_POST['s_score']}' WHERE s_id='{$_POST['s_id']}' AND c_id='{$_POST['c_id']}'";
    $query = mysqli_query($conn, $sql);
    if ($query) {
        echo "<script language=\"JavaScript\">alert(\"打分成功！\");</script>";
        //echo "<script language=\"JavaScript\">alert('{$sql}');</script>";
        //echo $sql;
    } else {
        echo "<script language=\"JavaScript\">alert(\"打分失败！\");</script>";
    }
}

//教师查询
$sql = "SELECT te.t_name,st.*,co.c_id,co.c_name,sc.s_score FROM teacher te LEFT JOIN course co ON co.t_id=te.t_id LEFT JOIN score sc ON sc.c_id=co.c_id LEFT JOIN student st ON st.s_id=sc.s_id WHERE te.t_id='{$_GET['id']}'";
$query = mysqli_query($conn, $sql);

$course_info = array();
$teacher_name = "";

while ($row = mysqli_fetch_array($query)) {
    $teacher_name = $row["t_name"];
    array_push($course_info, $row);
}

$sql = "SELECT COUNT(DISTINCT c_id) AS count FROM course WHERE t_id='{$_GET['id']}'";
$query = mysqli_query($conn, $sql);
$row = mysqli_fetch_array($query);

$course_num = $row["count"];
?>
<head>
    <title>简易教学管理系统【教师端】</title>
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

<fieldset style='width: 50%;font-size: 20px; margin: 0 auto;'>
    <legend>Hi!【<strong><?php echo $teacher_name; ?></strong>】老师，欢迎来到教务系统教师端！</legend>
    <center>
        <form method="get">
            <input type='hidden' size='5' readonly type='text' name='id' value='<?php echo $_GET['id']; ?>'/>
            <input size='15' type='text' name='open_course' value=''/>
            <input type='submit' name='Open' value='开课'/>
        </form>
    </center>
    <table align="center" cellpadding="25px" border="1px">
        <tr>
            <td colspan="7" style="font-size: 20px;">共教授【<?php echo $course_num; ?>】门课程</td>
        </tr>
        <tr>
            <th>ID</th>
            <th>姓名</th>
            <th>生日</th>
            <th>性别</th>
            <th>科目</th>
            <th>成绩</th>
            <th>操作</th>
        </tr>
        <?php
        $count = count($course_info);
        foreach ($course_info as $course) {
            echo "<form method='post'>";
            echo "<tr>";
            echo "<input type='hidden' size='5' readonly type='text' name='c_id' value='{$course['c_id']}'/>";
            echo "<td><input size='5' readonly type='text' name='s_id' value='{$course['s_id']}'/></td>";
            echo "<td>{$course['s_name']}</td>";
            echo "<td>{$course['s_birth']}</td>";
            echo "<td>{$course['s_sex']}</td>";
            echo "<td>{$course['c_name']}</td>";
            echo "<td><input type='number' name='s_score' value='{$course['s_score']}'/></td>";
            echo "<td><input type = 'submit' name='setGrade' value = '打分' /></td>";
            echo "</tr>";
            echo "</form>";
        }
        ?>
    </table>
</fieldset>
</body>

