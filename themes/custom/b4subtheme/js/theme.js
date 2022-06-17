(function ($) {
    console.log("theme js")
        window.onload = function(){
            let height = 70;

            if($("body").hasClass("path-frontpage")){
                height = 80
            }
                
            $(window).on("scroll", function () {
                if ($(this).scrollTop() > height) {
                    $("#header").addClass("not-transparent");
                    $("#header").removeClass("transparent");
                }
                else {
                    $("#header").removeClass("not-transparent");
                    $("#header").addClass("transparent");
                }
            });
        
    }

    window.onload = function(){


      //edit-actions
      document.querySelectorAll("[data-drupal-selector='edit-actions']").forEach(function(el){
        el.onclick= function(){
          let button = el.closest("form").querySelector("[data-drupal-selector='edit-submit']");
          button.click();
        }
          

      });
    if(document.querySelector('.animated')!=null){
        const observer = new IntersectionObserver(entries => {
            // Loop over the entries
            console.log(entries)
            entries.forEach(entry => {
              // If the element is visible
              if (entry.isIntersecting) {
                // Add the animation class
                entry.target.classList.add('animation');
              } else {
                entry.target.classList.remove('animation');
              }
            });
          });
          document.querySelectorAll('.animated').forEach(function(el){
            observer.observe(el);
          })
        }
    }
  })(jQuery);