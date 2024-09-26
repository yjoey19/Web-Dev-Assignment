<?php
$a[]="Standard";
$a[]="Deluxe";
$a[]="Suite";  //Array with names
$a[]="Executive";
$a[]="Family";
$q=$_REQUEST["q"];  //Get the q parameter from URL
$hint="";

//lookup all hints from array if $q is different from ""
if($q !== ""){
    $q = strtolower($q);
    $len = strlen($q);
    foreach($a as $name){
        if(stristr($q,substr($name,0,$len))){
            //searches for the 1st occurrence of a string inside another string.
            if($hint === ""){
                $hint = $name;
            }
            else{
                $hint .= ",$name";
            }
        }
    }
}
// Output "no suggestion" if no hint was found or output correct values 
echo $hint === ""?"no suggestion":$hint;
?>