/*jQuery*/
let navbar = $('.header .flex .navbar');
let menuButton = $('.header .flex #menu-button');

menuButton.click(() => {
   menuButton.toggleClass('fa-times');
   navbar.toggleClass('active');
});

$(window).scroll(() => {
   menuButton.removeClass('fa-times');
   navbar.removeClass('active');
});


$('input[type="number"]').on('input', (event) => {
   let inputNumber = $(event.currentTarget);
   if (inputNumber.val().length > inputNumber.attr('maxlength')) {
      inputNumber.val(inputNumber.val().slice(0, inputNumber.attr('maxlength')));
   }
});