$(function() {

    $("#filter li").click(function() {
  
      var category = $(this).html();
  
      category = category.toLowerCase();
  
      $("#filter li").removeClass("active");
  
      $(this).addClass("active");
  
      $("#locacao-equipamentos li").fadeOut();
  
      $("#locacao-equipamentos li").each(function() {
  
        if ($(this).hasClass(category)) {
          $(this).fadeIn();
        }
  
      });
  
    });
  
  });