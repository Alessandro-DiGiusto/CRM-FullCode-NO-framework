<?php  
 //Database connectivity  
 $con=mysqli_connect('localhost','root','','tutorials');  
 $sql=mysqli_query($con,"select * from login");  
 //Get Update id and status  
 if (isset($_GET['id']) && isset($_GET['status'])) {  
      $id=$_GET['id'];  
      $status=$_GET['status'];  
      mysqli_query($con,"update login set status='$status' where id='$id'");  
      header("location:index.php");  
      die();  
 }  
 ?>  
 <!DOCTYPE html>  
 <html>  
 <head>  
      <meta charset="utf-8">  
      <meta name="viewport" content="width=device-width, initial-scale=1">  
      <title>How to update pending status on button click using JavaScript with PHP and MySql.</title>  
      <style type="text/css">  
           *{  
                padding: 0;  
                margin: 0;  
                box-sizing: border-box;  
           }  
           body{  
                background: #ccc;  
                display: flex;  
                justify-content: center;  
           }  
           .container{  
                width: 100%;  
                max-width: 900px;  
                margin: 10rem auto;  
           }  
           .container table{  
                width: 100%;  
                margin: auto;  
                border-collapse: collapse;  
                font-size: 2rem;  
           }  
           .container table th{  
                background: red;  
                color: #fff;  
           }  
           select{  
                width: 100%;  
                padding: 0.5rem 0;  
                font-size: 1rem;  
           }  
      </style>  
 </head>  
 <body>  
 <div class="container">  
      <table border="1">  
           <tr>  
                <th>Sl. No.</th>  
                <th>Username</th>  
                <th>Date Time</th>  
                <th>Status</th>  
                <th>Action</th>  
           </tr>  
           <?php  
           $i=1;  
           if (mysqli_num_rows($sql)>0) {  
                 while ($row=mysqli_fetch_assoc($sql)) { ?>  
                 <tr>  
                      <td><?php echo $i++ ?></td>  
                      <td><?php echo $row['username'] ?></td>  
                      <td><?php echo $row['added_on'] ?></td>  
                      <td>  
                           <?php  
                           if ($row['status']==1) {  
                                echo "Pending";  
                           }if ($row['status']==2) {  
                                echo "Accept";  
                           }if ($row['status']==3) {  
                                echo "Reject";  
                           }  
                           ?>  
                      </td>  
                      <td>  
                           <select onchange="status_update(this.options[this.selectedIndex].value,'<?php echo $row['id'] ?>')">  
                                <option value="">Update Status</option>  
                                <option value="1">Pending</option>  
                                <option value="2">Accept</option>  
                                <option value="3">Reject</option>  
                           </select>  
                      </td>  
                 </tr>       
           <?php      }  
            } ?>  
      </table>  
 </div>  
 <script type="text/javascript">  
      function status_update(value,id){  
           //alert(id);  
           let url = "http://localhost/dashboard/cambiostato/index.php";  
           window.location.href= url+"?id="+id+"&status="+value;  
      }  
 </script>  
 </body>  
 </html>  