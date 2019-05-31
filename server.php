<?php
    include 'dbconnect.php';

    $call=$_POST['call'];
    switch($call){
        case "appdata": getappdata($conn); break; 
        case "additem": AddItem($conn); break;
        case "signup":Signup($conn);break;
        case "login":Login($conn);break;
        case "addtocart":addtocart($conn);break;
        case "removefromcart":removefromcart($conn);break;
        case "getcart":getcart($conn);break;
        case "getid":getid($conn);break;
        default: header("Location: http://cbd.drople.in/android/admin.html");
    }

    function getappdata($conn) {
        $baseurl="http://cbd.drople.in/";
        $arr=array();
        $res=mysqli_query($conn, "select * from catagory;");
        $catagory=array();
        while($data=mysqli_fetch_assoc($res)){
            $encode['c_id']=$data['c_id'];
            $encode['cat_name']=$data['cat_name'];
            $encode['c_status']=$data['c_status'];
            $encode['c_picture']=$baseurl.$data['c_picture'];
            array_push($catagory,$encode);
        }

        $res=mysqli_query($conn, "select * from product;");
        $product=array();
        while($data=mysqli_fetch_assoc($res)){
            $encode3['p_id']=$data['p_id'];
            $encode3['p_cat_id']=$data['p_cat_id'];
            $encode3['product_name']=$data['product_name'];
            $encode3['description']=$data['description'];
            $encode3['price']=$data['price'];
            $encode3['quantity']=$data['quantity'];
            $encode3['type']=$data['type'];
            $encode3['feature']=$data['feature'];
            $encode3['p_status']=$data['p_status'];
            $encode3['p_picture']=$baseurl.$data['p_picture'];
            array_push($product,$encode3);
        }

        $res=mysqli_query($conn, "select * from Cities; ");
        $cities=array();
        while($data=mysqli_fetch_assoc($res)){
            $encode4['city_id']=$data['city_id'];
            $encode4['city_name']=$data['city_name'];
            $encode4['city_state']=$data['city_state'];
            array_push($cities,$encode4);
        }


        array_push($arr,$catagory);
        array_push($arr,$product);
        array_push($arr,$cities);
        echo json_encode($arr);
    }

    function AddItem($conn){
        $p_cat_id=$_POST['p_cat_id'];
        $product_name=$_POST['product_name'];
        $product_url=$_POST['product_url'];
        $description=$_POST['description'];
        $price=$_POST['price'];
        $quantity=$_POST['quantity'];
        $type=$_POST['type'];
        $feature=$_POST['feature'];
        $p_status=$_POST['p_status'];
        $p_picture=$_POST['p_picture'];

        $q=mysqli_query($conn,"insert into product (p_cat_id, product_name, product_url, description, price, quantity, type, feature, p_status, p_picture) values ($p_cat_id,$sub_cat_id,'$product_name','$product_url','$description',$price,'$quantity',$type,$feature,$p_status,'$p_picture');");
        if($q) {
            echo "item added";
        }else {
            echo "Couldnt add item";
        }
    }

    function Login($conn) {
        $phone=$_POST['phone'];
        $password=$_POST['password'];
    
        $q=mysqli_query($conn, "select * from Users where phone='$phone';");
        if(mysqli_num_rows($q)==0){
            echo "-1";
        }else {
            $arr=array();
            $data=mysqli_fetch_assoc($q);
            if (strcmp($data['password'],$password)==0){
                $result=array();
                $encode['id']=$data['id'];
                $encode['name']=$data['name'];
                $encode['phone']=$data['phone'];
                $encode['city']=$data['city'];
                $encode['address']=$data['address'];
                $encode['pincode']=$data['pincode'];
                array_push($result,$encode);

                $result2=array();
                $id=$data['id'];
                $res=mysqli_query($conn, "select * from Cart where u_id=$id;");
                while($data=mysqli_fetch_assoc($res)){
                    $encode4['id']=$data['id'];
                    $encode4['u_id']=$data['u_id'];
                    $encode4['product_id']=$data['product_id'];
                    $encode4['datentime']=$data['datentime'];
                    $encode4['quantity']=$data['quantity'];
                    array_push($result2,$encode4);
                }

                $result3=array();
                $res=mysqli_query($conn,"select * from Orders where u_id=$id;");
                while($data=mysqli_fetch_assoc($res)){
                    $encode5['id']=$data['id'];
                    $encode5['token']=$data['token'];
                    $encode5['u_id']=$data['u_id'];
                    $encode5['p_id']=$data['p_id'];
                    $encode5['quantity']=$data['quantity'];
                    $encode5['price']=$data['price'];
                    $encode5['datentime']=$data['datentime'];
                    $encode5['status']=$data['status'];
                    array_push($result3,$encode5);
                }

                array_push($arr,$result);
                array_push($arr,$result2);
                array_push($arr,$result3);
                echo json_encode($arr);
            }else {
                echo "0";
            }
        }
    }


    function Signup($conn) {
        $name=$_POST['name'];
        $password=$_POST['password'];
        $phone=$_POST['phone'];
        $address=$_POST['address'];
        $pincode=$_POST['pincode'];
        $city=$_POST['city'];
    
        $a=mysqli_query($conn, "select * from Users where phone='$phone';");
        if(mysqli_num_rows($a)>0){
            echo "0";
        }else {
            $q=mysqli_query($conn, "insert into Users (name,password,phone,address,pincode,city) values ('$name','$password','$phone','$address',$pincode,'$city');");
        if($q){
            echo "1";
        }else {
            echo "-1";
        }
        }
    }


    function addtocart($conn) {
        $id=$_POST['u_id'];
        $password=$_POST['password'];
        $p_id=$_POST['p_id'];
        $dtn=$_POST['datentime'];
        $quantity=$_POST['quantity'];

        $k=login_chk($conn,$id,$password);
        if ($k==1){
            $q=mysqli_query($conn,"insert into Cart (u_id,product_id,datentime,quantity) values ($id, $p_id, '$dtn', $quantity);");
            echo "1";
        }else {
            echo "0";
        }

    }

    function login_chk($conn, $id, $password) {
        $res=mysqli_query($conn, "select * from Users where id=$id;");
        if (mysqli_num_rows($res)==0) {
            return -1;
        }else {
            $data=mysqli_fetch_assoc($res);
            if (strcmp($password,$data['password'])==0) {
                return 1;
            }else {
                return 0;
            }
        }
    }

    function removefromcart($conn) {
        $c_id=$_POST['c_id'];
        $password=$_POST['password'];
        $id=$_POST['id'];

        $k=login_chk($conn,$id,$password);
        if ($k==1) {
            mysqli_query($conn, "delete from Cart where id=$c_id;");
            echo "1";
        }else {
            echo "0";
        }
    }

    function getcart($conn) {
        $id=$_POST['id'];
        $password=$_POST['password'];

        $k=login_chk($conn,$id,$password);
        if($k==1) {
            $res=mysqli_query($conn, "select * from Cart where u_id=$id;");
            $result=array();
            while($data=mysqli_fetch_assoc($res)){
                $encode['id']=$data['id'];
                $encode['u_id']=$data['u_id'];
                $encode['product_id']=$data['product_id'];
                $encode['quantity']=$data['quantity'];
                $encode['datentime']=$data['datentime'];
                array_push($result,$encode);
            }
            echo json_encode($result);
        }else {
            echo "-1";
        }
    }

    
    function getid($conn) {
        $phone=$_POST['phone'];
        $password=$_POST['password'];

        $res=mysqli_query($conn, "select * from Users where phone='$phone';");
        $data=mysqli_fetch_assoc($res);
        if (strcmp($password,$data['password'])==0){
            echo $data['id'];
        }else {
            echo "-1";
        }
    }
?>