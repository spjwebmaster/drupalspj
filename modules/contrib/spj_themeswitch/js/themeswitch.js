(function ($) {
    console.log("themeswitcher")
   
        let themeVar = "theme_default;"
       function setTheme(theme){
            $("html").removeClass("theme_default theme_light theme_dark").addClass(theme);
            // in case of function call without click
            $("#block-spjthemeswitcherblock .btn").each(function(){
                let thisTheme = $(this).attr("data-theme");
                if(thisTheme == theme){
                    $(this).removeClass("btn-outline-secondary").addClass("active btn-secondary")
                } else {
                    $(this).removeClass("active btn-secondary").addClass("btn-outline-secondary")
                }
            })
            themeVar = theme;
       }
        var st = window.localStorage;
        if(st.getItem("spj_theme")){

            setTheme(st.getItem("spj_theme"));
        }
        $(document).on("click", ".block-spj-themeswitch-block a", function(e){
            e.preventDefault();
            let thisTheme = $(this).attr("data-theme");
            setTheme(thisTheme);
            st.setItem("spj_theme", thisTheme);
        });


    
  })(jQuery);