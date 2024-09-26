<?php

include 'connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   setcookie('user_id', create_unique_id(), time() + 60*60*24*30, '/');
   header('location:index.php');
}

if(isset($_POST['check_in'])){

   $check_in = $_POST['check_in'];
   $check_in = filter_var($check_in, FILTER_SANITIZE_STRING);

   $total_rooms = 0;

   $check_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE check_in = ?");
   $check_bookings->execute([$check_in]);

   while($fetch_bookings = $check_bookings->fetch(PDO::FETCH_ASSOC)){
      $total_rooms += $fetch_bookings['rooms'];
   }

   // if the hotel has total 30 rooms 
   if($total_rooms >= 30){
      $warning_msg[] = 'rooms are not available';
   }else{
      $success_msg[] = 'rooms are available';
   }

}

if(isset($_POST['book'])){

   $booking_id = create_unique_id();
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $room_type = $_POST['room_type'];
   $room_type = filter_var($room_type, FILTER_SANITIZE_STRING);
   $rooms = $_POST['rooms'];
   $rooms = filter_var($rooms, FILTER_SANITIZE_STRING);
   $check_in = $_POST['check_in'];
   $check_in = filter_var($check_in, FILTER_SANITIZE_STRING);
   $check_out = $_POST['check_out'];
   $check_out = filter_var($check_out, FILTER_SANITIZE_STRING);
   $adults = $_POST['adults'];
   $adults = filter_var($adults, FILTER_SANITIZE_STRING);
   $childs = $_POST['childs'];
   $childs = filter_var($childs, FILTER_SANITIZE_STRING);

   $total_rooms = 0;

   $check_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE check_in = ?");
   $check_bookings->execute([$check_in]);

   while($fetch_bookings = $check_bookings->fetch(PDO::FETCH_ASSOC)){
      $total_rooms += $fetch_bookings['rooms'];
   }

   if($total_rooms >= 30){
      $warning_msg[] = 'rooms are not available';
   }else{

      $verify_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE user_id = ? AND name = ? AND email = ? AND number = ? AND room_type = ? AND rooms = ? AND check_in = ? AND check_out = ? AND adults = ? AND childs = ?");
      $verify_bookings->execute([$user_id, $name, $email, $number, $room_type, $rooms, $check_in, $check_out, $adults, $childs]);

      if($verify_bookings->rowCount() > 0){
         $warning_msg[] = 'room booked alredy!';
      }else{
         $book_room = $conn->prepare("INSERT INTO `bookings`(booking_id, user_id, name, email, number, room_type, rooms, check_in, check_out, adults, childs) VALUES(?,?,?,?,?,?,?,?,?,?,?)");
         $book_room->execute([$booking_id, $user_id, $name, $email, $number, $room_type, $rooms, $check_in, $check_out, $adults, $childs]);
         $success_msg[] = 'room booked successfully!';
      }

   }

}

if(isset($_POST['send'])){

   $id = create_unique_id();
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $message = $_POST['msg'];
   $message = filter_var($message, FILTER_SANITIZE_STRING);

   $verify_message = $conn->prepare("SELECT * FROM `messages` WHERE name = ? AND email = ? AND number = ? AND message = ?");
   $verify_message->execute([$name, $email, $number, $message]);

   if($verify_message->rowCount() > 0){
      $warning_msg[] = 'message sent already!';
   }else{
      $insert_message = $conn->prepare("INSERT INTO `messages`(id, name, email, number, message) VALUES(?,?,?,?,?)");
      $insert_message->execute([$id, $name, $email, $number, $message]);
      $success_msg[] = 'message send successfully!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <style>
      #map{
        height: 400px;
        width: 100%;
      }

      .weather{
         position: absolute;
         top: 20px;
         right: 20px;
         padding: 1px;
         font-family: Arial, sans-serif;
         font-size: 16px;
         color: #d4af37;
      }
    </style>

   <title>LuxStaY Hotel</title>

   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="style.css">

</head>
<body>

<!-- header section starts  -->

<section class="header">

   <div class="flex">
      <a href="#home" class="logo" style="font-size: 30px;">LuxStaY Hotel</a>
      <div id="menu-btn" class="fas fa-bars"></div>
   </div>

   <!--weather-->
   <div class="weather">
   <?php
   $api_key = 'ec073a7ba3c191d4b5c94887d01c2506';
   $city_name = 'Ipoh';

   // Construct the API request URL
   $url = "https://api.openweathermap.org/data/2.5/weather?q=$city_name&appid=$api_key&units=metric";

   // Make the API request using cURL
   $curl = curl_init();
   curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_SSL_VERIFYHOST => false,
      CURLOPT_SSL_VERIFYPEER => false
   ));
   $response = curl_exec($curl);
   curl_close($curl);

   // Parse the API response
   $data = json_decode($response);

   // Check if the API request was successful
   if ($data->cod == 200) {
       // Extract the relevant weather data
      $temperature = $data->main->temp;
      $description = $data->weather[0]->description;
      $icon = $data->weather[0]->icon;

      // Display the weather data
      echo "<p>Current temperature in $city_name: $temperature&deg;C</p>";
      echo "<p>Weather description: $description</p>";
      echo "<img src='http://openweathermap.org/img/w/$icon.png' alt='$description'>";
   } else {
       // Display an error message
      echo "Unable to retrieve weather data for $city_name";
   }
   ?>
   </div>

<br><br>

<!--translate-->

<div>
   <script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    <script type="text/javascript">
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'en', // Set the default language of the page
                includedLanguages: 'ar,zh-CN,fr,de,it,ja,ko,pt,ru,es', // Set the languages to include in the dropdown menu
                layout: google.translate.TranslateElement.InlineLayout.SIMPLE, // Set the layout of the translation widget
                autoDisplay: false // Disable automatic display of the widget
            }, 'google_translate_element');
        }
    </script>
   
    <div id="google_translate_element"></div>

    
    <script type="text/javascript">
        function triggerTranslation() {
            // Get the selected language code from the translation widget
            var language = document.querySelector('#google_translate_element select').value;
            // Translate the entire webpage to the selected language
            google.translate.translatePage(language);
        }
        // Add an event listener to the translation widget to trigger the translation when the user selects a language
        document.querySelector('#google_translate_element select').addEventListener('change', triggerTranslation);
    </script>

</div>
      
   <nav class="navbar">
      <a href="#home">Home</a>
      <a href="#about">About</a>
      <a href="#reservation">Reservation</a>
      <a href="#gallery">Gallery</a>
      <a href="#contact">Contact</a>
      <a href="#reviews">Reviews</a>
   </nav>

</section>

<!-- header section ends -->

<!-- home section starts  -->

<section class="home" id="home">

   <div class="swiper home-slider">

      <div class="swiper-wrapper">

         <div class="box swiper-slide">
            <img src="images/home_img_1.jpg" alt="">
            <div class="flex">
               <h3>Luxurious Rooms</h3>
            </div>
         </div>

         <div class="box swiper-slide">
            <img src="images/home_img_2.jpg" alt="">
            <div class="flex">
               <h3>LuxStaY Cafe</h3>
               <a href="#reservation" class="btn">Make a reservation</a>
            </div>
         </div>

         <div class="box swiper-slide">
            <img src="images/home_img_3.jpg" alt="">
            <div class="flex">
               <h3>Boardway Lounge</h3>
               <a href="#contact" class="btn">Contact us</a>
            </div>
         </div>

      </div>

      <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div>

   </div>

</section>

<!-- home section ends -->

<!-- about section starts  -->

<section class="about" id="about">

   <div class="row">
      <div class="image">
         <img src="images/about_img_1.jpg" alt="">
      </div>
      <div class="content">
         <h3>Gym Room</h3>
         <p>Stay active and energized with 24/7 access to our well-equipped gym room at LuxStaY</p>
         <a href="#reservation" class="btn">make a reservation</a>
      </div>
   </div>

   <div class="row revers">
      <div class="image">
         <img src="images/about_img_2.jpg" alt="">
      </div>
      <div class="content">
         <h3>best foods and drinks</h3>
         <p>At LuxStaY, guests can enjoy delicious meals made with fresh, high-quality ingredients and expertly prepared by our skilled chefs in a warm and inviting atmosphere with friendly service.</p>
         <a href="#contact" class="btn">contact us</a>
      </div>
   </div>

   <div class="row">
      <div class="image">
         <img src="images/about_img_3.jpg" alt="">
      </div>
      <div class="content">
         <h3>swimming pool</h3>
         <p>Experience relaxation and rejuvenation in the tropical setting of LuxStaY's outdoor swimming pool.</p>
      </div>
   </div>

</section>

<!-- about section ends -->

<!-- services section starts  -->

<section class="services">

   <div class="box-container">

      <div class="box">
         <img src="images/icon-1.png" alt="">
         <h3>food & drinks</h3>
         <p>Indulge in a memorable culinary experience at LuxStaY, featuring fresh, expertly prepared cuisine and a variety of drinks.</p>
      </div>

      <div class="box">
         <img src="images/icon-2.png" alt="">
         <h3>outdoor dining</h3>
         <p>Savor delicious meals in the fresh air at LuxStaY's outdoor dining area, surrounded by a beautiful setting.</p>
      </div>

      <div class="box">
         <img src="images/icon-3.png" alt="">
         <h3>beach view</h3>
         <p>Enjoy breathtaking views of the beach from LuxStaY's prime location, perfect for a relaxing escape.</p>
      </div>

      <div class="box">
         <img src="images/icon-4.png" alt="">
         <h3>decorations</h3>
         <p>Experience sophisticated and stylish décor at LuxStaY, creating a warm and inviting atmosphere for guests.</p>
      </div>

      <div class="box">
         <img src="images/icon-5.png" alt="">
         <h3>swimming pool</h3>
         <p>Take a refreshing swim in LuxStaY's stunning outdoor swimming pool, surrounded by lush tropical gardens.</p>
      </div>

      <div class="box">
         <img src="images/icon-6.png" alt="">
         <h3>resort beach</h3>
         <p>Relax on the pristine sandy beach at LuxStaY, surrounded by breathtaking views of the ocean and lush tropical landscapes.</p>
      </div>

   </div>

</section>

<!-- services section ends -->

<!-- reservation section starts  -->

<section class="reservation" id="reservation">

   <form action="" method="post">
      <h3>make a reservation</h3>
      <div class="flex">
         <div class="box">
            <p>Name <span>*</span></p>
               <input type="text" name="name" maxlength="50" required placeholder="enter your name" class="input">
         </div>
         <div class="box">
            <p>Email address<span>*</span></p>
            <input type="email" name="email" maxlength="50" required placeholder="enter your email" class="input">
         </div>
         <div class="box">
            <p>Contact no <span>*</span></p>
            <input type="number" name="number" maxlength="10" min="0" max="9999999999" required placeholder="enter your number" class="input">
         </div>

         <div class="box">
            <p>Room types<span>*</span></p>
            <input type="text" onkeyup="showHint(this.value)" name="room_type" maxlength="10" min="0" max="9999999999" required placeholder="enter your room type (standard/deluxe/suite/executive/family)" class="input">
            <p style="font-size: 15px;">Suggestion: <span id="txtHint"></span></p>
         </div>

         <div class="box">
            <p>Rooms <span>*</span></p>
            <select name="rooms" class="input" required>
               <option value="1" selected>1 room</option>
               <option value="2">2 rooms</option>
               <option value="3">3 rooms</option>
               <option value="4">4 rooms</option>
               <option value="5">5 rooms</option>
               <option value="6">6 rooms</option>
            </select>
         </div>
         <div class="box">
            <p>Check in <span>*</span></p>
            <input type="date" name="check_in" class="input" required>
         </div>
         <div class="box">
            <p>Check out <span>*</span></p>
            <input type="date" name="check_out" class="input" required>
         </div>
         <div class="box">
            <p>Adults <span>*</span></p>
            <select name="adults" class="input" required>
               <option value="1" selected>1 adult</option>
               <option value="2">2 adults</option>
               <option value="3">3 adults</option>
               <option value="4">4 adults</option>
               <option value="5">5 adults</option>
               <option value="6">6 adults</option>
            </select>
         </div>
         <div class="box">
            <p>Childs <span>*</span></p>
            <select name="childs" class="input" required>
               <option value="0" selected>0 child</option>
               <option value="1">1 child</option>
               <option value="2">2 childs</option>
               <option value="3">3 childs</option>
               <option value="4">4 childs</option>
               <option value="5">5 childs</option>
               <option value="6">6 childs</option>
            </select>
         </div>
      </div>

      <script>
      function showHint(str){
        if(str.length == 0){
            document.getElementById("txtHint").innerHTML = "";
            return;
        }
        else{
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function(){
                if(this.readyState == 4 && this.status == 200){
                    document.getElementById("txtHint").innerHTML = this.responseText;
                }
            }
            xmlhttp.open("GET","roomType.php?q="+str,true);
            xmlhttp.send();
        }
    }
   </script>

      <input type="submit" value="book now" name="book" class="btn">

   </form>

</section>

<!-- reservation section ends -->

<!-- gallery section starts  -->

<section class="gallery" id="gallery">

   <div class="swiper gallery-slider">
      <div class="swiper-wrapper">
         <img src="images/gallery_image_1.jpg" class="swiper-slide" alt="">
         <img src="images/gallery_img_2.jpg" class="swiper-slide" alt="">
         <img src="images/gallery_img_3.jpg" class="swiper-slide" alt="">
         <img src="images/gallery_img_4.jpg" class="swiper-slide" alt="">
         <img src="images/gallery_img_5.jpg" class="swiper-slide" alt="">
         <img src="images/gallery_img_6.jpg" class="swiper-slide" alt="">
      </div>
      <div class="swiper-pagination"></div>
   </div>

</section>

<!-- gallery section ends -->

<!-- contact section starts  -->

<section class="contact" id="contact">

   <div class="row">

      <form action="" method="post">
         <h3>send us message</h3>
         <input type="text" name="name" required maxlength="50" placeholder="Enter your name" class="box">
         <input type="email" name="email" required maxlength="50" placeholder="Enter your email" class="box">
         <input type="text" name="number" required maxlength="10" min="0" max="9999999999" placeholder="Enter your number" class="box">
         <textarea name="msg" class="box" required maxlength="1000" placeholder="Enter your message" cols="30" rows="10"></textarea>
         <input type="submit" value="send message" name="send" class="btn">
      </form>

      <div class="faq">
         <h3 class="title">frequently asked questions</h3>
         <div class="box active">
            <h3>How to cancel?</h3>
            <p>Cancellations can be made by contacting our reservation team at <a href="tel:01136032770"><i class="fas fa-phone"></i> +601136032770</a> or by emailing <a href="mailto:esther.ng@qiu.edu.my"><i class="fas fa-envelope"></i> esther.ng@qiu.edu.my</a>.</p>
         </div>
         <div class="box">
            <h3>Is there any vacancy?</h3>
            <p>For the most up-to-date information on availability, please contact our reservation team at <a href="tel:01136032770"><i class="fas fa-phone"></i> +601136032770</a> or visit our website to check availability and make a booking</p>
         </div>
         <div class="box">
            <h3>What are the payment methods?</h3>
            <p>We accept a range of payment methods, including Credit Card, Debit Card, TNG, Online Banking and Cash. Our reservation team will be able to assist you with your payment options.</p>
         </div>
         <div class="box">
            <h3>How to claim coupons codes?</h3>
            <p>Coupon codes can be entered during the booking process on our website or by mentioning the code to our reservation team when making a booking over the phone.</p>
         </div>
      </div>

   </div>

</section>

<!-- contact section ends -->

<!-- reviews section starts  -->

<section class="reviews" id="reviews">

   <div class="swiper reviews-slider">

      <div class="swiper-wrapper">
         <div class="swiper-slide box">
            <img src="images/pic-1.png" alt="">
            <h3>William</h3>
            <p>Fantastic hotel with excellent service and comfortable rooms. Convenient location and great experience. Highly recommended.</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/pic-2.png" alt="">
            <h3>Olivia</h3>
            <p>Professional and well-equipped hotel for business travelers. Good facilities, great staff and a convenient location. Highly recommended.</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/pic-3.png" alt="">
            <h3>Alex</h3>
            <p>Family-friendly hotel with great facilities for kids and a comfortable stay. Staff went out of their way to make sure we had a great time. Highly recommended.</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/pic-4.png" alt="">
            <h3>Sophia</h3>
            <p>Affordable and clean hotel with friendly staff. Good value for money and a convenient location. Highly recommended.</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/pic-5.png" alt="">
            <h3>James</h3>
            <p>Romantic hotel with peaceful atmosphere and special touches from staff. Comfortable rooms and great facilities. Highly recommended for couples.</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/pic-6.png" alt="">
            <h3>Emma</h3>
            <p>Safe and comfortable hotel for solo travelers. Good facilities, friendly staff and a convenient location. Highly recommended.</p>
         </div>
      </div>

      <div class="swiper-pagination"></div>
   </div>

</section>

<!-- reviews section ends  -->

<!--Google maps API starts-->
<div id="map"></div>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCZs3cMVyoTw2J3Hkax1Zj9dlW3oMX_Bf4&callback=initMap"></script>
    <script src="map.js"></script>

    <?php
  $api_key = "AIzaSyCZs3cMVyoTw2J3Hkax1Zj9dlW3oMX_Bf4";
?>
<!--Google maps API ends-->

<!-- footer section starts  -->

<section class="footer">

   <div class="box-container">

      <div class="box">
         <a href="tel:01158849928"><i class="fas fa-phone"></i> +601158849928</a>
         <a href="tel:0125850497"><i class="fas fa-phone"></i> +60125850497</a>
         <a href="mailto:joey.yeoh@qiu.edu.my"><i class="fas fa-envelope"></i> joey.yeoh@qiu.edu.my</a>
         <a href="https://goo.gl/maps/kw2jDGD2EMo29xu67"><i class="fas fa-map-marker-alt"></i> Ipoh, Perak </a>
      </div>

      <div class="box">
         <a href="#home">Home</a>
         <a href="#about">About</a>
         <a href="#reservation">Reservation</a>
         <a href="#gallery">Gallery</a>
         <a href="#contact">Contact</a>
         <a href="#reviews">Reviews</a>
      </div>

      <div class="box">
         <a href="https://www.facebook.com/QuestInternationalUniversityOfficial">Facebook <i class="fab fa-facebook-f"></i></a>
         <a href="https://www.instagram.com/questinternationaluniversity/">Instagram <i class="fab fa-instagram"></i></a>
         <a href="https://www.linkedin.com/school/questinternationaluniversity/">Linkedin <i class="fab fa-linkedin"></i></a>
      </div>

   </div>

   <div class="credit">&copy; copyright @ 2023 by Jia Jie, Joey, Esther | all rights reserved!</div>

</section>
</body>
<!-- footer section ends -->

<script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>

<!-- custom js file link  -->
<script src="script.js"></script>

</body>
</html>





