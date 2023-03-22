//https://support.spjnetwork.org/getData.php?c=CHICAGO_PRO
var spjimpex = {
    url: "https://support.spjnetwork.org/getData.php?c=",
    init: function(){

        if(document.querySelector(".field--name-field-committee-code")){
            let code = document.querySelector(".field--name-field-committee-code .field__item").innerHTML;
            spjimpex.getData(code);
        }
        

    },
    buildList: function(data){
        let par = document.querySelector(".field--name-field-committee-code");
        let addition  = document.createElement("div");
        data.data.forEach(element => {
            let entry  = document.createElement("div");
            entry.innerHTML = `
                <span class="badge bg-success text-white">${element.positioncode}</span>
                <h3>${element.firstName} ${element.lastName}</h3>
                <a href="mailto:${element.email}">Email</a>
            `
            addition.append(entry);
        });
        par.append(addition);
    },
    getData: function(code){
        let newurl = spjimpex.url + code;
        fetch(newurl)
            .then(resp=>resp.json())
            .then(data=>{
                console.log("data",data)
                spjimpex.buildList(data);
            })
    }
}

spjimpex.init();