
<!DOCTYPE html>
<html>
  
<head>
    
    <style>
        h1,p {
            color: grey;
            font-family: verdana;
        }
        table, th, td {
  border: 1.5px solid black;
  padding-top: 10px;
  padding-bottom: 20px;
  padding-left: 30px;
  padding-right: 40px;
}
    </style>
</head>
  
<body style="background-color:white;">
<div id="chart_div"></div>
    <center>
        <h1>Welcome To Issue Tracker System <br> <?php echo $_SESSION['fullname']?></h1>
        <br>
        <h3><u>User Description</u></h3>
        <br>
    <table >
    <tr>
        <td><b>Username</b></td>
        <td> <?php echo $_SESSION['fullname']?></td>
    </tr>
    <tr>
        <td><b>Email</b></td>
        <td> <?php echo $_SESSION['email']?></td>
    </tr>
    <tr>
        <td><b>Designation</b></td>
        <td> <?php echo $_SESSION['designation']?></td>
    </tr>
    <tr>
        <td><b>Department</b></td>
        <td> <?php 
         require_once('DBConnection.php');

        $myid=$_SESSION['department_id'];
       // echo "$myid";
        $dep="SELECT name from department_list where rowid=$myid";
        $qry23 = $conn->query($dep)->fetchArray();
        echo end($qry23);
       
           
        ?>
        </td>
    </tr>

    <tr>
        <td><b>User Type</b></td>
        <td> <?php 
        if($_SESSION['type']==1)
        {
            echo "Admin";

        }
        else
        {
            echo "Employee";

        }
        
        ?>
        </td>
    </tr>
    </table>
    <br> <br>
<table>
    <tr>
        <td><b>Total Number of Issues</b></td>
        <td>
            <center>
        <?php
    require_once('DBConnection.php');
    $username=$_SESSION['username'];
    $department=$_SESSION['department_id'];
    if($_SESSION['type']==1)
    {
    $sql = "SELECT COUNT(issue_list.issue_id) from issue_list";    
    }
    else
    {
        $sql = "SELECT COUNT(issue_list.issue_id) from issue_list where department_id=$department";
    }
    $qry = $conn->query($sql)->fetchArray();
    echo end($qry);
    ?>
    </center>
        </td>
    </tr>
    <tr>
        <td>Latest issue Uploaded</td>
        <td>
            <?php
    require_once('DBConnection.php');
    $username=$_SESSION['username'];
    $department=$_SESSION['department_id'];
   
   $sql = "SELECT title,date_created from issue_list where department_id=$department AND issue_list.status=0" ;
   $qry = $conn->query($sql)->fetchArray();

  
   if($qry==false)
   {
    echo "----";
   }else{
    $dd=$qry[1];
    $exp=explode(' ',$dd);
    echo "issue: ".$qry[0]." on date: ".$exp[0]." at ".$exp[1];

   }
 
    
  
    ?>
        </td>
    </tr>
</table>
        <br><br>
        
        <p style="font-family:verdana;font-size:20px">DETECT,ISSUE,RESOLVE !!</p>
        
        
    </center>
</body>
  
</html>