var toc = {
    list: [],
    thisNode: document.querySelector(".block-spj-toc"),

    init: function(){
        console.log("making toc")
        let counter = 1;
        let contentWrapper= this.thisNode.closest(".content");
        if(contentWrapper.querySelectorAll("h2")!=null &&  contentWrapper.querySelectorAll("h2").length>1){
            this.buildTOCoshell();
            
            contentWrapper.querySelectorAll("h2:not(.visually-hidden)").forEach(function(el){
                let text = el.innerText;
                el.setAttribute("id", "heading_" + counter);
                toc.list.push("heading_" + counter);
                if(el.querySelector("a")){
                    text = el.querySelector("a").innerHTML;
                    let newId = el.querySelector("a").id;
                    if(newId!=null && newId!==""){
                        toc.buildTOCoMeat(newId, text);
                    } else {
                        toc.buildTOCoMeat("heading_" + counter, text);
                    }
                } else {
                    toc.buildTOCoMeat("heading_" + counter, text);
                }
                

                counter++;
            });
        } else {
            toc.thisNode.remove();
        }
    },
    
    buildTOCoshell: function(){
        let page = window.location.href;
        let listClass = "normal";
        if(page.indexOf("foi-az")>-1 || page.indexOf("foundation/grants")>-1){
            listClass = "az";
        }
        console.log(listClass, "class")

        let shell = document.createElement("div");
        shell.classList.add("spj_toc");
        let list = document.createElement("ul");
        list.classList.add(listClass);
        shell.append(list);
        this.thisNode.append(shell);
    },
    buildTOCoMeat: function(id, text){
        let listNode = this.thisNode.querySelector("ul");
        let listItem = document.createElement("li");
        let listItemAnchor = document.createElement("a");
        listItemAnchor.innerHTML = text;
        listItemAnchor.setAttribute("href", "#" + id);

        listItem.append(listItemAnchor);
        listNode.append(listItem)
        
    }

}
toc.init();




  (function($, window) {
    var adjustAnchor = function() {

        var $anchor = $(':target'),
                fixedElementHeight = 220;

        if ($anchor.length > 0) {

            $('html, body')
                .stop()
                .animate({
                    scrollTop: $anchor.offset().top - fixedElementHeight
                }, 200);

        }

    };

    $(window).on('hashchange load', function() {
        adjustAnchor();
        console.log("hash change")
    });

})(jQuery, window);