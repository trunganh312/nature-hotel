$(document).ready(function() {
    // Handle submenu toggle
    $('.dropdown-submenu > a').on("click", function(e) {
      var submenu = $(this).next('ul');
      $(this).parent().siblings().find('ul').hide();
      submenu.toggle();
      e.stopPropagation();
      e.preventDefault();
    });
    
    // Close all submenus when clicking elsewhere
    $(document).on("click", function() {
      $('.dropdown-submenu ul').hide();
    });
    
    // Mobile menu button toggle
    $('#mobileMenuBtn').on('click', function() {
      $('.show-nav-content').toggle();
    });
  });