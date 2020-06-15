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
    <title>简易教学管理系统</title>
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

<center style="width: 35%;margin:0 auto;">
    <fieldset>
        <legend>课程查询</legend>
        <form method="get">
            <input type="submit" value="所有课程" name="course_all"/>
            <input type="text" name="course_search"/>
            <input type="submit" value="课程查询" name="course"/>
        </form>
        <table align="center" cellpadding="10px" border="1px">
            <?php
            if (isset($_GET['course_all'])) {
                $sql = "SELECT te.t_name,co.c_name,AVG(sc.s_score) AS AVG FROM course co LEFT JOIN score sc ON sc.c_id=co.c_id LEFT JOIN teacher te ON te.t_id=co.t_id group by sc.c_id";
            } elseif (isset($_GET['course'])) {
                $sql = "SELECT te.t_name,co.c_name,AVG(sc.s_score) AS AVG FROM course co LEFT JOIN score sc ON sc.c_id=co.c_id LEFT JOIN teacher te ON te.t_id=co.t_id WHERE co.c_name='{$_GET["course_search"]}'";
            }
            if (isset($sql)) {
                ?>
                <tr>
                    <th>任课教师</th>
                    <th>课程</th>
                    <th>均分</th>
                </tr>
                <?php
                $query = mysqli_query($conn, $sql);
                while ($row = mysqli_fetch_array($query)) {
                    echo "<tr>";
                    echo "<td>" . $row["t_name"] . "</td>";
                    echo "<td>" . $row["c_name"] . "</td>";
                    echo "<td>" . $row["AVG"] . "</td>";
                    echo "</tr>";
                }
            }
            ?>
        </table>
    </fieldset>
</center>
<?php
//判断登录
// 处理用户登录信息
if (isset($_POST['login'])) {
    // 存储 session 数据(用户的登录信息)
    $_SESSION['username'] = $_POST['username'];
    $_SESSION['password'] = $_POST['password'];
    $_SESSION['usertype'] = $_POST['usertype'];

    //用户登录查询
    $sql = "SELECT * FROM users WHERE username='{$_SESSION['username']}' AND password='{$_SESSION['password']}' AND usertype='{$_SESSION['usertype']}' LIMIT 1";
    $query = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($query);
    if ($row) {
        //如果用户信息正确
        echo "<script language=\"JavaScript\">alert(\"登陆成功！\");</script>";
        Header('location:login.php?usertype=' . $_SESSION['usertype']);
    } else {
        //如果用户信息正确
        echo "<script language=\"JavaScript\">alert(\"账号或密码错误！\");</script>";
    }
} elseif (isset($_GET['usertype'])) {
    ?>
    <center>
        <form method="get">
            <input hidden type="text" name="usertype" value="<?php echo $_GET['usertype']; ?>"/>
            <input type="text" name="search_name"/>
            <input type="submit" value="Search" name="search"/>
        </form>
    </center>
    <table align="center" cellpadding="10px" border="1px">
        <?php
        switch ($_SESSION['usertype']) {
            case "s":
                if (isset($_GET['search'])) {
                    $sql = "SELECT s_id FROM student WHERE s_name='{$_GET['search_name']}'";
                    $query = mysqli_query($conn, $sql);
                    $row = mysqli_fetch_array($query);
                    if ($row) {
                        Header('location:student.php?id=' . $row["s_id"]);
                    }
                }
                //输出表头
                echo "<tr><th>姓名</th><th>生日</th><th>性别</th><th>操作</th></tr>";
                //用户登录查询
                $sql = "SELECT * FROM student";
                $query = mysqli_query($conn, $sql);
                while ($row = mysqli_fetch_array($query)) {
                    echo "<tr>";
                    echo "<td>" . $row["s_name"] . "</td>";
                    echo "<td>" . $row["s_birth"] . "</td>";
                    echo "<td>" . $row["s_sex"] . "</td>";
                    echo "<td><a href='student.php?id={$row["s_id"]}'>查看</a></td>";
                    echo "</tr>";
                }
                break;
            case "t":
                if (isset($_GET['search'])) {
                    $sql = "SELECT t_id FROM teacher WHERE t_name='{$_GET['search_name']}'";
                    $query = mysqli_query($conn, $sql);
                    $row = mysqli_fetch_array($query);
                    if ($row) {
                        Header('location:teacher.php?id=' . $row["t_id"]);
                    }
                }
                //输出表头
                echo "<tr><th>教师姓名</th><th>授课门数</th><th>操作</th></tr>";
                //用户登录查询
                $sql = "SELECT * FROM teacher";
                $query = mysqli_query($conn, $sql);
                while ($row = mysqli_fetch_array($query)) {
                    echo "<tr>";
                    echo "<td>" . $row["t_name"] . "</td>";

                    $sql_new = "SELECT COUNT(DISTINCT c_id) AS count FROM course WHERE t_id='{$row["t_id"]}'";
                    $query_new = mysqli_query($conn, $sql_new);
                    $row_new = mysqli_fetch_array($query_new);

                    echo "<td>" . $row_new["count"] . "</td>";
                    echo "<td><a href='teacher.php?id={$row["t_id"]}'>查看</a></td>";
                    echo "</tr>";
                }
                break;
            case "a":
                Header('location:admin.php');
                break;
        }
        ?>
    </table>
    <?php
} else {
    ?>
    <!--登录表单-->
    <form action="login.php" method="post" align="center" style="width: 35%;margin:0 auto;">
        <fieldset>
            <legend>用户登录</legend>
            <table align="center" cellpadding="10px">
                <tr>
                    <th>帐号：</th>
                    <td><input type="text" name="username"></td>
                </tr>
                <tr>
                    <th>密码：</th>
                    <td><input type="password" name="password"></td>
                </tr>
                <tr>
                    <th>登录端：</th>
                    <td>
                        <table border="2px" cellpadding="10px">
                            <tr>
                                <td>
                                    <input name="usertype" type="radio" value="s" checked><label>学生端</label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input name="usertype" type="radio" value="t"><label>教师端</label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input name="usertype" type="radio" value="a"> <label>管理员端</label>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="center">
                        <input type="submit" name="login" value="登录">
                    </td>
                </tr>
            </table>
        </fieldset>
    </form>
    <?php
}
?>
</body>
