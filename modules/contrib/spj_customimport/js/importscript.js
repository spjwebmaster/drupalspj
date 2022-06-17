var Ajax = {
    xhr : null,
    request : function (url,method, data,success,failure){
        if (!this.xhr){
            this.xhr = window.ActiveX ? new ActiveXObject("Microsoft.XMLHTTP"): new XMLHttpRequest();
        }
        var self = this.xhr;

        self.onreadystatechange = function () {
            if (self.readyState === 4 && self.status === 200){
                // the request is complete, parse data and call callback
                var response = JSON.parse(self.responseText);
                success(response);
            }else if (self.readyState === 4) { // something went wrong but complete
                failure();
            }
        };
        this.xhr.open(method,url,true);
        if (method === "POST"){
            this.xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            this.xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            this.xhr.send(data);
        }else {
                this.xhr.send();
        }
    },
};

var customimport = {
    init: function(){

        console.log ("Hello Import");
        this.binding();
    },
    postData: function(url, type){


        jQuery.ajax({
            url: url,
            data: {"ajax": true, "importSubmit": true},
            type: "post",
            success: function(res){
                //console.log(res);
                customimport.parseAndBuildList(res, type);
            }, 
            error: function(e){

            }
        })

    },
    formatDate(date) {
        var d = new Date(date),
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();
    
        if (month.length < 2) 
            month = '0' + month;
        if (day.length < 2) 
            day = '0' + day;
    
        return [year, month, day].join('-');
    },
    masterList:null,
    parseAndBuildList: function(res, type){
        customimport.masterList = [];
        let temp = document.createElement("div");
        temp.innerHTML = res;
        let node = temp.querySelector("#loaded");
        let text = node.innerText;
        let nodeDiv = document.createElement("div");
        nodeDiv.innerHTML = text;

        console.log("loaded content", type);
        if(type=="foundation"){
            nodeDiv.querySelector("#topnavigation").closest(".contentinner").querySelectorAll(".table21, .table22").forEach(function(row){

                //console.log("row", row);
                let twitter = "";
                let email = "";
                row.querySelectorAll("a").forEach(function(a){
                    if(a.getAttribute("href").indexOf("mailto")>-1){
                        email = a.getAttribute("href");
                        email = email.replace("mailto:", "");
                    } else {
                        twitter = a.innerText;
                    }
                });
                let body = "";
                if(row.querySelector("span.smallish:not(.linklike)")!=null){
                    body = row.querySelector("span.smallish:not(.linklike)").innerHTML;

                } else {
                    if(row.querySelector("p.smallish")!=null){
                        body = row.querySelector("p.smallish").innerHTML;
                    }
                }
                let image = "";
                if(row.querySelector("img")!=null){
                    image = row.querySelector("img").getAttribute("src");
                }

                let temp = {
                    title: row.querySelector("b").innerText.replace("\n",""),
                    field_title: "",
                    field_email: email,
                    field_twitter: twitter,
                    body: body,
                    field_profile_image: "https://spj.org/" + image

                }
                customimport.masterList.push(temp);
            });
        }
        if(type=="whistle"){
            nodeDiv.querySelectorAll(".biobox").forEach(function(row){

                console.log("row", row);
                let twitter = "";
                let email = "";
                let titletext = row.querySelector("h3.bold");
                let image = titletext.querySelector("img").getAttribute("src");
                image = image.replace("../","");
                let tempdiv = document.createElement("div");
                tempdiv.innerHTML = titletext.innerHTML;
                tempdiv.querySelector("img").remove();

                let titleall = tempdiv.innerHTML.split("<br>");
                let title = titleall[0];
                title = title.replaceAll("\n","");
                let body = "";
                row.querySelectorAll("p").forEach(function(p){
                    if(p.querySelector("a")){
                        twitter = p.querySelector("a").innerText;
                    } else {
                        body = p.innerHTML;
                    }
                });
                

                let temp = {
                    title: title,
                    field_title: "",
                    field_email: email,
                    field_twitter: twitter,
                    body: body,
                    field_profile_image: "https://spj.org/" + image

                }
                customimport.masterList.push(temp);
            });
        }
        if(type=="foi"){
            nodeDiv.querySelectorAll("div.biotexter").forEach(function(row){

                //console.log(row)
                if(row.querySelector(".headline1")){
                let state = row.querySelector(".headline1").innerHTML;
                row.querySelectorAll(".headline3").forEach(function(headline){
                    
                    console.log(state, ":", headline)
                    

                });
                

                let temp = {
                    state: state,
                   

                }
                customimport.masterList.push(temp);
                }
            });
        }
        else if(type=="ldf"){
            let size = nodeDiv.querySelector("#topnavigation").closest(".contentinner").querySelectorAll(".entry").length;
            console.log("ldf size", size)
            nodeDiv.querySelector("#topnavigation").closest(".contentinner").querySelectorAll(".entry").forEach(function(el){
                let pubDate = null;  
                let body = "";
                let title = el.querySelector(".headline4").innerText;
                if(el.querySelector(".smallish")){
                    pubDate = el.querySelector(".smallish").innerText;
                    pubDate = pubDate.replace("Submitted ", "");
                    pubDate = pubDate.replace("Filed ", "");
                    pubDate = pubDate.replace("Sent ", "");
                   
                        // cool 
                   
                } else {
                    //console.log(el);
                    if(Date.parse(title)){
                        pubDate = title;
                    } 
                    
                     
                }

               
                var d = customimport.formatDate(pubDate);

                body = el.innerHTML;
                body= body.replace(title, "");
                let tempDiv = document.createElement("div");
                tempDiv.innerHTML = body;
                let newbody = "";
                tempDiv.querySelectorAll("a").forEach(function(f){
                    console.log(f);
                    if(f.getAttribute("href")){
                        let href = f.getAttribute("href");
                        if(href.indexOf(".pdf")){
                            href = href.replace("https://www.spj.org/", "");
                            f.setAttribute("href", href);
                        }
                    }
                })
                newbody = tempDiv.innerHTML;
                    
                let temp = {
                    title: title,
                    body: newbody,
                    pubDate: d
                }
                customimport.masterList.push(temp);
            })
        }
        else if(type=="hq"){
            nodeDiv.querySelector("#topnavigation").closest(".contentinner").querySelectorAll(".table1row21alt, .table1row22alt").forEach(function(row){

                //console.log("row", row);
                let twitter = "";
                let email = "";
                row.querySelectorAll("a").forEach(function(a){
                    if(a.getAttribute("href").indexOf("mailto")>-1){
                        email = a.getAttribute("href");
                        email = email.replace("mailto:", "");
                    } else {
                        twitter = a.innerText;
                    }
                });
                let body = "";
                if(row.querySelector("span.smallish:not(.linklike)")!=null){
                    body = row.querySelector("span.smallish:not(.linklike)").innerHTML;

                } else {
                    if(row.querySelector("p.smallish")!=null){
                        body = row.querySelector("p.smallish").innerHTML;
                    }
                }
                let image = "";
                if(row.querySelector("img")!=null){
                    image = row.querySelector("img").getAttribute("src");
                }

                let temp = {
                    title: row.querySelector("b").innerText.replace("\n",""),
                    field_title: row.querySelector(".headline5").innerText,
                    field_email: email,
                    field_twitter: twitter,
                    body: body,
                    field_profile_image: "https://spj.org/" + image

                }
                customimport.masterList.push(temp);
                

            })
        } else if(type=="stc"){
            nodeDiv.querySelector("#topnavigation").closest(".contentinner").querySelectorAll(".table21, .table22").forEach(function(row){

                let thisTitle = "SPJ Student Trustee Council Member";
                let twitter = "";
                let email = "";
                if(row.children.length){
                    if(row.querySelector("a")!=null){
                        let a = row.querySelector("a");
                        twitter = a.innerText;
                    }
                    let image = "";
                    if(row.querySelector("img")!=null){
                        image = row.querySelector("img").getAttribute("src");
                    }
                
                    let temp = {
                        title: row.querySelector("b").innerText,
                        field_title: thisTitle,
                        field_email: email,
                        field_twitter: twitter,
                        body: "",
                        field_profile_image: "https://spj.org/" + image

                    }
                    customimport.masterList.push(temp);
                }

            });
        
        } else if(type=="board" || type=="rc"){
            nodeDiv.querySelector("#topnavigation").closest(".contentinner").querySelectorAll(".table21, .table22").forEach(function(row){

                //console.log("row", row);
                let twitter = "";
                let email = "";
                if(row.children.length){
                    if(row.querySelector("p")!=null){
                        
                       
                        
                        if(true){
                        let thisTitle = "Director At Large";
                        row.querySelectorAll("p").forEach(function(r){

                            let name ="";
                            if(r.querySelector("b")!=null){
                                name = r.querySelector("b").innerText.replace("\n","");
                            
                            r.querySelectorAll("a").forEach(function(a){
                                if(a.getAttribute("href").indexOf("mailto")>-1){
                                    email = a.getAttribute("href");
                                    email = email.replace("mailto:", "");
                                } else {
                                    twitter = a.innerHTML;
                                }
                            });

                            let image = "";
                            if(r.querySelector("img")!=null){
                                image = row.querySelector("img").getAttribute("src");
                            }

                            let temp = {
                                title: name ,
                                field_title: thisTitle,
                                field_email: email,
                                field_twitter: twitter,
                                body: "",
                                field_profile_image: "https://spj.org/" + image
    
                            }
                            customimport.masterList.push(temp);
                            }
                        })
                        }
                    
                    } else {
                        row.querySelectorAll("a").forEach(function(a){
                            if(a.getAttribute("href").indexOf("mailto")>-1){
                                email = a.getAttribute("href");
                                email = email.replace("mailto:", "");
                            } else {
                                twitter = a.innerText;
                            }
                        });
                    
                        let image = "";
                        if(row.querySelector("img")!=null){
                            image = row.querySelector("img").getAttribute("src");
                        }
                        let name = "";
                        if(row.querySelector(".headline4")!=null){
                            name = row.querySelector(".headline4").innerText
                        } else {
                            name = row.querySelector(".headline5").innerText
                        }
                        let temp = {
                            title: row.querySelector("b").innerText.replace("\n",""),
                            field_title: name,
                            field_email: email,
                            field_twitter: twitter,
                            body: "",
                            field_profile_image: "https://spj.org/" + image

                        }
                        customimport.masterList.push(temp);
                    }
                }
                

            })
        }
        console.log(customimport.masterList);
        let resultContainer = document.getElementById("loadData");
            resultContainer.innerHTML = JSON.stringify(customimport.masterList);
    },
    binding: function(){
        if(document.querySelector(".loadScrape")!=null){
        document.querySelector(".loadScrape").addEventListener("click", function(e){
            e.preventDefault();
            let type = e.target.getAttribute("data-type");
            let url  = e.target.href;
            customimport.postData(url, type)
            

        })
        }
    }
}
    
customimport.init();
