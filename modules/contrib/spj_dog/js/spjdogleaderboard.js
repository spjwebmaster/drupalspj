var spjdog = {
    target: null,
    data: null,
    init: function(){
        spjdog.target  = document.getElementById("leaderboardDOG");
        spjdog.getData();
    },
    getData: function(){
      let url = "https://dog.spjnetwork.org/dog-leaderboard-feed-js.php";
      fetch(url)
      .then(resp => resp.text())
      .then(str => new window.DOMParser().parseFromString(str, "text/xml"))
      .then(data => {
        
        spjdog.data = [];
        data.querySelectorAll("items item").forEach(item=> {
          let temp = {};
          console.log("item", item);
          let code =  item.getElementsByTagName("affinitycode")[0].innerHTML;
          if(code ==""){
            code = " ";
          }
          temp['guid'] = item.getElementsByTagName("GUID")[0].innerHTML;
          temp['name'] = item.getElementsByTagName("title")[0].innerHTML;
          temp['donation'] = item.getElementsByTagName("donation")[0].innerHTML;
          temp['code'] = code;
          spjdog.data.push(temp);
        })

        console.log("dog data",spjdog.data);
        spjdog.buildDisplay();
      });
    },
    buildDisplay: function(){
      let template = `
        
          
            
      `;
      spjdog.data.forEach(row=>{
        template += `<tr class=''>
            <td class='cols'>${row.name}</td>
            <td class='cols'>${row.donation}</td>
            <td class='cols'>${row.code}</td>
          </tr>`;
      })
      template += ``;
      spjdog.target.innerHTML = template;
    },
    
}
spjdog.init();