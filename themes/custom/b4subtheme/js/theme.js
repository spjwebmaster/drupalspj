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

      document.querySelector("#block-secondarynav .navbar-nav li:nth-child(1) a").addEventListener("click", function(e){
        e.preventDefault();
        console.log("show search");
        let searcher = document.querySelector(".flexSearch");
        if(searcher.classList.contains("open")){
          searcher.classList.remove("open"); 
        } else {
          searcher.classList.add("open");
          searcher.querySelector("input[data-drupal-selector='edit-keys']").focus();
        }
      })

      document.querySelectorAll(".layout__region--second .navigation .dropdown-toggle").forEach(function(el){

        // remove dropdown menu for now?
        if(el.closest("li").querySelector(".dropdown-menu")){
          el.closest("li").querySelector(".dropdown-menu").classList.add("nope");
        }

          el.addEventListener("click", function(e){

            let href = e.target.href;
            window.location.href = href;
          })
        
      })
      



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