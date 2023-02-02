var spjads = {
    init: function(){
        console.log("load ads: skyscraper")
        spjads.getData("skyscraper");
    },
    list: [],
    getRandom: function(){
        let max = spjads.list.length;
        let min= 1;
        let randomNum = Math.round(Math.random() * (max - min) + min);

        let entry = spjads.list[(randomNum-1)];

        spjads.buildAd(entry);

    },
    buildAd: function(entry){
        let target = document.querySelector(".block-spj-ads-block .spjad");
        let wrapper = document.createElement("div");
        wrapper.classList.add("spjadentry");

        let image = document.createElement("img");
        image.setAttribute("src", entry.image.attributes.uri.url);
        
        let link = document.createElement("a");
        link.setAttribute("href", link);
        link.setAttribute("target", "_blank");

        link.append(image);
        wrapper.append(link)
        target.append(wrapper);
    },
    getData: function(type){

        let url = `/jsonapi/node/ad?include=field_ad_image`;

        fetch(url)
            .then(response=>response.json())
            .then(data => {

                data.data.forEach(function(ad){
                    let type= ad.relationships.field_ad_type.data.meta.drupal_internal__target_id;
                    let start = ad.attributes.field_start;
                    let end = ad.attributes.field_end;
                    if(start!=null && end!=null){
                        //check if it is out of date range
                    } else {
                        let temp = ad;
                        temp.image = data.included.filter(t=>t.id == ad.relationships.field_ad_image.data.id)[0];
                        spjads.list.push(temp)
                    }

                })
                spjads.getRandom();
            });
        
    }
}
spjads.init();