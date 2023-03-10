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
        console.log("dropdown disable")

          el.classList.remove("dropdown-toggle");
          el.removeAttribute("data-toggle")
          el.addEventListener("click", function(e){

            let href = e.target.href;
            window.location.href = href;
          })
        
      })
      

      //views filter
      document.querySelectorAll(".view-id-news .form-filter-toggle").forEach(element=>{
        element.addEventListener("click", function(e){
          e.preventDefault();
          let self = e.target;
          let par = self.closest(".view-filters");
          let sib = par.querySelector(".form--inline");
          if(par.classList.contains("form-hidden")){
            par.classList.remove("form-hidden")
          } else {
            par.classList.add("form-hidden")
          }

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

    // get ?state=XXX preloader for region pages


    if(document.querySelectorAll('.accordian_wrapper')!=null){
      console.log("accordian")
      document.querySelectorAll('.accordian_wrapper').forEach(function(el){
        let trigger = el.querySelector(".accordian_trigger");
        trigger.addEventListener("click", function(e){
          e.preventDefault();
          let wrap = e.target.closest(".accordian_wrapper");
          let summary = wrap.querySelector(".accordian_summary");
          let full =  wrap.querySelector(".accordian_full");
          if(full.classList.contains("shown")){
            full.classList.remove("shown");
            summary.classList.add("shown");
          } else {
            full.classList.add("shown");
            summary.classList.remove("shown");
          }
        })
      })

    }
  })(jQuery);