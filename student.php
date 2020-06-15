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

if(isset($_GET["Learn"]) and $_GET["select_course"]){
    $sql = "INSERT INTO score VALUES('{$_GET["id"]}','{$_GET["select_course"]}',0)";
    $query = mysqli_query($conn, $sql);
    Header('location:student.php?id=' . $_GET["id"]);
}

//学生查询
$sql = "SELECT st.*,co.c_name,sc.s_score,te.t_name FROM student st LEFT JOIN score sc on sc.s_id=st.s_id LEFT JOIN course co ON co.c_id=sc.c_id LEFT JOIN teacher te ON te.t_id=co.t_id WHERE st.s_id='{$_GET['id']}'";
$query = mysqli_query($conn, $sql);
$course_info = array();
$stu_info = array();
while ($row = mysqli_fetch_array($query)) {
    $stu_info = array("name" => $row["s_name"], "birth" => $row["s_birth"], "sex" => $row["s_sex"]);
    array_push($course_info, $row);
}
?>
<head>
    <title>简易教学管理系统【学生端】</title>
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
    <legend>Hi!【<strong><?php echo $stu_info["name"]; ?></strong>】同学，欢迎来到教务系统学生端！</legend>
    <center>
        <form method="get">
            <input type='hidden' size='5' readonly type='text' name='id' value='<?php echo $_GET['id']; ?>'/>
            <select name="select_course" style="width:110px;">
                <option value="" selected="selected">选择课程...</option>
                <?php
                $sql = "SELECT * FROM course";
                $query = mysqli_query($conn, $sql);
                while ($row = mysqli_fetch_array($query)) {
                    echo "<option value='{$row["c_id"]}'>{$row["c_name"]}</option>";
                }
                ?>
                <input type='submit' name='Learn' value='选课'/>
            </select>
        </form>
    </center>
    <table align="center" cellpadding="25px" border="1px">
        <tr>
            <th>生日</th>
            <th>性别</th>
            <th>科目</th>
            <th>成绩</th>
            <th>任课教师</th>
        </tr>
        <?php
        $i = 0;
        $count = count($course_info);
        foreach ($course_info as $course) {
            echo "<tr>";
            if ($i == 0) {
                echo "<td rowspan='{$count}'>{$stu_info['birth']}</td>";
                echo "<td rowspan='{$count}'>{$stu_info['sex']}</td>";
            }
            echo "<td>{$course['c_name']}</td>";
            echo "<td>{$course['s_score']}</td>";
            echo "<td>{$course['t_name']}</td>";

            echo "</tr>";
            $i++;
        }
        ?>
    </table>
</fieldset>
</body>

